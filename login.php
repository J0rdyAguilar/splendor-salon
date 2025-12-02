<?php
session_start();
if (isset($_SESSION['usuario'])) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Ingreso - Splendor</title>

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

<body class="bg-black flex items-center justify-center min-h-screen">

<!-- CONTENEDOR DEL LOGIN -->
<div class="bg-neutral-900 p-10 rounded-3xl shadow-2xl w-full max-w-md border border-neutral-800 text-center
            backdrop-blur-lg">

    <!-- TÍTULO SPLENDOR -->
    <h1 class="text-gold text-3xl font-bold mb-4 tracking-wide">
        SPLENDOR
    </h1>

    <!-- LOGO -->
    <div class="flex justify-center mb-8">
        <img src="assets/logo.png"
             alt="Logo Splendor"
             class="opacity-90 drop-shadow-lg"
             style="width: 230px;">
    </div>

    <!-- ERROR -->
    <?php if (isset($_GET['error'])): ?>
        <p class="text-red-400 mb-4 font-semibold">Usuario o contraseña incorrectos</p>
    <?php endif; ?>

    <!-- FORMULARIO -->
    <form action="validar_login.php" method="post" class="space-y-6 text-left">

        <!-- USUARIO -->
        <div>
            <label class="block mb-1 text-gray-300 font-medium">Usuario</label>
            <input type="text" name="username"
                class="w-full p-3 rounded-xl bg-black border border-neutral-700 text-gray-200 
                       focus:border-gold focus:ring-2 focus:ring-gold outline-none transition">
        </div>

        <!-- CONTRASEÑA -->
        <div>
            <label class="block mb-1 text-gray-300 font-medium">Contraseña</label>
            <input type="password" name="password"
                class="w-full p-3 rounded-xl bg-black border border-neutral-700 text-gray-200 
                       focus:border-gold focus:ring-2 focus:ring-gold outline-none transition">
        </div>

        <!-- BOTÓN -->
        <button 
            class="w-full bg-gold text-black font-semibold py-3 rounded-xl mt-2
                   hover:bg-gold-dark transition shadow-lg text-lg">
            Ingresar
        </button>

    </form>

</div>

</body>
</html>
