<?php
require "db.php";

$id = $_GET['id'];

// 1. Marcar cita como pagada
$stmt = $pdo->prepare("UPDATE citas SET estado='pagado' WHERE id=?");
$stmt->execute([$id]);

// 2. Obtener monto y fecha real
$stmt = $pdo->prepare("SELECT monto, fecha FROM citas WHERE id=?");
$stmt->execute([$id]);
$cita = $stmt->fetch(PDO::FETCH_ASSOC);

// 3. Registrar movimiento con cita_id
$stmt = $pdo->prepare("
    INSERT INTO movimientos (tipo, fecha, descripcion, monto, cita_id)
    VALUES ('ingreso', ?, ?, ?, ?)
");
$stmt->execute([
    $cita['fecha'],
    "Pago de cita",
    $cita['monto'],
    $id
]);

// Regresar
$fecha = $_GET['fecha'] ?? date('Y-m-d');
header("Location: citas.php?fecha=$fecha");
exit;
