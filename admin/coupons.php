<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}
require_once __DIR__.'/../config/db.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = strtoupper(trim($_POST['code']));
    $percent = (int)$_POST['percentage'];
    $expiry = $_POST['expiry_date'];

    if (!empty($_POST['id'])) {
        
        $id = (int)$_POST['id'];
        $stmt = $conn->prepare(
            "UPDATE discount_codes SET code=?, percentage=?, expiry_date=? WHERE id=?"
        );
        $stmt->bind_param("sisi", $code, $percent, $expiry, $id);
    } else {
        
        $stmt = $conn->prepare(
            "INSERT INTO discount_codes (code, percentage, expiry_date) VALUES (?,?,?)"
        );
        $stmt->bind_param("sis", $code, $percent, $expiry);
    }
    $stmt->execute();
    header("Location: coupons.php");
    exit;
}


if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM discount_codes WHERE id=$id");
    header("Location: coupons.php");
    exit;
}

$res = $conn->query("SELECT * FROM discount_codes ORDER BY expiry_date DESC");
include_once __DIR__.'/../includes/header.php';
?>

<h3>Manage Coupons</h3>

<div class="card p-3 mb-3">
<form method="post" class="row g-2">
    <input type="hidden" name="id" id="cid">
    <div class="col-md-3">
        <input name="code" id="ccode" class="form-control" placeholder="CODE" required>
    </div>
    <div class="col-md-3">
        <input name="percentage" id="cper" type="number" class="form-control" placeholder="Discount %" required>
    </div>
    <div class="col-md-3">
        <input name="expiry_date" id="cexp" type="date" class="form-control" required>
    </div>
    <div class="col-md-3">
        <button class="btn btn-primary w-100">Save</button>
    </div>
</form>
</div>

<table class="table table-bordered">
<thead>
<tr><th>Code</th><th>%</th><th>Expiry</th><th>Action</th></tr>
</thead>
<tbody>
<?php while($r=$res->fetch_assoc()): ?>
<tr>
<td><?= htmlspecialchars($r['code']) ?></td>
<td><?= $r['percentage'] ?>%</td>
<td><?= $r['expiry_date'] ?></td>
<td>
<button class="btn btn-sm btn-warning"
onclick='editCoupon(<?= json_encode($r) ?>)'>Edit</button>
<a href="?delete=<?= $r['id'] ?>" class="btn btn-sm btn-danger">Delete</a>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div></div>
<script>
function editCoupon(c){
    cid.value = c.id;
    ccode.value = c.code;
    cper.value = c.percentage;
    cexp.value = c.expiry_date;
}
</script>

<?php include_once __DIR__.'/../includes/admin_footer.php'; ?>
