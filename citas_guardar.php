<?php
require "db.php";

// ------------------------
// DATOS RECIBIDOS
// ------------------------
$fecha      = $_POST['fecha'] ?? date('Y-m-d');
$hora       = $_POST['hora'] ?? '';
$duracion   = intval($_POST['duracion']); // en minutos (30, 60, 90, 120, 150, 180)
$cliente_id = $_POST['cliente_id'] ?? '';
$monto      = $_POST['monto'] ?? null;
$notas      = $_POST['notas'] ?? '';


// ------------------------
// CALCULAR HORA FIN
// ------------------------
$inicio = strtotime($hora);
$fin = strtotime("+$duracion minutes", $inicio);
$hora_fin = date("H:i:s", $fin);


// ------------------------
// VERIFICAR SOLAPAMIENTO
// (Solo citas NO canceladas)
// ------------------------
$stmt = $pdo->prepare("
    SELECT *
    FROM citas
    WHERE fecha = ?
    AND estado != 'cancelado'
    AND (
        (hora <= ? AND hora_fin > ?) OR
        (hora < ? AND hora_fin >= ?)
    )
");

$stmt->execute([
    $fecha,
    $hora,       // ¿Alguien empieza antes y termina después?
    $hora,       // (se cruza)
    $hora_fin,   // ¿Alguien empieza antes del final?
    $hora_fin    // (se cruza)
]);

$conflictos = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($conflictos) > 0) {
    // Volver a la página con error
    header("Location: citas.php?fecha={$fecha}&error=solapada");
    exit;
}


// ------------------------
// INSERTAR CITA
// ------------------------
$stmt = $pdo->prepare("
    INSERT INTO citas (cliente_id, fecha, hora, hora_fin, duracion, monto, notas, estado)
    VALUES (?, ?, ?, ?, ?, ?, ?, 'pendiente')
");

$stmt->execute([
    $cliente_id,
    $fecha,
    $hora,
    $hora_fin,
    $duracion,
    $monto,
    $notas
]);


// ------------------------
// REGRESAR
// ------------------------
header("Location: citas.php?fecha=" . $fecha);
exit;
