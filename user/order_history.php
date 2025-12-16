<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /user/login.php");
    exit;
}

require_once __DIR__ . '/../config/db.php';
include_once __DIR__ . '/../includes/header.php';

$res = $conn->prepare("
    SELECT * FROM orders WHERE user_id=? ORDER BY created_at DESC
");
$res->bind_param("i", $_SESSION['user_id']);
$res->execute();
$orders = $res->get_result();
?>

<h3>My Orders</h3>

<table class="table">
<thead>
<tr>
    <th>ID</th><th>Total</th><th>Status</th><th>Track</th>
</tr>
</thead>
<tbody>
<?php while ($o = $orders->fetch_assoc()): ?>
<tr>
    <td>#<?= $o['id']; ?></td>
    <td>₹<?= $o['total_amount']; ?></td>
    <td><?= $o['status']; ?></td>
    <td>
        <a href="/user/order_status.php?id=<?= $o['id']; ?>" class="btn btn-sm btn-primary">
            Track
        </a>
    </td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div></div>
<?php include_once __DIR__ . '/../includes/footer.php'; ?>
