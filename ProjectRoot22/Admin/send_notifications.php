<?php
include "../config.php";
protectAdmin();

$recipient_err=$message_err=$success="";

if($_SERVER['REQUEST_METHOD']==="POST"){
    $recipient=sanitize($_POST['recipient']);
    $message=sanitize($_POST['message']);

    if(empty($recipient)) $recipient_err="Recipient ID required";
    if(empty($message)) $message_err="Message required";
    if(strlen($message)>200) $message_err="Message too long";

    if(empty($recipient_err) && empty($message_err)){
        $stmt=$conn->prepare("INSERT INTO notifications(user_id,message) VALUES(?,?)");
        $stmt->bind_param("is",$recipient,$message);
        $stmt->execute();
        $success="Notification sent successfully.";
    }
}

$users=$conn->query("SELECT id,name,email FROM users ORDER BY id DESC");
?>
<html>
<head>
<title>Send Notifications</title>
<link rel="stylesheet" href="../style.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="../script.js"></script>
</head>
<body>
<div class="container">
<h2>Send Notifications</h2>
<form method="post">
<label>Recipient ID:</label>
<input type="text" name="recipient">
<span class="error"><?php
