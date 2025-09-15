<?php
include "../config.php";
protectAdmin();

$make_err=$model_err=$year_err=$plate_err=$success="";

if($_SERVER['REQUEST_METHOD']==="POST"){
    $make=sanitize($_POST['make']);
    $model=sanitize($_POST['model']);
    $year=sanitize($_POST['year']);
    $plate=sanitize($_POST['plate']);

    if(empty($make)) $make_err="Car make required";
    if(empty($model)) $model_err="Car model required";
    if(empty($year) || !is_numeric($year)) $year_err="Valid year required";
    if(empty($plate)) $plate_err="Plate number required";

    if(empty($make_err) && empty($model_err) && empty($year_err) && empty($plate_err)){
        $stmt=$conn->prepare("INSERT INTO cars(make,model,year,plate) VALUES(?,?,?,?)");
        $stmt->bind_param("ssis",$make,$model,$year,$plate);
        $stmt->execute();
        $success="Car added successfully.";
    }
}

$cars=$conn->query("SELECT id,make,model,year,plate FROM cars ORDER BY id DESC");
?>
<html>
<head>
<title>Manage Cars</title>
<link rel="stylesheet" href="../style.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="../script.js"></script>
</head>
<body>
<div class="container">
<h2>Manage Cars</h2>
<form method="post">
<label>Make:</label>
<input type="text" name="make">
<span class="error"><?php echo $make_err; ?></span>

<label>Model:</label>
<input type="text" name="model">
<span class="error"><?php echo $model_err; ?></span>

<label>Year:</label>
<input type="text" name="year">
<span class="error"><?php echo $year_err; ?></span>

<label>Plate Number:</label>
<input type="text" name="plate">
<span class="error"><?php echo $plate_err; ?></span>

<button type="submit">Add Car</button>
</form>

<?php if($success) echo "<div class='alert alert-success'>$success</div>"; ?>

<h3>Existing Cars</h3>
<table>
<tr><th>ID</th><th>Make</th><th>Model</th><th>Year</th><th>Plate</th></tr>
<?php while($row=$cars->fetch_assoc()): ?>
<tr>
<td><?php echo $row['id']; ?></td>
<td><?php echo $row['make']; ?></td>
<td><?php echo $row['model']; ?></td>
<td><?php echo $row['year']; ?></td>
<td><?php echo $row['plate']; ?></td>
</tr>
<?php endwhile; ?>
</table>
<a href="../dashboard.php"><button>Back to Dashboard</button></a>
</div>
</body>
</html>
