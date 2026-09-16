<?php
session_start();
// Validamos que el usuario haya iniciado sesión y sea administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Tienda Perrito</title>
    <link rel="stylesheet" href="../css/style_admin.css">
    <style>
        .admin-container { max-width: 1000px; margin: 40px auto; padding: 20px; }
        .admin-menu { display: flex; gap: 20px; margin-top: 30px; }
        .admin-card { background: #f4f4f4; padding: 20px; border-radius: 8px; text-decoration: none; color: #333; flex: 1; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .admin-card:hover { background: #e2e2e2; }
    </style>
</head>
<body>
    <div class="admin-container">
        <h1>Bienvenido al Panel de Administración, <?php echo htmlspecialchars($_SESSION['nombre']); ?> 🐶</h1>
        <p>Desde aquí puedes gestionar todo el inventario y las ventas de tu tienda.</p>
        
        <div class="admin-menu">
            <a href="admin_productos.php" class="admin-card">
                <h3>Gestionar Productos y Stock</h3>
                <p>Agregar, ver o eliminar productos del catálogo.</p>
            </a>
            <a href="admin_pedidos.php" class="admin-card">
                <h3>Ver Pedidos</h3>
                <p>Monitorear las compras de los clientes.</p>
            </a>
            <a href="../logout.php" class="admin-card" style="background: #ffe6e6;">
                <h3>Cerrar Sesión</h3>
                <p>Salir del sistema de administración.</p>
            </a>
        </div>
    </div>
</body>
</html>