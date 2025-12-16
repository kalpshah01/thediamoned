<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: /admin/login.php");
    exit;
}

require_once __DIR__ . '/../config/db.php';


if (isset($_POST['assign'])) {
    $order_id = intval($_POST['order_id']);
    $emp_id   = intval($_POST['employee_id']);
    $mins     = intval($_POST['delivery_time_minutes']);

    $stmt = $conn->prepare("
        UPDATE orders SET
            employee_id = ?,
            delivery_estimate_minutes = ?,
            start_time = NOW(),
            initial_distance = NULL,
            status = 'Out for Delivery'
        WHERE id = ?
    ");
    $stmt->bind_param("iii", $emp_id, $mins, $order_id);
    $stmt->execute();

    header("Location: orders.php");
    exit;
}


if (isset($_POST['reset_order'])) {
    $order_id = intval($_POST['order_id']);

    $stmt = $conn->prepare("
        UPDATE orders SET
            employee_id = NULL,
            delivery_estimate_minutes = NULL,
            start_time = NULL,
            initial_distance = NULL,
            status = 'Placed'
        WHERE id = ?
    ");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();

    header("Location: orders.php");
    exit;
}

$res = $conn->query("
    SELECT o.*, u.name AS user_name, e.name AS emp_name
    FROM orders o
    LEFT JOIN users u ON u.id=o.user_id
    LEFT JOIN employees e ON e.id=o.employee_id
    ORDER BY o.created_at DESC
");

$emps = $conn->query("SELECT id, name FROM employees")->fetch_all(MYSQLI_ASSOC);

include_once __DIR__ . '/../includes/header.php';
?>

<h3>Orders</h3>

<table class="table">
<thead>
<tr>
    <th>Oid</th><th>User</th><th>Total</th><th>Employee</th><th>Status</th><th>Action</th>
</tr>
</thead>
<tbody>
<?php while ($r = $res->fetch_assoc()): ?>
<tr>
    <td><?= $r['id']; ?></td>
    <td><?= htmlspecialchars($r['user_name']); ?></td>
    <td>₹<?= $r['total_amount']; ?></td>
    <td><?= htmlspecialchars($r['emp_name'] ?? '—'); ?></td>
    <td><?= $r['status']; ?></td>
    <td>

        <form method="post" class="mb-1 d-flex gap-1">
            <input type="hidden" name="order_id" value="<?= $r['id']; ?>">

            <select name="employee_id" class="form-control form-control-sm">
                <?php foreach ($emps as $e): ?>
                    <option value="<?= $e['id']; ?>"><?= $e['name']; ?></option>
                <?php endforeach; ?>
            </select>

            <select name="delivery_time_minutes" class="form-control form-control-sm">
                <option value="10">10</option>
                <option value="20" selected>20</option>
                <option value="30">30</option>
            </select>

            <button name="assign" class="btn btn-sm btn-primary">Assign</button>
        </form>

        <form method="post">
            <input type="hidden" name="order_id" value="<?= $r['id']; ?>">
            <button name="reset_order" class="btn btn-sm btn-danger">Reset</button>
        </form>

    </td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
                </div>
                </div>
<?php include_once __DIR__.'/../includes/admin_footer.php'; ?>
