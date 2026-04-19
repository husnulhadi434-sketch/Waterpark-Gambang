<?php
include("conn.php");

if(!isset($_SESSION['userid'])){
    header("Location: login.php");
    exit();
}

if(!isset($_GET['id'])){
    echo "Invalid Request.";
    exit();
}

$id = intval($_GET['id']);
$uid = $_SESSION['userid'];
$role = $_SESSION['role'];

if($role == 'admin'){
    $stmt = $conn->prepare("
        SELECT bookings.*, users.fullname 
        FROM bookings 
        JOIN users ON bookings.user_id = users.id
        WHERE bookings.id=?
    ");
    $stmt->bind_param("i",$id);
} else {
    $stmt = $conn->prepare("
        SELECT bookings.*, users.fullname 
        FROM bookings 
        JOIN users ON bookings.user_id = users.id
        WHERE bookings.id=? AND bookings.user_id=?
    ");
    $stmt->bind_param("ii",$id,$uid);
}

$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0){
    echo "Ticket data not found or access denied.";
    exit();
}

$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Print Ticket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print { .no-print { display: none; } }
        .ticket-card { border: 2px solid #333; max-width: 450px; margin: 50px auto; }
    </style>
</head>
<body>

<div class="container">
    <div class="card ticket-card shadow">
        <div class="card-header bg-dark text-white text-center">
            <h4>GAMBANG WATERPARK OFFICIAL TICKET</h4>
        </div>
        <div class="card-body">
            <p><strong>Customer Name:</strong> <?= htmlspecialchars($row['fullname']) ?></p>
            <p><strong>Booking Date:</strong> <?= htmlspecialchars($row['booking_date']) ?></p>
            <p><strong>Ticket Type:</strong> <?= htmlspecialchars($row['category']) ?></p>
            <p><strong>Quantity:</strong> <?= htmlspecialchars($row['quantity']) ?> Person(s)</p>
            <hr>
            <h5 class="text-end">Total Price: RM <?= number_format($row['total_price'], 2) ?></h5>
        </div>

        <div class="card-footer text-center no-print">
            <button onclick="window.print()" class="btn btn-success">Print Ticket</button>
            <a href="booking_history.php" class="btn btn-secondary">Close</a>
        </div>
    </div>
</div>

</body>
</html>