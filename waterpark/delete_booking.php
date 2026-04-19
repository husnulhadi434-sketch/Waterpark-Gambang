<?php
include("conn.php");

if(!isset($_SESSION['userid'])){
    header("Location: login.php");
    exit();
}

if(!isset($_GET['id'])){
    header("Location: booking_history.php");
    exit();
}

$id = intval($_GET['id']);
$uid = $_SESSION['userid'];
$role = $_SESSION['role'];

if($role == 'admin'){
    $stmt = $conn->prepare("DELETE FROM bookings WHERE id=?");
    $stmt->bind_param("i",$id);
} else {
    $stmt = $conn->prepare("DELETE FROM bookings WHERE id=? AND user_id=?");
    $stmt->bind_param("ii",$id,$uid);
}

if($stmt->execute()){
    echo "<script>
            alert('Booking successfully deleted.');
            window.location.href='booking_history.php';
          </script>";
} else {
    echo "Error deleting booking.";
}
?>