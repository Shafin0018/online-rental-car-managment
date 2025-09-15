<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$host   = "localhost";
$user   = "root";
$pass   = "";
$dbname = "demo_rental_db";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

require_once __DIR__ . "/functions.php";
?>


