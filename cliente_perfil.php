<?php
$titulo = "Perfil del Cliente";
include "layout.php";

$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = ?");
$stmt->execute([$id]);
$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cliente) {
    echo "<p class='text-red-500'>Cliente no encontrado.</p>";
    include "layout_footer.php";
    exit;
}

// Citas del cliente
$stmt = $pdo->prepare("
    SELECT *
    FROM citas
    WHERE cliente_id = ?
    ORDER BY fecha DESC, hora DESC
");
$stmt->execute([$id]);
$citas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Total gastado
$stmt = $pdo->prepare("
    SELECT SUM(monto) 
    FROM citas 
    WHERE cliente_id = ? 
    AND estado = 'pagado'
");
$stmt->execute([$id]);
$total_gastado = $stmt->fetchColumn() ?? 0;
?>

<h2 class="text-gold text-3xl font-semibold mb-6">
    Perfil de <?= $cliente['nombre'] ?>
</h2>

<div class="grid grid-cols-1 md:grid-cols-2 gap-8">

    <!-- Datos del cliente -->
    <div class="bg-neutral-900 p-6 rounded-xl border border-neutral-700 shadow">

        <h3 class="text-gold text-xl mb-4">Información del cliente</h3>

        <p class="text-gray-300 text-lg"><strong>Nombre:</strong> <?= $cliente['nombre'] ?></p>
        <p class="text-gray-300 text-lg"><strong>Teléfono:</strong> <?= $cliente['telefono'] ?></p>
        <p class="text-gray-300 text-lg"><strong>Registrado:</strong> <?= $cliente['fecha_registro'] ?></p>

        <div class="mt-6 p-4 bg-neutral-800 rounded-lg border border-neutral-600">
            <p class="text-gold text-xl font-semibold">Total gastado:</p>
            <p class="text-3xl mt-2">Q <?= number_format($total_gastado,2) ?></p>
        </div>

    </div>


    <!-- Citas -->
    <div class="bg-neutral-900 p-6 rounded-xl border border-neutral-700 shadow">

        <h3 class="text-gold text-xl mb-4">Historial de citas</h3>

        <div class="max-h-[400px] overflow-y-auto pr-2">

            <?php if (count($citas) == 0): ?>
                <p class="text-gray-400">Este cliente no tiene citas registradas.</p>
            <?php endif; ?>

            <?php foreach ($citas as $c): ?>

                <div class="p-4 mb-3 bg-neutral-800 rounded-xl border border-neutral-700 hover:border-gold transition">

                    <p><strong class="text-gold"><?= $c['fecha'] ?> <?= substr($c['hora'],0,5) ?></strong></p>

                    <p class="text-gray-300">
                        <strong>Estado:</strong>
                        <?php if ($c['estado'] == 'pendiente'): ?>
                            <span class="px-2 py-1 bg-gray-600 text-white rounded">Pendiente</span>
                        <?php elseif ($c['estado'] == 'pagado'): ?>
                            <span class="px-2 py-1 bg-green-600 text-black rounded">Pagado</span>
                        <?php else: ?>
                            <span class="px-2 py-1 bg-red-600 text-white rounded">Cancelado</span>
                        <?php endif; ?>
                    </p>

                    <?php if ($c['monto']): ?>
                        <p class="text-gray-300"><strong>Monto:</strong> Q <?= number_format($c['monto'],2) ?></p>
                    <?php endif; ?>

                    <?php if ($c['notas']): ?>
                        <p class="text-gray-400 mt-1"><strong>Notas:</strong> <?= nl2br(htmlspecialchars($c['notas'])) ?></p>
                    <?php endif; ?>

                </div>

            <?php endforeach; ?>

        </div>

    </div>

</div>

<?php include "layout_footer.php"; ?>
