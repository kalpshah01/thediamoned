<?php
session_start();
require_once __DIR__.'/../config/db.php';

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $pass  = $_POST['new_password'];

    if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{8,}$/', $pass)) {
        $msg = "Password must be strong";
    } else {
        $s = $conn->prepare("SELECT id FROM users WHERE email=?");
        $s->bind_param("s", $email);
        $s->execute();
        $u = $s->get_result()->fetch_assoc();

        if ($u) {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $up = $conn->prepare("UPDATE users SET password=? WHERE id=?");
            $up->bind_param("si", $hash, $u['id']);
            $up->execute();
            header("Location: login.php?reset=success");
            exit;
        } else {
            $msg = "Email not found";
        }
    }
}

include_once __DIR__.'/../includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
<link rel="stylesheet" href="../asstes/css/style.css">
</head>
<body>
    <h3 class="text-center">Forgot Password</h3>
    
    <?php if($msg): ?>
        <div class="alert alert-danger"><?= $msg ?></div>
<?php endif; ?>
<div class="p">
<form method="post" class="card p-3 text-center" >
    <input name="email" type="email" class="form-control mb-2"
    placeholder="Registered Email" required>
    <input name="new_password" type="password" class="form-control mb-2"
    placeholder="New Password" required>
    <button class="btn btn-primary" >Reset Password</button>
</form></div>
</div></div>

</body>
</html>
<?php include_once __DIR__.'/../includes/footer.php'; ?>
