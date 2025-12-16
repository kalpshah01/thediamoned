<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: menu.php");
    exit;
}
include_once __DIR__.'/../includes/header.php';
?>

<div class="text-center mt-5">
    <h2>Welcome to DelishBite</h2>
    <p class="text-muted">Order food or book tables easily</p>

    <div class="d-flex justify-content-center gap-3 mt-4">
        <a href="login.php" class="btn btn-primary btn-lg">Login</a>
        <a href="register.php" class="btn btn-outline-primary btn-lg">Register</a>
    </div>
</div>
</div>
</div>

<?php include_once __DIR__.'/../includes/footer.php'; ?>
