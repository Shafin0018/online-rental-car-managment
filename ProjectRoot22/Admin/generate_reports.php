<?php
include "../config.php";
protectAdmin();

$report="";
if($_SERVER['REQUEST_METHOD']==="POST"){
    $type=sanitize($_POST['type']);
    $start=sanitize($_POST['start']);
    $end=sanitize($_POST['end']);

    if($type==="bookings"){
        $stmt=$conn->prepare("SELECT id,user_id,car_id,status FROM bookings WHERE date BETWEEN ? AND ?");
        $stmt->bind_param("ss",$start,$end);
        $stmt->execute();
        $report=$stmt->get_result();
    } elseif($type==="payments"){
        $stmt=$conn->prepare("SELECT id,booking_id,amount,status FROM payments WHERE date BETWEEN ? AND ?");
        $stmt->bind_param("ss",$start,$end);
        $stmt->execute();
        $report=$stmt->get_result();
    }
}
?>
<html>
<head>
<title>Generate Reports</title>
<link rel="stylesheet" href="../style.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="../script.js"></script>
</head>
<body>
<div class="container">
<h2>Generate Reports</h2>
<form method="post">
<label>Report Type:</label>
<select name="type">
<option value="">Select Type</option>
<option value="bookings">Bookings</option>
<option value="payments">Payments</option>
</select>

<label>Start Date:</label>
<input type="date" name="start">
<label>End Date:</label>
<input type="date" name="end">
<button type="submit">Generate</button>
</form>

<?php if($report && $report->num_rows>0): ?>
<h3>Report Results</h3>
<table>
<tr>
<?php
$fields=$report->fetch_fields();
foreach($fields as $f){ echo "<th>{$f->name}</th>"; }
?>
</tr>
<?php
$report->data_seek(0);
while($row=$report->fetch_assoc()):
?>
<tr>
<?php foreach($row as $v) echo "<td>$v</td>"; ?>
</tr>
<?php endwhile; ?>
</table>
<?php endif; ?>

<a href="../dashboard.php"><button>Back to Dashboard</button></a>
</div>
</body>
</html>
