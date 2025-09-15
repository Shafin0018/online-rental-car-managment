<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/controllers/AuthController.php';

$controller = new AuthController();

$action = $_GET['action'] ?? 'login';

switch ($action) {
    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->processLogin();
        } else {
            $controller->showLogin();
        }
        break;
        
    case 'register':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->processRegister();
        } else {
            $controller->showRegister();
        }
        break;
        
    case 'logout':
        $controller->processLogout();
        break;
        
    case 'forgot-password':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->processForgotPassword();
        } else {
            $controller->showForgotPassword();
        }
        break;
        
    case 'reset-password':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->processResetPassword();
        } else {
            $controller->showResetPassword();
        }
        break;
        
    default:
        header('Location: login.php');
        exit;
}
?>
