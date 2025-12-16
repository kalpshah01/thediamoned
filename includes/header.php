<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$uri = $_SERVER['REQUEST_URI'];
$_SESSION['role'] = str_contains($uri, '/admin/') ? 'admin' : 'user';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>DelishBite</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="/assets/css/style.css" rel="stylesheet">
</head>

<body class="d-flex flex-column min-vh-100">

<?php
if ($_SESSION['role'] === 'admin') {
    include __DIR__.'/admin_navbar.php';
} else {
    include __DIR__.'/user_navbar.php';
}
?>

<!-- PAGE CONTENT START -->
<div class="page-wrapper flex-grow-1">
       <div class="container-fluid content-wrapper pt-4">
    
