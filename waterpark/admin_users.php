<?php
include("header.php");

if(!isset($_SESSION['userid']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

// delete user
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);

    // elak admin delete diri sendiri
    if($id != $_SESSION['userid']){
        $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
        $stmt->bind_param("i",$id);
        $stmt->execute();
    }

    header("Location: admin_users.php");
    exit();
}

// change role
if(isset($_POST['change_role'])){
    $id = intval($_POST['user_id']);
    $newRole = $_POST['new_role'];

    if($newRole == 'admin' || $newRole == 'student'){
        $stmt = $conn->prepare("UPDATE users SET role=? WHERE id=?");
        $stmt->bind_param("si",$newRole,$id);
        $stmt->execute();
    }

    header("Location: admin_users.php");
    exit();
}

$result = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
?>

<div class="glass">
    <h3 class="mb-4">Manage Users</h3>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Fullname</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Role</th>
                <th>Registered Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($result)){ ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['fullname']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['phone']) ?></td>
                <td><?= htmlspecialchars($row['role']) ?></td>
                <td><?= htmlspecialchars($row['regdate']) ?></td>
                <td>
                    <form method="POST" class="d-inline">
                        <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                        <select name="new_role" class="form-select form-select-sm d-inline w-auto">
                            <option value="student" <?= $row['role']=='student' ? 'selected' : '' ?>>Student</option>
                            <option value="admin" <?= $row['role']=='admin' ? 'selected' : '' ?>>Admin</option>
                        </select>
                        <button type="submit" name="change_role" class="btn btn-primary btn-sm">Update Role</button>
                    </form>

                    <?php if($row['id'] != $_SESSION['userid']){ ?>
                    <a href="admin_users.php?delete=<?= $row['id'] ?>" 
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('Delete this user?')">
                       Delete
                    </a>
                    <?php } ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php include("footer.php"); ?>