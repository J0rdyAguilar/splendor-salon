<?php
require "db.php";

$id = $_GET['id'];

// marcar cita como transferencia pendiente
$stmt = $pdo->prepare("UPDATE citas SET estado='transferencia_pendiente' WHERE id=?");
$stmt->execute([$id]);

$fecha = $_GET['fecha'] ?? date('Y-m-d');
header("Location: citas.php?fecha=$fecha");
exit;
