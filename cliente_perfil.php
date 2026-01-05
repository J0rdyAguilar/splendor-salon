<?php
$titulo = "Perfil del Cliente";
include "layout.php";

$rol = $_SESSION['rol'];
$id = $_GET['id'] ?? 0;

// Cliente
$stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = ?");
$stmt->execute([$id]);
$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cliente) {
    echo "<p class='text-red-500'>Cliente no encontrado.</p>";
    include "layout_footer.php";
    exit;
}

// Citas
$stmt = $pdo->prepare("
    SELECT *
    FROM citas
    WHERE cliente_id = ?
    ORDER BY fecha DESC, hora DESC
");
$stmt->execute([$id]);
$citas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Total gastado (SOLO ADMIN)
$total_gastado = 0;
if ($rol === 'admin') {
    $stmt = $pdo->prepare("
        SELECT SUM(monto)
        FROM citas
        WHERE cliente_id = ?
        AND estado = 'pagado'
    ");
    $stmt->execute([$id]);
    $total_gastado = $stmt->fetchColumn() ?? 0;
}
?>

<h2 class="text-gold text-3xl font-semibold mb-6">
    Perfil de <?= htmlspecialchars($cliente['nombre']) ?>
</h2>

<div class="grid grid-cols-1 md:grid-cols-2 gap-8">

    <!-- DATOS CLIENTE -->
    <div class="bg-neutral-900 p-6 rounded-xl border border-neutral-700">

        <h3 class="text-gold text-xl mb-4">Información del cliente</h3>

        <p class="text-gray-300"><strong>Nombre:</strong> <?= $cliente['nombre'] ?></p>

        <?php if ($rol === 'admin'): ?>
            <p class="text-gray-300"><strong>Teléfono:</strong> <?= $cliente['telefono'] ?></p>
            <p class="text-gray-300"><strong>Registrado:</strong> <?= $cliente['fecha_registro'] ?></p>
        <?php endif; ?>

        <?php if ($rol === 'admin'): ?>
            <div class="mt-6 p-4 bg-neutral-800 rounded-lg">
                <p class="text-gold text-xl font-semibold">Total gastado</p>
                <p class="text-3xl mt-2">Q <?= number_format($total_gastado, 2) ?></p>
            </div>
        <?php endif; ?>

    </div>

    <!-- HISTORIAL -->
    <div class="bg-neutral-900 p-6 rounded-xl border border-neutral-700">

        <h3 class="text-gold text-xl mb-4">Historial de citas</h3>

        <?php foreach ($citas as $c): ?>
            <div class="p-4 mb-3 bg-neutral-800 rounded-lg">

                <p class="text-gold font-semibold">
                    <?= $c['fecha'] ?> <?= substr($c['hora'], 0, 5) ?>
                </p>

                <p class="text-gray-300">
                    <strong>Estado:</strong>
                    <?= ucfirst($c['estado']) ?>
                </p>

                <?php if ($rol === 'admin' && $c['monto']): ?>
                    <p class="text-gray-300">
                        <strong>Monto:</strong> Q <?= number_format($c['monto'], 2) ?>
                    </p>
                <?php endif; ?>

                <?php if ($c['notas']): ?>
                    <p class="text-gray-400">
                        <strong>Notas:</strong> <?= nl2br(htmlspecialchars($c['notas'])) ?>
                    </p>
                <?php endif; ?>

            </div>
        <?php endforeach; ?>

    </div>

</div>

<?php include "layout_footer.php"; ?>
