<?php
session_start();
if (!isset($_SESSION['admin_id'])) header('Location: login.php');
require_once __DIR__ . '/../config/db.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') header('Location: manage_menu.php');
$id = intval($_POST['id'] ?? 0); if (!$id) header('Location: manage_menu.php');
$stmt = $conn->prepare('SELECT image FROM menu_items WHERE id=?'); $stmt->bind_param('i',$id); $stmt->execute(); $row = $stmt->get_result()->fetch_assoc();
$del = $conn->prepare('DELETE FROM menu_items WHERE id=?'); $del->bind_param('i',$id);
if ($del->execute()) { if (!empty($row['image']) && file_exists(__DIR__.'/../'.$row['image'])) @unlink(__DIR__.'/../'.$row['image']); header('Location: manage_menu.php?deleted=1'); } else header('Location: manage_menu.php?error=1');
?>