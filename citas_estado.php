<?php
require "db.php";

$id = $_GET['id'];
$fecha = $_GET['fecha'];

$pdo->prepare("UPDATE citas SET estado = 'cancelado' WHERE id = ?")->execute([$id]);

header("Location: citas.php?fecha=".$fecha);
exit;
