<?php
date_default_timezone_set('America/Guatemala');

$host = "127.0.0.1";
$port = "3306";
$dbname = "splendor_db";
$user = "root";
$pass = "Cuilco123@"; // o la contraseña que tú pusiste

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("ERROR CONEXIÓN: " . $e->getMessage());
}

session_start();
