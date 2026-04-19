<?php 
// masukkan header
include("header.php");

// semak sama ada user sudah login
if(!isset($_SESSION['userid'])){
header("Location: login.php");
exit();
}

// harga tiket ikut kategori
$price=array(
"Adult"=>45,
"Child"=>30,
"Senior"=>25
);

// fungsi kira jumlah harga
function totalPrice($cat,$qty,$price){
return $price[$cat]*$qty;
}

// bila button proceed ditekan
if(isset($_POST['proceed']))
{

// ambil data dari form
$date=$_POST['date'];
$cat=$_POST['category'];
$qty=$_POST['qty'];

// semak kalau ada input kosong
if(empty($date) || empty($qty)){
echo "<div class='alert alert-danger'>Sila isi semua maklumat</div>";
}

// semak kalau tarikh sudah lepas
else if($date < date("Y-m-d")){
echo "<div class='alert alert-danger'>Cannot book past date</div>";
}

else{

// kira jumlah harga
$total=totalPrice($cat,$qty,$price);

// simpan data dalam session untuk page seterusnya
$_SESSION['bdate']=$date;
$_SESSION['cat']=$cat;
$_SESSION['qty']=$qty;
$_SESSION['total']=$total;

// pergi ke page confirm booking
header("Location: confirm_booking.php");
exit();

}

}
?>

<div class="glass">

<h4>🎟 Book Waterpark Ticket</h4>

<!-- maklumat user dari session -->
<p><b>Name:</b> <?=htmlspecialchars($_SESSION['fullname'])?></p>
<p><b>Phone:</b> <?=htmlspecialchars($_SESSION['phone'])?></p>

<form method="POST">

<label>Booking Date</label>
<input type="date" name="date" class="form-control mb-3">

<label>Ticket Category</label>

<select name="category" class="form-control mb-3">

<option value="Adult">Adult RM45</option>
<option value="Child">Child RM30</option>
<option value="Senior">Senior RM25</option>

</select>

<label>Quantity</label>

<input type="number" name="qty" class="form-control mb-3">

<!-- live Price -->
<h5>Total Price: RM <span id="total">0</span></h5>

<!-- button untuk teruskan tempahan -->
<button class="btn btn-success w-100" name="proceed">
Proceed Booking
</button>

</form>

</div>

<?php 
// masukkan footer
include("footer.php"); 
?>
<script>
function calculate(){
let price = {Adult:45, Child:30, Senior:25};

let cat = document.querySelector("[name='category']").value;
let qty = document.querySelector("[name='qty']").value;

let total = (price[cat] * qty) || 0;

document.getElementById("total").innerText = total;
}

document.querySelector("[name='category']").addEventListener("change", calculate);
document.querySelector("[name='qty']").addEventListener("keyup", calculate);
</script>