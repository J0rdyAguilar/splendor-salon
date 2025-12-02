<?php
require "db.php";

$id    = $_GET['id'] ?? 0;
$desde = $_GET['desde'] ?? date('Y-m-01');
$hasta = $_GET['hasta'] ?? date('Y-m-d');

$stmt = $pdo->prepare("DELETE FROM movimientos WHERE id = ?");
$stmt->execute([$id]);

header("Location: contabilidad.php?desde=".$desde."&hasta=".$hasta);
exit;
