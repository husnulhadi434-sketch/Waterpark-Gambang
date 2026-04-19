<?php
// include connection database + session
include("conn.php");

// check kalau bukan admin → tak boleh access page ini
if(!isset($_SESSION['userid']) || $_SESSION['role']!='admin'){
    header("Location: login.php");
    exit();
}

// check kalau ada id booking dihantar melalui URL
if(isset($_GET['id'])){
    
    // ambil booking id dan tukar kepada integer (elak error / injection basic)
    $id = intval($_GET['id']);

    // update status payment kepada "Paid"
    // ini bermaksud admin sudah sahkan pembayaran user
    $stmt = $conn->prepare("UPDATE bookings SET payment_status='Paid' WHERE id=?");
    $stmt->bind_param("i",$id);
    $stmt->execute();
}

// lepas approve → redirect balik ke dashboard admin
header("Location: admin_dashboard.php");
?>