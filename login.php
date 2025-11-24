<?php
session_start();

// Si ya estás logueada, te manda directo al panel
if (isset($_SESSION['admin_logged']) && $_SESSION['admin_logged'] === true) {
    header("Location: admin.php");
    exit;
}

$error = '';

// Verificar usuario y contraseña (Hardcoded para simplificar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['usuario'];
    $pass = $_POST['password'];

    if ($user === 'admin' && $pass === 'monos2025') {
        $_SESSION['admin_logged'] = true;
        header("Location: admin.php");
        exit;
    } else {
        $error = "Usuario o contraseña incorrectos 🎀";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Admin | Ambar</title>
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
                        'sans': ['Inter', 'sans-serif'],
                    },
                    animation: {
                        'blob': 'blob 7s infinite',
                    },
                    keyframes: {
                        blob: {
                            '0%': { transform: 'translate(0px, 0px) scale(1)' },
                            '33%': { transform: 'translate(30px, -50px) scale(1.1)' },
                            '66%': { transform: 'translate(-20px, 20px) scale(0.9)' },
                            '100%': { transform: 'translate(0px, 0px) scale(1)' },
                        }
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-pink-50 h-screen flex items-center justify-center relative overflow-hidden font-sans">

    <!-- Background Blobs -->
    <div class="absolute top-0 left-0 w-96 h-96 bg-pink-200 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob"></div>
    <div class="absolute bottom-0 right-0 w-96 h-96 bg-purple-200 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob animation-delay-2000"></div>

    <div class="bg-white/80 backdrop-blur-lg p-8 rounded-3xl shadow-2xl w-96 border border-white/50 relative z-10">
        <div class="text-center mb-8">
            <img src="logo.png" alt="Ambar" class="h-12 mx-auto mb-4">
            <h1 class="text-2xl font-serif font-bold text-brand-dark">Panel de Control</h1>
            <p class="text-slate-500 text-sm">Ingresa para gestionar tu tienda</p>
        </div>
        
        <?php if($error): ?>
            <div class="bg-red-50 border border-red-100 text-red-500 p-3 rounded-xl mb-6 text-sm text-center flex items-center justify-center gap-2 animate-pulse">
                <i data-lucide="alert-circle" class="w-4 h-4"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1 ml-1">Usuario</label>
                <div class="relative">
                    <i data-lucide="user" class="absolute left-3 top-3 w-5 h-5 text-slate-400"></i>
                    <input type="text" name="usuario" class="w-full border border-slate-200 bg-white/50 rounded-xl py-2.5 pl-10 pr-4 focus:ring-2 focus:ring-brand-accent focus:border-transparent outline-none transition-all" placeholder="Ej. admin" required>
                </div>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1 ml-1">Contraseña</label>
                <div class="relative">
                    <i data-lucide="lock" class="absolute left-3 top-3 w-5 h-5 text-slate-400"></i>
                    <input type="password" name="password" class="w-full border border-slate-200 bg-white/50 rounded-xl py-2.5 pl-10 pr-4 focus:ring-2 focus:ring-brand-accent focus:border-transparent outline-none transition-all" placeholder="••••••••" required>
                </div>
            </div>
            
            <button type="submit" class="w-full bg-brand-dark text-white py-3 rounded-xl hover:bg-brand-accent transition-all duration-300 font-bold shadow-lg hover:shadow-pink-500/30 hover:-translate-y-0.5 mt-2">
                Entrar al Sistema
            </button>
        </form>
        
        <div class="mt-6 text-center">
            <a href="index.php" class="text-sm text-slate-400 hover:text-brand-accent transition flex items-center justify-center gap-1">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Volver a la tienda
            </a>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>