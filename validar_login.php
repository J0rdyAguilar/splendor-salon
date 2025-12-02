<?php
// validar_login.php
require "db.php";

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Buscar usuario
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Validar contraseña
if ($user && password_verify($password, $user['password_hash'])) {

    // Guardar datos en sesión
    $_SESSION['usuario'] = $user['username'];
    $_SESSION['rol'] = $user['rol'];  // <--- IMPORTANTE

    header("Location: dashboard.php");
    exit;
} 

// Si falla:
header("Location: login.php?error=1");
exit;
