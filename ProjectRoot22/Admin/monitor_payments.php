<?php
include "../config.php";
protectAdmin();

$payments=$conn->query("
SELECT p.id,p.booking_id,p.amount,p.status 
FROM payments p
JOIN bookings b ON p.booking_id=b.id
ORDER BY p.id DESC
");
?>
<html>
<head>
<title>Monitor Payments</title>
<link rel="stylesheet" href="../style.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="../script.js"></script>
</head>
<body>
<div class="container">
<h2>Monitor Payments</h2>
<table>
<tr><th>ID</th><th>Booking ID</th><th>Amount</th><th>Status</th></tr>
<?php while($row=$payments->fetch_assoc()): ?>
<tr>
<td><?php echo $row['id']; ?></td>
<td><?php echo $row['booking_id']; ?></td>
<td><?php echo $row['amount']; ?></td>
<td><?php echo $row['status']; ?></td>
</tr>
<?php endwhile; ?>
</table>
<a href="../dashboard.php"><button>Back to Dashboard</button></a>
</div>
</body>
</html>
