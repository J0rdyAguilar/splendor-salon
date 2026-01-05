<?php
require "db.php";

$id = $_GET['id'] ?? 0;

// 1️⃣ Verificar si el cliente tiene citas
$stmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM citas 
    WHERE cliente_id = ?
");
$stmt->execute([$id]);
$totalCitas = $stmt->fetchColumn();

// 2️⃣ Si tiene citas → NO borrar
if ($totalCitas > 0) {
    header("Location: clientes.php?error=tiene_citas");
    exit;
}

// 3️⃣ Si NO tiene citas → borrar cliente
$stmt = $pdo->prepare("DELETE FROM clientes WHERE id = ?");
$stmt->execute([$id]);

header("Location: clientes.php?ok=eliminado");
exit;
