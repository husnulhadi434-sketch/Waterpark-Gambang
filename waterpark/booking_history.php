<?php
// include header (sambung database + start session + navbar)
include("header.php");

// check kalau user belum login, terus hantar balik ke login page
if(!isset($_SESSION['userid'])){
    header("Location: login.php");
    exit();
}

// ambil user id dari session (untuk filter data nanti)
$uid = $_SESSION['userid'];


// ================= AMBIL DATA BOOKING =================

// kalau admin → dia boleh nampak semua booking dalam sistem
if($_SESSION['role'] == 'admin'){
    $stmt = $conn->prepare("
        SELECT bookings.*, users.fullname 
        FROM bookings 
        JOIN users ON bookings.user_id = users.id
        ORDER BY bookings.id DESC
    ");
    $stmt->execute();
} 
// kalau user biasa → hanya nampak booking sendiri sahaja
else {
    $stmt = $conn->prepare("
        SELECT bookings.*, users.fullname 
        FROM bookings 
        JOIN users ON bookings.user_id = users.id
        WHERE bookings.user_id=?
        ORDER BY bookings.id DESC
    ");
    $stmt->bind_param("i",$uid);
    $stmt->execute();
}

// simpan result query
$result = $stmt->get_result();
?>

<div class="container mt-4">
<div class="glass">

<h4 class="mb-3">📋 Booking History</h4>

<!-- table untuk paparkan semua booking -->
<table class="table table-striped table-bordered">

<thead class="table-dark">
<tr>
    <!-- kalau admin, tambah column nama user -->
    <?php if($_SESSION['role'] == 'admin'){ ?><th>User</th><?php } ?>
    
    <th>Date</th>
    <th>Category</th>
    <th>Quantity</th>
    <th>Total Price</th>
    <th>Payment</th>
    <th>Receipt</th>
    <th>Action</th>
</tr>
</thead>

<tbody>

<?php while($row = $result->fetch_assoc()){ ?>
<tr>

    <!-- paparkan nama user (admin sahaja nampak) -->
    <?php if($_SESSION['role'] == 'admin'){ ?>
    <td><?= htmlspecialchars($row['fullname']) ?></td>
    <?php } ?>

    <!-- tarikh booking -->
    <td><?= htmlspecialchars($row['booking_date']) ?></td>

    <!-- jenis tiket -->
    <td><?= htmlspecialchars($row['category']) ?></td>

    <!-- bilangan tiket -->
    <td><?= htmlspecialchars($row['quantity']) ?></td>

    <!-- jumlah harga -->
    <td>RM <?= number_format($row['total_price'],2) ?></td>

    <!-- ================= STATUS PAYMENT ================= -->
    <td>
    <?php if($row['payment_status'] == 'Paid'){ ?>
        <!-- kalau dah bayar -->
        <span class="badge bg-success">Paid</span>
    <?php } else { ?>
        <!-- kalau belum bayar -->
        <span class="badge bg-warning text-dark">Pending</span>
    <?php } ?>
    </td>

    <!-- ================= RECEIPT ================= -->
    <td>

    <?php if(!empty($row['receipt'])){ ?>
        <!-- kalau dah upload receipt → boleh view -->
        <a href="receipts/<?= $row['receipt'] ?>" target="_blank" class="btn btn-success btn-sm">
            View
        </a>

    <?php } else { ?>

        <!-- kalau user biasa → boleh upload receipt -->
        <?php if($_SESSION['role'] != 'admin'){ ?>
        <form method="POST" action="upload_receipt.php" enctype="multipart/form-data">

            <!-- pass booking id -->
            <input type="hidden" name="booking_id" value="<?= $row['id'] ?>">

            <!-- pilih file -->
            <input type="file" name="receipt" required class="form-control form-control-sm mb-1">

            <!-- button upload -->
            <button class="btn btn-primary btn-sm w-100">
                Upload
            </button>

        </form>

        <?php } else { ?>
            <!-- admin tak perlu upload -->
            No Receipt
        <?php } ?>

    <?php } ?>

    </td>

    <!-- ================= BUTTON ACTION ================= -->
    <td>

        <!-- edit booking -->
        <a class="btn btn-warning btn-sm" href="edit_booking.php?id=<?= $row['id'] ?>">
            Edit
        </a>

        <!-- delete booking -->
        <a class="btn btn-danger btn-sm" 
           href="delete_booking.php?id=<?= $row['id'] ?>"
           onclick="return confirm('Delete this booking?')">
           Delete
        </a>

        <!-- print ticket -->
        <a class="btn btn-success btn-sm" href="print_ticket.php?id=<?= $row['id'] ?>">
            Print
        </a>

    </td>

</tr>
<?php } ?>

</tbody>
</table>

</div>
</div>

<?php include("footer.php"); ?>