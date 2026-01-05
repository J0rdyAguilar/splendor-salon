<?php
require "db.php";

$id = $_GET['id'] ?? null;
$fecha = $_GET['fecha'] ?? date('Y-m-d');

if (!$id) {
    header("Location: citas.php?fecha=" . $fecha);
    exit;
}

// Obtener cita
$stmt = $pdo->prepare("
    SELECT c.*, cl.nombre AS cliente
    FROM citas c
    INNER JOIN clientes cl ON cl.id = c.cliente_id
    WHERE c.id = ?
");
$stmt->execute([$id]);
$cita = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cita) {
    die("Cita no encontrada");
}

$total  = floatval($cita['monto'] ?? 0);          // total (monto final)
$pagado = floatval($cita['monto_pagado'] ?? 0);   // acumulado
$saldo  = max(0, $total - $pagado);
?>

<?php include "layout.php"; ?>

<h2 class="text-gold text-3xl font-semibold mb-8">Cobrar cita — <?= htmlspecialchars($cita['cliente']) ?></h2>

<div class="bg-neutral-900 border border-neutral-700 p-8 rounded-2xl shadow-xl max-w-lg">

    <p class="text-gray-300 mb-6">
        <strong>Fecha:</strong> <?= htmlspecialchars($cita['fecha']) ?><br>
        <strong>Hora:</strong> <?= substr($cita['hora'], 0, 5) ?>
    </p>

    <form action="citas_confirmar_pago.php" method="post" class="space-y-6">
        <input type="hidden" name="id" value="<?= (int)$cita['id'] ?>">
        <input type="hidden" name="fecha" value="<?= htmlspecialchars($fecha) ?>">

        <!-- Mantener input original -->
        <div>
            <label class="block text-gray-300 mb-1">Monto final (Q) *</label>
            <input
                id="monto_final"
                type="number"
                step="0.01"
                name="monto"
                required
                value="<?= $total > 0 ? htmlspecialchars($total) : '' ?>"
                class="w-full bg-black border border-neutral-700 p-4 rounded-xl text-gray-200 focus:border-gold outline-none"
                
            >
        </div>

        <!-- Checkbox -->
        <div class="flex items-center gap-3">
            <input
                id="chk_abonar"
                type="checkbox"
                name="es_abono"
                value="1"
                class="w-5 h-5 accent-yellow-500"
            >
            <label for="chk_abonar" class="text-gray-200 font-medium">
                ¿Abonar?
            </label>
        </div>

        <!-- Bloque abono (oculto por defecto) -->
        <div id="bloque_abono" class="hidden border border-neutral-700 rounded-2xl p-5 bg-black/20">
            <p class="text-gray-300 mb-4">
                <strong>Total:</strong> Q <span id="txt_total"><?= number_format($total, 2) ?></span><br>
                <strong>Abonado:</strong> Q <span id="txt_pagado"><?= number_format($pagado, 2) ?></span><br>
                <strong>Saldo:</strong> Q <span id="txt_saldo"><?= number_format($saldo, 2) ?></span>
            </p>

            <div>
                <label class="block text-gray-300 mb-1">¿Cuánto abonó? (Q) *</label>
                <input
                    id="abono"
                    type="number"
                    step="0.01"
                    name="abono"
                    class="w-full bg-black border border-neutral-700 p-4 rounded-xl text-gray-200 focus:border-gold outline-none"
                    
                >
                <p class="text-gray-500 text-sm mt-2">
                    * Este campo solo es obligatorio si marcaste “¿Abonar?”
                </p>
            </div>
        </div>

        <button class="bg-gold hover:opacity-90 text-black px-6 py-3 rounded-xl font-semibold transition shadow">
            Confirmar pago
        </button>

    </form>

</div>

<script>
(function(){
    const chk = document.getElementById('chk_abonar');
    const bloque = document.getElementById('bloque_abono');
    const abono = document.getElementById('abono');
    const montoFinal = document.getElementById('monto_final');

    const pagado = <?= json_encode($pagado) ?>;

    function recalcular(){
        const total = parseFloat(montoFinal.value || "0") || 0;
        const saldo = Math.max(0, total - pagado);

        document.getElementById('txt_total').textContent  = total.toFixed(2);
        document.getElementById('txt_pagado').textContent = pagado.toFixed(2);
        document.getElementById('txt_saldo').textContent  = saldo.toFixed(2);

        abono.max = saldo.toFixed(2);
    }

    function toggle(){
        const on = chk.checked;
        bloque.classList.toggle('hidden', !on);

        if(on){
            abono.required = true;
            recalcular();
        }else{
            abono.required = false;
            abono.value = "";
        }
    }

    chk.addEventListener('change', toggle);
    montoFinal.addEventListener('input', () => { if(chk.checked) recalcular(); });

    toggle();
})();
</script>

<?php include "layout_footer.php"; ?>