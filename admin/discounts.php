<?php
session_start();
if (!isset($_SESSION['admin_id'])) header('Location: login.php');
require_once __DIR__ . '/../config/db.php';
$errors=[]; if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['action'])) {
  $action = $_POST['action'];
  if ($action==='add') {
    $code = strtoupper(trim($_POST['code'] ?? '')); $perc = intval($_POST['percentage'] ?? 0); $expiry = $_POST['expiry'] ?? null;
    if ($code==='' || $perc<=0) $errors[]='Code and percentage required';
    else {
      $ins = $conn->prepare('INSERT INTO discount_codes (code,percentage,expiry_date) VALUES (?,?,?)');
      $ins->bind_param('sis',$code,$perc,$expiry); $ins->execute();
    }
  } elseif ($action==='delete') {
    $id = intval($_POST['id']); $del = $conn->prepare('DELETE FROM discount_codes WHERE id=?'); $del->bind_param('i',$id); $del->execute();
  }
}
$list = $conn->query('SELECT * FROM discount_codes ORDER BY created_at DESC');
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Discounts</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"><link href="/assets/css/style.css" rel="stylesheet"></head><body class="bg-light"><div class="container container-max py-4"><div class="d-flex justify-content-between align-items-center mb-3"><h2>Discount Codes</h2><div><a href="index.php" class="btn btn-secondary">Dashboard</a></div></div><div class="card p-3 shadow-sm"><h5>Add Code</h5><?php if(!empty($errors)) echo '<div class="alert alert-danger">'.implode('<br>',$errors).'</div>'; ?><form method="post" class="row g-2 mb-3"><input type="hidden" name="action" value="add"><div class="col-md-4"><input class="form-control" name="code" placeholder="CODE10" required></div><div class="col-md-3"><input class="form-control" name="percentage" type="number" placeholder="10" required></div><div class="col-md-3"><input class="form-control" name="expiry" type="date"></div><div class="col-md-2"><button class="btn btn-primary">Add</button></div></form><table class="table"><thead><tr><th>#</th><th>Code</th><th>%</th><th>Expiry</th><th>Actions</th></tr></thead><tbody><?php $i=1; while($r=$list->fetch_assoc()): ?><tr><td><?php echo $i++; ?></td><td><?php echo htmlspecialchars($r['code']); ?></td><td><?php echo (int)$r['percentage']; ?></td><td><?php echo htmlspecialchars($r['expiry_date']); ?></td><td><form method="post" style="display:inline"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?php echo $r['id']; ?>"><button class="btn btn-sm btn-danger">Delete</button></form></td></tr><?php endwhile; if($list->num_rows===0) echo '<tr><td colspan="5">No codes</td></tr>'; ?></tbody></table></div></div></body></html>