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


<body class="analytics">
<div class="container">
  <h1>System Analytics</h1>
  <form method="post">
    <button class="analytics-btn" name="generate">Generate Report</button>
  </form>
  <h3>Analytics Reports</h3>
  <ul>
    <?php while($row = $result->fetch_assoc()): ?>
      <li><?= $row['report'] ?> (<?= $row['created_at'] ?>)</li>
    <?php endwhile; ?>
  </ul>
  <span class="back" onclick="location.href='index.html'">← Back</span>
</div>
</body>
</html>
