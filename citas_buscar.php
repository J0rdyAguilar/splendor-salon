<?php
$titulo = "Buscar Citas";
include "layout.php";

$cliente = null;
$citas = [];
$busqueda = $_GET['q'] ?? '';

if ($busqueda) {
    // Buscar cliente exacto
    $stmt = $pdo->prepare("SELECT * FROM clientes WHERE nombre LIKE ?");
    $stmt->execute(['%' . $busqueda . '%']);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cliente) {
        // Obtener citas del cliente
        $stmt = $pdo->prepare("
            SELECT *
            FROM citas
            WHERE cliente_id = ?
            ORDER BY fecha DESC, hora DESC
        ");
        $stmt->execute([$cliente['id']]);
        $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<h2 class="text-gold text-3xl font-semibold mb-10">Buscar Citas</h2>

<!-- Buscar -->
<div class="bg-neutral-900 border border-neutral-700 p-8 rounded-2xl shadow-xl mb-10">
    <form class="flex gap-4" method="get">
        <input 
            type="text"
            name="q"
            placeholder="Buscar cliente por nombre..."
            value="<?= htmlspecialchars($busqueda) ?>"
            class="flex-1 bg-black border border-neutral-700 p-4 rounded-xl text-gray-200 focus:border-gold outline-none"
        >

        <button class="bg-gold text-black px-6 rounded-xl font-semibold hover:bg-gold-dark transition shadow-md">
            Buscar
        </button>
    </form>
</div>


<?php if ($busqueda && !$cliente): ?>
    <p class="text-red-400 text-xl">No se encontró ningún cliente con ese nombre.</p>
<?php endif; ?>


<?php if ($cliente): ?>

<!-- Perfil -->
<div class="bg-neutral-900 border border-neutral-700 p-8 rounded-2xl shadow-xl mb-10">

    <h3 class="text-gold text-2xl font-semibold mb-4">
        Cliente: <?= htmlspecialchars($cliente['nombre']) ?>
    </h3>

    <p class="text-gray-300">Tel: <?= $cliente['telefono'] ?: 'N/A' ?></p>
    <p class="text-gray-300 mb-4">Notas: <?= nl2br(htmlspecialchars($cliente['notas'])) ?></p>

</div>


<!-- HISTORIAL COMPLETO DE CITAS -->
<div class="bg-neutral-900 border border-neutral-700 p-8 rounded-2xl shadow-xl">

    <h3 class="text-gold text-xl font-semibold mb-6">Historial de citas</h3>

    <div class="overflow-x-auto rounded-xl border border-neutral-800">

        <table class="w-full border-collapse">

            <thead class="bg-gold text-black">
                <tr>
                    <th class="p-4 text-left">Fecha</th>
                    <th class="p-4 text-left">Hora</th>
                    <th class="p-4 text-left">Monto</th>
                    <th class="p-4 text-left">Estado</th>
                    <th class="p-4 text-left">Notas</th>
                    <th class="p-4 text-center">Acciones</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($citas as $ci): ?>
                <tr class="border-b border-neutral-800 hover:bg-neutral-800/70 transition">

                    <td class="p-4"><?= $ci['fecha'] ?></td>
                    <td class="p-4"><?= substr($ci['hora'], 0, 5) ?></td>
                    <td class="p-4"><?= $ci['monto'] ? 'Q '.number_format($ci['monto'],2) : '' ?></td>

                    <td class="p-4">
                        <?php if ($ci['estado'] == 'pendiente'): ?>
                            <span class="px-3 py-1 rounded bg-gray-600 text-white">Pendiente</span>

                        <?php elseif ($ci['estado'] == 'transferencia_pendiente'): ?>
                            <span class="px-3 py-1 rounded bg-blue-600 text-white">Transf. Pendiente</span>

                        <?php elseif ($ci['estado'] == 'pagado'): ?>
                            <span class="px-3 py-1 rounded bg-green-600 text-black">Pagado</span>

                        <?php else: ?>
                            <span class="px-3 py-1 rounded bg-red-600 text-white">Cancelado</span>
                        <?php endif; ?>
                    </td>

                    <td class="p-4"><?= nl2br(htmlspecialchars($ci['notas'])) ?></td>

                    <td class="p-4 text-center space-y-2">

                        <?php if ($ci['estado'] == 'pendiente'): ?>

                            <a href="citas_pagar.php?id=<?= $ci['id'] ?>&fecha=<?= $ci['fecha'] ?>"
                                class="block bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg shadow transition">
                                Marcar pagado
                            </a>

                            <a href="citas_transferencia.php?id=<?= $ci['id'] ?>&fecha=<?= $ci['fecha'] ?>"
                                class="block bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg shadow transition">
                                Transferencia pendiente
                            </a>

                            <a href="citas_estado.php?id=<?= $ci['id'] ?>&fecha=<?= $ci['fecha'] ?>"
                                class="block bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg shadow transition">
                                Cancelar
                            </a>

                        <?php elseif ($ci['estado'] == 'transferencia_pendiente'): ?>

                            <a href="citas_confirmar_pago.php?id=<?= $ci['id'] ?>&fecha=<?= $ci['fecha'] ?>"
                                class="block bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg shadow transition">
                                Confirmar pago
                            </a>

                        <?php endif; ?>

                        <!-- Ir al día -->
                        <a href="citas.php?fecha=<?= $ci['fecha'] ?>"
                            class="block bg-neutral-700 hover:bg-gold hover:text-black transition px-3 py-2 rounded-lg shadow">
                            Ver en calendario
                        </a>

                    </td>

                </tr>
                <?php endforeach; ?>

            </tbody>
        </table>

    </div>

</div>

<?php endif; ?>

<?php include "layout_footer.php"; ?>
