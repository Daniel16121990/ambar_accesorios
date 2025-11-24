<?php
session_start();
require 'db.php';

$productos_en_carrito = [];
$total_pagar = 0;
$cantidad_total_items = 0; // Para el contador del icono

// Verificar si hay algo en el carrito
if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
    
    // Obtener solo los IDs (las llaves del array)
    $ids = array_keys($_SESSION['carrito']);
    $ids_string = implode(',', array_map('intval', $ids));

    if ($ids_string) {
        $sql = "SELECT * FROM productos WHERE id IN ($ids_string)";
        $result = $conn->query($sql);

        while($row = $result->fetch_assoc()) {
            // Recuperamos la cantidad que el usuario pidió de la sesión
            $id_prod = $row['id'];
            $cantidad = $_SESSION['carrito'][$id_prod];
            $subtotal = $row['precio'] * $cantidad;

            // Guardamos todo en un array para mostrarlo abajo
            $productos_en_carrito[] = [
                'info' => $row,
                'cantidad' => $cantidad,
                'subtotal' => $subtotal
            ];

            $total_pagar += $subtotal;
            $cantidad_total_items += $cantidad;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tu Carrito | Ambar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'brand-pink': '#fce7f3',
                        'brand-dark': '#831843',
                        'brand-accent': '#db2777',
                    },
                    fontFamily: {
                        'serif': ['Georgia', 'serif'],
                    }
                }
            }
        }
    </script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-brand-pink font-sans text-slate-700">

    <nav class="bg-white/80 backdrop-blur-md fixed w-full z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <a href="index.php" class="flex items-center gap-2 text-brand-dark font-serif font-bold text-lg hover:text-brand-accent transition">
                <i data-lucide="arrow-left" class="w-5 h-5"></i> Seguir comprando
            </a>
            <span class="font-serif font-bold text-xl text-brand-accent"><img src="logo.png" alt="Ambar" class="h-8 inline-block mr-2">Ambar</span>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 pt-28 pb-12">
        <h1 class="text-3xl font-serif font-bold text-brand-dark mb-8 text-center md:text-left">Tu Bolsa de Compras 🎀</h1>

        <?php if (empty($productos_en_carrito)): ?>
            <div class="text-center py-20 bg-white rounded-3xl shadow-sm border border-pink-100 mx-auto max-w-2xl">
                <div class="inline-block p-6 bg-pink-50 rounded-full mb-4">
                    <i data-lucide="shopping-bag" class="h-12 w-12 text-brand-accent/50"></i>
                </div>
                <h2 class="text-xl font-bold text-slate-600">Tu carrito está vacío</h2>
                <a href="index.php" class="inline-block bg-brand-accent text-white px-8 py-3 rounded-full font-medium hover:bg-pink-600 transition shadow-lg mt-6">
                    Ir a la tienda
                </a>
            </div>
        <?php else: ?>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-2 space-y-4">
    <?php foreach ($productos_en_carrito as $item): 
        $id = $item['info']['id']; // Guardamos ID en variable corta
    ?>
        <div id="producto-fila-<?php echo $id; ?>" class="flex flex-col sm:flex-row items-center gap-4 bg-white p-4 rounded-2xl shadow-sm border border-pink-100 transition-all duration-300">
            
            <div class="w-24 h-24 flex-shrink-0 rounded-xl overflow-hidden bg-gray-100">
                <img src="<?php echo $item['info']['imagen']; ?>" class="w-full h-full object-cover">
            </div>
            
            <div class="flex-1 text-center sm:text-left">
                <h3 class="text-md font-bold text-slate-800"><?php echo $item['info']['nombre']; ?></h3>
                <div class="text-slate-400 text-sm mb-2">$<?php echo $item['info']['precio']; ?></div>
            </div>

            <div class="flex items-center border border-pink-200 rounded-lg bg-pink-50 select-none">
                <button onclick="cambiarCantidad(<?php echo $id; ?>, 'restar')" class="p-2 text-brand-dark hover:bg-pink-200 rounded-l-lg transition cursor-pointer">
                    <i data-lucide="minus" class="w-4 h-4"></i>
                </button>
                
                <span id="qty-<?php echo $id; ?>" class="px-3 font-bold text-brand-dark w-8 text-center">
                    <?php echo $item['cantidad']; ?>
                </span>
                
                <button onclick="cambiarCantidad(<?php echo $id; ?>, 'sumar')" class="p-2 text-brand-dark hover:bg-pink-200 rounded-r-lg transition cursor-pointer">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                </button>
            </div>

            <div class="text-right min-w-[80px]">
                <div id="subtotal-<?php echo $id; ?>" class="text-brand-accent font-bold text-lg">
                    $<?php echo number_format($item['subtotal'], 2); ?>
                </div>
                
                <a href="acciones_carrito.php?accion=eliminar&id=<?php echo $id; ?>" class="text-xs text-red-400 hover:text-red-600 underline mt-1 inline-block">
                    Eliminar
                </a>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="lg:col-span-1">
    <div class="bg-white p-8 rounded-[2rem] shadow-xl border border-pink-100 sticky top-28 transition-all duration-300 hover:shadow-2xl hover:border-pink-200">
        
        <h3 class="text-2xl font-serif font-bold text-brand-dark mb-6 flex items-center gap-2">
            Resumen <i data-lucide="sparkles" class="w-5 h-5 text-brand-accent"></i>
        </h3>
        
        <!-- Calculadora de Envío -->
        <div class="mb-6 p-4 bg-slate-50 rounded-xl border border-slate-100">
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Calcular Envío</label>
            <div class="flex gap-2">
                <input type="text" id="codigo_postal" placeholder="Código Postal" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-brand-accent transition">
                <button onclick="calcularEnvio()" class="bg-slate-200 text-slate-600 px-3 py-2 rounded-lg hover:bg-slate-300 transition">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </button>
            </div>
            <p id="mensaje-envio" class="text-xs mt-2 text-slate-400 hidden"></p>
        </div>

        <div class="space-y-4 mb-6">
            <div class="flex justify-between text-slate-600 text-base">
                <span>Subtotal</span>
                <span id="resumen-subtotal" class="font-medium">$<?php echo number_format($total_pagar, 2); ?></span>
            </div>
            
            <div class="flex justify-between text-slate-600 text-base items-center">
                <span class="flex items-center gap-1">
                    Envío <i data-lucide="truck" class="w-3 h-3 text-slate-400"></i>
                </span>
                <span id="costo-envio-display" class="text-slate-500 font-medium">--</span>
            </div>

            <div class="flex justify-between text-slate-400 text-sm">
                <span>Impuestos estimados</span>
                <span>$0.00</span>
            </div>
        </div>
        
        <div class="border-t-2 border-dashed border-pink-200 my-6"></div>
        
        <div class="flex justify-between items-end mb-8">
            <span class="text-lg font-bold text-brand-dark">Total a pagar</span>
            <span id="gran-total" class="text-3xl font-black text-brand-accent tracking-tight">
                $<?php echo number_format($total_pagar, 2); ?>
            </span>
        </div>

        <!-- Formulario para ir a Checkout (Mercado Pago) -->
        <form action="checkout.php" method="POST">
            <input type="hidden" name="codigo_postal" id="input_cp_checkout">
            <button type="submit" 
               class="group w-full bg-[#009EE3] text-white text-center py-4 rounded-2xl font-bold text-lg hover:bg-[#0081b9] transition-all duration-300 shadow-lg hover:shadow-blue-300/50 hover:-translate-y-1 flex items-center justify-center gap-2 overflow-hidden relative">
                
                <span class="relative flex items-center gap-2">
                    Pagar con Mercado Pago <img src="https://http2.mlstatic.com/frontend-assets/ui-navigation/5.18.9/mercadopago/logo__small.png" class="h-5 bg-white rounded px-1">
                </span>
            </button>
        </form>

        <div class="mt-6 flex items-center justify-center gap-2 text-xs text-slate-400 bg-slate-50 py-2 rounded-lg">
            <i data-lucide="lock" class="w-3 h-3"></i>
            Pagos procesados por Mercado Pago
        </div>
    </div>
