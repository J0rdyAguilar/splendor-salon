<?php
require "db.php";

$id = $_GET['id'];

// Cambiar estado a pagado
$stmt = $pdo->prepare("UPDATE citas SET estado='pagado' WHERE id=?");
$stmt->execute([$id]);

// Obtener datos reales de la cita
$stmt = $pdo->prepare("SELECT monto, fecha FROM citas WHERE id=?");
$stmt->execute([$id]);
$cita = $stmt->fetch(PDO::FETCH_ASSOC);

// Registrar ingreso REAL
$stmt = $pdo->prepare("
  INSERT INTO movimientos (tipo, fecha, descripcion, monto, cita_id)
  VALUES ('ingreso', ?, 'Pago de cita', ?, ?)
");
$stmt->execute([
    $cita['fecha'],
    $cita['monto'],
    $id
]);

$fecha = $_GET['fecha'] ?? date('Y-m-d');
header("Location: citas.php?fecha=$fecha");
exit;
