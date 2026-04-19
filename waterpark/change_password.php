<?php
include("header.php");

if(!isset($_SESSION['userid'])){
    header("Location: login.php");
    exit();
}

$uid = $_SESSION['userid'];

if(isset($_POST['change_password'])){
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    $stmt = $conn->prepare("SELECT password FROM users WHERE id=?");
    $stmt->bind_param("i",$uid);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if(empty($current) || empty($new) || empty($confirm)){
        $msg = "<div class='alert alert-danger'>Please fill in all fields.</div>";
    } elseif(!password_verify($current, $user['password'])){
        $msg = "<div class='alert alert-danger'>Current password is incorrect.</div>";
    } elseif(strlen($new) < 6){
        $msg = "<div class='alert alert-danger'>New password must be at least 6 characters.</div>";
    } elseif($new != $confirm){
        $msg = "<div class='alert alert-danger'>New password and confirm password do not match.</div>";
    } else {
        $hash = password_hash($new, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
        $stmt->bind_param("si",$hash,$uid);

        if($stmt->execute()){
            $msg = "<div class='alert alert-success'>Password changed successfully.</div>";
        } else {
            $msg = "<div class='alert alert-danger'>Password update failed.</div>";
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="glass">
            <h3 class="mb-4">Change Password</h3>

            <?php if(isset($msg)) echo $msg; ?>

            <form method="POST">
                <label>Current Password</label>
                <input type="password" name="current_password" class="form-control mb-3" required>

                <label>New Password</label>
                <input type="password" name="new_password" class="form-control mb-3" required>

                <label>Confirm New Password</label>
                <input type="password" name="confirm_password" class="form-control mb-3" required>

                <button type="submit" name="change_password" class="btn btn-primary w-100">Change Password</button>
            </form>
        </div>
    </div>
</div>

<?php include("footer.php"); ?>