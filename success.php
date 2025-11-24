<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¡Pago Exitoso! | Ambar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-green-50 h-screen flex items-center justify-center font-sans">

    <div class="bg-white p-10 rounded-3xl shadow-xl text-center max-w-md mx-4">
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <i data-lucide="check" class="w-10 h-10 text-green-600"></i>
        </div>
        
        <h1 class="text-3xl font-bold text-slate-800 mb-4">¡Gracias por tu compra!</h1>
        <p class="text-slate-500 mb-8">Tu pago ha sido procesado correctamente. Te enviaremos un correo con los detalles de tu pedido.</p>
        
        <a href="index.php" class="inline-block bg-green-600 text-white px-8 py-3 rounded-full font-bold hover:bg-green-700 transition shadow-lg hover:shadow-green-500/30">
            Volver a la tienda
        </a>
    </div>

    <script>
        lucide.createIcons();
        // Limpiar carrito (Opcional: podrías hacerlo en PHP antes de mostrar esto)
        localStorage.removeItem('carrito'); 
    </script>
</body>
</html>
