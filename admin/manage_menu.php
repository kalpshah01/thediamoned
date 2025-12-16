<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: /user/login.php');
    exit;
}

require_once __DIR__.'/../config/db.php';


if (isset($_POST['delete'])) {
    $id = intval($_POST['delete']);
    $conn->query("DELETE FROM menu_items WHERE id=$id");
    header('Location: manage_menu.php');
    exit;
}


if (isset($_POST['toggle_stock'])) {
    $id = intval($_POST['toggle_stock']);

    $conn->query("
        UPDATE menu_items 
        SET stock_status = IF(stock_status='in','out','in') 
        WHERE id=$id
    ");
    header('Location: manage_menu.php');
    exit;
}

$res = $conn->query("SELECT * FROM menu_items ORDER BY category, name");

include_once __DIR__.'/../includes/header.php';
?>

<h3>Menu Items</h3>

<div class="card p-3">
    <a class="btn btn-primary mb-3" href="add_menu.php" style="width:20%">Add Dish</a>

    <table class="table">
        <thead>
        <tr>
            <th>Did</th>
            <th>Name</th>
            <th>Category</th>
            <th>Price</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>

        <?php $i=1; while($r=$res->fetch_assoc()): ?>
        <tr>
            <td><?= $i++; ?></td>
            <td><?= htmlspecialchars($r['name']); ?></td>
            <td><?= htmlspecialchars($r['category']); ?></td>

            <td>
                <?php if ($r['stock_status']==='out'): ?>
                    <span class="text-danger fw-bold">Out of Stock</span>
                <?php else: ?>
                    ₹<?= number_format($r['price'],2); ?>
                <?php endif; ?>
            </td>

            <td>
                <span class="badge <?= $r['stock_status']==='in'?'bg-success':'bg-danger'; ?>">
                    <?= strtoupper($r['stock_status']); ?>
                </span>
            </td>

            <td>
                <form method="post" class="d-inline">
                    <button name="toggle_stock"
                            value="<?= $r['id']; ?>"
                            class="btn btn-sm <?= $r['stock_status']==='in'?'btn-warning':'btn-success'; ?>">
                        <?= $r['stock_status']==='in'?'Out of Stock':'Back In Stock'; ?>
                    </button>
                </form>

                <a href="edit_menu.php?id=<?= $r['id']; ?>" class="btn btn-sm btn-primary">
                    Edit
                </a>

                <form method="post" class="d-inline">
                    <button name="delete"
                            value="<?= $r['id']; ?>"
                            class="btn btn-sm btn-danger"
                            onclick="return confirm('Delete this item?')">
                        Delete
                    </button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>

        </tbody>
    </table>
</div>
</div>
</div>

<?php include_once __DIR__.'/../includes/admin_footer.php'; ?>
