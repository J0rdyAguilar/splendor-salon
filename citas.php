<?php
$titulo = "Citas";
include "layout.php";

// ======================
// FECHA SELECCIONADA
// ======================
$fecha = $_GET['fecha'] ?? date('Y-m-d');

// ======================
// HORARIOS DISPONIBLES (1 HORA)
// ======================
$horas_disponibles = [
    "09:00","10:00","11:00","12:00",
    "13:00","14:00","15:00","16:00","17:00"
];

// ======================
// HORAS OCUPADAS (solo NO canceladas)
// ======================
$stmt = $pdo->prepare("
    SELECT hora
    FROM citas
    WHERE fecha = ?
    AND estado != 'cancelado'
");
$stmt->execute([$fecha]);
$ocupadas_raw = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Convertir 09:00:00 → 09:00
$ocupadas = array_map(fn($h) => substr($h, 0, 5), $ocupadas_raw);

// ======================
// LISTA DE CLIENTES
// ======================
$clientes = $pdo->query("SELECT id, nombre FROM clientes ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);

// ======================
// CITAS DEL DÍA
// ======================
$stmt = $pdo->prepare("
    SELECT c.*, cl.nombre AS cliente
    FROM citas c
    INNER JOIN clientes cl ON cl.id = c.cliente_id
    WHERE fecha = ?
    ORDER BY hora
");
$stmt->execute([$fecha]);
$citas = $stmt->fetchAll(PDO::FETCH_ASSOC);


// ======================
// CALENDARIO MENSUAL
// ======================
$month = $_GET['month'] ?? date('m');
$year  = $_GET['year'] ?? date('Y');

$firstDay = strtotime("$year-$month-01");
$daysInMonth = date('t', $firstDay);
$startWeekDay = date('N', $firstDay);

$stmt = $pdo->prepare("
    SELECT fecha, COUNT(*) as total
    FROM citas
    WHERE MONTH(fecha)=? AND YEAR(fecha)=?
    AND estado != 'cancelado'
    GROUP BY fecha
");
$stmt->execute([$month, $year]);
$citasMes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Indexar
$citasIndex = [];
foreach ($citasMes as $c) {
    $citasIndex[$c['fecha']] = $c['total'];
}

$prevMonth = date('m', strtotime("-1 month", $firstDay));
$prevYear  = date('Y', strtotime("-1 month", $firstDay));
$nextMonth = date('m', strtotime("+1 month", $firstDay));
$nextYear  = date('Y', strtotime("+1 month", $firstDay));

?>


<h2 class="text-gold text-3xl font-semibold mb-10">Citas</h2>



<!-- ====================== -->
<!-- NAVEGACIÓN DEL MES -->
<!-- ====================== -->
<div class="flex justify-between items-center bg-neutral-900 border border-neutral-700 p-6 rounded-xl shadow mb-8">

    <a href="citas.php?month=<?= $prevMonth ?>&year=<?= $prevYear ?>"
       class="px-4 py-2 rounded-lg bg-neutral-800 text-gold hover:bg-gold hover:text-black transition shadow">
        ← Mes anterior
    </a>

    <h3 class="text-gold text-2xl font-semibold">
        <?php
        $meses = [
            1 => "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
            "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
        ];
        echo $meses[intval($month)] . " " . $year;
        ?>
    </h3>

    <a href="citas.php?month=<?= $nextMonth ?>&year=<?= $nextYear ?>"
       class="px-4 py-2 rounded-lg bg-neutral-800 text-gold hover:bg-gold hover:text-black transition shadow">
        Mes siguiente →
    </a>

</div>




<!-- ====================== -->
<!-- CALENDARIO MENSUAL -->
<!-- ====================== -->
<div class="bg-neutral-900 border border-neutral-700 p-8 rounded-2xl shadow-xl mb-12">

    <div class="grid grid-cols-7 gap-3 text-center">

        <?php
        $dias = ['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'];
        foreach ($dias as $d):
            echo "<div class='text-gold font-semibold py-2'>$d</div>";
        endforeach;
        ?>

        <?php for ($i = 1; $i < $startWeekDay; $i++): ?>
            <div></div>
        <?php endfor; ?>

        <?php for ($d = 1; $d <= $daysInMonth; $d++): 

            $fechaActual = "$year-$month-" . str_pad($d, 2, '0', STR_PAD_LEFT);
            $ocupadasDia = $citasIndex[$fechaActual] ?? 0;
            $esHoy = ($fechaActual == date('Y-m-d'));

            if ($esHoy) {
                $bg = "bg-gold text-black font-bold";
            } elseif ($ocupadasDia > 0) {
                $bg = "bg-red-600 text-white";
            } else {
                $bg = "bg-neutral-800 text-gray-200";
            }
        ?>

        <a href="citas.php?fecha=<?= $fechaActual ?>&month=<?= $month ?>&year=<?= $year ?>"
           class="p-4 rounded-xl border border-neutral-700 hover:border-gold transition <?= $bg ?>">
            <div class="text-xl"><?= $d ?></div>
            <div class="text-xs opacity-75">
                <?= $ocupadasDia > 0 ? "$ocupadasDia citas" : "Libre" ?>
            </div>
        </a>

        <?php endfor; ?>

    </div>

</div>

<hr class="border-neutral-700 mb-10">



<!-- ====================== -->
<!-- NUEVA CITA -->
<!-- ====================== -->
<div class="bg-neutral-900 border border-neutral-700 p-8 rounded-2xl shadow-xl mb-12">

    <h3 class="text-gold text-xl font-semibold mb-6">
        Nueva cita para <span class="text-white"><?= $fecha ?></span>
    </h3>

    <form action="citas_guardar.php" method="post" class="grid grid-cols-1 md:grid-cols-2 gap-8">

        <input type="hidden" name="fecha" value="<?= $fecha ?>">

        <!-- HORA -->
        <div>
            <label class="text-gray-300 mb-1 block">Hora *</label>
            <select name="hora" required
                class="w-full bg-black border border-neutral-700 p-4 rounded-xl text-gray-200 focus:ring-gold outline-none">

                <option value="">Seleccione una hora</option>

                <?php foreach ($horas_disponibles as $h): ?>
                    <?php if (in_array($h, $ocupadas)): ?>
                        <option value="<?= $h ?>" disabled class="bg-red-800 text-red-300">
                            <?= $h ?> (Ocupado)
                        </option>
                    <?php else: ?>
                        <option value="<?= $h ?>" class="text-green-300">
                            <?= $h ?> Disponible
                        </option>
                    <?php endif; ?>
                <?php endforeach; ?>

            </select>
        </div>

        <!-- CLIENTE CON BUSCADOR -->
        <div class="flex flex-col">
            <label class="text-gray-300 mb-1 block">Cliente *</label>

            <div class="space-y-3">
                <!-- BUSCADOR -->
                <input 
                    type="text" id="buscarCliente"
                    placeholder="Buscar cliente por nombre..."
                    class="w-full bg-black border border-neutral-700 p-3 rounded-xl text-gold outline-none 
                           focus:border-gold focus:ring-2 focus:ring-gold"
                    onkeyup="filtrarClientes()"
                />

                <!-- SELECT -->
                <select name="cliente_id" id="selectCliente" required
                    class="w-full bg-black border border-neutral-700 p-3 rounded-xl text-gray-200 
                           focus:border-gold outline-none">
                    <option value="">Seleccione cliente</option>

                    <?php foreach ($clientes as $cli): ?>
                        <option value="<?= $cli['id'] ?>"><?= htmlspecialchars($cli['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>

            </div>
        </div>

        <script>
        function filtrarClientes() {
            let input = document.getElementById("buscarCliente").value.toLowerCase();
            let select = document.getElementById("selectCliente");
            let options = select.getElementsByTagName("option");

            for (let i = 1; i < options.length; i++) {
                let txt = options[i].textContent.toLowerCase();
                options[i].style.display = txt.includes(input) ? "" : "none";
            }
        }
        </script>

        <!-- MONTO -->
        <div>
            <label class="text-gray-300 mb-1 block">Monto (Q)</label>
            <input type="number" step="0.01" name="monto"
                class="w-full bg-black border border-neutral-700 p-4 rounded-xl text-gray-200 focus:ring-gold outline-none" />
        </div>

        <!-- NOTAS -->
        <div class="md:col-span-2">
            <label class="text-gray-300 mb-1 block">Notas</label>
            <textarea name="notas" rows="3"
                class="w-full bg-black border border-neutral-700 p-4 rounded-xl text-gray-200 focus:ring-gold outline-none"></textarea>
        </div>

        <div class="md:col-span-2">
            <button class="bg-gold text-black px-6 py-3 rounded-xl font-semibold hover:bg-gold-dark transition shadow-md">
                Guardar cita
            </button>
        </div>

    </form>

</div>





<!-- ====================== -->
<!-- LISTADO DE CITAS DEL DÍA -->
<!-- ====================== -->
<div class="bg-neutral-900 border border-neutral-700 p-8 rounded-2xl shadow-xl">

    <h3 class="text-gold text-xl font-semibold mb-6">Citas del día</h3>

    <div class="overflow-x-auto rounded-xl border border-neutral-800">

        <table class="w-full border-collapse">

            <thead class="bg-gold text-black">
                <tr>
                    <th class="p-4 text-left">Hora</th>
                    <th class="p-4 text-left">Cliente</th>
                    <th class="p-4 text-left">Monto</th>
                    <th class="p-4 text-left">Estado</th>
                    <th class="p-4 text-left">Notas</th>
                    <th class="p-4 text-center">Acciones</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($citas as $ci): ?>
                <tr class="border-b border-neutral-800 hover:bg-neutral-800/70 transition">

                    <td class="p-4"><?= substr($ci['hora'], 0, 5) ?></td>
                    <td class="p-4"><?= $ci['cliente'] ?></td>
                    <td class="p-4"><?= $ci['monto'] ? 'Q '.number_format($ci['monto'],2) : '' ?></td>

                    <td class="p-4">
                        <?php if ($ci['estado'] == 'pendiente'): ?>
                            <span class="px-3 py-1 rounded bg-gray-600 text-white">Pendiente</span>

                        <?php elseif ($ci['estado'] == 'transferencia_pendiente'): ?>
                            <span class="px-3 py-1 rounded bg-blue-600 text-white">Transf. pendiente</span>

                        <?php elseif ($ci['estado'] == 'pagado'): ?>
                            <span class="px-3 py-1 rounded bg-green-600 text-black">Pagado</span>

                        <?php else: ?>
                            <span class="px-3 py-1 rounded bg-red-600 text-white">Cancelado</span>
                        <?php endif; ?>
                    </td>

                    <td class="p-4"><?= nl2br(htmlspecialchars($ci['notas'])) ?></td>

                    <td class="p-4 text-center space-y-2">

                        <?php if ($ci['estado'] == 'pendiente'): ?>

                            <a href="citas_pagar.php?id=<?= $ci['id'] ?>&fecha=<?= $fecha ?>"
                               class="block bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow transition">
                                Marcar pagado
                            </a>

                            <a href="citas_transferencia.php?id=<?= $ci['id'] ?>&fecha=<?= $fecha ?>"
                               class="block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow transition">
                                Transferencia pendiente
                            </a>

                            <a href="citas_estado.php?id=<?= $ci['id'] ?>&fecha=<?= $fecha ?>"
                               class="block bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg shadow transition">
                                Cancelar
                            </a>

                        <?php elseif ($ci['estado'] == 'transferencia_pendiente'): ?>

                            <a href="citas_confirmar_pago.php?id=<?= $ci['id'] ?>&fecha=<?= $fecha ?>"
                               class="block bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow transition">
                                Confirmar pago
                            </a>

                        <?php endif; ?>

                    </td>

                </tr>
                <?php endforeach; ?>

            </tbody>

        </table>

    </div>

</div>



<?php include "layout_footer.php"; ?>
