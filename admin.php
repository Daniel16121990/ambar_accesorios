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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Productos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gray-50">

    <nav class="bg-white shadow px-4 sm:px-6 py-4 flex flex-col sm:flex-row justify-between items-center gap-3">
        <span class="font-bold text-xl text-pink-700">Admin Panel 🎀</span>
        <div class="flex gap-3 sm:gap-4 text-sm sm:text-base">
            <a href="index.php" target="_blank" class="text-gray-500 hover:text-pink-600 flex gap-1 items-center"><i data-lucide="external-link" class="w-4 h-4"></i> Ver Tienda</a>
            <a href="logout.php" class="text-red-500 hover:text-red-700 font-medium">Cerrar Sesión</a>
        </div>
    </nav>

    <div class="max-w-5xl mx-auto p-4 sm:p-8">
        
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 sm:mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Tus Productos</h1>
            <a href="agregar.php" class="bg-pink-600 text-white px-4 sm:px-6 py-2 rounded-lg shadow hover:bg-pink-700 flex items-center gap-2 transition text-sm sm:text-base w-full sm:w-auto justify-center">
                <i data-lucide="plus-circle" class="w-5 h-5"></i> Nuevo Producto
            </a>
        </div>

        <!-- Vista de tabla para desktop -->
        <div class="hidden md:block bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
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
                            <div class="flex gap-2 justify-end">
                                <a href="editar.php?id=<?php echo $row['id']; ?>" class="text-blue-500 hover:text-blue-700 p-2" title="Editar">
                                    <i data-lucide="edit" class="w-5 h-5 inline"></i>
                                </a>
                                <a href="admin.php?borrar=<?php echo $row['id']; ?>" onclick="return confirm('¿Segura que quieres borrar este moño?')" class="text-red-400 hover:text-red-600 p-2" title="Eliminar">
                                    <i data-lucide="trash-2" class="w-5 h-5 inline"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Vista de tarjetas para mobile -->
        <div class="md:hidden space-y-4">
            <?php 
            // Reset el resultado para mobile
            $result->data_seek(0);
            while($row = $result->fetch_assoc()): 
            ?>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                <div class="flex gap-4">
                    <img src="<?php echo $row['imagen']; ?>" class="w-20 h-20 object-cover rounded-lg border border-gray-200 flex-shrink-0">
                    <div class="flex-1 min-w-0">
                        <h3 class="font-bold text-gray-800 text-base mb-1 truncate"><?php echo $row['nombre']; ?></h3>
                        <p class="text-pink-600 font-bold text-lg mb-3">$<?php echo $row['precio']; ?></p>
                        <div class="flex gap-2">
                            <a href="editar.php?id=<?php echo $row['id']; ?>" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center justify-center gap-2 transition">
                                <i data-lucide="edit" class="w-4 h-4"></i> Editar
                            </a>
                            <a href="admin.php?borrar=<?php echo $row['id']; ?>" onclick="return confirm('¿Segura que quieres borrar este moño?')" class="flex-1 bg-red-400 hover:bg-red-500 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center justify-center gap-2 transition">
                                <i data-lucide="trash-2" class="w-4 h-4"></i> Borrar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
    <script>lucide.createIcons();</script>
</body>
</html>