<?php
// validar_login.php
require "db.php"; // aquí ya debería estar session_start()

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// Buscar usuario
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Validar contraseña
if ($user && password_verify($password, $user['password_hash'])) {

    // Guardar datos en sesión
    $_SESSION['id']      = $user['id'];       // ✔ útil para permisos y registros
    $_SESSION['usuario'] = $user['username']; // ✔ nombre visible
    $_SESSION['rol']     = $user['rol'];      // ✔ admin / empleado

    header("Location: dashboard.php");
    exit;
}

// Si falla:
header("Location: login.php?error=1");
exit;
