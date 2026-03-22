<?php
session_start();
require 'db.php';

// Seguridad: Si no es admin, pa' fuera
if (!isset($_SESSION['admin_logged'])) {
    header("Location: login.php");
    exit;
}

// Lógica para ACTUALIZAR estado manualmente (opcional, en caso de que falle MP)
if (isset($_POST['actualizar_estado']) && isset($_POST['pedido_id'])) {
    $p_id = (int)$_POST['pedido_id'];
    $nuevo_estado = $conn->real_escape_string($_POST['nuevo_estado']);
    $conn->query("UPDATE pedidos SET estado_pago = '$nuevo_estado' WHERE id = $p_id");
    header("Location: admin_pedidos.php");
    exit;
}

// Obtener pedidos con sus items
$sql = "SELECT * FROM pedidos ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Pedidos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gray-50">

    <nav class="bg-white shadow px-4 sm:px-6 py-4 flex flex-col sm:flex-row justify-between items-center gap-3">
        <span class="font-bold text-xl text-pink-700">Admin Panel 🎀</span>
        <div class="flex gap-3 sm:gap-4 text-sm sm:text-base items-center">
            <a href="admin.php" class="text-gray-500 hover:text-pink-600 flex gap-1 items-center"><i data-lucide="box" class="w-4 h-4"></i> Productos</a>
            <a href="admin_pedidos.php" class="text-pink-600 font-bold flex gap-1 items-center"><i data-lucide="shopping-bag" class="w-4 h-4"></i> Mis Ventas</a>
            <span class="text-gray-300">|</span>
            <a href="index.php" target="_blank" class="text-gray-500 hover:text-pink-600 flex gap-1 items-center"><i data-lucide="external-link" class="w-4 h-4"></i> Ver Tienda</a>
            <a href="logout.php" class="text-red-500 hover:text-red-700 font-medium ml-2">Cerrar Sesión</a>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto p-4 sm:p-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 sm:mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Tus Ventas / Pedidos</h1>
        </div>

        <?php if ($result->num_rows == 0): ?>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center text-gray-500">
                Aún no tienes pedidos registrados.
            </div>
        <?php else: ?>
            <div class="space-y-6">
                <?php while($pedido = $result->fetch_assoc()): 
                    $dir = json_decode($pedido['direccion_json'], true);
                    
                    // Colores del estado
                    $badgeRes = 'bg-gray-100 text-gray-600';
                    if ($pedido['estado_pago'] === 'aprobado') $badgeRes = 'bg-green-100 text-green-700';
                    if ($pedido['estado_pago'] === 'rechazado') $badgeRes = 'bg-red-100 text-red-700';
                    if ($pedido['estado_pago'] === 'pendiente') $badgeRes = 'bg-yellow-100 text-yellow-700';
                ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <!-- Cabecera del pedido -->
                    <div class="bg-gray-50 border-b border-gray-200 p-4 flex flex-wrap justify-between items-center gap-4">
                        <div>
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wide">Pedido #<?php echo str_pad($pedido['id'], 5, '0', STR_PAD_LEFT); ?></span>
                            <div class="font-bold text-gray-800 text-lg"><?php echo date('d/m/Y H:i', strtotime($pedido['fecha'])); ?></div>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase <?php echo $badgeRes; ?>">
                                <?php echo $pedido['estado_pago']; ?>
                            </span>
                            <div class="text-right">
                                <span class="text-xs text-gray-500">Total Pagado</span>
                                <div class="font-bold text-xl text-pink-600">$<?php echo number_format($pedido['total'], 2); ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Detalles de Envio/Contacto -->
                        <div>
                            <h3 class="font-bold text-sm text-gray-800 border-b pb-2 mb-3">Detalles del Envío</h3>
                            <p class="text-sm text-gray-600 mb-1"><span class="font-semibold">Email:</span> <?php echo $pedido['user_email']; ?></p>
                            <p class="text-sm text-gray-600 mb-1"><span class="font-semibold">Modo:</span> <?php echo $pedido['envio_modo'] === 'mercado_envios' ? 'Mercado Envíos (ME2)' : 'Correo Argentino'; ?></p>
                            
                            <?php if ($pedido['envio_modo'] === 'correo_argentino' && $dir): ?>
                                <div class="mt-3 bg-pink-50 rounded-lg p-3 text-sm text-gray-700 border border-pink-100">
                                    <p><i data-lucide="user" class="inline w-4 h-4 mr-1 text-pink-500"></i> <?php echo htmlspecialchars($dir['envio_nombre']); ?></p>
                                    <p><i data-lucide="map-pin" class="inline w-4 h-4 mr-1 text-pink-500"></i> <?php echo htmlspecialchars($dir['envio_direccion']) . ($dir['envio_piso'] ? " - " . htmlspecialchars($dir['envio_piso']) : ""); ?></p>
                                    <p class="ml-5"><?php echo htmlspecialchars($dir['envio_ciudad']) . ", " . htmlspecialchars($dir['envio_provincia']) . " (" . htmlspecialchars($dir['envio_cp']) . ")"; ?></p>
                                </div>
                            <?php elseif ($pedido['envio_modo'] === 'mercado_envios'): ?>
                                <div class="mt-3 text-sm text-gray-500 italic">
                                    La etiqueta de envío se generará automáticamente en Mercado Pago.
                                </div>
                            <?php endif; ?>
                            
                            <!-- Mini Formulario por si MP no actualiza -->
                            <form method="POST" class="mt-4 flex items-center gap-2">
                                <input type="hidden" name="pedido_id" value="<?php echo $pedido['id']; ?>">
                                <select name="nuevo_estado" class="text-xs border rounded p-1 outline-none">
                                    <option value="pendiente" <?php if($pedido['estado_pago']=='pendiente') echo 'selected'; ?>>Pendiente</option>
                                    <option value="aprobado" <?php if($pedido['estado_pago']=='aprobado') echo 'selected'; ?>>Aprobado</option>
                                    <option value="rechazado" <?php if($pedido['estado_pago']=='rechazado') echo 'selected'; ?>>Rechazado</option>
                                </select>
                                <button type="submit" name="actualizar_estado" class="text-xs bg-gray-200 hover:bg-gray-300 px-2 py-1 rounded">Forzar Estado</button>
                            </form>
                        </div>
                        
                        <!-- Lista de Items -->
                        <div>
                            <h3 class="font-bold text-sm text-gray-800 border-b pb-2 mb-3">Productos Comprados</h3>
                            <ul class="space-y-3">
                                <?php
                                $items_sql = "SELECT pi.cantidad, pi.precio_unitario, pr.nombre, pr.imagen FROM pedidos_items pi JOIN productos pr ON pi.producto_id = pr.id WHERE pi.pedido_id = " . $pedido['id'];
                                $items_res = $conn->query($items_sql);
                                while ($item = $items_res->fetch_assoc()):
                                ?>
                                <li class="flex items-center gap-3">
                                    <img src="<?php echo $item['imagen']; ?>" class="w-10 h-10 object-cover rounded bg-gray-100">
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-gray-800 leading-tight"><?php echo $item['nombre']; ?></p>
                                        <p class="text-xs text-gray-500"><?php echo $item['cantidad']; ?>x $<?php echo $item['precio_unitario']; ?></p>
                                    </div>
                                    <div class="text-sm font-bold text-gray-700">
                                        $<?php echo number_format($item['cantidad'] * $item['precio_unitario'], 2); ?>
                                    </div>
                                </li>
                                <?php endwhile; ?>
                                
                                <!-- Fila de envío -->
                                <?php if ($pedido['envio_costo'] > 0): ?>
                                <li class="flex items-center gap-3 pt-2 border-t border-gray-100">
                                    <div class="w-10 h-10 flex items-center justify-center rounded bg-gray-50 text-gray-400">
                                        <i data-lucide="truck" class="w-5 h-5"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-600 leading-tight">Costo de Envío</p>
                                    </div>
                                    <div class="text-sm font-bold text-gray-700">
                                        $<?php echo number_format($pedido['envio_costo'], 2); ?>
                                    </div>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <script>lucide.createIcons();</script>
</body>
</html>
