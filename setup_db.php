<?php
require 'db.php';

echo "<h2>Creando tablas para el Sistema de Pedidos...</h2>";

// Crear tabla pedidos
$sql_pedidos = "CREATE TABLE IF NOT EXISTS pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_email VARCHAR(255) NOT NULL,
    total DECIMAL(10, 2) NOT NULL,
    envio_costo DECIMAL(10, 2) DEFAULT 0,
    envio_modo VARCHAR(50) NOT NULL,
    direccion_json TEXT,
    estado_pago VARCHAR(50) DEFAULT 'pendiente',
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    mp_preference_id VARCHAR(255),
    payment_id VARCHAR(255) NULL
)";

if ($conn->query($sql_pedidos) === TRUE) {
    echo "Tabla 'pedidos' creada o ya existe.<br>";
} else {
    echo "Error creando 'pedidos': " . $conn->error . "<br>";
}

// Crear tabla pedidos_items
$sql_items = "CREATE TABLE IF NOT EXISTS pedidos_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE
)";

if ($conn->query($sql_items) === TRUE) {
    echo "Tabla 'pedidos_items' creada o ya existe.<br>";
} else {
    echo "Error creando 'pedidos_items': " . $conn->error . "<br>";
}

echo "<br><b>¡Base de datos actualizada correctamente!</b> Puedes borrar este archivo (setup_db.php).";
?>
