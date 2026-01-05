<?php
require "db.php";

$id    = $_GET['id'] ?? null;
$fecha = $_GET['fecha'] ?? date('Y-m-d');

if (!$id) {
    header("Location: citas.php?fecha=".$fecha);
    exit;
}

// Ahora cobrar siempre se hace en la pantalla de cobrar_cita.php
header("Location: cobrar_cita.php?id=".$id."&fecha=".$fecha."&accion=pagar");
exit;
