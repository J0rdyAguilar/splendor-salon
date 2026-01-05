<?php
$id = $_GET['id'] ?? null;
$fecha = $_GET['fecha'] ?? date('Y-m-d');
$accion = $_GET['accion'] ?? 'pagar';

if(!$id){
  header("Location: buscar_citas.php");
  exit;
}

if($accion === 'transferencia'){
  header("Location: citas_transferencia.php?id=".$id."&fecha=".$fecha);
  exit;
}

// pagar (efectivo) por defecto
header("Location: citas_cobrar.php?id=".$id."&fecha=".$fecha);
exit;
