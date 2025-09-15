<?php
require_once __DIR__ . '/../models/Auth.php';
require_once __DIR__ . '/../config/config.php';

class AuthController {
    private $authModel;
    
    public function __construct() {
        $this->authModel = new Auth();
    }
    
    public function showLogin() {
        requireGuest();
        include __DIR__ . '/../views/login.php';
    }
    
    public function processLogin() {
        requireGuest();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: login.php');
            exit;
        }
        
        if (!checkRateLimit('login_' . getClientIP(), 5, 300)) {
            setFlashMessage('error', 'Too many login attempts. Please try again later.');
            header('Location: login.php');
            exit;
        }
        
        if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('error', 'Invalid request. Please try again.');
            header('Location: login.php');
            exit;
        }
        
        $errors = $this->validateLoginData($_POST);
        
        if (empty($errors)) {
            $identifier = sanitizeInput($_POST['identifier']);
            $password = $_POST['password'];
            $rememberMe = isset($_POST['remember_me']);
            
            $result = $this->authModel->login($identifier, $password, $rememberMe);
            
            if ($result['success']) {
                setFlashMessage('success', $result['message']);
                header('Location: ' . $result['redirect']);
                exit;
            } else {
                setFlashMessage('error', $result['message']);
            }
        } else {
            setFlashMessage('error', implode('<br>', $errors));
        }
        
        $_SESSION['login_form_data'] = [
            'identifier' => $_POST['identifier'] ?? '',
            'remember_me' => $_POST['remember_me'] ?? false
        ];
        
        header('Location: login.php');
        exit;
    }
    
    public function showRegister() {
        requireGuest();
        include __DIR__ . '/../views/register.php';
    }
    
    public function processRegister() {
        requireGuest();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: register.php');
            exit;
        }
        
        if (!checkRateLimit('register_' . getClientIP(), 3, 600)) {
            setFlashMessage('error', 'Too many registration attempts. Please try again later.');
            header('Location: register.php');
            exit;
        }
        
        if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('error', 'Invalid request. Please try again.');
            header('Location: register.php');
            exit;
        }
        
        $errors = $this->validateRegistrationData($_POST);
        
        if (empty($errors)) {
            $userData = [
                'username' => sanitizeInput($_POST['username']),
                'email' => sanitizeInput($_POST['email']),
                'password' => $_POST['password'],
                'full_name' => sanitizeInput($_POST['full_name']),
                'phone' => sanitizeInput($_POST['phone']),
                'role' => sanitizeInput($_POST['role'] ?? 'customer')
            ];
            
            $result = $this->authModel->register($userData);
            
            if ($result['success']) {
                setFlashMessage('success', $result['message']);
                header('Location: login.php');
                exit;
            } else {
                setFlashMessage('error', $result['message']);
            }
        } else {
            setFlashMessage('error', implode('<br>', $errors));
        }
        
        $_SESSION['register_form_data'] = $_POST;
        unset($_SESSION['register_form_data']['password']);
        unset($_SESSION['register_form_data']['confirm_password']);
        
        header('Location: register.php');
        exit;
    }
    
    public function processLogout() {
        requireLogin();
        
        $result = $this->authModel->logout();
        setFlashMessage('success', $result['message']);
        header('Location: login.php');
        exit;
    }
    
    public function showForgotPassword() {
        requireGuest();
        include __DIR__ . '/../views/forgot-password.php';
    }
    
    public function processForgotPassword() {
        requireGuest();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: forgot-password.php');
            exit;
        }
        
        if (!checkRateLimit('forgot_password_' . getClientIP(), 3, 600)) {
            setFlashMessage('error', 'Too many password reset attempts. Please try again later.');
            header('Location: forgot-password.php');
            exit;
        }
        
        if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('error', 'Invalid request. Please try again.');
            header('Location: forgot-password.php');
            exit;
        }
        
        $email = sanitizeInput($_POST['email']);
        
        if (!isValidEmail($email)) {
            setFlashMessage('error', 'Please enter a valid email address.');
            header('Location: forgot-password.php');
            exit;
        }
        
        $result = $this->authModel->requestPasswordReset($email);
        
        setFlashMessage('success', 'If your email is registered, you will receive a password reset link.');
        
        header('Location: forgot-password.php');
        exit;
    }
    
    public function showResetPassword() {
        requireGuest();
        
        $token = $_GET['token'] ?? '';
        if (empty($token)) {
            setFlashMessage('error', 'Invalid reset token.');
            header('Location: login.php');
            exit;
        }
        
        include __DIR__ . '/../views/reset-password.php';
    }
    
    public function processResetPassword() {
        requireGuest();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: login.php');
            exit;
        }
        
        if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('error', 'Invalid request. Please try again.');
            header('Location: login.php');
            exit;
        }
        
        $token = sanitizeInput($_POST['token']);
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm_password'];
        
        $errors = [];
        
        if (empty($token)) {
            $errors[] = 'Invalid reset token.';
        }
        
        if (strlen($password) < AuthConfig::PASSWORD_MIN_LENGTH) {
            $errors[] = 'Password must be at least ' . AuthConfig::PASSWORD_MIN_LENGTH . ' characters long.';
        }
        
        if ($password !== $confirmPassword) {
            $errors[] = 'Passwords do not match.';
        }
        
        if (!empty($errors)) {
            setFlashMessage('error', implode('<br>', $errors));
            header('Location: reset-password.php?token=' . $token);
            exit;
        }
        
        $result = $this->authModel->resetPassword($token, $password);
        
        if ($result['success']) {
            setFlashMessage('success', $result['message'] . ' You can now login with your new password.');
            header('Location: login.php');
        } else {
            setFlashMessage('error', $result['message']);
            header('Location: reset-password.php?token=' . $token);
        }
        exit;
    }
    
    private function validateLoginData($data) {
        $errors = [];
        
        if (empty($data['identifier'])) {
            $errors[] = 'Username or email is required.';
        }
        
        if (empty($data['password'])) {
            $errors[] = 'Password is required.';
        }
        
        return $errors;
    }
    
    private function validateRegistrationData($data) {
        $errors = [];
        
        if (empty($data['username'])) {
            $errors[] = 'Username is required.';
        } elseif (strlen($data['username']) < 3) {
            $errors[] = 'Username must be at least 3 characters long.';
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $data['username'])) {
            $errors[] = 'Username can only contain letters, numbers, and underscores.';
        }
        
        if (empty($data['email'])) {
            $errors[] = 'Email is required.';
        } elseif (!isValidEmail($data['email'])) {
            $errors[] = 'Please enter a valid email address.';
        }
        
        if (empty($data['full_name'])) {
            $errors[] = 'Full name is required.';
        } elseif (strlen($data['full_name']) < 2) {
            $errors[] = 'Full name must be at least 2 characters long.';
        }
        
        if (empty($data['password'])) {
            $errors[] = 'Password is required.';
        } elseif (strlen($data['password']) < AuthConfig::PASSWORD_MIN_LENGTH) {
            $errors[] = 'Password must be at least ' . AuthConfig::PASSWORD_MIN_LENGTH . ' characters long.';
        } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/', $data['password'])) {
            $errors[] = 'Password must contain at least one uppercase letter, one lowercase letter, and one number.';
        }
        
        if (empty($data['confirm_password'])) {
            $errors[] = 'Please confirm your password.';
        } elseif ($data['password'] !== $data['confirm_password']) {
            $errors[] = 'Passwords do not match.';
        }
        
        if (!empty($data['phone']) && !preg_match('/^[\+]?[0-9\-\s\(\)]+$/', $data['phone'])) {
            $errors[] = 'Please enter a valid phone number.';
        }
        
        $allowedRoles = ['customer', 'car_owner'];
        if (!empty($data['role']) && !in_array($data['role'], $allowedRoles)) {
            $errors[] = 'Invalid role selected.';
        }
        
        return $errors;
    }
}
?>
