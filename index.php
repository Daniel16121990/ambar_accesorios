<?php
session_start();
require 'db.php';

// Inicializar carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

$producto_agregado = false; // Bandera para la animación

// Lógica al agregar producto
if (isset($_POST['agregar_id'])) {
    $id_producto = $_POST['agregar_id'];
    $_SESSION['carrito'][] = $id_producto;
    $producto_agregado = true; // Activamos la animación
}

$cantidad_carrito = count($_SESSION['carrito']);
?>

<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ambar | Tienda de Moños</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'brand-pink': '#fce7f3', // Rosa muy claro
                        'brand-dark': '#831843', // Vino/Rosa oscuro
                        'brand-accent': '#db2777', // Fucsia
                        'brand-light': '#fdf2f8',
                    },
                    fontFamily: {
                        'serif': ['Georgia', 'serif'],
                        'sans': ['Inter', 'sans-serif'],
                    },
                    animation: {
                        'blob': 'blob 7s infinite',
                        'float': 'float 6s ease-in-out infinite',
                    },
                    keyframes: {
                        blob: {
                            '0%': { transform: 'translate(0px, 0px) scale(1)' },
                            '33%': { transform: 'translate(30px, -50px) scale(1.1)' },
                            '66%': { transform: 'translate(-20px, 20px) scale(0.9)' },
                            '100%': { transform: 'translate(0px, 0px) scale(1)' },
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-20px)' },
                        }
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, .font-serif { font-family: 'Georgia', serif; }
    </style>
