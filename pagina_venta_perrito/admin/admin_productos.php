<?php
session_start();
include '../conexion.php';

// Seguridad: Solo admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$mensaje = "";
$mensaje_cat = "";

// 1. PROCESAR EL FORMULARIO DE NUEVO PRODUCTO
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agregar_producto'])) {
    $nombre = trim($_POST['nombre']);
    $marca = !empty($_POST['marca']) ? trim($_POST['marca']) : null;
    $descripcion = trim($_POST['descripcion']);
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $stock_minimo = $_POST['stock_minimo'];
    $categoria_id = $_POST['categoria_id'];
    
    // Campos para ofertas
    $en_oferta = isset($_POST['en_oferta']) ? 1 : 0;
    $precio_oferta = !empty($_POST['precio_oferta']) ? $_POST['precio_oferta'] : null;

    // Manejo de la imagen
    $imagen_nombre = "";
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $carpeta_destino = '../imagenes/productos/';
        if (!file_exists($carpeta_destino)) {
            mkdir($carpeta_destino, 0777, true);
        }

        $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $imagen_nombre = uniqid('prod_') . '.' . $ext;
        $ruta_destino = $carpeta_destino . $imagen_nombre;
        
        move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_destino);
    }

    // Insertar en la BD
    try {
        $sql = "INSERT INTO productos (categoria_id, nombre, marca, descripcion, precio, stock, stock_minimo, imagen, en_oferta, precio_oferta) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        if ($stmt->execute([$categoria_id, $nombre, $marca, $descripcion, $precio, $stock, $stock_minimo, $imagen_nombre, $en_oferta, $precio_oferta])) {
            $mensaje = "¡Producto agregado con éxito!";
        } else {
            $mensaje = "Error al agregar el producto.";
        }
    } catch (PDOException $e) {
        $mensaje = "Error de Base de Datos: " . $e->getMessage();
    }
}

// 2. PROCESAR EL FORMULARIO DE NUEVA CATEGORÍA
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agregar_categoria'])) {
    $nombre_cat = trim($_POST['nombre_categoria']);

    if (!empty($nombre_cat)) {
        try {
            $sql_cat = "INSERT INTO categorias (nombre) VALUES (?)";
            $stmt_cat = $conexion->prepare($sql_cat);
            if ($stmt_cat->execute([$nombre_cat])) {
                $mensaje_cat = "¡Categoría creada con éxito!";
            }
        } catch (PDOException $e) {
            $mensaje_cat = "Error: La categoría ya existe o hay un problema.";
        }
    } else {
        $mensaje_cat = "El nombre de la categoría no puede estar vacío.";
    }
}

// 3. ELIMINAR PRODUCTO
if (isset($_GET['eliminar'])) {
    $id_eliminar = $_GET['eliminar'];
    
    // Opcional: borrar archivo físico de imagen si existe
    $stmt_img = $conexion->prepare("SELECT imagen FROM productos WHERE id = ?");
    $stmt_img->execute([$id_eliminar]);
    $prod_img = $stmt_img->fetch(PDO::FETCH_ASSOC);
    if ($prod_img && !empty($prod_img['imagen'])) {
        $ruta_img = '../imagenes/productos/' . $prod_img['imagen'];
        if (file_exists($ruta_img)) {
            unlink($ruta_img);
        }
    }

    $del_sql = "DELETE FROM productos WHERE id = ?";
    $del_stmt = $conexion->prepare($del_sql);
    $del_stmt->execute([$id_eliminar]);
    header("Location: admin_productos.php");
    exit();
}

