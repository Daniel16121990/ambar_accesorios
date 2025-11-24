<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago Fallido | Ambar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-red-50 h-screen flex items-center justify-center font-sans">

    <div class="bg-white p-10 rounded-3xl shadow-xl text-center max-w-md mx-4">
        <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <i data-lucide="x" class="w-10 h-10 text-red-600"></i>
        </div>
        
        <h1 class="text-3xl font-bold text-slate-800 mb-4">Algo salió mal</h1>
        <p class="text-slate-500 mb-8">No pudimos procesar tu pago. Por favor intenta nuevamente o usa otro método de pago.</p>
        
        <div class="flex gap-4 justify-center">
            <a href="carrito.php" class="inline-block bg-slate-200 text-slate-700 px-6 py-3 rounded-full font-bold hover:bg-slate-300 transition">
                Volver al Carrito
            </a>
            <a href="index.php" class="inline-block border-2 border-red-200 text-red-600 px-6 py-3 rounded-full font-bold hover:bg-red-50 transition">
                Ir al Inicio
            </a>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
