<?php
include "../db/db.php";
if (isset($_POST['save_settings'])) {
    $darkMode = isset($_POST['dark']) ? 1 : 0;
    $notify   = isset($_POST['notify']) ? 1 : 0;
    $sql = "INSERT INTO settings (dark_mode, notifications) VALUES ($darkMode, $notify)";
    $conn->query($sql);
}

$result = $conn->query("SELECT * FROM settings ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head><title>System Settings</title><link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="../css/style.css">
</head>
<body class="settings">
<div class="container">
  <h1>System Settings</h1>
  <form method="post">
    <label><input type="checkbox" name="dark"> Enable Dark Mode</label><br><br>
    <label><input type="checkbox" name="notify"> Enable Notifications</label><br><br>
    <button class="settings-btn" name="save_settings">Save Settings</button>
  </form>
  <h3>Previous Settings</h3>
  <ul>
    <?php while($row = $result->fetch_assoc()): ?>
      <li>Dark Mode: <?= $row['dark_mode'] ? "ON" : "OFF" ?> | Notifications: <?= $row['notifications'] ? "ON" : "OFF" ?></li>
    <?php endwhile; ?>
  </ul>
  <span class="back" onclick="location.href='index.html'">← Back</span>
</div>
</body>
</html>
