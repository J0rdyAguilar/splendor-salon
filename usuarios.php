<?php
$titulo = "Usuarios";
include "layout.php";

// ==============================
// SOLO ADMIN
// ==============================
if ($_SESSION['rol'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

// ==============================
// OBTENER USUARIOS
// ==============================
$stmt = $pdo->query("
    SELECT id, username, rol
    FROM usuarios
    ORDER BY id DESC
");
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2 class="text-gold text-3xl font-semibold mb-8">Usuarios del sistema</h2>

<!-- ============================== -->
<!-- CREAR USUARIO -->
<!-- ============================== -->
<div class="bg-neutral-900 border border-neutral-700 p-8 rounded-2xl shadow-xl mb-10">

    <h3 class="text-gold text-xl font-semibold mb-6">Crear nuevo usuario</h3>

    <form action="usuarios_guardar.php" method="post" class="grid grid-cols-1 md:grid-cols-2 gap-8">

        <!-- Usuario -->
        <div>
            <label class="text-gray-300 mb-1 block">Usuario *</label>
            <input 
                name="username" 
                required
                class="w-full bg-black border border-neutral-700 p-4 rounded-xl text-gray-200 focus:ring-gold outline-none"
            >
        </div>

        <!-- Contraseña -->
        <div>
            <label class="text-gray-300 mb-1 block">Contraseña *</label>
            <input 
                type="password" 
                name="password" 
                required
                class="w-full bg-black border border-neutral-700 p-4 rounded-xl text-gray-200 focus:ring-gold outline-none"
            >
        </div>

        <!-- Rol -->
        <div>
            <label class="text-gray-300 mb-1 block">Rol *</label>
            <select 
                name="rol" 
                required
                class="w-full bg-black border border-neutral-700 p-4 rounded-xl text-gray-200 focus:ring-gold outline-none"
            >
                <option value="empleado">Empleado</option>
                <option value="admin">Administrador</option>
            </select>
        </div>

        <!-- Botón -->
        <div class="md:col-span-2">
            <button 
                class="bg-gold text-black px-6 py-3 rounded-xl font-semibold hover:bg-gold-dark transition shadow">
                Crear usuario
            </button>
        </div>

    </form>
</div>

<!-- ============================== -->
<!-- LISTADO DE USUARIOS -->
<!-- ============================== -->
<div class="bg-neutral-900 border border-neutral-700 p-8 rounded-2xl shadow-xl">

    <h3 class="text-gold text-xl font-semibold mb-6">Usuarios registrados</h3>

    <div class="overflow-x-auto rounded-xl border border-neutral-800">

        <table class="w-full border-collapse">

            <thead class="bg-gold text-black">
                <tr>
                    <th class="p-4 text-left">Usuario</th>
                    <th class="p-4 text-left">Rol</th>
                    <th class="p-4 text-center">Acciones</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($usuarios as $u): ?>
                <tr class="border-b border-neutral-800 hover:bg-neutral-800/70 transition">

                    <td class="p-4"><?= htmlspecialchars($u['username']) ?></td>
                    <td class="p-4 capitalize"><?= $u['rol'] ?></td>

                    <td class="p-4 text-center">
                        <?php if ($u['username'] !== $_SESSION['usuario']): ?>
                            <a 
                                href="usuarios_eliminar.php?id=<?= $u['id'] ?>"
                                onclick="return confirm('¿Seguro que deseas eliminar este usuario?')"
                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded shadow transition"
                            >
                                Eliminar
                            </a>
                        <?php else: ?>
                            <span class="text-gray-400 italic">Usuario actual</span>
                        <?php endif; ?>
                    </td>

                </tr>
                <?php endforeach; ?>
            </tbody>

        </table>

    </div>

</div>

<?php include "layout_footer.php"; ?>
