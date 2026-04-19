<?php
include("header.php");

if(!isset($_SESSION['userid']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

// ================= BASIC STATS =================
$totalUsers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users"))['total'];

$totalBookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM bookings"))['total'];

$totalSales = mysqli_fetch_assoc(mysqli_query($conn, "SELECT IFNULL(SUM(total_price),0) AS total FROM bookings WHERE payment_status='Paid'"))['total'];

// ================= SALES REPORT =================

// DAILY
$dailySales = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT IFNULL(SUM(total_price),0) AS total 
FROM bookings 
WHERE DATE(booking_date) = CURDATE() 
AND payment_status='Paid'
"))['total'];

// WEEKLY
$weeklySales = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT IFNULL(SUM(total_price),0) AS total 
FROM bookings 
WHERE YEARWEEK(booking_date, 1) = YEARWEEK(CURDATE(), 1)
AND payment_status='Paid'
"))['total'];

// MONTHLY
$monthlySales = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT IFNULL(SUM(total_price),0) AS total 
FROM bookings 
WHERE MONTH(booking_date) = MONTH(CURDATE())
AND YEAR(booking_date) = YEAR(CURDATE())
AND payment_status='Paid'
"))['total'];
?>

<div class="glass">
<h3 class="mb-4">Admin Dashboard</h3>

<!-- ================= MAIN STATS ================= -->
<div class="row text-center">

<div class="col-md-4 mb-3">
<div class="card shadow">
<div class="card-body">
<h5>Total Users</h5>
<h2><?= $totalUsers ?></h2>
</div>
</div>
</div>

<div class="col-md-4 mb-3">
<div class="card shadow">
<div class="card-body">
<h5>Total Bookings</h5>
<h2><?= $totalBookings ?></h2>
</div>
</div>
</div>

<div class="col-md-4 mb-3">
<div class="card shadow">
<div class="card-body">
<h5>Total Sales (Paid)</h5>
<h2>RM <?= number_format($totalSales,2) ?></h2>
</div>
</div>
</div>

</div>

<hr>

<!-- ================= SALES REPORT ================= -->
<h4 class="mb-3">📊 Sales Report</h4>

<div class="row text-center">

<div class="col-md-4 mb-3">
<div class="card border-success shadow">
<div class="card-body">
<h6>Daily Sales</h6>
<h3 class="text-success">RM <?= number_format($dailySales,2) ?></h3>
</div>
</div>
</div>

<div class="col-md-4 mb-3">
<div class="card border-primary shadow">
<div class="card-body">
<h6>Weekly Sales</h6>
<h3 class="text-primary">RM <?= number_format($weeklySales,2) ?></h3>
</div>
</div>
</div>

<div class="col-md-4 mb-3">
<div class="card border-warning shadow">
<div class="card-body">
<h6>Monthly Sales</h6>
<h3 class="text-warning">RM <?= number_format($monthlySales,2) ?></h3>
</div>
</div>
</div>

</div>

<hr>

<!-- ================= PAYMENT MANAGEMENT ================= -->
<h5>💳 Payment Management</h5>

<table class="table table-bordered table-striped">
<thead class="table-dark">
<tr>
<th>User</th>
<th>Date</th>
<th>Total</th>
<th>Status</th>
<th>Receipt</th>
<th>Action</th>
</tr>
</thead>

<tbody>
<?php
$result = mysqli_query($conn,"
SELECT bookings.*, users.fullname 
FROM bookings 
JOIN users ON bookings.user_id = users.id
ORDER BY bookings.id DESC
");

while($row = mysqli_fetch_assoc($result)){
?>
<tr>

<td><?= htmlspecialchars($row['fullname']) ?></td>
<td><?= htmlspecialchars($row['booking_date']) ?></td>
<td>RM <?= number_format($row['total_price'],2) ?></td>

<td>
<?php if($row['payment_status']=='Paid'){ ?>
<span class="badge bg-success">Paid</span>
<?php } else { ?>
<span class="badge bg-warning text-dark">Pending</span>
<?php } ?>
</td>

<td>
<?php if(!empty($row['receipt'])){ ?>
<a href="receipts/<?= $row['receipt'] ?>" target="_blank" class="btn btn-info btn-sm">View</a>
<?php } else { ?>
No Receipt
<?php } ?>
</td>

<td>
<?php if($row['payment_status']=='Pending' && !empty($row['receipt'])){ ?>
<a href="approve_payment.php?id=<?= $row['id'] ?>" 
   class="btn btn-success btn-sm"
   onclick="return confirm('Approve this payment?')">
   Approve
</a>
<?php } ?>
</td>

</tr>
<?php } ?>
</tbody>
</table>

</div>

<?php include("footer.php"); ?>