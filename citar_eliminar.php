<?php
require "db.php";

$id    = $_GET['id'] ?? 0;
$fecha = $_GET['fecha'] ?? date('Y-m-d');

// Borrar movimientos asociados a esa cita
$stmtM = $pdo->prepare("DELETE FROM movimientos WHERE cita_id = ?");
$stmtM->execute([$id]);

// Borrar cita
$stmt = $pdo->prepare("DELETE FROM citas WHERE id = ?");
$stmt->execute([$id]);

header("Location: citas.php?fecha=".$fecha);
exit;
