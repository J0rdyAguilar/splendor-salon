<?php
require "db.php";

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("DELETE FROM clientes WHERE id = ?");
$stmt->execute([$id]);

header("Location: clientes.php");
exit;
