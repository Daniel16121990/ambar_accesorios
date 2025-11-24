<?php
session_start();
require 'db.php';

if (!isset($_SESSION['admin_logged'])) {
    header("Location: login.php");
    exit;
}

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $descripcion = $_POST['descripcion'];
    
    // Manejo de la IMAGEN
    $directorio = "uploads/";
    $nombre_archivo = basename($_FILES["foto"]["name"]);
    // Generar nombre único para evitar duplicados (ej: 1740293_nombrefoto.jpg)
    $ruta_final = $directorio . time() . "_" . $nombre_archivo;
    
    $uploadOk = 1;

    // Verificar si es una imagen real
    $check = getimagesize($_FILES["foto"]["tmp_name"]);
    if($check === false) {
        $error = "El archivo no es una imagen.";
        $uploadOk = 0;
    }

    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["foto"]["tmp_name"], $ruta_final)) {
            // Insertar en la Base de Datos
            // Nota: Guardamos la ruta en la BD (ej: "uploads/foto.jpg")
            $stmt = $conn->prepare("INSERT INTO productos (nombre, descripcion, precio, imagen) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssds", $nombre, $descripcion, $precio, $ruta_final);
            
            if ($stmt->execute()) {
                header("Location: admin.php");
                exit;
            } else {
                $error = "Error al guardar en BD.";
            }
        } else {
            $error = "Hubo un error al subir la imagen.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Producto</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gray-50">

    <div class="max-w-2xl mx-auto p-8">
        <a href="admin.php" class="text-gray-500 hover:text-pink-600 flex items-center gap-2 mb-6">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Volver al panel
        </a>

        <div class="bg-white p-8 rounded-2xl shadow-lg border border-pink-100">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Nuevo Moño 🎀</h1>

            <?php if(isset($error)): ?>
                <div class="bg-red-100 text-red-600 p-3 rounded mb-4 text-sm"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="space-y-5">
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nombre del producto</label>
                    <input type="text" name="nombre" required class="w-full border border-gray-300 rounded-lg p-3 focus:border-pink-500 focus:ring-1 focus:ring-pink-500 outline-none">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Precio</label>
                        <input type="number" step="0.01" name="precio" required class="w-full border border-gray-300 rounded-lg p-3 focus:border-pink-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Foto</label>
                        <input type="file" name="foto" accept="image/*" required class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-pink-50 file:text-pink-700 hover:file:bg-pink-100">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Descripción</label>
                    <textarea name="descripcion" rows="4" required class="w-full border border-gray-300 rounded-lg p-3 focus:border-pink-500 outline-none"></textarea>
                </div>

                <button type="submit" class="w-full bg-pink-600 text-white py-3 rounded-lg font-bold hover:bg-pink-700 transition shadow-lg">
                    Guardar Producto
                </button>
            </form>
        </div>
    </div>
    <script>lucide.createIcons();</script>
</body>
</html>