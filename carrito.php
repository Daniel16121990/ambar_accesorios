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
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Método de Envío</label>
            
            <div class="space-y-3 mb-4">
                <label class="flex items-center p-3 border border-pink-200 rounded-lg cursor-pointer hover:bg-pink-50 transition w-full">
                    <input type="radio" name="opcion_envio" value="mercado_envios" class="w-4 h-4 text-brand-accent focus:ring-brand-accent" checked onchange="cambiarMetodoEnvio()">
                    <span class="ml-3 text-sm font-medium text-slate-700 flex flex-col">
                        <span>Mercado Envíos <i data-lucide="zap" class="w-3 h-3 text-yellow-500 inline"></i></span>
                        <span class="text-xs text-slate-400 font-normal">Calculado y cobrado por Mercado Pago automático.</span>
                    </span>
                </label>
                
                <label class="flex items-center p-3 border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-100 transition w-full">
                    <input type="radio" name="opcion_envio" value="correo_argentino" class="w-4 h-4 text-slate-500 focus:ring-brand-accent" onchange="cambiarMetodoEnvio()">
                    <span class="ml-3 text-sm font-medium text-slate-700 flex flex-col">
                        <span>Correo Argentino <i data-lucide="package" class="w-3 h-3 text-blue-500 inline"></i></span>
                        <span class="text-xs text-slate-400 font-normal">Envío clásico por correo nacional.</span>
                    </span>
                </label>
            </div>

            <div id="contenedor-cp" class="hidden transition-all">
                <p class="text-xs text-slate-500 mb-2">Ingresa tu CP para calcular el costo por Correo Argentino:</p>
                <div class="flex gap-2">
                    <input type="number" id="codigo_postal" placeholder="Código Postal (ej: 1425)" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-brand-accent transition">
                    <button type="button" onclick="calcularEnvio()" class="bg-slate-200 text-slate-600 px-3 py-2 rounded-lg hover:bg-slate-300 transition">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </button>
                </div>
                <p id="mensaje-envio" class="text-xs mt-2 text-slate-400 hidden"></p>
                <!-- Formulario de Dirección -->
                <div id="contenedor-direccion" class="mt-4 bg-white p-4 rounded-xl shadow-sm border border-slate-100 hidden">
                    <p class="text-xs font-bold text-brand-dark uppercase tracking-wider mb-3">Dirección de Destino</p>
                    <div class="space-y-3">
                        <input type="text" id="dir_nombre" placeholder="Nombre de quien recibe" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-brand-accent transition">
                        <input type="text" id="dir_calle" placeholder="Calle y Número (Ej: Av. San Martín 123)" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-brand-accent transition">
                        <input type="text" id="dir_piso" placeholder="Piso / Depto / Lote (Opcional)" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-brand-accent transition">
                        <div class="flex gap-2">
                            <input type="text" id="dir_ciudad" placeholder="Ciudad / Localidad" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-brand-accent transition">
                            <input type="text" id="dir_provincia" placeholder="Provincia" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-brand-accent transition">
                        </div>
                    </div>
                </div>
            </div>
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
        <form action="checkout.php" method="POST" id="form-checkout" onsubmit="return validarCheckout()">
            <input type="hidden" name="tipo_envio" id="input_tipo_envio_checkout" value="mercado_envios">
            <input type="hidden" name="codigo_postal" id="input_cp_checkout">
            <input type="hidden" name="costo_envio" id="input_costo_envio_checkout" value="0">
            <!-- Campos de dirección ocultos -->
            <input type="hidden" name="dir_nombre" id="input_dir_nombre">
            <input type="hidden" name="dir_calle" id="input_dir_calle">
            <input type="hidden" name="dir_piso" id="input_dir_piso">
            <input type="hidden" name="dir_ciudad" id="input_dir_ciudad">
            <input type="hidden" name="dir_provincia" id="input_dir_provincia">
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
    function cambiarMetodoEnvio() {
        const metodo = document.querySelector('input[name="opcion_envio"]:checked').value;
        const contenedorCp = document.getElementById('contenedor-cp');
        const display = document.getElementById('costo-envio-display');
        const inputTipoCheckout = document.getElementById('input_tipo_envio_checkout');
        const granTotalDisplay = document.getElementById('gran-total');
        const msg = document.getElementById('mensaje-envio');

        // Resetear visuales y valores ocultos
        inputTipoCheckout.value = metodo;
        document.getElementById('input_costo_envio_checkout').value = '0';
        msg.classList.add('hidden');
        
        let subtotal = <?php echo $total_pagar; ?>;

        if (metodo === 'mercado_envios') {
            contenedorCp.classList.add('hidden');
            document.getElementById('contenedor-direccion').classList.add('hidden');
            display.innerText = "Calculado en pago";
            display.classList.remove('text-brand-dark', 'font-bold');
            display.classList.add('text-slate-500');
            granTotalDisplay.innerText = '$' + subtotal.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        } else {
            contenedorCp.classList.remove('hidden');
            display.innerText = "Falta ingresar CP";
            display.classList.remove('text-brand-dark', 'font-bold');
            display.classList.add('text-slate-500');
            granTotalDisplay.innerText = '$' + subtotal.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        }
    }

    // Inicializar visual al cargar la pagina por si queda cacheado en ME2
    cambiarMetodoEnvio();

    function calcularEnvio() {
        const cp = document.getElementById('codigo_postal').value;
        const msg = document.getElementById('mensaje-envio');
        const display = document.getElementById('costo-envio-display');
        const inputCpCheckout = document.getElementById('input_cp_checkout');
        const inputCostoCheckout = document.getElementById('input_costo_envio_checkout');
        const granTotalDisplay = document.getElementById('gran-total');
        
        if(cp.length >= 4) {
            msg.innerText = "Calculando...";
            msg.className = "text-xs mt-2 text-slate-500 block";

            // Fetch a nuestra API de envíos local
            fetch('api_envios.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ cp: cp })
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    let costo = parseFloat(data.costo);
                    
                    display.innerText = '$' + costo.toFixed(2);
                    display.classList.remove('text-slate-500');
                    display.classList.add('text-brand-dark', 'font-bold');
                    
                    msg.innerHTML = "Costo calculado vía " + data.proveedor + "<br><span class='font-medium'>🚚 LLegada estimada: " + data.tiempo_estimado + "</span>";
                    msg.className = "text-xs mt-2 text-green-600 block bg-green-50 p-2 rounded border border-green-100";
                    
                    inputCpCheckout.value = cp;
                    inputCostoCheckout.value = costo;
                    
                    document.getElementById('contenedor-direccion').classList.remove('hidden');

                    // Actualizar Gran Total
                    let subtotal = <?php echo $total_pagar; ?>;
                    let total = subtotal + costo;
                    granTotalDisplay.innerText = '$' + total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                } else {
                    msg.innerText = data.message || "Error al calcular.";
                    msg.className = "text-xs mt-2 text-red-500 block";
                }
            })
            .catch(error => {
                console.error('Error:', error);
                msg.innerText = "Hubo un error de conexión.";
                msg.className = "text-xs mt-2 text-red-500 block";
            });

        } else {
            msg.innerText = "Ingresa un CP válido (4 dígitos Arg)";
            msg.className = "text-xs mt-2 text-red-500 block";
        }
    }
    
    function validarCheckout() {
        const metodo = document.querySelector('input[name="opcion_envio"]:checked').value;
        if (metodo === 'correo_argentino') {
            const cp = document.getElementById('codigo_postal').value;
            const nombre = document.getElementById('dir_nombre').value.trim();
            const calle = document.getElementById('dir_calle').value.trim();
            const ciudad = document.getElementById('dir_ciudad').value.trim();
            const provincia = document.getElementById('dir_provincia').value.trim();
            const costo = document.getElementById('input_costo_envio_checkout').value;
            
            if (costo === '0' || !cp) {
                alert("Por favor, calcula primero el costo de envío ingresando tu código postal.");
                return false;
            }
            
            if (!nombre || !calle || !ciudad || !provincia) {
                alert("Por favor, completa todos los campos de la dirección (Nombre, Calle, Ciudad y Provincia).");
                return false;
            }
            
            // Pasar valores a los inputs ocultos
            document.getElementById('input_dir_nombre').value = nombre;
            document.getElementById('input_dir_calle').value = calle;
            document.getElementById('input_dir_piso').value = document.getElementById('dir_piso').value.trim();
            document.getElementById('input_dir_ciudad').value = ciudad;
            document.getElementById('input_dir_provincia').value = provincia;
        }
        return true;
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