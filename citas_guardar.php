<?php
require "db.php";

$fecha      = $_POST['fecha'] ?? date('Y-m-d');
$hora       = $_POST['hora'] ?? '';
$cliente_id = $_POST['cliente_id'] ?? '';
$monto      = $_POST['monto'] ?? null;
$notas      = $_POST['notas'] ?? '';

// Insertar la cita como pendiente
$stmt = $pdo->prepare("
  INSERT INTO citas (cliente_id, fecha, hora, monto, notas, estado)
  VALUES (?, ?, ?, ?, ?, 'pendiente')
");
$stmt->execute([$cliente_id, $fecha, $hora, $monto, $notas]);

// NO crear movimientos aquí

header("Location: citas.php?fecha=".$fecha);
exit;
