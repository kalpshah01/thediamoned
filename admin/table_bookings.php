<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: /user/login.php');
    exit;
}

require_once __DIR__.'/../config/db.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['id'])) {
    $id  = intval($_POST['id']);
    $act = $_POST['action'];

    if (in_array($act, ['Confirmed', 'Cancelled'])) {
        $up = $conn->prepare("UPDATE table_bookings SET status=? WHERE id=?");
        $up->bind_param("si", $act, $id);
        $up->execute();
    }

    header('Location: table_bookings.php');
    exit;
}


$res = $conn->query("
    SELECT tb.*, u.name AS user_name
    FROM table_bookings tb
    LEFT JOIN users u ON u.id = tb.user_id
    ORDER BY booking_date DESC
");

include_once __DIR__.'/../includes/header.php';
?>

<h3>Table Bookings</h3>

<div class="card p-3">
<table class="table align-middle">
<thead>
<tr>
    <th>#</th>
    <th>User</th>
    <th>Date</th>
    <th>Time</th>
    <th>Guests</th>
    <th>Status</th>
    <th>Action</th>
</tr>
</thead>

<tbody>
<?php $i=1; while ($r = $res->fetch_assoc()): ?>
<tr>
    <td><?= $i++; ?></td>
    <td><?= htmlspecialchars($r['user_name']); ?></td>
    <td><?= $r['booking_date']; ?></td>
    <td><?= $r['booking_time']; ?></td>
    <td><?= (int)$r['guests']; ?></td>

    <td>
        <?php if ($r['status'] === 'Pending'): ?>
            <span class="badge bg-warning text-dark">Pending</span>
        <?php elseif ($r['status'] === 'Confirmed'): ?>
            <span class="badge bg-success">Confirmed</span>
        <?php else: ?>
            <span class="badge bg-danger">Cancelled</span>
        <?php endif; ?>
    </td>

    <td>
        <?php if ($r['status'] === 'Pending'): ?>

            
            <form method="post" class="d-inline">
                <input type="hidden" name="id" value="<?= $r['id']; ?>">
                <button name="action" value="Confirmed"
                        class="btn btn-sm btn-success">
                    Accept
                </button>
            </form>

           
            <form method="post" class="d-inline">
                <input type="hidden" name="id" value="<?= $r['id']; ?>">
                <button name="action" value="Cancelled"
                        class="btn btn-sm btn-danger">
                    Reject
                </button>
            </form>

        <?php elseif ($r['status'] === 'Confirmed'): ?>

           
            <form method="post" class="d-inline">
                <input type="hidden" name="id" value="<?= $r['id']; ?>">
                <button name="action" value="Cancelled"
                        class="btn btn-sm btn-outline-danger"
                        onclick="return confirm('Cancel this booking?');">
                    Cancel
                </button>
            </form>

        <?php else: ?>
            <span class="text-muted small">No actions</span>
        <?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
        </div>
        </div>
<?php include_once __DIR__.'/../includes/admin_footer.php'; ?>
