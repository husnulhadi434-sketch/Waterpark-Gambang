<?php
include("header.php");

if(!isset($_SESSION['userid'])){
    header("Location: login.php");
    exit();
}

$uid = $_SESSION['userid'];

// Ambil data user dahulu
$stmt = $conn->prepare("SELECT fullname, email, phone, role, profile_pic FROM users WHERE id=?");
$stmt->bind_param("i",$uid);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Update profile
if(isset($_POST['update_profile'])){

    $fullname = trim($_POST['fullname']);
    $phone = trim($_POST['phone']);

    // default gambar lama
    $filename = $user['profile_pic'];

    // VALIDATION BASIC
    if(empty($fullname) || empty($phone)){
        $msg = "<div class='alert alert-danger'>Please fill in all fields.</div>";
    } 
    elseif(!preg_match("/^[0-9]{10,15}$/",$phone)){
        $msg = "<div class='alert alert-danger'>Invalid phone number.</div>";
    }

    // HANDLE UPLOAD (ONLY IF FILE EXISTS)
    elseif(isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0){

        $file = $_FILES['profile_pic'];

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $size = $file['size'];

        $allowed = ['jpg','jpeg','png','pdf'];
        $maxSize = 2 * 1024 * 1024; // 2MB

        if(!in_array($ext, $allowed)){
            $msg = "<div class='alert alert-danger'>Only JPG, JPEG, PNG, PDF allowed.</div>";
        }
        elseif($size > $maxSize){
            $msg = "<div class='alert alert-danger'>File size must be less than 2MB.</div>";
        }
        else{
            // delete gambar lama (optional)
            if(!empty($user['profile_pic']) && file_exists("images/".$user['profile_pic'])){
                unlink("images/".$user['profile_pic']);
            }

            $newname = time() . "." . $ext;
            move_uploaded_file($file['tmp_name'], "images/" . $newname);

            $filename = $newname;
        }
    }

    // UPDATE DATABASE (ONLY IF NO ERROR)
    if(!isset($msg)){
        $stmt = $conn->prepare("UPDATE users SET fullname=?, phone=?, profile_pic=? WHERE id=?");
        $stmt->bind_param("sssi",$fullname,$phone,$filename,$uid);

        if($stmt->execute()){
            $_SESSION['fullname'] = $fullname;
            $_SESSION['phone'] = $phone;

            $msg = "<div class='alert alert-success'>Profile updated successfully.</div>";

            // refresh data
            $user['fullname'] = $fullname;
            $user['phone'] = $phone;
            $user['profile_pic'] = $filename;
        } 
        else {
            $msg = "<div class='alert alert-danger'>Update failed.</div>";
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="glass">

            <h3 class="mb-4">My Profile</h3>

            <?php if(isset($msg)) echo $msg; ?>

            <!-- PAPAR GAMBAR -->
            <div class="text-center mb-3">
                <img src="images/<?= !empty($user['profile_pic']) ? $user['profile_pic'] : 'default.png' ?>" 
                     width="120" height="120"
                     style="border-radius:50%; object-fit:cover;">
            </div>

            <form method="POST" enctype="multipart/form-data">

                <label>Fullname</label>
                <input type="text" name="fullname" class="form-control mb-3"
                       value="<?= htmlspecialchars($user['fullname']) ?>" required>

                <label>Email</label>
                <input type="email" class="form-control mb-3"
                       value="<?= htmlspecialchars($user['email']) ?>" disabled>

                <label>Phone</label>
                <input type="text" name="phone" class="form-control mb-3"
                       value="<?= htmlspecialchars($user['phone']) ?>" required>

                <label>Profile Picture</label>
                <input type="file" name="profile_pic" class="form-control mb-3">

                <label>Role</label>
                <input type="text" class="form-control mb-3"
                       value="<?= htmlspecialchars($user['role']) ?>" disabled>

                <button type="submit" name="update_profile" class="btn btn-success w-100">
                    Update Profile
                </button>

            </form>
        </div>
    </div>
</div>

<?php include("footer.php"); ?>