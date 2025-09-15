<?php
include "../config.php";
protectAdmin();

$name_err = $email_err = $pass_err = $role_err = $success = "";

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $password = sanitize($_POST['password']);
    $role = sanitize($_POST['role']);

    if (empty($name)) $name_err = "Name required";
    if (empty($email)) $email_err = "Email required";
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) $email_err = "Invalid email";
    if (empty($password)) $pass_err = "Password required";
    if (!empty($password) && !validatePassword($password)) $pass_err = "Password must be 8+ chars with letters and numbers";
    if (empty($role)) $role_err = "Role required";

    if (empty($name_err) && empty($email_err) && empty($pass_err) && empty($role_err)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            $hash = hashPassword($password);
            $insert = $conn->prepare("INSERT INTO users(name,email,password,role) VALUES(?,?,?,?)");
            $insert->bind_param("ssss", $name, $email, $hash, $role);
            $insert->execute();
            $success = "User added successfully.";
        } else {
            $email_err = "Email already exists";
        }
    }
}

$users = $conn->query("SELECT id, name, email, role FROM users ORDER BY id DESC");
?>
<html>
<head>
<title>Manage Users</title>
<link rel="stylesheet" href="../style.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="../script.js"></script>
</head>
<body>
<div class="container">
<h2>Manage Users</h2>
<form method="post">
<label>Name:</label>
<input type="text" name="name">
<span class="error"><?php echo $name_err; ?></span>

<label>Email:</label>
<input type="email" name="email">
<span class="error"><?php echo $email_err; ?></span>

<label>Password:</label>
<input type="password" name="password">
<span class="error"><?php echo $pass_err; ?></span>

<label>Role:</label>
<select name="role">
<option value="">Select Role</option>
<option value="customer">Customer</option>
<option value="owner">Owner</option>
<option value="admin">Admin</option>
</select>
<span class="error"><?php echo $role_err; ?></span>

<button type="submit">Add User</button>
</form>

<?php if ($success) echo "<div class='alert alert-success'>$success</div>"; ?>

<h3>Existing Users</h3>
<table>
<tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th></tr>
<?php while($row=$users->fetch_assoc()): ?>
<tr>
<td><?php echo $row['id']; ?></td>
<td><?php echo $row['name']; ?></td>
<td><?php echo $row['email']; ?></td>
<td><?php echo $row['role']; ?></td>
</tr>
<?php endwhile; ?>
</table>
<a href="../dashboard.php"><button>Back to Dashboard</button></a>
</div>
</body>
</html>
