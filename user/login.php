<?php 
session_start();
require_once __DIR__.'/../config/db.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email']);
    $pwd   = $_POST['password'];

    
    $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    if ($row && password_verify($pwd, $row['password'])) {

        
        if ($row['role'] === 'admin') {
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_name'] = $row['name'];
            $_SESSION['role'] = 'admin';
            header("Location: /admin/index.php");
            exit;
        }

        
        else {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['name'];
            $_SESSION['role'] = 'user';
            header("Location: /user/menu.php");
            exit;
        }
    } 
    else {
        $error = "Invalid email or password!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - DelishBite</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card p-4 shadow-sm">

                <h3 class="mb-3">Login</h3>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <form method="post">
                    <input class="form-control mb-2" name="email" type="email" placeholder="Email" required>

                    <input class="form-control mb-3" name="password" type="password" placeholder="Password" required>

                    <button class="btn btn-primary w-100">Login</button>
                    <a href="./register.php">No Account register first</a>
                </form>

            </div>
        </div>
    </div>
</div>

</body>
</html>
