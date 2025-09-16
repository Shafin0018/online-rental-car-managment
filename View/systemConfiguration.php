<?php
include "../db/db.php";

if (isset($_POST['scan'])) {
    $status = "All systems normal at " . date("Y-m-d H:i:s");
    $sql = "INSERT INTO security_logs (status) VALUES ('$status')";
    $conn->query($sql);
}


$result = $conn->query("SELECT * FROM security_logs ORDER BY id DESC");

?>
<!DOCTYPE html>
<html>
<head><title>Monitor Security</title><link rel="stylesheet" href="../css/style.css"></head>

<body class="config">
<div class="container">
  <h1>System Configuration</h1>
  <form method="post">
    <textarea name="config" placeholder="Enter configuration details..." required></textarea>
    <button class="config-btn" name="save_config">Save Configuration</button>
  </form>
  <h3>Previous Configurations</h3>
  
  <span class="back" onclick="location.href='index.html'">← Back</span>
</div>
</body>
</html>
