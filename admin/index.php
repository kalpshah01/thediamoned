<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: /user/login.php');
    exit;
}

require_once __DIR__.'/../config/db.php';
include_once __DIR__.'/../includes/header.php';

$total_menu = $conn->query("SELECT COUNT(*) c FROM menu_items")->fetch_assoc()['c'];
$total_orders = $conn->query("SELECT COUNT(*) c FROM orders")->fetch_assoc()['c'];
?>

<div class="container py-4">
    <h2>Admin Dashboard</h2>

    <div class="d-flex gap-2 mb-3">
        <a class="btn btn-primary" href="manage_menu.php">Manage Menu</a>
        <a class="btn btn-secondary" href="table_bookings.php">Table Bookings</a>
        <a class="btn btn-secondary" href="orders.php">Food Orders</a>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card p-3">
                Total Dishes
                <h2><?= $total_menu ?></h2>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-3">
                Total Orders
                <h2><?= $total_orders ?></h2>
            </div>
        </div>
    </div>
</div>

</div> 

<?php include_once __DIR__.'/../includes/admin_footer.php'; ?>
