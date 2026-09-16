<?php
$host = 'localhost:3307'; 
$dbname = 'pagina_venta_perrito';
$username = 'root';
$password = ''; // Por defecto en XAMPP suele estar vacío
$charset = 'utf8mb4';

try {
    // Creamos la conexión usando PDO
    $conexion = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Configuramos el modo de error de PDO para que lance excepciones
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Opcional: Para verificar que conectó (puedes comentarlo después)
    // echo "¡Conexión exitosa a la base de datos!";

} catch (PDOException $e) {
    // Si hay un error en la conexión, detenemos el script y mostramos el mensaje
    die("El error de conexión es: " . $e->getMessage());
}
?>