<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}
require_once __DIR__.'/../config/db.php';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    $conn->query("DELETE FROM orders WHERE user_id=$id");
    $conn->query("DELETE FROM table_bookings WHERE user_id=$id");

    $conn->query("DELETE FROM users WHERE id=$id AND role='user'");

    header("Location: users.php");
    exit;
}


$res = $conn->query("SELECT id,name,email,created_at FROM users WHERE role='user'");
include_once __DIR__.'/../includes/header.php';
?>

<h3>Users</h3>

<table class="table table-striped">
<thead>
<tr><th>Name</th><th>Email</th><th>Joined</th><th>Action</th></tr>
</thead>
<tbody>
<?php while($u=$res->fetch_assoc()): ?>
<tr>
<td><?= htmlspecialchars($u['name']) ?></td>
<td><?= htmlspecialchars($u['email']) ?></td>
<td><?= $u['created_at'] ?></td>
<td>
<a href="?delete=<?= $u['id'] ?>" class="btn btn-sm btn-danger">Delete</a>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div></div>
<?php include_once __DIR__.'/../includes/admin_footer.php'; ?>
