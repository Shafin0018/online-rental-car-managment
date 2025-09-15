<?php
include "config.php";

$otp_err = $pass_err = $success = "";

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $otp = sanitize($_POST['otp']);
    $password = sanitize($_POST['password']);

    if (empty($otp)) $otp_err = "OTP required";
    if (empty($password)) $pass_err = "Password required";
    if (!empty($password) && !validatePassword($password)) 
        $pass_err = "Password must be 8+ chars with letters and numbers";

    if (empty($otp_err) && empty($pass_err)) {
        if (isset($_SESSION['otp']) && $otp == $_SESSION['otp'] && isset($_SESSION['reset_email'])) {
            $hash = hashPassword($password);
            $stmt = $conn->prepare("UPDATE users SET password=? WHERE email=?");
            $stmt->bind_param("ss", $hash, $_SESSION['reset_email']);
            $stmt->execute();
            unset($_SESSION['otp']);
            unset($_SESSION['reset_email']);
            $success = "Password reset successful. You can login now.";
        } else {
            $otp_err = "Invalid OTP";
        }
    }
}
?>
<html>
<head>
<title>Reset Password</title>
<link rel="stylesheet" href="style.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="script.js"></script>
</head>
<body>
<div class="container">
<h2>Reset Password</h2>
<form method="post">
<label>OTP:</label>
<input type="text" name="otp">
<span class="error"><?php echo $otp_err; ?></span>

<label>New Password:</label>
<input type="password" name="password">
<span class="error"><?php echo $pass_err; ?></span>

<button type="submit">Reset Password</button>
</form>

<?php if ($success) echo "<div class='alert alert-success'>$success</div>"; ?>

<p><a href="index.php">Back to Login</a></p>
</div>
</body>
</html>
