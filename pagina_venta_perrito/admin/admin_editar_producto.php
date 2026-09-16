<?php
session_start();
include '../conexion.php';

// Seguridad: Solo admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$mensaje = "";

// Verificar si se pasó un ID por la URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: admin_productos.php");
    exit();
}

$id_producto = $_GET['id'];

// Obtener los datos actuales del producto
$stmt = $conexion->prepare("SELECT * FROM productos WHERE id = ?");
$stmt->execute([$id_producto]);
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$producto) {
    header("Location: admin_productos.php");
    exit();
}

// Obtener categorías para el selector
$categorias = $conexion->query("SELECT * FROM categorias ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);

// PROCESAR LA ACTUALIZACIÓN
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['actualizar_producto'])) {
    $nombre = trim($_POST['nombre']);
    $marca = !empty($_POST['marca']) ? trim($_POST['marca']) : null;
    $descripcion = trim($_POST['descripcion']);
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $stock_minimo = $_POST['stock_minimo'];
    $categoria_id = $_POST['categoria_id'];
    
    $en_oferta = isset($_POST['en_oferta']) ? 1 : 0;
    $precio_oferta = !empty($_POST['precio_oferta']) ? $_POST['precio_oferta'] : null;

    // Manejo de la nueva imagen (si se subió una)
    $imagen_nombre = $producto['imagen']; // Mantener la imagen actual por defecto
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $carpeta_destino = '../imagenes/productos/';
        if (!file_exists($carpeta_destino)) {
            mkdir($carpeta_destino, 0777, true);
        }

        $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $nueva_imagen = uniqid('prod_') . '.' . $ext;
        $ruta_destino = $carpeta_destino . $nueva_imagen;
        
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_destino)) {
            // Opcional: Borrar la imagen vieja si existe
            if (!empty($producto['imagen']) && file_exists($carpeta_destino . $producto['imagen'])) {
                unlink($carpeta_destino . $producto['imagen']);
            }
            $imagen_nombre = $nueva_imagen;
        }
    }

    try {
        $sql = "UPDATE productos SET categoria_id = ?, nombre = ?, marca = ?, descripcion = ?, precio = ?, stock = ?, stock_minimo = ?, imagen = ?, en_oferta = ?, precio_oferta = ? WHERE id = ?";
        $stmt_update = $conexion->prepare($sql);
        
        if ($stmt_update->execute([$categoria_id, $nombre, $marca, $descripcion, $precio, $stock, $stock_minimo, $imagen_nombre, $en_oferta, $precio_oferta, $id_producto])) {
            $mensaje = "¡Producto actualizado con éxito!";
            // Recargar los datos del producto para que se vean reflejados en el formulario
            $stmt = $conexion->prepare("SELECT * FROM productos WHERE id = ?");
            $stmt->execute([$id_producto]);
            $producto = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $mensaje = "Error al actualizar el producto.";
        }
    } catch (PDOException $e) {
        $mensaje = "Error de Base de Datos: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Producto - Tienda Perrito</title>
    <link rel="stylesheet" href="../css/style_admin.css">
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f9f9f9; }
        .container { max-width: 800px; margin: auto; background: white; padding: 25px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .form-group-checkbox { display: flex; align-items: center; gap: 8px; margin-top: 25px; }
        .form-group-checkbox input { width: 20px; height: 20px; }
        .btn { background: #007bff; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; }
        .btn-volver { background: #6c757d; color: white; padding: 8px 12px; text-decoration: none; border-radius: 4px; display: inline-block; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <a href="admin_productos.php" class="btn-volver">⬅ Volver a Productos</a>
        <h2>Editar Producto: <?php echo htmlspecialchars($producto['nombre']); ?></h2>

        <?php if (!empty($mensaje)): ?>
            <p style="color: green; font-weight: bold;"><?php echo $mensaje; ?></p>
        <?php endif; ?>

        <form action="admin_editar_producto.php?id=<?php echo $id_producto; ?>" method="POST" enctype="multipart/form-data">
            <div class="form-grid">
                <div class="form-group">
                    <label>Nombre del Producto:</label>
                    <input type="text" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Marca (Opcional):</label>
                    <input type="text" name="marca" value="<?php echo htmlspecialchars($producto['marca'] ?? ''); ?>" placeholder="Ej. Pedigree, Kong...">
                </div>
                <div class="form-group">
                    <label>Categoría:</label>
                    <select name="categoria_id" required>
                        <option value="">Selecciona una categoría</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo ($producto['categoria_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Precio Regular ($):</label>
                    <input type="number" step="0.01" name="precio" value="<?php echo $producto['precio']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Precio Oferta (Opcional):</label>
                    <input type="number" step="0.01" name="precio_oferta" value="<?php echo $producto['precio_oferta']; ?>">
                </div>
                <div class="form-group">
                    <label>Stock Disponible (Cantidad):</label>
                    <input type="number" name="stock" value="<?php echo $producto['stock']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Stock Mínimo (Alerta):</label>
                    <input type="number" name="stock_minimo" value="<?php echo $producto['stock_minimo']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Imagen Actual:</label>
                    <?php if (!empty($producto['imagen'])): ?>
                        <br><img src="../imagenes/productos/<?php echo $producto['imagen']; ?>" width="60" style="border-radius: 4px; margin-bottom: 5px;">
                    <?php else: ?>
                        <br><span>Sin imagen</span>
                    <?php endif; ?>
                    <input type="file" name="imagen" accept="image/*" style="margin-top: 5px;">
                    <small style="color: #666; display:block;">(Deja este campo vacío si no quieres cambiar la imagen)</small>
                </div>
                <div class="form-group form-group-checkbox" style="grid-column: span 2;">
                    <input type="checkbox" name="en_oferta" id="en_oferta" value="1" <?php echo ($producto['en_oferta'] == 1) ? 'checked' : ''; ?>>
                    <label for="en_oferta" style="margin-bottom:0; cursor:pointer;">¿Mostrar en Carrusel Home? 🚀</label>
                </div>
            </div>
            <div class="form-group" style="margin-top: 15px;">
                <label>Descripción:</label>
                <textarea name="descripcion" rows="3"><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
            </div>
            <button type="submit" name="actualizar_producto" class="btn">Guardar Cambios</button>
        </form>
    </div>
</body>
</html>