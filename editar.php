<?php
session_start();
require 'db.php';

if (!isset($_SESSION['admin_logged'])) {
    header("Location: login.php");
    exit;
}

// Obtener el ID del producto a editar
if (!isset($_GET['id'])) {
    header("Location: admin.php");
    exit;
}

$id = (int)$_GET['id'];

// Obtener datos del producto
$stmt = $conn->prepare("SELECT * FROM productos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$producto = $result->fetch_assoc();

if (!$producto) {
    header("Location: admin.php");
    exit;
}

// Procesar el formulario de edición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $descripcion = $_POST['descripcion'];
    
    // Verificar si se subió una nueva imagen
    if (!empty($_FILES["foto"]["name"])) {
        // Manejo de la nueva imagen
        $directorio = "uploads/";
        $nombre_archivo = basename($_FILES["foto"]["name"]);
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
                // Eliminar la imagen anterior si existe
                if (file_exists($producto['imagen'])) {
                    unlink($producto['imagen']);
                }
                
                // Actualizar con nueva imagen
                $stmt = $conn->prepare("UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, imagen = ? WHERE id = ?");
                $stmt->bind_param("ssdsi", $nombre, $descripcion, $precio, $ruta_final, $id);
            } else {
                $error = "Hubo un error al subir la imagen.";
            }
        }
    } else {
        // Actualizar sin cambiar la imagen
        $stmt = $conn->prepare("UPDATE productos SET nombre = ?, descripcion = ?, precio = ? WHERE id = ?");
        $stmt->bind_param("ssdi", $nombre, $descripcion, $precio, $id);
    }
    
    if (!isset($error) && $stmt->execute()) {
        header("Location: admin.php");
        exit;
    } else if (!isset($error)) {
        $error = "Error al actualizar en BD.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gray-50">

    <div class="max-w-2xl mx-auto p-4 sm:p-8">
        <a href="admin.php" class="text-gray-500 hover:text-pink-600 flex items-center gap-2 mb-6">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Volver al panel
        </a>

        <div class="bg-white p-4 sm:p-8 rounded-2xl shadow-lg border border-pink-100">
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6">Editar Producto 🎀</h1>

            <?php if(isset($error)): ?>
                <div class="bg-red-100 text-red-600 p-3 rounded mb-4 text-sm"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="space-y-5">
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nombre del producto</label>
                    <input type="text" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required class="w-full border border-gray-300 rounded-lg p-3 focus:border-pink-500 focus:ring-1 focus:ring-pink-500 outline-none">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Precio</label>
                        <input type="number" step="0.01" name="precio" value="<?php echo $producto['precio']; ?>" required class="w-full border border-gray-300 rounded-lg p-3 focus:border-pink-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Foto (opcional)</label>
                        <input type="file" name="foto" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-pink-50 file:text-pink-700 hover:file:bg-pink-100">
                    </div>
                </div>

                <!-- Mostrar imagen actual -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Imagen actual</label>
                    <img src="<?php echo $producto['imagen']; ?>" class="w-32 h-32 object-cover rounded-lg border border-gray-200">
                    <p class="text-xs text-gray-500 mt-1">Deja el campo de foto vacío si no quieres cambiarla</p>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Descripción</label>
                    <textarea name="descripcion" rows="4" required class="w-full border border-gray-300 rounded-lg p-3 focus:border-pink-500 outline-none"><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
                </div>

                <button type="submit" class="w-full bg-pink-600 text-white py-3 rounded-lg font-bold hover:bg-pink-700 transition shadow-lg">
                    Actualizar Producto
                </button>
            </form>
        </div>
    </div>
    <script>lucide.createIcons();</script>
</body>
</html>
