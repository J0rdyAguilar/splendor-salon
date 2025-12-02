<?php
$titulo = "Contabilidad";
include "layout.php";

// Solo ADMIN puede entrar
if ($_SESSION['rol'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

// Fechas del filtro
$desde = $_GET['desde'] ?? date('Y-m-01');
$hasta = $_GET['hasta'] ?? date('Y-m-d');


// ===============================
// MOVIMIENTOS (MANUALES + CITAS PAGADAS)
// ===============================
//
// Regla:
//  - Si el movimiento tiene cita_id -> solo se muestra si la cita está PAGADA
//  - Si cita_id es NULL -> es movimiento manual, se muestra siempre
//
$stmt = $pdo->prepare("
    SELECT m.*
    FROM movimientos m
    LEFT JOIN citas c ON c.id = m.cita_id
    WHERE m.fecha BETWEEN ? AND ?
      AND (
            m.cita_id IS NULL        -- movimientos manuales
            OR c.estado = 'pagado'   -- solo citas pagadas
      )
    ORDER BY m.fecha DESC
");
$stmt->execute([$desde, $hasta]);
$movs = $stmt->fetchAll(PDO::FETCH_ASSOC);


// ===============================
// TOTALES (MISMA LÓGICA)
// ===============================
$stmt = $pdo->prepare("
    SELECT 
        SUM(CASE WHEN m.tipo='ingreso' THEN m.monto ELSE 0 END) AS ingresos,
        SUM(CASE WHEN m.tipo='gasto' THEN m.monto ELSE 0 END) AS gastos
    FROM movimientos m
    LEFT JOIN citas c ON c.id = m.cita_id
    WHERE m.fecha BETWEEN ? AND ?
      AND (
            m.cita_id IS NULL
            OR c.estado = 'pagado'
      )
");
$stmt->execute([$desde, $hasta]);
$tot = $stmt->fetch(PDO::FETCH_ASSOC);

?>

<h2 class="text-gold text-3xl font-semibold mb-10">Contabilidad</h2>


<!-- ===================================== -->
<!-- FILTRO DE FECHAS (NO TOCADO) -->
<!-- ===================================== -->

<div class="bg-neutral-900 border border-neutral-700 p-8 rounded-2xl shadow-xl mb-10">

    <h3 class="text-gold text-xl font-semibold mb-6">Filtrar por fechas</h3>

    <form method="get" class="grid grid-cols-1 md:grid-cols-3 gap-8">

        <div>
            <label class="block text-gray-300 mb-1">Desde:</label>
            <input 
                type="text"
                id="desde"
                name="desde"
                value="<?= $desde ?>"
                readonly
                onclick="abrir('desde')"
                class="w-full bg-black border border-neutral-700 p-4 rounded-xl text-gray-200 cursor-pointer
                       focus:ring-2 focus:ring-gold outline-none"
            >
        </div>

        <div>
            <label class="block text-gray-300 mb-1">Hasta:</label>
            <input 
                type="text"
                id="hasta"
                name="hasta"
                value="<?= $hasta ?>"
                readonly
                onclick="abrir('hasta')"
                class="w-full bg-black border border-neutral-700 p-4 rounded-xl text-gray-200 cursor-pointer
                       focus:ring-2 focus:ring-gold outline-none"
            >
        </div>

        <div class="flex items-end">
            <button 
                class="bg-gold text-black px-6 py-3 rounded-xl font-semibold hover:bg-gold-dark transition shadow-md w-full">
                Filtrar
            </button>
        </div>

    </form>

    <div id="popup-calendario"
         class="hidden mt-6 bg-neutral-900 border border-neutral-700 p-6 rounded-xl shadow-xl">

        <div class="flex flex-wrap gap-3 mb-4">
            <button type="button" data-range="hoy"
                class="px-4 py-2 bg-neutral-700 hover:bg-gold hover:text-black rounded transition">Hoy</button>
            <button type="button" data-range="ayer"
                class="px-4 py-2 bg-neutral-700 hover:bg-gold hover:text-black rounded transition">Ayer</button>
            <button type="button" data-range="7"
                class="px-4 py-2 bg-neutral-700 hover:bg-gold hover:text-black rounded transition">7 días</button>
            <button type="button" data-range="mes"
                class="px-4 py-2 bg-neutral-700 hover:bg-gold hover:text-black rounded transition">Este mes</button>
            <button type="button" data-range="mespasado"
                class="px-4 py-2 bg-neutral-700 hover:bg-gold hover:text-black rounded transition">Mes pasado</button>
        </div>

        <div class="flex items-center justify-between mb-4">
            <button type="button" id="prevMes"
                class="px-3 py-1 bg-neutral-700 hover:bg-gold hover:text-black rounded transition">←</button>

            <div id="titulo-calendario" class="text-gold text-lg font-semibold"></div>

            <button type="button" id="sigMes"
                class="px-3 py-1 bg-neutral-700 hover:bg-gold hover:text-black rounded transition">→</button>
        </div>

        <div id="calendario" class="grid grid-cols-7 gap-2 text-center"></div>
    </div>

</div>




<!-- ===================================== -->
<!-- NUEVO MOVIMIENTO (NO TOCADO) -->
<!-- ===================================== -->

<div class="bg-neutral-900 border border-neutral-700 p-8 rounded-2xl shadow-xl mb-10">

    <h3 class="text-gold text-xl font-semibold mb-6">Nuevo movimiento</h3>

    <form action="contabilidad_guardar.php" method="post" class="grid grid-cols-1 md:grid-cols-2 gap-8">

        <div>
            <label class="block text-gray-300 mb-1">Tipo</label>
            <select name="tipo"
                class="w-full bg-black border border-neutral-700 p-4 rounded-xl text-gray-200
                       focus:ring-gold outline-none">
                <option value="ingreso">Ingreso</option>
                <option value="gasto">Gasto</option>
            </select>
        </div>

        <div>
            <label class="block text-gray-300 mb-1">Fecha</label>
            <input type="date" name="fecha" value="<?= date('Y-m-d') ?>"
                class="w-full bg-black border border-neutral-700 p-4 rounded-xl text-gray-200
                       focus:ring-gold outline-none">
        </div>

        <div class="md:col-span-2">
            <label class="block text-gray-300 mb-1">Descripción</label>
            <input name="descripcion" required
                class="w-full bg-black border border-neutral-700 p-4 rounded-xl text-gray-200
                       focus:ring-gold outline-none">
        </div>

        <div class="md:col-span-2">
            <label class="block text-gray-300 mb-1">Monto (Q)</label>
            <input type="number" step="0.01" name="monto" required
                class="w-full bg-black border border-neutral-700 p-4 rounded-xl text-gray-200
                       focus:ring-gold outline-none">
        </div>

        <div class="md:col-span-2">
            <button class="bg-gold text-black px-6 py-3 rounded-xl font-semibold hover:bg-gold-dark transition shadow-md">
                Guardar movimiento
            </button>
        </div>

    </form>
</div>




<!-- ===================================== -->
<!-- RESUMEN -->
<!-- ===================================== -->

<div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">

    <div class="bg-neutral-900 border border-neutral-700 p-6 rounded-2xl shadow text-center">
        <h3 class="text-gold text-lg font-semibold">Ingresos</h3>
        <p class="text-3xl mt-2">Q <?= number_format($tot['ingresos'], 2) ?></p>
    </div>

    <div class="bg-neutral-900 border border-neutral-700 p-6 rounded-2xl shadow text-center">
        <h3 class="text-gold text-lg font-semibold">Gastos</h3>
        <p class="text-3xl mt-2">Q <?= number_format($tot['gastos'], 2) ?></p>
    </div>

    <div class="bg-neutral-900 border border-neutral-700 p-6 rounded-2xl shadow text-center">
        <h3 class="text-gold text-lg font-semibold">Balance</h3>
        <p class="text-3xl mt-2">
            Q <?= number_format($tot['ingresos'] - $tot['gastos'], 2) ?>
        </p>
    </div>

</div>




<!-- ===================================== -->
<!-- LISTADO DE MOVIMIENTOS -->
<!-- ===================================== -->

<div class="bg-neutral-900 border border-neutral-700 p-8 rounded-2xl shadow-xl">

    <h3 class="text-gold text-xl font-semibold mb-6">Movimientos</h3>

    <div class="overflow-x-auto rounded-xl border border-neutral-800">

        <table class="w-full border-collapse">

            <thead class="bg-gold text-black">
                <tr>
                    <th class="p-4 text-left">Fecha</th>
                    <th class="p-4 text-left">Tipo</th>
                    <th class="p-4 text-left">Descripción</th>
                    <th class="p-4 text-left">Monto</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($movs as $m): ?>
                <tr class="border-b border-neutral-800 hover:bg-neutral-800/70 transition">

                    <td class="p-4"><?= $m['fecha'] ?></td>
                    <td class="p-4"><?= ucfirst($m['tipo']) ?></td>
                    <td class="p-4"><?= $m['descripcion'] ?></td>
                    <td class="p-4">Q <?= number_format($m['monto'], 2) ?></td>

                </tr>
                <?php endforeach; ?>

            </tbody>

        </table>

    </div>

</div>

<?php include "layout_footer.php"; ?>
