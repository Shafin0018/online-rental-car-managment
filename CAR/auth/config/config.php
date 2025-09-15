<?php
class AuthConfig {
    private $host = 'localhost';
    private $db_name = 'car_rental_system';
    private $username = 'root';
    private $password = '';
    private $conn;
    
    const SESSION_LIFETIME = 3600;
    const MAX_LOGIN_ATTEMPTS = 5;
    const LOCKOUT_DURATION = 900;
    const PASSWORD_MIN_LENGTH = 8;
    const REMEMBER_ME_DURATION = 2592000;
    const DEFAULT_REDIRECT = '../index.php';
    
    public static function getLoginRedirects() {
        return array(
            'admin' => '../admin/index.php',
            'car_owner' => '../index.php',
            'customer' => '../customer/index.php'
        );
    }
    
    public function connect() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password,
                array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
                )
            );
        } catch(PDOException $e) {
            throw new Exception('Connection Error: ' . $e->getMessage());
        }
        
        return $this->conn;
    }
}

if (session_status() == PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 0);
    ini_set('session.use_strict_mode', 1);
    session_start();
}

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function getClientIP() {
    $ipKeys = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 
               'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 
               'REMOTE_ADDR');
    
    foreach ($ipKeys as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (array_map('trim', explode(',', $_SERVER[$key])) as $ip) {
                if (filter_var($ip, FILTER_VALIDATE_IP, 
                    FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                    return $ip;
                }
            }
        }
    }
    
    return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
}

function getUserAgent() {
    return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Unknown';
}

function formatDate($date) {
    return date('M d, Y g:i A', strtotime($date));
}

function setFlashMessage($type, $message) {
    $_SESSION['flash'][$type] = $message;
}

function getFlashMessage($type) {
    if (isset($_SESSION['flash'][$type])) {
        $message = $_SESSION['flash'][$type];
        unset($_SESSION['flash'][$type]);
        return $message;
    }
    return null;
}

function getAllFlashMessages() {
    $messages = isset($_SESSION['flash']) ? $_SESSION['flash'] : array();
    unset($_SESSION['flash']);
    return $messages;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function requireGuest() {
    if (isLoggedIn()) {
        redirectBasedOnRole();
    }
}

function redirectBasedOnRole($role = null) {
    $userRole = $role;
    if ($userRole === null) {
        $userRole = isset($_SESSION['role']) ? $_SESSION['role'] : 'customer';
    }
    
    $redirects = AuthConfig::getLoginRedirects();
    $redirect = isset($redirects[$userRole]) ? $redirects[$userRole] : AuthConfig::DEFAULT_REDIRECT;
    header('Location: ' . $redirect);
    exit;
}

function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = generateToken();
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function checkRateLimit($key, $limit = 10, $window = 3600) {
    $currentTime = time();
    $windowStart = $currentTime - $window;
    
    if (!isset($_SESSION['rate_limit'][$key])) {
        $_SESSION['rate_limit'][$key] = array();
    }
    
    $_SESSION['rate_limit'][$key] = array_filter(
        $_SESSION['rate_limit'][$key], 
        function($timestamp) use ($windowStart) {
            return $timestamp > $windowStart;
        }
    );
    
    if (count($_SESSION['rate_limit'][$key]) >= $limit) {
        return false;
    }
    
    $_SESSION['rate_limit'][$key][] = $currentTime;
    return true;
}
?>
