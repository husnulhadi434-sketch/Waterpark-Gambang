<?php
include("header.php");

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
    $stmt = $conn->prepare("SELECT * FROM bookings WHERE id=?");
    $stmt->bind_param("i",$id);
} else {
    $stmt = $conn->prepare("SELECT * FROM bookings WHERE id=? AND user_id=?");
    $stmt->bind_param("ii",$id,$uid);
}
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0){
    echo "Booking record not found or access denied.";
    exit();
}

$row = $result->fetch_assoc();

$current_booking_date = $row['booking_date'];
$today = date('Y-m-d');
$tomorrow = date('Y-m-d', strtotime('+1 day'));

$edit_deadline = date('Y-m-d', strtotime($current_booking_date . ' -1 day'));
$is_editable = (strtotime($today) < strtotime($edit_deadline));

if(isset($_POST['update'])) {
    $new_date = $_POST['date'];
    $qty = intval($_POST['qty']);

    if(!$is_editable) {
        $error = "Update failed: You can only modify bookings at least 1 day before the scheduled date.";
    } elseif($new_date < $tomorrow) {
        $error = "Update failed: The new booking date must be from tomorrow onwards.";
    } else {
        if($role == 'admin'){
            $stmt2 = $conn->prepare("UPDATE bookings SET booking_date=?, quantity=? WHERE id=?");
            $stmt2->bind_param("sii",$new_date,$qty,$id);
        } else {
            $stmt2 = $conn->prepare("UPDATE bookings SET booking_date=?, quantity=? WHERE id=? AND user_id=?");
            $stmt2->bind_param("siii",$new_date,$qty,$id,$uid);
        }

        if($stmt2->execute()){
            echo "<script>alert('Booking updated successfully!'); window.location.href='booking_history.php';</script>";
            exit();
        } else {
            $error = "Database Error.";
        }
    }
}
?>

<div class="row justify-content-center">
<div class="col-md-6">
<div class="glass">
    <h2>Edit Booking</h2>

    <?php if(isset($error)){ ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php } ?>

    <?php if(!$is_editable){ ?>
        <div class="alert alert-warning">
            Note: This booking is locked as it is less than 1 day away.
        </div>
    <?php } ?>

    <form method="POST" onsubmit="return confirm('Are you sure you want to update this booking?');">
        <label>New Booking Date</label>
        <input type="date"
               name="date"
               class="form-control mb-3"
               value="<?= htmlspecialchars($row['booking_date']) ?>"
               min="<?= $tomorrow ?>"
               <?= !$is_editable ? 'disabled' : '' ?>
               required>

        <label>Quantity</label>
        <input type="number"
               name="qty"
               class="form-control mb-3"
               value="<?= htmlspecialchars($row['quantity']) ?>"
               min="1"
               <?= !$is_editable ? 'disabled' : '' ?>
               required>

        <button type="submit" name="update" class="btn btn-success w-100" <?= !$is_editable ? 'disabled' : '' ?>>
            Update Booking
        </button>
    </form>

    <a href="booking_history.php" class="btn btn-secondary mt-3">Back</a>
</div>
</div>
</div>

<?php include("footer.php"); ?>