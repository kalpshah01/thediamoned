<?php

require_once __DIR__ . '/../config/db.php';
$admin_email = 'admin@example.com';
$admin_password = 'Admin@123';
$admin_name = 'Admin';
$stmt = $conn->prepare('SELECT id FROM users WHERE email=? LIMIT 1');
$stmt->bind_param('s',$admin_email); $stmt->execute(); $stmt->store_result();
if ($stmt->num_rows) { echo 'Admin exists. Delete this file.'; exit; }
$hash = password_hash($admin_password, PASSWORD_DEFAULT);
$ins = $conn->prepare('INSERT INTO users (name,email,password,role) VALUES (?,?,?,"admin")');
$ins->bind_param('sss',$admin_name,$admin_email,$hash);
if ($ins->execute()) echo 'Admin created: '.$admin_email.' / '.$admin_password.' - delete this file now'; else echo 'Failed: '.$ins->error;
?>