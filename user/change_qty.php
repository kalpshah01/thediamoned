<?php session_start(); 
$id = intval($_POST['id'] ?? 0);
$op = $_POST['op'] ?? '';

if (!isset($_SESSION['cart'][$id])) exit;

if ($op === 'inc') {
    $_SESSION['cart'][$id]['qty']++;
}
if ($op === 'dec') {
    $_SESSION['cart'][$id]['qty']--;
    if ($_SESSION['cart'][$id]['qty'] <= 0) {
        unset($_SESSION['cart'][$id]);
    }
}

header("Location: cart.php");
exit;
 ?>