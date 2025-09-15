<?php
include "config.php";

$email_err = $pass_err = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = sanitize($_POST['email']);
    $password = sanitize($_POST['password']);

    if (empty($email)) $email_err = "Email required";
    if (empty($password)) $pass_err = "Password required";

    if ($email && $password) {
        $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $hash, $role);
            $stmt->fetch();
            if (verifyPassword($password, $hash)) {
                $_SESSION['user_id'] = $id;
                $_SESSION['role'] = $role;
                redirect("dashboard.php");
            } else $pass_err = "Incorrect password";
        } else $email_err = "Email not found";
    }
}
?>
<html>
<head>
<title>Login</title>
<link rel="stylesheet" href="style.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="script.js"></script>
</head>
<body>
<div class="container">
<h2>Login</h2>
<form method="post">
<label>Email:</label>
<input type="email" name="email">
<span class="error"><?php echo $email_err; ?></span>

<label>Password:</label>
<input type="password" name="password">
<span class="error"><?php echo $pass_err; ?></span>

<button type="submit">Login</button>
<p><a href="forgot_password.php">Forgot Password?</a></p>
</form>
</div>
</body>
</html>
