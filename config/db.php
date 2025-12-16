<?php
$host='localhost'; $user='root'; $pass=''; $dbname='restaurant_db';
$conn = new mysqli($host,$user,$pass,$dbname);
if ($conn->connect_error) die('DB connection error: '.$conn->connect_error);
$conn->set_charset('utf8mb4');
?>