<?php
require "db.php";

$id    = $_POST['id'] ?? null;
$fecha = $_POST['fecha'] ?? date('Y-m-d');

$monto_form = floatval($_POST['monto'] ?? 0); // total
$es_abono   = isset($_POST['es_abono']) && $_POST['es_abono'] == "1";
$abono      = floatval($_POST['abono'] ?? 0);

if (!$id || $monto_form <= 0) {
    header("Location: citas.php?fecha=" . $fecha);
    exit;
}

// Cita + cliente
$stmt = $pdo->prepare("
    SELECT c.*, cl.nombre AS cliente
    FROM citas c
    INNER JOIN clientes cl ON cl.id = c.cliente_id
    WHERE c.id = ?
");
$stmt->execute([$id]);
$cita = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cita) die("Cita no encontrada");

$pagado_db = floatval($cita['monto_pagado'] ?? 0);
$total = $monto_form;

// ==============================
// MODO NORMAL (NO ABONO)
// ==============================
if (!$es_abono) {

    // Solo marcar transferencia pendiente (sin registrar ingreso)
    $stmt = $pdo->prepare("UPDATE citas SET monto=?, estado='transferencia_pendiente' WHERE id=?");
    $stmt->execute([$total, $id]);

    header("Location: citas.php?fecha=" . $fecha);
    exit;
}

// ==============================
// MODO ABONO
// ==============================
if ($abono <= 0) die("Debes ingresar el abono.");

$saldo = max(0, $total - $pagado_db);

if ($abono > $saldo + 0.0001) {
    die("El abono no puede ser mayor al saldo. Saldo: Q " . number_format($saldo, 2));
}

$nuevo_pagado = $pagado_db + $abono;
$nuevo_saldo  = max(0, $total - $nuevo_pagado);

// Estado: si completa -> pagado; si no -> transferencia_pendiente
$estado = ($nuevo_saldo <= 0.0001) ? "pagado" : "transferencia_pendiente";

$stmt = $pdo->prepare("UPDATE citas SET monto=?, monto_pagado=?, estado=? WHERE id=?");
$stmt->execute([$total, $nuevo_pagado, $estado, $id]);

// Movimiento por el ABONO
$desc = "Abono transferencia — " . $cita['cliente'];

$stmt = $pdo->prepare("
    INSERT INTO movimientos (tipo, fecha, descripcion, monto, cita_id)
    VALUES ('ingreso', CURDATE(), ?, ?, ?)
");
$stmt->execute([$desc, $abono, $id]);

header("Location: citas.php?fecha=" . $fecha);
exit;
