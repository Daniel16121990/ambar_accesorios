<?php
// db.php
$host = 'localhost';
$user = 'root';      // Cambia esto por tu usuario de Hostinger luego
$pass = '';          // Cambia esto por tu contraseña
$db   = 'tienda_monos';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>