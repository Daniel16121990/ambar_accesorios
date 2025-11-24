<?php
session_start();
require 'db.php';

// 1. Validar ID
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int)$_GET['id'];

// 2. Buscar producto
$sql = "SELECT * FROM productos WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    header("Location: index.php");
    exit;
}

$producto = $result->fetch_assoc();

// 3. Contador carrito para el navbar
$cantidad_carrito = isset($_SESSION['carrito']) ? count($_SESSION['carrito']) : 0;
if(isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
    $cantidad_carrito = count($_SESSION['carrito']); // Simplificado para contar items
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $producto['nombre']; ?> | Ambar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'brand-pink': '#fce7f3',
                        'brand-dark': '#831843',
                        'brand-accent': '#db2777',
                        'brand-light': '#fdf2f8',
                    },
                    fontFamily: {
                        'serif': ['Georgia', 'serif'],
                        'sans': ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-brand-light font-sans text-slate-700">

    <nav class="bg-white/80 backdrop-blur-md fixed w-full z-50 shadow-sm transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <a href="index.php" class="flex items-center gap-2 text-slate-600 hover:text-brand-dark transition font-medium">
                <i data-lucide="arrow-left" class="w-5 h-5"></i> Volver
            </a>
            
            <div class="flex items-center gap-6">
                <a href="index.php" class="font-serif font-bold text-xl text-brand-dark"><img src="logo.png" alt="Ambar" class="h-8 inline-block mr-2">Ambar</a>
                
                <a href="carrito.php" id="btn-carrito" class="relative cursor-pointer text-slate-600 hover:text-brand-accent transition p-2 hover:bg-pink-50 rounded-full">
                    <i data-lucide="shopping-bag" class="h-6 w-6"></i>
                    <span class="cart-badge <?php echo ($cantidad_carrito > 0) ? '' : 'hidden'; ?> absolute top-0 right-0 bg-brand-accent text-white text-[10px] font-bold rounded-full h-5 w-5 flex items-center justify-center border-2 border-white shadow-sm">
                        <?php echo $cantidad_carrito; ?>
                    </span>
                </a>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 pt-28 pb-16">
        
        <div class="text-sm text-slate-400 mb-8 flex items-center gap-2">
            <a href="index.php" class="hover:text-brand-accent transition">Inicio</a> 
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
            <a href="index.php#coleccion" class="hover:text-brand-accent transition">Colección</a>
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
            <span class="text-brand-dark font-medium"><?php echo $producto['nombre']; ?></span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-start">
            
            <!-- Galería -->
            <div class="bg-white rounded-[2.5rem] p-4 shadow-xl border border-white relative group flex gap-4 h-[500px]">
                
                <div class="flex flex-col gap-3 w-20 py-2 overflow-y-auto scrollbar-hide">
                    <button onclick="cambiarImagen(this)" class="border-2 border-brand-accent rounded-xl overflow-hidden h-20 w-full flex-shrink-0 transition hover:opacity-80 focus:outline-none">
                        <img src="<?php echo $producto['imagen']; ?>" class="w-full h-full object-cover">
                    </button>
                    
                    <!-- Simulando más imágenes con filtros -->
                    <button onclick="cambiarImagen(this)" class="border-2 border-transparent hover:border-pink-200 rounded-xl overflow-hidden h-20 w-full flex-shrink-0 transition opacity-60 hover:opacity-100 focus:outline-none">
                        <img src="<?php echo $producto['imagen']; ?>" class="w-full h-full object-cover filter sepia">
                    </button>
                    
                    <button onclick="cambiarImagen(this)" class="border-2 border-transparent hover:border-pink-200 rounded-xl overflow-hidden h-20 w-full flex-shrink-0 transition opacity-60 hover:opacity-100 focus:outline-none">
                        <img src="<?php echo $producto['imagen']; ?>" class="w-full h-full object-cover filter grayscale">
                    </button>
                </div>

                <div class="flex-1 relative overflow-hidden rounded-[2rem] bg-gray-50 group-image cursor-zoom-in" onmousemove="zoomImagen(event)" onmouseleave="resetZoom(event)">
                    <img id="imagen-principal" src="<?php echo $producto['imagen']; ?>" alt="<?php echo $producto['nombre']; ?>" class="w-full h-full object-cover transition-transform duration-200 origin-center">
                    
                    <div class="absolute top-6 right-6 bg-white/90 backdrop-blur px-4 py-2 rounded-full text-xs font-bold text-brand-dark shadow-sm">
                        NEW ARRIVAL
                    </div>
                </div>
            </div>

            <!-- Info Producto -->
            <div class="space-y-8 pt-4">
                <div>
                    <h1 class="text-4xl font-serif font-bold text-brand-dark mb-2"><?php echo $producto['nombre']; ?></h1>
                    <div class="flex items-center gap-4 mb-4">
                        <span class="text-3xl font-bold text-brand-accent">$<?php echo number_format($producto['precio'], 2); ?></span>
                        <div class="flex text-yellow-400 text-sm">
                            <i data-lucide="star" class="fill-current w-4 h-4"></i>
                            <i data-lucide="star" class="fill-current w-4 h-4"></i>
                            <i data-lucide="star" class="fill-current w-4 h-4"></i>
                            <i data-lucide="star" class="fill-current w-4 h-4"></i>
                            <i data-lucide="star" class="fill-current w-4 h-4"></i>
                            <span class="text-slate-400 ml-2 text-xs">(24 reviews)</span>
                        </div>
                    </div>
                </div>

                <p class="text-slate-600 leading-relaxed text-lg font-light">
                    <?php echo $producto['descripcion']; ?>
                    <br><br>
                    Hecho a mano con materiales de alta calidad para asegurar que tu peinado luzca perfecto todo el día. Cada pieza es única.
                </p>

                <hr class="border-pink-100">

                <!-- Formulario de Compra -->
                <form onsubmit="agregarCarrito(event, this)" action="acciones_carrito.php" method="POST">
                    <input type="hidden" name="accion" value="agregar">
                    <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                    
                    <!-- Selector de Variante -->
                    <div class="mb-8">
                        <label class="block text-sm font-bold text-brand-dark mb-3">Tipo de Broche:</label>
                        <input type="hidden" name="variante" id="input-variante" value="Pinza Cocodrilo">
                        <div class="flex flex-wrap gap-3">
                            <button type="button" onclick="seleccionarVariante('Pinza Cocodrilo', this)" class="btn-variante px-4 py-2 border-2 border-brand-accent text-brand-accent bg-pink-50 rounded-lg font-medium text-sm transition-all">Pinza Cocodrilo</button>
                            <button type="button" onclick="seleccionarVariante('Liga Elástica', this)" class="btn-variante px-4 py-2 border border-slate-200 text-slate-500 hover:border-brand-accent rounded-lg font-medium text-sm transition-all">Liga Elástica</button>
                            <button type="button" onclick="seleccionarVariante('Diadema (+$20)', this)" class="btn-variante px-4 py-2 border border-slate-200 text-slate-500 hover:border-brand-accent rounded-lg font-medium text-sm transition-all">Diadema (+$20)</button>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="w-24">
                            <input type="number" name="cantidad" value="1" min="1" max="10" class="w-full border-2 border-pink-100 rounded-xl py-4 text-center font-bold text-brand-dark focus:border-brand-accent outline-none bg-white transition-colors">
                        </div>

                        <button type="submit" class="flex-1 bg-brand-dark text-white py-4 rounded-xl font-bold text-lg shadow-lg hover:bg-brand-accent transition-all hover:scale-[1.02] hover:shadow-pink-500/30 flex items-center justify-center gap-2">
                            <i data-lucide="shopping-bag" class="w-5 h-5"></i> Agregar a mi bolsa
                        </button>
                    </div>
                </form>
                
                <div class="bg-pink-50 rounded-xl p-4 flex items-center justify-center gap-6 text-xs text-slate-500">
                    <div class="flex items-center gap-2">
                        <i data-lucide="truck" class="w-4 h-4 text-brand-accent"></i> Envío gratis > $500
                    </div>
                    <div class="flex items-center gap-2">
                        <i data-lucide="shield-check" class="w-4 h-4 text-brand-accent"></i> Pago Seguro
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-white border-t border-pink-100 py-8 mt-12">
        <div class="text-center text-slate-400 text-sm">
            &copy; 2024 Ambar.
        </div>
    </footer>

    <script src="tienda.js"></script>
    <script>
        lucide.createIcons();
        
        // Lógica de la Galería
        function cambiarImagen(thumbnail) {
            const nuevaSrc = thumbnail.querySelector('img').src;
            const filtros = getComputedStyle(thumbnail.querySelector('img')).filter;
            
            const imagenPrincipal = document.getElementById('imagen-principal');
            
            // Transición
            imagenPrincipal.style.opacity = '0.5';
            
            setTimeout(() => {
                imagenPrincipal.src = nuevaSrc;
                imagenPrincipal.style.filter = filtros; 
                imagenPrincipal.style.opacity = '1';
            }, 150);

            // Estilos de borde activo
            const botones = thumbnail.parentElement.querySelectorAll('button');
            botones.forEach(btn => {
                btn.classList.remove('border-brand-accent', 'opacity-100');
                btn.classList.add('border-transparent', 'opacity-60');
            });
            thumbnail.classList.remove('border-transparent', 'opacity-60');
            thumbnail.classList.add('border-brand-accent', 'opacity-100');
        }

        // Zoom Effect
        function zoomImagen(e) {
            const img = document.getElementById('imagen-principal');
            const container = e.currentTarget;
            const { left, top, width, height } = container.getBoundingClientRect();
            
            const x = (e.clientX - left) / width;
            const y = (e.clientY - top) / height;
            
            img.style.transformOrigin = `${x * 100}% ${y * 100}%`;
            img.style.transform = 'scale(1.5)';
        }

        function resetZoom(e) {
            const img = document.getElementById('imagen-principal');
            img.style.transform = 'scale(1)';
            setTimeout(() => {
                img.style.transformOrigin = 'center center';
            }, 200);
        }

        // Selector de Variantes
        function seleccionarVariante(valor, btn) {
            document.getElementById('input-variante').value = valor;
            
            // Reset estilos
            document.querySelectorAll('.btn-variante').forEach(b => {
                b.className = 'btn-variante px-4 py-2 border border-slate-200 text-slate-500 hover:border-brand-accent rounded-lg font-medium text-sm transition-all';
            });
            
            // Estilo activo
            btn.className = 'btn-variante px-4 py-2 border-2 border-brand-accent text-brand-accent bg-pink-50 rounded-lg font-medium text-sm transition-all';
        }
    </script>
</body>
</html>