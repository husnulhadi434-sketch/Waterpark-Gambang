<?php 
// include header
include("header.php");

// check kalau user belum login
if(!isset($_SESSION['userid'])){
header("Location: login.php");
exit();
}

// bila button confirm ditekan
if(isset($_POST['confirm']))
{

// ambil data dari session
$uid=$_SESSION['userid'];
$date=$_SESSION['bdate'];
$cat=$_SESSION['cat'];
$qty=$_SESSION['qty'];
$total=$_SESSION['total'];

// simpan data booking ke database
$sql="INSERT INTO bookings(user_id,booking_date,category,quantity,total_price)
VALUES('$uid','$date','$cat','$qty','$total')";

mysqli_query($conn,$sql);

// lepas simpan terus pergi ke booking history
header("Location: booking_history.php");
exit();

}

?>

<div class="glass">

<h4>Confirm Booking</h4>

<!-- paparkan maklumat booking -->
<p>Date: <?=htmlspecialchars($_SESSION['bdate'])?></p>
<p>Category: <?=htmlspecialchars($_SESSION['cat'])?></p>
<p>Quantity: <?=htmlspecialchars($_SESSION['qty'])?></p>
<p>Total: RM <?=htmlspecialchars($_SESSION['total'])?></p>

<form method="POST">

<!-- button untuk sahkan booking -->
<button class="btn btn-success" name="confirm">
Confirm Booking
</button>

<!-- button kembali ke page booking -->
<a href="booking.php" class="btn btn-secondary">Back</a>

</form>

</div>

<?php 
// include footer
include("footer.php"); 
?>