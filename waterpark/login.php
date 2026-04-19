<?php 
// include header (database connection + session + navbar)
include("header.php"); 
?>

<?php
// check kalau user tekan button login
if(isset($_POST['login']))
{
    // ambil input dari form
    $email = trim($_POST['email']);     // buang space kosong
    $password = $_POST['password'];

    $errors = []; // array untuk simpan error

    // check email kosong
    if(empty($email))
    {
        $errors[] = "Email is required";
    }
    // check format email betul atau tidak
    elseif(!filter_var($email, FILTER_VALIDATE_EMAIL))
    {
        $errors[] = "Invalid email format";
    }

    // check password kosong
    if(empty($password))
    {
        $errors[] = "Password is required";
    }


    // kalau tiada error → terus check database
    if(empty($errors))
    {
        // ambil user berdasarkan email
        $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
        $stmt->bind_param("s",$email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        // check kalau user wujud dan password betul
        if($user && password_verify($password,$user['password']))
        {
            // hanya role tertentu dibenarkan login
            if($user['role'] == "customer" || $user['role'] == "admin")
            {
                // simpan data user dalam session
                $_SESSION['userid']   = $user['id'];
                $_SESSION['fullname'] = $user['fullname'];
                $_SESSION['phone']    = $user['phone'];
                $_SESSION['role']     = $user['role'];

                // simpan cookie (optional - untuk ingat user sementara)
                setcookie("userid",$user['id'],time()+3600,"");
                setcookie("email",$user['email'],time()+3600,"");

                // redirect ke page booking lepas login berjaya
                header("Location: booking.php");
                exit();
            }
            else
            {
                // kalau role tak dibenarkan
                echo "<div class='alert alert-danger'>Unauthorized user</div>";
            }
        }
        else
        {
            // kalau email/password salah
            echo "<div class='alert alert-danger'>Invalid email or password</div>";
        }

        $stmt->close();
    }
    else
    {
        // kalau ada error → paparkan satu per satu
        foreach($errors as $error)
        {
            echo "<div class='alert alert-danger'>$error</div>";
        }
    }
}
?>

<div class="row justify-content-center">
<div class="col-md-4">
<div class="glass">

<h3 class="text-center mb-4">Login</h3>

<!-- form login -->
<form method="POST">

<!-- input email -->
<input type="email" class="form-control mb-3" name="email" placeholder="Email" required>

<!-- input password -->
<input type="password" class="form-control mb-3" name="password" placeholder="Password" required>

<!-- button login -->
<button class="btn btn-primary w-100" name="login">
Login
</button>

</form>

<br>

<!-- link pergi register -->
<a href="register.php">Create Account</a>

</div>
</div>
</div>

<?php include("footer.php"); ?>