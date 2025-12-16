<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__.'/../config/db.php';


$stmt = $conn->prepare("SELECT name,email,password FROM users WHERE id=?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$msg = "";


if (isset($_POST['update_profile'])) {
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);

    $u = $conn->prepare("UPDATE users SET name=?, email=? WHERE id=?");
    $u->bind_param("ssi", $name, $email, $_SESSION['user_id']);
    $u->execute();

    $_SESSION['user_name'] = $name; 
    $msg = "Profile updated successfully";

       header("Location: menu.php");
}



if (isset($_POST['change_password'])) {
    $old = $_POST['old_password'];
    $new = $_POST['new_password'];

    if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{8,}$/', $new)) {
        $msg = "Password must be 8+ chars, upper, lower & number";
    } elseif (!password_verify($old, $user['password'])) {
        $msg = "Old password incorrect";
    } else {
        $hash = password_hash($new, PASSWORD_DEFAULT);
        $u = $conn->prepare("UPDATE users SET password=? WHERE id=?");
        $u->bind_param("si", $hash, $_SESSION['user_id']);
        $u->execute();
        $msg = "Password updated successfully";

       header("Location: menu.php");
    }
}
include_once __DIR__.'/../includes/header.php';
?>

<h3>Update Profile</h3>

<?php if($msg): ?>
<div class="alert alert-info"><?= $msg ?></div>
<?php endif; ?>


<div class="card p-3 mb-3">
<form method="post" action="update_profile.php">
    <h5>Profile Info</h5>
    <label for="name">Username</label>
    <input name="name" class="form-control mb-2"
           value="<?= htmlspecialchars($user['name']) ?>"  required>
    <label for="email">Email</label>
           <input name="email" class="form-control mb-2"
           value="<?= htmlspecialchars($user['email']) ?>" required>
    <button name="update_profile" class="btn btn-primary">Save</button>
</form>
</div>

<div class="card p-3">
<form method="post">
    <h5>Change Password</h5>
    <input type="password" name="old_password"
           class="form-control mb-2" placeholder="Old Password" required>
    <input type="password" name="new_password"
           class="form-control mb-2"
           placeholder="New Password (Strong)" required>
    <button name="change_password" class="btn btn-warning">Change</button>
</form>
</div>

<a href="forgot_password.php" class="d-block mt-3">
    Forgot password?
</a>

<?php include_once __DIR__.'/../includes/footer.php'; ?>
