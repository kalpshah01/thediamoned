<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__.'/../config/db.php';
include_once __DIR__.'/../includes/header.php';


$stmt = $conn->prepare(
    "SELECT id,name,category,price,description,image,stock_status 
     FROM menu_items 
     ORDER BY category,name"
);
$stmt->execute();
$res = $stmt->get_result();


$grouped = [];
while ($it = $res->fetch_assoc()) {
    $grouped[$it['category']][] = $it;
}
?>

<div class="py-4" style="padding:2px">
    <h1 style="font-weight:700;font-size:42px;margin-bottom:6px;">Menu</h1>
    <p class="text-muted">Choose your favourite dishes</p>

<?php foreach ($grouped as $cat => $items): ?>
    <h4 class="mt-4"><?= htmlspecialchars($cat); ?></h4>

    <div class="menu-grid">
    <?php foreach ($items as $it): ?>
        <div class="menu-card">

            
            <div class="menu-image">
                <?php if (!empty($it['image']) && file_exists(__DIR__.'/../'.$it['image'])): ?>
                    <img src="/<?= htmlspecialchars($it['image']); ?>" 
                         alt="<?= htmlspecialchars($it['name']); ?>">
                <?php else: ?>
                   
                     <img src="../asstes/images/menu/kajukatri.png" alt="">
                <?php endif; ?>
            </div>

          
            <div class="menu-body">
                <div class="menu-title"><?= htmlspecialchars($it['name']); ?></div>
                <div class="menu-desc"><?= htmlspecialchars($it['description']); ?></div>

                <div class="menu-footer">
                    <div>
                        <?php if ($it['stock_status'] === 'out'): ?>
                            <span class="text-danger fw-bold">Out of Stock</span>
                        <?php else: ?>
                            <strong>₹<?= number_format((float)$it['price'], 2); ?></strong>
                        <?php endif; ?>
                    </div>

                   
                    <?php if ($it['stock_status'] === 'out'): ?>
                        <button class="btn btn-secondary add-btn" disabled>
                            Out of Stock
                        </button>
                    <?php else: ?>
                       <form method="post" action="/user/add_to_cart.php">
    <input type="hidden" name="item_id" value="<?= $it['id']; ?>">
    <input type="hidden" name="qty" value="1">
    <button class="btn btn-warning add-btn">Add to Cart</button>
</form>

                    <?php endif; ?>

                </div>
            </div>
        </div>
    <?php endforeach; ?>
    </div>
<?php endforeach; ?>
</div></div></div>
<?php include_once __DIR__.'/../includes/footer.php'; ?>
