<?php
include "../config.php";
protectAdmin();

$booking_id_err=$success="";

if($_SERVER['REQUEST_METHOD']==="POST"){
    $booking_id=sanitize($_POST['booking_id']);
    if(empty($booking_id)) $booking_id_err="Booking ID required";

    if(empty($booking_id_err)){
        $stmt=$conn->prepare("SELECT status,car_id FROM bookings WHERE id=?");
        $stmt->bind_param("i",$booking_id);
        $stmt->execute();
        $stmt->store_result();
        if($stmt->num_rows===1){
            $stmt->bind_result($status,$car_id);
            $stmt->fetch();
            if($status==="pending"){
                $check=$conn->prepare("SELECT id FROM bookings WHERE car_id=? AND status='approved'");
                $check->bind_param("i",$car_id);
                $check->execute();
                $check->store_result();
                if($check->num_rows===0){
                    $update=$conn->prepare("UPDATE bookings SET status='approved' WHERE id=?");
                    $update->bind_param("i",$booking_id);
                    $update->execute();
                    $success="Booking approved successfully.";
                } else $booking_id_err="Car already booked.";
            } else $booking_id_err="Booking not pending.";
        } else $booking_id_err="Booking ID not found.";
    }
}

$bookings=$conn->query("SELECT id,user_id,car_id,status FROM bookings ORDER BY id DESC");
?>
<html>
<head>
<title>Approve Bookings</title>
<link rel="stylesheet" href="../style.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="../script.js"></script>
</head>
<body>
<div class="container">
<h2>Approve Bookings</h2>
<form method="post">
<label>Booking ID:</label>
<input type="text" name="booking_id">
<span class="error"><?php echo $booking_id_err; ?></span>

<button type="submit">Approve</button>
</form>

<?php if($success) echo "<div class='alert alert-success'>$success</div>"; ?>

<h3>Existing Bookings</h3>
<table>
<tr><th>ID</th><th>User ID</th><th>Car ID</th><th>Status</th></tr>
<?php while($row=$bookings->fetch_assoc()): ?>
<tr>
<td><?php echo $row['id']; ?></td>
<td><?php echo $row['user_id']; ?></td>
<td><?php echo $row['car_id']; ?></td>
<td><?php echo $row['status']; ?></td>
</tr>
<?php endwhile; ?>
</table>
<a href="../dashboard.php"><button>Back to Dashboard</button></a>
</div>
</body>
</html>
