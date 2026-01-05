<?php
require "db.php";

// SOLO ADMIN
if ($_SESSION['rol'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$rol      = $_POST['rol'] ?? 'empleado';

if (!$username || !$password) {
    die("Datos incompletos");
}

// Hash seguro
$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("
    INSERT INTO usuarios (username, password_hash, rol)
    VALUES (?, ?, ?)
");

$stmt->execute([$username, $hash, $rol]);

header("Location: usuarios.php");
exit;
