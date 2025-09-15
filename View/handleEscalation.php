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

<body class="escalation">
<div class="container">
  <h1>Handle Escalation</h1>
  <form method="post">
    <textarea name="issue" placeholder="Describe the issue..." required></textarea>
    <button class="escalation-btn" name="submit_issue">Submit Escalation</button>
  </form>
  <h3>Escalation Issues</h3>
  <ul>
    <?php while($row = $result->fetch_assoc()): ?>
      <li><?= $row['issue'] ?> (<?= $row['created_at'] ?>)</li>
    <?php endwhile; ?>
  </ul>
  <span class="back" onclick="location.href='index.html'">← Back</span>
</div>
</body>
</html>