</head>
<body class="bg-brand-light font-sans text-slate-700 overflow-x-hidden">

    <!-- Navbar -->
    <nav class="bg-white/80 backdrop-blur-md fixed w-full z-50 shadow-sm transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex-shrink-0 flex items-center cursor-pointer group">
                    <img src="logo.png" alt="Ambar Logo" class="h-12 w-auto mr-3 group-hover:rotate-12 transition-transform duration-300">
                </div>
                
                <div class="flex items-center space-x-8">
                    <a href="index.php" class="text-slate-600 hover:text-brand-accent transition font-medium text-sm uppercase tracking-wider">Inicio</a>
                    <a href="#coleccion" class="text-slate-600 hover:text-brand-accent transition font-medium text-sm uppercase tracking-wider">Colección</a>
                    
                    <a href="carrito.php" id="btn-carrito" class="relative cursor-pointer text-slate-600 hover:text-brand-accent transition-all duration-300 ease-out p-2 hover:bg-pink-50 rounded-full">
                        <i data-lucide="shopping-bag" class="h-6 w-6"></i>
                        <span class="cart-badge <?php echo ($cantidad_carrito > 0) ? '' : 'hidden'; ?> absolute top-0 right-0 bg-brand-accent text-white text-[10px] font-bold rounded-full h-5 w-5 flex items-center justify-center border-2 border-white shadow-sm">
                            <?php echo $cantidad_carrito; ?>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <span class="inline-block py-1 px-3 rounded-full bg-pink-100 text-brand-dark text-xs font-bold tracking-widest mb-6 animate-bounce">NUEVA COLECCIÓN 2025</span>
            <h1 class="text-5xl md:text-7xl font-serif font-bold text-brand-dark mb-6 leading-tight">
                El toque final <br/> <span class="text-brand-accent italic">para tu estilo</span>
            </h1>
            <p class="mt-4 max-w-2xl mx-auto text-xl text-slate-600 mb-10 font-light leading-relaxed">
                Moños artesanales y accesorios únicos diseñados para chicas que aman los detalles "aesthetic". Hechos a mano con amor.
            </p>
            <div class="flex justify-center gap-4">
                <a href="#coleccion" class="inline-block bg-brand-accent text-white font-semibold px-8 py-4 rounded-full shadow-lg hover:bg-pink-600 hover:shadow-pink-300/50 hover:-translate-y-1 transition-all duration-300">
                    Ver Colección
                </a>
                <a href="#newsletter" class="inline-block bg-white text-brand-dark border border-pink-200 font-semibold px-8 py-4 rounded-full shadow-sm hover:bg-pink-50 hover:border-brand-accent transition-all duration-300">
                    Suscribirse
                </a>
            </div>
        </div>
        
        <!-- Background Blobs -->
        <div class="absolute top-0 left-0 w-96 h-96 bg-pink-200 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob"></div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-purple-200 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob animation-delay-2000"></div>
        <div class="absolute -bottom-32 left-1/2 w-96 h-96 bg-yellow-100 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob animation-delay-4000"></div>
    </header>

    <!-- Features Section -->
    <section class="py-12 bg-white border-y border-pink-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center divide-y md:divide-y-0 md:divide-x divide-pink-100">
                <div class="p-4">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-pink-50 text-brand-accent mb-4">
                        <i data-lucide="sparkles" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-lg font-bold text-brand-dark mb-2">Diseños Únicos</h3>
                    <p class="text-slate-500 text-sm">Cada pieza es creada artesanalmente, asegurando que lleves algo exclusivo.</p>
                </div>
                <div class="p-4">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-pink-50 text-brand-accent mb-4">
                        <i data-lucide="heart-handshake" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-lg font-bold text-brand-dark mb-2">Hecho a Mano</h3>
                    <p class="text-slate-500 text-sm">Cuidamos cada detalle, costura y acabado para la mejor calidad.</p>
                </div>
                <div class="p-4">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-pink-50 text-brand-accent mb-4">
                        <i data-lucide="shield-check" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-lg font-bold text-brand-dark mb-2">Compra Segura</h3>
                    <p class="text-slate-500 text-sm">Envíos rápidos y pagos protegidos para tu tranquilidad.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Collection Section -->
    <section id="coleccion" class="py-20 bg-brand-light min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-serif font-bold text-brand-dark mb-4">Nuestros Favoritos</h2>
                <div class="w-24 h-1 bg-brand-accent mx-auto rounded-full"></div>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <?php
                $sql = "SELECT * FROM productos";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                ?>
                        <div class="group bg-white rounded-3xl p-4 shadow-sm hover:shadow-xl transition-all duration-500 hover:-translate-y-2 relative overflow-hidden">
                            <!-- Discount Badge (Example) -->
                            <div class="absolute top-6 left-6 z-10 bg-brand-dark text-white text-xs font-bold px-2 py-1 rounded-md shadow-md">
                                BEST SELLER
                            </div>

                            <a href="producto.php?id=<?php echo $row['id']; ?>" class="block relative overflow-hidden rounded-2xl h-72 mb-5 cursor-pointer bg-gray-100">
                                <img src="<?php echo $row['imagen']; ?>" alt="<?php echo $row['nombre']; ?>" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700">
                                
                                <div class="absolute inset-0 bg-black/10 group-hover:bg-black/20 transition-colors duration-300"></div>
                                
                                <div class="absolute bottom-4 left-0 right-0 flex justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 translate-y-4 group-hover:translate-y-0">
                                    <span class="bg-white text-brand-dark px-6 py-2 rounded-full text-sm font-bold shadow-lg hover:bg-brand-accent hover:text-white transition-colors">Ver detalle</span>
                                </div>
                            </a>

                            <div class="px-2">
                                <h3 class="text-lg font-bold text-slate-800 mb-1 truncate">
                                    <a href="producto.php?id=<?php echo $row['id']; ?>" class="hover:text-brand-accent transition">
                                        <?php echo $row['nombre']; ?>
                                    </a>
                                </h3>
                                
                                <p class="text-sm text-slate-500 mb-4 truncate"><?php echo $row['descripcion']; ?></p>
                                
                                <div class="flex justify-between items-center pt-2 border-t border-slate-100">
                                    <span class="text-xl font-bold text-brand-dark">$<?php echo $row['precio']; ?></span>
                                    
                                    <form onsubmit="agregarCarrito(event, this)" action="acciones_carrito.php" method="POST">
                                        <input type="hidden" name="accion" value="agregar">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                        <input type="hidden" name="cantidad" value="1">
                                        <button type="submit" class="bg-pink-50 text-brand-dark hover:bg-brand-accent hover:text-white w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300 shadow-sm hover:shadow-md group-btn">
                                            <i data-lucide="plus" class="h-5 w-5 transition-transform group-btn-hover:rotate-90"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                <?php 
                    }
                } else {
                    echo "<p class='text-center col-span-4 text-slate-400'>No hay productos disponibles.</p>";
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Newsletter Section -->
    <section id="newsletter" class="py-20 bg-brand-dark relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        <div class="max-w-4xl mx-auto px-4 relative z-10 text-center">
            <i data-lucide="mail" class="w-12 h-12 text-pink-300 mx-auto mb-6"></i>
            <h2 class="text-3xl md:text-4xl font-serif font-bold text-white mb-4">Únete al Club Ambar</h2>
            <p class="text-pink-100 mb-8 text-lg">Recibe noticias de nuevos lanzamientos, descuentos exclusivos y tips de estilo.</p>
            
            <form class="flex flex-col sm:flex-row gap-4 justify-center max-w-lg mx-auto">
                <input type="email" placeholder="Tu correo electrónico" class="flex-1 px-6 py-4 rounded-full border-none focus:ring-2 focus:ring-pink-400 outline-none text-slate-800" required>
                <button type="button" onclick="mostrarToast('¡Gracias por suscribirte! 💌')" class="bg-brand-accent text-white px-8 py-4 rounded-full font-bold hover:bg-pink-600 transition shadow-lg hover:shadow-pink-500/30">
                    Suscribirme
                </button>
            </form>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-300 py-16">
        <div class="max-w-7xl mx-auto px-4 grid grid-cols-1 md:grid-cols-4 gap-12">
            <div class="col-span-1 md:col-span-2">
                <div class="flex items-center gap-2 mb-6">
                    <div class="w-8 h-8 bg-brand-accent rounded-full flex items-center justify-center text-white font-serif font-bold">A</div>
                    <span class="font-serif text-2xl font-bold text-white">Ambar</span>
                </div>
                <p class="text-slate-400 text-sm leading-relaxed max-w-xs">
                    Tienda online dedicada a ofrecer los accesorios más lindos y en tendencia para complementar tu estilo único.
                </p>
            </div>
            
            <div>
                <h4 class="text-white font-bold mb-6">Enlaces Rápidos</h4>
                <ul class="space-y-3 text-sm">
                    <li><a href="#" class="hover:text-brand-accent transition">Inicio</a></li>
                    <li><a href="#coleccion" class="hover:text-brand-accent transition">Colección</a></li>
                    <li><a href="carrito.php" class="hover:text-brand-accent transition">Mi Carrito</a></li>
                    <li><a href="login.php" class="hover:text-brand-accent transition">Admin</a></li>
                </ul>
            </div>
            
            <div>
                <h4 class="text-white font-bold mb-6">Síguenos</h4>
                <div class="flex gap-4">
                    <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-brand-accent hover:text-white transition"><i data-lucide="instagram" class="w-5 h-5"></i></a>
                    <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-brand-accent hover:text-white transition"><i data-lucide="facebook" class="w-5 h-5"></i></a>
                    <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-brand-accent hover:text-white transition"><i data-lucide="twitter" class="w-5 h-5"></i></a>
                </div>
            </div>
        </div>
        <div class="border-t border-slate-800 mt-12 pt-8 text-center text-xs text-slate-500">
            &copy; 2024 Ambar Tienda Online. Todos los derechos reservados.
        </div>
    </footer>

    <!-- Floating WhatsApp Button -->
    <a href="https://wa.me/1234567890" target="_blank" class="fixed bottom-6 left-6 bg-green-500 text-white p-4 rounded-full shadow-xl hover:bg-green-600 hover:scale-110 transition-all duration-300 z-50 animate-float group">
        <i data-lucide="message-circle" class="w-8 h-8"></i>
        <span class="absolute left-full ml-3 bg-white text-slate-700 px-3 py-1 rounded-lg shadow-md text-sm font-bold opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
            ¡Contáctanos!
        </span>
    </a>

    <script>
        lucide.createIcons();

        <?php if ($producto_agregado): ?>
        document.addEventListener('DOMContentLoaded', () => {
            const btnCarrito = document.getElementById('btn-carrito');
            if(btnCarrito) {
                // Efecto POP: Escala + Color Fucsia
                btnCarrito.classList.add('scale-125', 'text-brand-accent', 'bg-pink-100');
                
                // Quitar efecto después de 300ms
                setTimeout(() => {
                    btnCarrito.classList.remove('scale-125', 'text-brand-accent', 'bg-pink-100');
                }, 500);
            }
        });
        <?php endif; ?>
    </script>
    <script src="tienda.js"></script>
</body>
</html>