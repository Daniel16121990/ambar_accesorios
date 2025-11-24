<?php
session_start();
require 'db.php';

// Seguridad: Si no es admin, pa' fuera
if (!isset($_SESSION['admin_logged'])) {
    header("Location: login.php");
    exit;
}

// Lógica para ELIMINAR producto
if (isset($_GET['borrar'])) {
    $id = (int)$_GET['borrar'];
    $conn->query("DELETE FROM productos WHERE id = $id");
    header("Location: admin.php"); // Recargar para ver cambios
}

// Obtener productos
$sql = "SELECT * FROM productos ORDER BY id DESC"; // Los más nuevos primero
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar Productos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gray-50">

    <nav class="bg-white shadow px-6 py-4 flex justify-between items-center">
        <span class="font-bold text-xl text-pink-700">Admin Panel 🎀</span>
        <div class="flex gap-4">
            <a href="index.php" target="_blank" class="text-gray-500 hover:text-pink-600 flex gap-1 items-center"><i data-lucide="external-link" class="w-4 h-4"></i> Ver Tienda</a>
            <a href="logout.php" class="text-red-500 hover:text-red-700 font-medium">Cerrar Sesión</a>
        </div>
    </nav>

    <div class="max-w-5xl mx-auto p-8">
        
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Tus Productos</h1>
            <a href="agregar.php" class="bg-pink-600 text-white px-6 py-2 rounded-lg shadow hover:bg-pink-700 flex items-center gap-2 transition">
                <i data-lucide="plus-circle" class="w-5 h-5"></i> Nuevo Producto
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="p-4">Imagen</th>
                        <th class="p-4">Nombre</th>
                        <th class="p-4">Precio</th>
                        <th class="p-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr class="hover:bg-pink-50/50 transition">
                        <td class="p-4">
                            <img src="<?php echo $row['imagen']; ?>" class="w-16 h-16 object-cover rounded-lg border border-gray-200">
                        </td>
                        <td class="p-4 font-medium text-gray-800"><?php echo $row['nombre']; ?></td>
                        <td class="p-4 text-pink-600 font-bold">$<?php echo $row['precio']; ?></td>
                        <td class="p-4 text-right">
                            <a href="admin.php?borrar=<?php echo $row['id']; ?>" onclick="return confirm('¿Segura que quieres borrar este moño?')" class="text-red-400 hover:text-red-600 p-2">
                                <i data-lucide="trash-2" class="w-5 h-5 inline"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script>lucide.createIcons();</script>
</body>
</html>