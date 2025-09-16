<?php
include "../db/db.php";

if (isset($_POST['add_admin'])) {
    $name = $_POST['name'] ?? "";
    $sql = "INSERT INTO admins (name) VALUES ('$name')";
    $conn->query($sql);
}

$result = $conn->query("SELECT * FROM admins");
?>
<!DOCTYPE html>
<html>
<head><title>Manage Admins</title><link rel="stylesheet" href="../css/style.css"></head>
<body class="manage">
<div class="container">
  <h1>Manage Admins</h1>
  <form method="post">
    <input type="text" name="name" placeholder="Enter Admin Name" required>
    <button class="manage-btn" name="add_admin">Add Admin</button>
  </form>
  <h3>Admin List</h3>
  
  <span class="back" onclick="location.href='index.html'">← Back to Dashboard</span>
</div>
</body>


</html>
