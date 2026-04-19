<?php include("conn.php"); ?>

<!DOCTYPE html>
<html>
<head>

<title>Waterpark Ticket System</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background-image:url("https://product-image.globaltix.com/live-gtImage/5bb67483-ff52-4451-a872-5499eff458da");
background-size:cover;
background-attachment:fixed;
}

.glass{
background:rgba(255,255,255,0.9);
border-radius:15px;
padding:25px;
box-shadow:0 8px 20px rgba(0,0,0,0.2);
}

</style>

</head>

<body>

<nav class="navbar navbar-dark bg-dark">
<div class="container">

<span class="navbar-brand">🌊 Gambang Waterpark Booking</span>

<?php if(isset($_SESSION['userid'])){ ?>

<div class="d-flex gap-2 flex-wrap">

<!-- CUSTOMER ONLY -->
<?php if($_SESSION['role'] == 'customer'){ ?>
    <a href="booking.php" class="btn btn-light btn-sm">Book</a>
<?php } ?>

<!-- SEMUA USER -->
<a href="booking_history.php" class="btn btn-info btn-sm">History</a>
<a href="profile.php" class="btn btn-secondary btn-sm">Profile</a>

<!-- ADMIN ONLY -->
<?php if($_SESSION['role'] == 'admin'){ ?>
    <a href="admin_dashboard.php" class="btn btn-warning btn-sm">Dashboard</a>
    <a href="admin_users.php" class="btn btn-light btn-sm">Manage Users</a>
<?php } ?>

<!-- LOGOUT -->
<a href="logout.php" class="btn btn-danger btn-sm">Logout</a>

</div>

<?php } ?>

</div>
</nav>

<div class="container mt-5">