<?php
date_default_timezone_set('America/Guatemala');
require "db.php";
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Splendor - <?php echo $titulo ?? 'Panel'; ?></title>

<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = {
    theme: {
        extend: {
            colors: {
                gold: "#d4af37",
                "gold-dark": "#b8962c"
            }
        }
    }
}
</script>

</head>

<body class="bg-black text-gray-200">

<!-- ====================== -->
<!-- HEADER -->
<!-- ====================== -->
<header class="border-b border-gold/30 bg-black/90 sticky top-0 z-50 backdrop-blur-md shadow-lg">

    <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">

        <!-- LOGO + TÍTULO -->
        <div class="flex items-center gap-4">
            <img src="assets/logo.png"
                alt="Logo Splendor"
                class="mx-auto mb-6 opacity-90" style="max-width: 140px; width: 100%;">

            <h1 class="text-2xl font-bold tracking-widest text-gold">
                SPLENDOR
            </h1>
        </div>

        <!-- NAVEGACIÓN -->
        <nav class="flex items-center gap-6 text-gray-300 font-medium">

            <a href="dashboard.php" class="hover:text-gold transition">Inicio</a>

            <a href="clientes.php" class="hover:text-gold transition">Clientes</a>

            <a href="citas.php" class="hover:text-gold transition">Citas</a>

            <a href="citas_buscar.php" class="hover:text-gold transition">Buscar Citas</a>

            <!-- SOLO ADMIN VE CONTABILIDAD -->
            <?php if ($_SESSION['rol'] === 'admin'): ?>
                <a href="contabilidad.php" class="hover:text-gold transition">Contabilidad</a>
            <?php endif; ?>

            <a href="logout.php" class="hover:text-gold transition">Cerrar sesión</a>

        </nav>

    </div>

    <style>
    /* Restaurar datepicker para Chrome/Firefox */
    input[type="date"] {
        appearance: auto !important;
        -webkit-appearance: auto !important;
        -moz-appearance: auto !important;
        background-color: #000;
        color: #fff;
        padding-right: 2.5rem !important;
    }
    </style>

</header>

<!-- ====================== -->
<!-- CONTENIDO -->
<!-- ====================== -->
<div class="max-w-6xl mx-auto mt-10 bg-neutral-900 p-8 rounded-xl shadow-xl border border-neutral-800">
