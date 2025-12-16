<?php
session_start();
require_once __DIR__.'/../config/db.php';
include_once __DIR__.'/../includes/header.php';


if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
$cart = $_SESSION['cart'];

$discountPercent = $_SESSION['discount_percent'] ?? 0;
$discountCode    = $_SESSION['discount_code'] ?? '';
$total = 0;
?>

<div class="f4" style="width:60%;margin-left:215px">
<h2>Your Cart</h2>

<?php if (empty($cart)): ?>
    <div class="alert alert-info">Your cart is empty</div>
<?php else: ?>

<table class="table align-middle">
<thead>
<tr>
    <th>Dish</th>
    <th>Price</th>
    <th style="width:160px">Qty</th>
    <th>Total</th>
    <th></th>
</tr>
</thead>

<tbody>
<?php foreach ($cart as $id => $it):

    $stmt = $conn->prepare("SELECT stock_status FROM menu_items WHERE id=?");
    $stmt->bind_param("i",$id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    $outOfStock = (!$row || $row['stock_status']==='out');
    $line = $it['price'] * $it['qty'];
    if (!$outOfStock) $total += $line;
?>

<tr class="<?= $outOfStock ? 'table-danger':'' ?>">
    <td><?= htmlspecialchars($it['name']) ?></td>
    <td><?= $outOfStock ? '<span class="text-danger fw-bold">Out</span>' : '₹'.number_format($it['price'],2) ?></td>

    <td>
        <?php if(!$outOfStock): ?>
        <form method="post" action="change_qty.php" class="d-flex gap-1">
            <input type="hidden" name="id" value="<?= $id ?>">
            <button name="op" value="dec" class="btn btn-sm btn-outline-secondary">−</button>
            <input type="text" value="<?= $it['qty'] ?>" readonly class="form-control text-center" style="width:60px">
            <button name="op" value="inc" class="btn btn-sm btn-outline-secondary">+</button>
        </form>
        <?php else: ?> —
        <?php endif; ?>
    </td>

    <td><?= $outOfStock ? '—' : '₹'.number_format($line,2) ?></td>
    <td><a href="/user/remove_cart_item.php?id=<?= $id ?>" class="btn btn-sm btn-danger">Remove</a></td>
</tr>

<?php endforeach; ?>
</tbody>
</table>


<div class="card p-3 mb-3">
    <label class="fw-bold mb-2">Apply Coupon</label>

    <input type="text"
           id="coupon"
           class="form-control coupon-input"
           placeholder="Enter coupon code"
           value="<?= htmlspecialchars($discountCode) ?>">

    <small id="coupon-msg" class="mt-1"></small>
</div>

<?php
$discountAmount = ($total * $discountPercent) / 100;
$grandTotal = $total - $discountAmount;
?>

<div class="d-flex justify-content-between align-items-center">
    <div>
        <p>Subtotal: ₹<?= number_format($total,2) ?></p>
        <?php if($discountPercent>0): ?>
            <p class="text-success">Discount (<?= $discountPercent ?>%): −₹<?= number_format($discountAmount,2) ?></p>
        <?php endif; ?>
        <h4>Total: ₹<?= number_format($grandTotal,2) ?></h4>
    </div>

    <a href="/user/checkout.php"
       class="btn btn-success <?= $grandTotal<=0?'disabled':'' ?>">
       Checkout
    </a>
</div>

<?php endif; ?>
</div>

<script>
let timer = null;
const input = document.getElementById('coupon');
const msg = document.getElementById('coupon-msg');

input.addEventListener('input', () => {
    clearTimeout(timer);
    input.classList.remove('is-valid','is-invalid');
    msg.textContent = 'Checking...';
    msg.className = 'text-warning';

    timer = setTimeout(() => {
        fetch('/user/validate_coupon.php?code='+input.value)
        .then(r=>r.json())
        .then(d=>{
            if(d.valid){
                input.classList.add('is-valid');
                msg.textContent = 'Coupon applied!';
                msg.className = 'text-success';
                location.reload();
            } else {
                input.classList.add('is-invalid');
                msg.textContent = 'Invalid coupon';
                msg.className = 'text-danger';
            }
        });
    },800);
});
</script>

<?php include_once __DIR__.'/../includes/footer.php'; ?>
