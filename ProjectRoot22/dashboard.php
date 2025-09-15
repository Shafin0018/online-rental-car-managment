<?php
include("config.php");
session_start();
if (isset($_SESSION["admin"])) { header("Location: login.php"); exit; }

$users = mysqli_query($conn, "SELECT id, name, email, role FROM users");
$cars = mysqli_query($conn, "SELECT id, name, model, price FROM cars");
$bookings = mysqli_query($conn, "SELECT id, user_id, car_id, status FROM bookings");
$payments = mysqli_query($conn, "SELECT id, booking_id, amount, status FROM payments");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js"></script>
    <style>
        body { font-family: Arial; margin:0; }
        .sidebar { width:220px; background:#333; color:white; height:100vh; position:fixed; padding:20px; }
        .sidebar a { display:block; color:white; padding:10px; text-decoration:none; margin-bottom:5px; }
        .sidebar a:hover { background:#444; }
        .content { margin-left:240px; padding:20px; }
        .hidden { display:none; }
        .dark-mode { background:#121212; color:white; }
        table { width:100%; border-collapse:collapse; margin-top:15px; }
        th, td { border:1px solid gray; padding:8px; text-align:left; }
        input[type="text"] { padding:6px; width:200px; margin-bottom:10px; }
        button { padding:6px 12px; cursor:pointer; }
    </style>
</head>
<body>
<div class="sidebar">
    <h3>Admin Panel</h3>
    <a href="manage_users.php" onclick="showSection('users')">Manage Users</a>
    <a href="manage_cars.php" onclick="showSection('cars')">Manage Cars</a>
    <a href="approve_bookings.php" onclick="showSection('bookings')">Approve Bookings</a>
    <a href="monitor_payments.php" onclick="showSection('payments')">Monitor Payments</a>
    <a href="generate_reports" onclick="showSection('reports')">Reports</a>
    <a href="send_notifications" onclick="showSection('notifications')">Notifications</a>
    <a href="logout.php">Logout</a>
    <button onclick="toggleDarkMode()">Toggle Dark Mode</button>
</div>

<div class="content">
    <h2>Welcome to Admin Dashboard</h2>

    <div id="users" class="section">
        <h3>Users</h3>
        <input type="text" id="searchUsers" placeholder="Search users..." onkeyup="filterTable('userTable','searchUsers')">
        <table id="userTable">
            <tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th></tr>
            <?php while($row = mysqli_fetch_assoc($users)) { ?>
            <tr>
                <td><?php echo $row["id"]; ?></td>
                <td><?php echo $row["name"]; ?></td>
                <td><?php echo $row["email"]; ?></td>
                <td><?php echo $row["role"]; ?></td>
            </tr>
            <?php } ?>
        </table>
    </div>

    <div id="cars" class="section hidden">
        <h3>Cars</h3>
        <input type="text" id="searchCars" placeholder="Search cars..." onkeyup="filterTable('carTable','searchCars')">
        <table id="carTable">
            <tr><th>ID</th><th>Name</th><th>Model</th><th>Price</th></tr>
            <?php while($row = mysqli_fetch_assoc($cars)) { ?>
            <tr>
                <td><?php echo $row["id"]; ?></td>
                <td><?php echo $row["name"]; ?></td>
                <td><?php echo $row["model"]; ?></td>
                <td><?php echo $row["price"]; ?></td>
            </tr>
            <?php } ?>
        </table>
    </div>

    <div id="bookings" class="section hidden">
        <h3>Bookings</h3>
        <input type="text" id="searchBookings" placeholder="Search bookings..." onkeyup="filterTable('bookingTable','searchBookings')">
        <table id="bookingTable">
            <tr><th>ID</th><th>User ID</th><th>Car ID</th><th>Status</th></tr>
            <?php while($row = mysqli_fetch_assoc($bookings)) { ?>
            <tr>
                <td><?php echo $row["id"]; ?></td>
                <td><?php echo $row["user_id"]; ?></td>
                <td><?php echo $row["car_id"]; ?></td>
                <td><?php echo $row["status"]; ?></td>
            </tr>
            <?php } ?>
        </table>
    </div>

    <div id="payments" class="section hidden">
        <h3>Payments</h3>
        <input type="text" id="searchPayments" placeholder="Search payments..." onkeyup="filterTable('paymentTable','searchPayments')">
        <table id="paymentTable">
            <tr><th>ID</th><th>Booking ID</th><th>Amount</th><th>Status</th></tr>
            <?php while($row = mysqli_fetch_assoc($payments)) { ?>
            <tr>
                <td><?php echo $row["id"]; ?></td>
                <td><?php echo $row["booking_id"]; ?></td>
                <td><?php echo $row["amount"]; ?></td>
                <td><?php echo $row["status"]; ?></td>
            </tr>
            <?php } ?>
        </table>
    </div>

    <div id="reports" class="section hidden">
        <h3>Reports</h3>
        <p>Generate and view reports here.</p>
    </div>

    <div id="notifications" class="section hidden">
        <h3>Notifications</h3>
        <p>Send or view notifications here.</p>
    </div>
</div>
</body>
</html>