// Obtener categorías actualizadas
$categorias = $conexion->query("SELECT * FROM categorias ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);

// Obtener lista de productos
$productos = $conexion->query("SELECT p.*, c.nombre AS categoria_nombre FROM productos p LEFT JOIN categorias c ON p.categoria_id = c.id ORDER BY p.id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar Productos y Categorías - Tienda Perrito</title>
    <link rel="stylesheet" href="../css/style_admin.css">
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f9f9f9; }
        .container { max-width: 1150px; margin: auto; background: white; padding: 25px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #333; color: white; }
        .alerta-stock { background-color: #ffcccc; color: #990000; padding: 3px 6px; border-radius: 4px; font-weight: bold; }
        .badge-oferta { background-color: #ff9800; color: white; padding: 3px 6px; border-radius: 4px; font-size: 0.85rem; font-weight: bold; }
        .badge-marca { background-color: #6c757d; color: white; padding: 2px 6px; border-radius: 3px; font-size: 0.8rem; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .form-group-checkbox { display: flex; align-items: center; gap: 8px; margin-top: 25px; }
        .form-group-checkbox input { width: 20px; height: 20px; }
        .btn { background: #28a745; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; }
        .btn-secundario { background: #007bff; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; }
        .btn-editar { background: #ffc107; padding: 5px 10px; color: #000; text-decoration: none; border-radius: 3px; font-weight: bold; margin-right: 5px; }
        .btn-danger { background: #dc3545; padding: 5px 10px; color: white; text-decoration: none; border-radius: 3px; }
        .secciones-superiores { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; }
        @media(max-width: 768px) { .secciones-superiores { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <div class="container">
        <a href="admin.php">⬅ Volver al Panel Principal</a>
        <h2>Gestión de Productos, Inventario y Categorías</h2>

        <?php if (!empty($mensaje)): ?>
            <p style="color: green; font-weight: bold;"><?php echo $mensaje; ?></p>
        <?php endif; ?>
        <?php if (!empty($mensaje_cat)): ?>
            <p style="color: blue; font-weight: bold;"><?php echo $mensaje_cat; ?></p>
        <?php endif; ?>

        <div class="secciones-superiores">
            <!-- Formulario Producto -->
            <fieldset style="border: 1px solid #ccc; padding: 15px; border-radius: 6px; margin-bottom: 30px;">
                <legend><b>Agregar Nuevo Producto</b></legend>
                <form action="admin_productos.php" method="POST" enctype="multipart/form-data">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Nombre del Producto:</label>
                            <input type="text" name="nombre" required>
                        </div>
                        <div class="form-group">
                            <label>Marca (Opcional):</label>
                            <input type="text" name="marca" placeholder="Ej. Pedigree, Kong, Purina...">
                        </div>
                        <div class="form-group">
                            <label>Categoría:</label>
                            <select name="categoria_id" required>
                                <option value="">Selecciona una categoría</option>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['nombre']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Precio Regular ($):</label>
                            <input type="number" step="0.01" name="precio" required>
                        </div>
                        <div class="form-group">
                            <label>Precio Oferta (Opcional):</label>
                            <input type="number" step="0.01" name="precio_oferta">
                        </div>
                        <div class="form-group">
                            <label>Stock Inicial (Cantidad):</label>
                            <input type="number" name="stock" required>
                        </div>
                        <div class="form-group">
                            <label>Stock Mínimo (Alerta):</label>
                            <input type="number" name="stock_minimo" value="5" required>
                        </div>
                        <div class="form-group">
                            <label>Imagen del Producto:</label>
                            <input type="file" name="imagen" accept="image/*" required>
                        </div>
                        <div class="form-group form-group-checkbox" style="grid-column: span 2;">
                            <input type="checkbox" name="en_oferta" id="en_oferta" value="1">
                            <label for="en_oferta" style="margin-bottom:0; cursor:pointer;">¿Mostrar en Carrusel Home? 🚀</label>
                        </div>
                    </div>
                    <div class="form-group" style="margin-top: 15px;">
                        <label>Descripción:</label>
                        <textarea name="descripcion" rows="2"></textarea>
                    </div>
                    <button type="submit" name="agregar_producto" class="btn">Guardar Producto</button>
                </form>
            </fieldset>

            <!-- Formulario Categoría -->
            <fieldset style="border: 1px solid #ccc; padding: 15px; border-radius: 6px; margin-bottom: 30px; background: #fafafa;">
                <legend><b>Añadir Categoría Rápida</b></legend>
                <form action="admin_productos.php" method="POST">
                    <div class="form-group">
                        <label>Nombre de la Categoría:</label>
                        <input type="text" name="nombre_categoria" required placeholder="Ej. Juguetes, Ofertas...">
                    </div>
                    <button type="submit" name="agregar_categoria" class="btn-secundario">Crear Categoría</button>
                </form>

                <hr style="margin: 15px 0; border:0; border-top:1px solid #ddd;">
                
                <p style="font-size: 0.85rem; color: #555; margin-bottom: 5px;"><b>Categorías existentes:</b></p>
                <div style="max-height: 120px; overflow-y: auto; font-size: 0.9rem;">
                    <ul style="padding-left: 15px; margin: 0;">
                        <?php foreach ($categorias as $c): ?>
                            <li><?php echo htmlspecialchars($c['nombre']); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </fieldset>
        </div>

        <!-- Tabla -->
        <h3>Inventario Actual y Alertas de Stock</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Imagen</th>
                    <th>Producto / Marca</th>
                    <th>Categoría</th>
                    <th>Precios</th>
                    <th>Estado / Oferta</th>
                    <th>Stock Disponible</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $prod): ?>
                <tr>
                    <td><?php echo $prod['id']; ?></td>
                    <td>
                        <?php if ($prod['imagen']): ?>
                            <img src="../imagenes/productos/<?php echo $prod['imagen']; ?>" width="50" style="border-radius: 4px;">
                        <?php else: ?>
                            Sin imagen
                        <?php endif; ?>
                    </td>
                    <td>
                        <b><?php echo htmlspecialchars($prod['nombre']); ?></b>
                        <?php if (!empty($prod['marca'])): ?>
                            <br><span class="badge-marca">Marca: <?php echo htmlspecialchars($prod['marca']); ?></span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($prod['categoria_nombre'] ?? 'Sin categoría'); ?></td>
                    <td>
                        Regular: $<?php echo number_format($prod['precio'], 2); ?>
                        <?php if (!empty($prod['precio_oferta'])): ?>
                            <br><span style="color: #e91e63; font-weight:bold;">Oferta: $<?php echo number_format($prod['precio_oferta'], 2); ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($prod['en_oferta'] == 1): ?>
                            <span class="badge-oferta">⭐ En Carrusel</span>
                        <?php else: ?>
                            Normal
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($prod['stock'] == 0): ?>
                            <span class="alerta-stock" style="background:#000; color:#fff;">¡Agotado!</span>
                        <?php elseif ($prod['stock'] <= $prod['stock_minimo']): ?>
                            <span class="alerta-stock">¡Últimas unidades! (<?php echo $prod['stock']; ?>)</span>
                        <?php else: ?>
                            <?php echo $prod['stock']; ?> un.
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="admin_editar_producto.php?id=<?php echo $prod['id']; ?>" class="btn-editar">Editar</a>
                        <a href="admin_productos.php?eliminar=<?php echo $prod['id']; ?>" class="btn-danger" onclick="return confirm('¿Estás seguro de eliminar este producto?');">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>