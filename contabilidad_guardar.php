<?php
require "db.php";

$tipo        = $_POST['tipo'] ?? 'ingreso';
$fecha       = $_POST['fecha'] ?? date('Y-m-d');
$descripcion = $_POST['descripcion'] ?? '';
$monto       = $_POST['monto'] ?? 0;

$stmt = $pdo->prepare("
  INSERT INTO movimientos (tipo, fecha, descripcion, monto)
  VALUES (?, ?, ?, ?)
");
$stmt->execute([$tipo, $fecha, $descripcion, $monto]);

header("Location: contabilidad.php");
exit;
