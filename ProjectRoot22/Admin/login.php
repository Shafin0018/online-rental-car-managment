<?php
session_start();
include "../config.php";

$error = "";

if(isset($_POST['login'])){
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Server-side validation
    if($email == "" || $password == ""){
        $error = "Both fields are required.";
    } else {
        $sql = "SELECT * FROM users WHERE email='$email'";
        $result = mysqli_query($conn, $sql);

        if(mysqli_num_rows($result) == 1){
            $row = mysqli_fetch_assoc($result);
            if($password === $row['password']){
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['role'] = $row['role'];
                $_SESSION['name'] = $row['name'];
                // Redirect based on role
                if($row['role'] == 'admin'){
                    header("Location: ../dashboard.php");
                    exit;
                } elseif($row['role'] == 'owner'){
                    header("Location: owner_dashboard.php");
                    exit;
                } else {
                    header("Location: customer_dashboard.php");
                    exit;
                }
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "Email not found.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        body { font-family: Arial; display: flex; justify-content: center; align-items: center; height: 100vh; }
        form { border: 1px solid #ccc; padding: 20px; border-radius: 5px; }
        input { display: block; margin-bottom: 10px; padding: 8px; width: 250px; }
        button { padding: 8px 20px; }
        #error { color: red; margin-bottom: 10px; }
    </style>
</head>
<body>
    <form id="loginForm" method="post" action="">
        <span id="error"><?php echo $error; ?></span>
        <label>Email:</label>
        <input type="text" id="email" name="email">
        <label>Password:</label>
        <input type="password" id="password" name="password">
        <button type="submit" name="login">Login</button>
    </form>

    <script>
        const form = document.getElementById('loginForm');
        form.addEventListener('submit', function(e){
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value.trim();
            const errorSpan = document.getElementById('error');

            if(email === "" || password === ""){
                e.preventDefault();
                errorSpan.innerHTML = "Both fields are required.";
                return false;
            }
            // Simple Gmail validation
            if(!email.includes("@gmail.com")){
                e.preventDefault();
                errorSpan.innerHTML = "Email must be a Gmail address.";
                return false;
            }
        });
    </script>
</body>
</html>

