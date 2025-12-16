<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$id = intval($_GET['id'] ?? 0);


if (!isset($_SESSION['user_id']) || $id <= 0) {
    echo "NO";
    exit;
}


$stmt = $conn->prepare("UPDATE orders SET status='Delivered' WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $id, $_SESSION['user_id']);
$stmt->execute();

echo "OK";
