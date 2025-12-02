<?php
$titulo = "Dashboard";
include "layout.php";

// Obtener datos del usuario
$usuario = $_SESSION['usuario'];
$rol = $_SESSION['rol'];

// Convertir rol a texto bonito
$rolTexto = $rol === "admin" ? "Administrador" : "Empleado";

// Nombre bonito (primera letra mayúscula)
$nombreMostrar = ucfirst($usuario);

// Fecha actual
$hoy = date('Y-m-d');

// Total clientes
$clientesTotal = $pdo->query("SELECT COUNT(*) FROM clientes")->fetchColumn();

// Citas de hoy
$stmtCitas = $pdo->prepare("SELECT COUNT(*) FROM citas WHERE fecha = ?");
$stmtCitas->execute([$hoy]);
$citasHoy = $stmtCitas->fetchColumn();

// Ingresos de hoy
$stmtIng = $pdo->prepare("SELECT IFNULL(SUM(monto),0) FROM movimientos WHERE tipo='ingreso' AND fecha = ?");
$stmtIng->execute([$hoy]);
$ingresosHoy = $stmtIng->fetchColumn();
?>

<!-- TARJETA DE BIENVENIDA -->
<div class="bg-neutral-800 border border-neutral-700 p-8 rounded-xl mb-10 text-center shadow-xl">
    <h2 class="text-gold text-3xl font-semibold tracking-wide mb-3">
        Bienvenido, <?php echo $nombreMostrar; ?>

</div>

<!-- TARJETAS DE RESUMEN -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">

    <!-- Clientes -->
    <div class="bg-neutral-800 border border-neutral-700 p-6 rounded-xl text-center shadow">
        <h3 class="text-gold text-xl font-semibold mb-2">Clientes registrados</h3>
        <p class="text-white text-3xl font-bold"><?php echo $clientesTotal; ?></p>
    </div>

    <!-- Citas -->
    <div class="bg-neutral-800 border border-neutral-700 p-6 rounded-xl text-center shadow">
        <h3 class="text-gold text-xl font-semibold mb-2">Citas para hoy</h3>
        <p class="text-white text-3xl font-bold"><?php echo $citasHoy; ?></p>
        <span class="text-gray-400 text-sm"><?php echo $hoy; ?></span>
    </div>

    <!-- Ingresos -->
    <?php if ($_SESSION['rol'] === 'admin'): ?>
    <div class="bg-neutral-800 border border-neutral-700 p-6 rounded-xl text-center shadow">
        <h3 class="text-gold text-xl font-semibold mb-2">Ingresos de hoy</h3>
        <p class="text-white text-3xl font-bold">Q <?php echo number_format($ingresosHoy, 2); ?></p>
    </div>
    <?php else: ?>
    <!-- Si es empleado, no mostrar contabilidad -->
    <div class="bg-neutral-800 border border-neutral-700 p-6 rounded-xl text-center shadow opacity-50">
        <h3 class="text-gold text-xl font-semibold mb-2">Ingresos de hoy</h3>
        <p class="text-gray-400 text-lg">No permitido</p>
    </div>
    <?php endif; ?>

</div>

<?php include "layout_footer.php"; ?>
