<?php
session_start();
include '../conexion.php';

// Seguridad: Solo admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Actualizar estado del pedido si se envía el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['actualizar_estado'])) {
    $pedido_id = $_POST['pedido_id'];
    $nuevo_estado = $_POST['estado_envio'];
    
    $update_sql = "UPDATE pedidos SET estado_envio = ? WHERE id = ?";
    $update_stmt = $conexion->prepare($update_sql);
    $update_stmt->execute([$nuevo_estado, $pedido_id]);
    
    header("Location: admin_pedidos.php");
    exit();
}

// Consultar los pedidos junto con los datos del usuario
$sql = "SELECT p.*, u.nombre AS cliente_nombre, u.email AS cliente_email 
        FROM pedidos p 
        JOIN usuarios u ON p.usuario_id = u.id 
        ORDER BY p.fecha_pedido DESC";
$pedidos = $conexion->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar Pedidos - Tienda Perrito</title>
    <link rel="stylesheet" href="../css/style_admin.css">
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f9f9f9; }
        .container { max-width: 1100px; margin: auto; background: white; padding: 25px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; margin-bottom: 30px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 14px; }
        th { background: #333; color: white; }
        .detalle-tabla { width: 100%; margin-top: 10px; background: #fdfdfd; }
        .detalle-tabla th { background: #555; font-size: 12px; }
        .detalle-tabla td { font-size: 12px; }
        .btn-estado { padding: 5px; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <a href="admin.php">⬅ Volver al Panel</a>
        <h2>Control y Seguimiento de Pedidos</h2>

        <?php if (empty($pedidos)): ?>
            <p>Aún no hay pedidos registrados en la tienda.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID Pedido</th>
                        <th>Cliente</th>
                        <th>Total</th>
                        <th>Datos de Envío</th>
                        <th>Estado Actual</th>
                        <th>Fecha</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pedidos as $ped): ?>
                    <tr>
                        <td><strong>#<?php echo $ped['id']; ?></strong></td>
                        <td>
                            <?php echo htmlspecialchars($ped['cliente_nombre']); ?><br>
                            <small style="color: #666;"><?php echo htmlspecialchars($ped['cliente_email']); ?></small>
                        </td>
                        <td>$<?php echo number_format($ped['total'], 2); ?></td>
                        <td>
                            <b>Tel:</b> <?php echo htmlspecialchars($ped['telefono_contacto']); ?><br>
                            <b>Dir:</b> <?php echo htmlspecialchars($ped['direccion_envio']); ?>
                        </td>
                        <td>
                            <form action="admin_pedidos.php" method="POST" style="display:inline;">
                                <input type="hidden" name="pedido_id" value="<?php echo $ped['id']; ?>">
                                <select name="estado_envio" class="btn-estado" onchange="this.form.submit()">
                                    <option value="Procesando" <?php if($ped['estado_envio']=='Procesando') echo 'selected'; ?>>Procesando</option>
                                    <option value="Enviado" <?php if($ped['estado_envio']=='Enviado') echo 'selected'; ?>>Enviado</option>
                                    <option value="Entregado" <?php if($ped['estado_envio']=='Entregado') echo 'selected'; ?>>Entregado</option>
                                </select>
                                <input type="hidden" name="actualizar_estado" value="1">
                            </form>
                        </td>
                        <td><?php echo $ped['fecha_pedido']; ?></td>
                        <td>
                            <!-- Desplegable interno o lista de productos comprados en este pedido -->
                            <details>
                                <summary style="cursor:pointer; color:#007bff; font-weight:bold;">Ver Productos</summary>
                                <table class="detalle-tabla">
                                    <thead>
                                        <tr>
                                            <th>Producto</th>
                                            <th>Cant.</th>
                                            <th>Precio U.</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Consultamos los productos de este pedido específico
                                        $det_sql = "SELECT dp.*, pr.nombre FROM detalle_pedidos dp JOIN productos pr ON dp.producto_id = pr.id WHERE dp.pedido_id = ?";
                                        $det_stmt = $conexion->prepare($det_sql);
                                        $det_stmt->execute([$ped['id']]);
                                        $detalles = $det_stmt->fetchAll(PDO::FETCH_ASSOC);

                                        foreach ($det_alles as $det):
                                        ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($det['nombre']); ?></td>
                                            <td><?php echo $det['cantidad']; ?></td>
                                            <td>$<?php echo number_format($det['precio_unitario'], 2); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </details>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>