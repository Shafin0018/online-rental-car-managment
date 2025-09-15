<?php
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES);
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function protectAdmin() {
    if (!isLoggedIn() || !isAdmin()) {
        header("Location: ../index.php");
        exit;
    }
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

function generateOTP($length = 6) {
    $otp = '';
    for ($i = 0; $i < $length; $i++) {
        $otp .= rand(0, 9);
    }
    return $otp;
}

function validatePassword($password) {
    return preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/', $password);
}
?>
