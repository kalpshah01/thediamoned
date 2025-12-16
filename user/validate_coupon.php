<?php
session_start();
require_once __DIR__.'/../config/db.php';

$code = strtoupper(trim($_GET['code'] ?? ''));

$res = ['valid'=>false];

if($code){
    $stmt = $conn->prepare("
        SELECT percentage
        FROM discount_codes
        WHERE code=? AND expiry_date >= CURDATE()
        LIMIT 1
    ");
    $stmt->bind_param("s",$code);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    if($row){
        $_SESSION['discount_code'] = $code;
        $_SESSION['discount_percent'] = (int)$row['percentage'];
        $res['valid'] = true;
    } else {
        unset($_SESSION['discount_code'],$_SESSION['discount_percent']);
    }
}

header('Content-Type: application/json');
echo json_encode($res);
