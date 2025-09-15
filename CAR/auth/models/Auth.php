<?php
require_once __DIR__ . '/../config/config.php';

class Auth {
    private $conn;
    
    public function __construct() {
        $config = new AuthConfig();
        $this->conn = $config->connect();
    }
    
    public function register($userData) {
        try {
            if ($this->userExists($userData['username'], $userData['email'])) {
                return array('success' => false, 'message' => 'Username or email already exists');
            }
            
            $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
            $verificationToken = generateToken();
            
            $sql = "INSERT INTO users (username, email, password, role, full_name, phone, email_verification_token) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute(array(
                $userData['username'],
                $userData['email'],
                $hashedPassword,
                isset($userData['role']) ? $userData['role'] : 'customer',
                $userData['full_name'],
                isset($userData['phone']) ? $userData['phone'] : null,
                $verificationToken
            ));
            
            if ($result) {
                $userId = $this->conn->lastInsertId();
                return array(
                    'success' => true, 
                    'message' => 'Registration successful! Please check your email to verify your account.',
                    'user_id' => $userId,
                    'verification_token' => $verificationToken
                );
            }
            
            return array('success' => false, 'message' => 'Registration failed');
            
        } catch (Exception $e) {
            return array('success' => false, 'message' => 'Registration error: ' . $e->getMessage());
        }
    }
    
    public function login($identifier, $password, $rememberMe = false) {
        try {
            if ($this->isAccountLocked($identifier)) {
                return array('success' => false, 'message' => 'Account is temporarily locked due to multiple failed login attempts');
            }
            
            $user = $this->getUserByIdentifier($identifier);
            $this->logLoginAttempt($identifier, false);
            
            if (!$user) {
                return array('success' => false, 'message' => 'Invalid credentials');
            }
            
            if ($user['status'] !== 'active') {
                return array('success' => false, 'message' => 'Account is not active');
            }
            
            if (!password_verify($password, $user['password'])) {
                $this->incrementLoginAttempts($user['id']);
                return array('success' => false, 'message' => 'Invalid credentials');
            }
            
            $this->logLoginAttempt($identifier, true);
            $this->resetLoginAttempts($user['id']);
            $this->updateLastLogin($user['id']);
            $this->createSession($user, $rememberMe);
            
            return array(
                'success' => true, 
                'message' => 'Login successful',
                'user' => $user,
                'redirect' => $this->getRedirectUrl($user['role'])
            );
            
        } catch (Exception $e) {
            return array('success' => false, 'message' => 'Login error: ' . $e->getMessage());
        }
    }
    
    public function logout() {
        try {
            if (isset($_SESSION['session_id'])) {
                $this->deleteSession($_SESSION['session_id']);
            }
            
            session_unset();
            session_destroy();
            
            if (isset($_COOKIE['remember_token'])) {
                setcookie('remember_token', '', time() - 3600, '/');
            }
            
            return array('success' => true, 'message' => 'Logged out successfully');
            
        } catch (Exception $e) {
            return array('success' => false, 'message' => 'Logout error: ' . $e->getMessage());
        }
    }
    
    private function userExists($username, $email) {
        $sql = "SELECT id FROM users WHERE username = ? OR email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array($username, $email));
        return $stmt->fetch() !== false;
    }
    
    private function getUserByIdentifier($identifier) {
        $sql = "SELECT * FROM users WHERE username = ? OR email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array($identifier, $identifier));
        return $stmt->fetch();
    }
    
    private function createSession($user, $rememberMe = false) {
        $sessionId = generateToken();
        $expiresAt = date('Y-m-d H:i:s', time() + AuthConfig::SESSION_LIFETIME);
        
        try {
            $sql = "INSERT INTO user_sessions (user_id, session_id, ip_address, user_agent, expires_at) 
                    VALUES (?, ?, ?, ?, ?)";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(array(
                $user['id'],
                $sessionId,
                getClientIP(),
                getUserAgent(),
                $expiresAt
            ));
        } catch (Exception $e) {
            error_log("Session table error: " . $e->getMessage());
        }
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['phone'] = $user['phone'];
        $_SESSION['logged_in'] = true;
        $_SESSION['session_id'] = $sessionId;
        $_SESSION['login_time'] = time();
        
        if ($rememberMe) {
            try {
                $rememberToken = generateToken();
                $rememberExpires = time() + AuthConfig::REMEMBER_ME_DURATION;
                
                setcookie('remember_token', $rememberToken, $rememberExpires, '/');
                
                $sql = "SHOW COLUMNS FROM users LIKE 'remember_token'";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute();
                
                if ($stmt->fetch()) {
                    $sql = "UPDATE users SET remember_token = ?, remember_expires = ? WHERE id = ?";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->execute(array(
                        password_hash($rememberToken, PASSWORD_DEFAULT),
                        date('Y-m-d H:i:s', $rememberExpires),
                        $user['id']
                    ));
                }
            } catch (Exception $e) {
                error_log("Remember me error: " . $e->getMessage());
            }
        }
    }
    
    private function deleteSession($sessionId) {
        try {
            $sql = "UPDATE user_sessions SET is_active = 0 WHERE session_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(array($sessionId));
        } catch (Exception $e) {}
    }
    
    private function logLoginAttempt($identifier, $success) {
        try {
            $sql = "INSERT INTO login_attempts (email, ip_address, user_agent, success) 
                    VALUES (?, ?, ?, ?)";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(array(
                $identifier,
                getClientIP(),
                getUserAgent(),
                $success ? 1 : 0
            ));
        } catch (Exception $e) {}
    }
    
    private function isAccountLocked($identifier) {
        $user = $this->getUserByIdentifier($identifier);
        if (!$user) return false;
        return isset($user['locked_until']) && $user['locked_until'] && strtotime($user['locked_until']) > time();
    }
    
    private function incrementLoginAttempts($userId) {
        try {
            $sql = "UPDATE users SET login_attempts = login_attempts + 1 WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(array($userId));
            
            $user = $this->getUserById($userId);
            if (isset($user['login_attempts']) && $user['login_attempts'] >= AuthConfig::MAX_LOGIN_ATTEMPTS) {
                $lockUntil = date('Y-m-d H:i:s', time() + AuthConfig::LOCKOUT_DURATION);
                $sql = "UPDATE users SET locked_until = ? WHERE id = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute(array($lockUntil, $userId));
            }
        } catch (Exception $e) {}
    }
    
    private function resetLoginAttempts($userId) {
        try {
            $sql = "UPDATE users SET login_attempts = 0, locked_until = NULL WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(array($userId));
        } catch (Exception $e) {}
    }
    
    private function updateLastLogin($userId) {
        try {
            $sql = "UPDATE users SET last_login = NOW() WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(array($userId));
        } catch (Exception $e) {}
    }
    
    public function getUserById($userId) {
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array($userId));
        return $stmt->fetch();
    }
    
    private function getRedirectUrl($role) {
        $redirects = AuthConfig::getLoginRedirects();
        return isset($redirects[$role]) ? $redirects[$role] : AuthConfig::DEFAULT_REDIRECT;
    }
    
    public function validateSession($sessionId) {
        try {
            $sql = "SELECT u.*, s.expires_at 
                    FROM users u 
                    JOIN user_sessions s ON u.id = s.user_id 
                    WHERE s.session_id = ? AND s.is_active = 1 AND s.expires_at > NOW()";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(array($sessionId));
            return $stmt->fetch();
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function requestPasswordReset($email) {
        try {
            $user = $this->getUserByIdentifier($email);
            if (!$user) {
                return array('success' => false, 'message' => 'Email not found');
            }
            
            $resetToken = generateToken();
            $resetExpires = date('Y-m-d H:i:s', time() + 3600);
            
            $sql = "SHOW COLUMNS FROM users LIKE 'password_reset_token'";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            
            if ($stmt->fetch()) {
                $sql = "UPDATE users SET password_reset_token = ?, password_reset_expires = ? WHERE id = ?";
                $stmt = $this->conn->prepare($sql);
                $result = $stmt->execute(array(
                    $resetToken,
                    $resetExpires,
                    $user['id']
                ));
                
                if ($result) {
                    return array(
                        'success' => true, 
                        'message' => 'Password reset link sent to your email',
                        'reset_token' => $resetToken
                    );
                }
            }
            
            return array('success' => false, 'message' => 'Password reset not available');
            
        } catch (Exception $e) {
            return array('success' => false, 'message' => 'Reset error: ' . $e->getMessage());
        }
    }
    
    public function resetPassword($token, $newPassword) {
        try {
            $sql = "SELECT id FROM users WHERE password_reset_token = ? AND password_reset_expires > NOW()";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(array($token));
            $user = $stmt->fetch();
            
            if (!$user) {
                return array('success' => false, 'message' => 'Invalid or expired reset token');
            }
            
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            
            $sql = "UPDATE users SET password = ?, password_reset_token = NULL, password_reset_expires = NULL WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute(array(
                $hashedPassword,
                $user['id']
            ));
            
            if ($result) {
                return array('success' => true, 'message' => 'Password reset successfully');
            }
            
            return array('success' => false, 'message' => 'Failed to reset password');
            
        } catch (Exception $e) {
            return array('success' => false, 'message' => 'Reset error: ' . $e->getMessage());
        }
    }
    
    public function cleanExpiredSessions() {
        try {
            $sql = "UPDATE user_sessions SET is_active = 0 WHERE expires_at < NOW()";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute();
        } catch (Exception $e) {
            return false;
        }
    }
}
?>
