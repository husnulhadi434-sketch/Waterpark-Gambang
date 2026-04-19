<?php 
include("header.php"); 
?>

<?php
if(isset($_POST['register']))
{
    $fullname = trim($_POST['fullname']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $phone    = trim($_POST['phone']);
    $role     = trim($_POST['role']);

    $errors = [];

    if(empty($fullname))
    {
        $errors[] = "Fullname is required";
    }

    if(empty($email))
    {
        $errors[] = "Email is required";
    }
    elseif(!filter_var($email, FILTER_VALIDATE_EMAIL))
    {
        $errors[] = "Invalid email format";
    }

    if(strlen($password) < 6)
    {
        $errors[] = "Password must be at least 6 characters";
    }

    if(!preg_match("/^[0-9]{10,15}$/",$phone))
    {
        $errors[] = "Invalid phone number";
    }

    if(empty($role))
    {
        $errors[] = "Role is required";
    }
    elseif($role != "customer" && $role != "admin")
    {
        $errors[] = "Invalid role selected";
    }

    if(empty($errors))
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $regdate = date("Y-m-d");

        $stmt = $conn->prepare("INSERT INTO users (fullname, email, password, phone, regdate, role) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $fullname, $email, $hash, $phone, $regdate, $role);

        if($stmt->execute())
        {
            echo "<div class='alert alert-success'>Registration Successful. Redirecting to login...</div>";
            header("refresh:2;url=login.php");
        }
        else
        {
            echo "<div class='alert alert-danger'>Registration Failed</div>";
        }

        $stmt->close();
    }
    else
    {
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

<h3 class="text-center mb-4">Register</h3>

<form method="POST">
<input class="form-control mb-3" name="fullname" placeholder="Fullname" required>
<input type="email" class="form-control mb-3" name="email" placeholder="Email" required>
<input type="password" class="form-control mb-3" name="password" placeholder="Password" required>
<input class="form-control mb-3" name="phone" placeholder="Phone Number" required>

<select name="role" class="form-control mb-3" required>
    <option value="">-- Select Role --</option>
    <option value="customer">Customer</option>
</select>

<button class="btn btn-success w-100" name="register">
Register
</button>
</form>

<br>
<a href="login.php">Already have account? Login</a>

</div>
</div>
</div>

<?php include("footer.php"); ?>