<?php
session_start();
require_once __DIR__ . '/../config/db.php';


$item_id = intval($_POST['item_id'] ?? 0);
$qty     = intval($_POST['qty'] ?? 1);

if ($item_id <= 0 || $qty <= 0) {
    header("Location: menu.php");
    exit;
}

$stmt = $conn->prepare("
    SELECT id, name, price, stock_status 
    FROM menu_items 
    WHERE id = ?
");
$stmt->bind_param("i", $item_id);
$stmt->execute();
$item = $stmt->get_result()->fetch_assoc();

if (!$item || $item['stock_status'] === 'out') {
    header("Location: menu.php?error=out_of_stock");
    exit;
}

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
if (isset($_SESSION['cart'][$item_id])) {
    $_SESSION['cart'][$item_id]['qty'] += $qty;
} else {
    $_SESSION['cart'][$item_id] = [
        'name'  => $item['name'],
        'price' => $item['price'],
        'qty'   => $qty
    ];
}

header("Location: cart.php");
exit;