</div>

<script>
    function calcularEnvio() {
        const cp = document.getElementById('codigo_postal').value;
        const msg = document.getElementById('mensaje-envio');
        const display = document.getElementById('costo-envio-display');
        const inputCheckout = document.getElementById('input_cp_checkout');
        const granTotalDisplay = document.getElementById('gran-total');
        
        // Simulación básica (Debería ser AJAX a tu backend real)
        if(cp.length === 5) {
            let costo = 150;
            if(cp.startsWith('0') || cp.startsWith('1')) costo = 50; // Ejemplo: CDMX más barato
            
            display.innerText = '$' + costo.toFixed(2);
            display.classList.add('text-brand-dark', 'font-bold');
            
            msg.innerText = "Costo calculado para " + cp;
            msg.className = "text-xs mt-2 text-green-600 block";
            
            inputCheckout.value = cp;

            // Actualizar Gran Total Visualmente
            let subtotal = <?php echo $total_pagar; ?>;
            let total = subtotal + costo;
            granTotalDisplay.innerText = '$' + total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'); // Formato simple

        } else {
            msg.innerText = "Ingresa un CP válido (5 dígitos)";
            msg.className = "text-xs mt-2 text-red-500 block";
        }
    }
</script>

            </div>
        <?php endif; ?>
    </div>
    <script>lucide.createIcons();</script>
    <script src="tienda.js"></script>
</body>
</body>
</html>