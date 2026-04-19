<?php
include("conn.php");

if(!isset($_SESSION['userid'])){
    header("Location: login.php");
    exit();
}

if(isset($_POST['booking_id']) && isset($_FILES['receipt'])){

    $id = intval($_POST['booking_id']);
    $uid = $_SESSION['userid'];

    $file = $_FILES['receipt'];

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','pdf'];

    if(!in_array($ext,$allowed)){
        die("Invalid file type");
    }

    $newname = time() . "_" . rand(1000,9999) . "." . $ext;

    if(!is_dir("receipts")){
        mkdir("receipts");
    }

    move_uploaded_file($file['tmp_name'], "receipts/" . $newname);

    $stmt = $conn->prepare("UPDATE bookings SET receipt=? WHERE id=? AND user_id=?");
    $stmt->bind_param("sii",$newname,$id,$uid);
    $stmt->execute();

    header("Location: booking_history.php");
}
?>