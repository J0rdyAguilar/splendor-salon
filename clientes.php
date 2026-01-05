<?php
$titulo = "Clientes";
include "layout.php";

// ===============================
// BUSCADOR
// ===============================
$busqueda = $_GET['q'] ?? '';

// Si hay búsqueda → filtrar
if ($busqueda) {
    $stmt = $pdo->prepare("
        SELECT *
        FROM clientes
        WHERE nombre LIKE ? 
           OR telefono LIKE ?
        ORDER BY id DESC
    ");
    $stmt->execute([
        '%' . $busqueda . '%',
        '%' . $busqueda . '%'
    ]);

    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

} else {
    // Sin búsqueda → mostrar todos
    $clientes = $pdo->query("SELECT * FROM clientes ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
}
?>

<h2 class="text-gold text-3xl font-semibold mb-8">Clientas</h2>

<?php if (isset($_GET['error']) && $_GET['error'] === 'tiene_citas'): ?>
    <div class="bg-red-600 text-white p-4 rounded-xl mb-6 shadow">
        ❌ No se puede eliminar el cliente porque tiene citas registradas.
    </div>
<?php endif; ?>

<?php if (isset($_GET['ok']) && $_GET['ok'] === 'eliminado'): ?>
    <div class="bg-green-600 text-black p-4 rounded-xl mb-6 shadow">
        ✅ Cliente eliminado correctamente.
    </div>
<?php endif; ?>


<!-- =============================== -->
<!-- SECCIÓN: NUEVO CLIENTE -->
<!-- =============================== -->

<div class="bg-neutral-900 border border-neutral-700 p-8 rounded-2xl shadow-xl mb-10">

    <h3 class="text-gold text-xl font-semibold mb-6">Nueva clienta</h3>

    <form action="clientes_guardar.php" method="post" class="grid grid-cols-1 md:grid-cols-2 gap-8">

        <!-- Nombre -->
        <div class="md:col-span-2">
            <label class="block text-gray-300 mb-1">Nombre y Apellido *</label>
            <input 
                name="nombre" 
                required
                placeholder="Ejemplo: María López"
                class="w-full bg-black border border-neutral-700 p-4 rounded-xl text-gray-200 focus:border-gold focus:ring-2 focus:ring-gold outline-none"
            />
        </div>

        <!-- Teléfono -->
        <div>
            <label class="block text-gray-300 mb-1">Teléfono</label>
            <input 
                name="telefono" 
                placeholder="Ej: 5555-1234"
                class="w-full bg-black border border-neutral-700 p-4 rounded-xl text-gray-200 focus:border-gold focus:ring-2 focus:ring-gold outline-none"
            />
        </div>

        <!-- Notas -->
        <div class="md:col-span-2">
            <label class="block text-gray-300 mb-1">Notas</label>
            <textarea 
                name="notas" 
                rows="4"
                placeholder="Información importante del cliente"
                class="w-full bg-black border border-neutral-700 p-4 rounded-xl text-gray-200 focus:border-gold focus:ring-2 focus:ring-gold outline-none"
            ></textarea>
        </div>

        <!-- Botón -->
        <div class="md:col-span-2 flex">
            <button 
                class="bg-gold text-black px-6 py-3 rounded-xl font-semibold hover:bg-gold-dark transition shadow-md w-full md:w-auto"
            >
                Guardar clienta
            </button>
        </div>

    </form>
</div>

<!-- =============================== -->
<!-- SECCIÓN: BUSCADOR -->
<!-- =============================== -->

<div class="bg-neutral-900 border border-neutral-700 p-6 rounded-2xl shadow-xl mb-8">
    <form method="get" class="flex gap-4">
        <input 
            type="text" 
            name="q"
            placeholder="Buscar por nombre o teléfono..."
            value="<?= htmlspecialchars($busqueda) ?>"
            class="flex-1 bg-black border border-neutral-700 p-4 rounded-xl text-gray-200 focus:border-gold outline-none"
        >
        <button class="bg-gold text-black px-6 rounded-xl font-semibold hover:bg-gold-dark transition shadow">
            Buscar
        </button>
    </form>

    <?php if ($busqueda && count($clientes) === 0): ?>
        <p class="text-red-400 mt-4 text-lg">No se encontraron resultados.</p>
    <?php endif; ?>
</div>

<!-- =============================== -->
<!-- SECCIÓN: LISTADO -->
<!-- =============================== -->

<div class="bg-neutral-900 border border-neutral-700 p-8 rounded-2xl shadow-xl">

    <h3 class="text-gold text-xl font-semibold mb-6">Listado de clientas</h3>

    <div class="overflow-x-auto rounded-xl border border-neutral-800">
        <table class="w-full border-collapse">
            <thead class="bg-gold text-black">
                <tr>
                    <th class="p-4 text-left">Nombre</th>
                    <th class="p-4 text-left">Teléfono</th>
                    <th class="p-4 text-left">Notas</th>
                    <th class="p-4 text-center">Acciones</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($clientes as $c): ?>
                <tr class="border-b border-neutral-800 hover:bg-neutral-800/70 transition">

                    <td class="p-4"><?= htmlspecialchars($c['nombre']); ?></td>
                    <td class="p-4"><?= htmlspecialchars($c['telefono']); ?></td>
                    <td class="p-4"><?= nl2br(htmlspecialchars($c['notas'])); ?></td>

                    <td class="p-4 text-center space-y-2">

                        <!-- PERFIL -->
                        <a href="cliente_perfil.php?id=<?= $c['id'] ?>"
                           class="block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition shadow">
                            Ver perfil
                        </a>

                        <!-- ELIMINAR -->
                        <a href="clientes_eliminar.php?id=<?= $c['id']; ?>"
                           class="block bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition shadow">
                            Eliminar
                        </a>

                    </td>

                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>

<?php include "layout_footer.php"; ?>
