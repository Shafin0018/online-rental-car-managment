<?php
include "config.php";

$email_err = $success = "";

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $email = sanitize($_POST['email']);

    if (empty($email)) {
        $email_err = "Email required";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $_SESSION['reset_email'] = $email;
            $_SESSION['otp'] = generateOTP();
            $success = "OTP generated: " . $_SESSION['otp'] . " (use this in Reset Password)";
        } else {
            $email_err = "Email not found";
        }
    }
}
?>
<html>
<head>
<title>Forgot Password</title>
<link rel="stylesheet" href="style.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="script.js"></script>
</head>
<body>
<div class="container">
<h2>Forgot Password</h2>
<form method="post">
<label>Email:</label>
<input type="email" name="email">
<span class="error"><?php echo $email_err; ?></span>

<button type="submit">Generate OTP</button>
</form>

<?php if ($success) echo "<div class='alert alert-success'>$success</div>"; ?>

<p><a href="reset_password.php">Reset Password</a></p>
</div>
</body>
</html>
