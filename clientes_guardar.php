<?php
require "db.php";

$nombre = $_POST['nombre'] ?? '';
$telefono = $_POST['telefono'] ?? '';
$email = $_POST['email'] ?? '';
$notas = $_POST['notas'] ?? '';

$stmt = $pdo->prepare("INSERT INTO clientes (nombre, telefono, email, notas) VALUES (?, ?, ?, ?)");
$stmt->execute([$nombre, $telefono, $email, $notas]);

header("Location: clientes.php");
exit;
