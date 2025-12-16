<?php
session_start();
if (!isset($_SESSION['admin_id'])) header('Location: login.php');
require_once __DIR__ . '/../config/db.php';
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['action'])) {
  $id = intval($_POST['id']); $action = $_POST['action'];
  if (in_array($action,['Confirmed','Cancelled'])) {
    $up = $conn->prepare('UPDATE table_bookings SET status=? WHERE id=?'); $up->bind_param('si',$action,$id); $up->execute();
  }
}
$stmt = $conn->prepare('SELECT tb.id,tb.booking_date,tb.booking_time,tb.guests,tb.status,u.name AS user_name,u.email FROM table_bookings tb LEFT JOIN users u ON u.id=tb.user_id ORDER BY tb.booking_date DESC, tb.booking_time DESC');
$stmt->execute(); $res = $stmt->get_result();
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Bookings</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"><link href="/assets/css/style.css" rel="stylesheet"></head><body class="bg-light"><div class="container container-max py-4"><div class="d-flex justify-content-between align-items-center mb-3"><h2>Bookings</h2><div><a href="index.php" class="btn btn-secondary">Dashboard</a></div></div><div class="card p-3 shadow-sm"><table class="table"><thead><tr><th>#</th><th>User</th><th>Date</th><th>Time</th><th>Guests</th><th>Status</th><th>Actions</th></tr></thead><tbody><?php $i=1; while($r=$res->fetch_assoc()): ?><tr><td><?php echo $i++; ?></td><td><?php echo htmlspecialchars($r['user_name'] ?? 'Guest'); ?><br><small class="small-muted"><?php echo htmlspecialchars($r['email'] ?? ''); ?></small></td><td><?php echo htmlspecialchars($r['booking_date']); ?></td><td><?php echo htmlspecialchars($r['booking_time']); ?></td><td><?php echo (int)$r['guests']; ?></td><td><?php echo htmlspecialchars($r['status']); ?></td><td><form method="post" style="display:inline"><input type="hidden" name="id" value="<?php echo $r['id']; ?>"><button name="action" value="Confirmed" class="btn btn-sm btn-success">Confirm</button></form> <form method="post" style="display:inline"><input type="hidden" name="id" value="<?php echo $r['id']; ?>"><button name="action" value="Cancelled" class="btn btn-sm btn-danger">Cancel</button></form></td></tr><?php endwhile; if($res->num_rows===0) echo '<tr><td colspan="7">No bookings</td></tr>'; ?></tbody></table></div></div></body></html>