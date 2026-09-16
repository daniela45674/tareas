<?php
// Configuración de la conexión a la base de datos
include 'conexion.php';

$productos_db = [];

try {
    $conexion = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Consulta para traer los productos y el nombre de su categoría
    $sql = "SELECT p.id AS producto_id, p.nombre, p.precio, p.descripcion, p.imagen, c.nombre AS categoria 
            FROM productos p 
            LEFT JOIN categorias c ON p.categoria_id = c.id 
            ORDER BY p.id DESC";
    
    $stmt = $conexion->query($sql);
    $productos_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Si hay error de conexión
    $productos_db = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patitas Felices - Productos</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/style_productos.css">
    <link rel="stylesheet" href="css/style_footer.css">
</head>
<body>

<header class="header">
    <div class="logo">
        <img src="imagenes/otras_imagenes/logo2.jpg" alt="Logo Patitas Felices">
        <span>Patitas Felices</span>
    </div>

    <!-- BARRA DE BÚSQUEDA -->
    <div class="buscador-container">
        <input type="text" id="inputBuscar" placeholder="buscar..." class="input-buscar" oninput="filtrarProductos()">
    </div>

    <div id="hamburger" class="hamburger">
        <span></span>
        <span></span>
        <span></span>
    </div>
    
    <nav id="menu" class="menu">
        <ul>
            <li><a href="home.php">Inicio</a></li>
            <li><a href="productos.php" style="margin-left: 20px; text-decoration: none; color: #e11d48; font-weight: bold;">Productos</a></li>
            <li>
                <a href="carrito.php">
                    <img src="imagenes/otras_imagenes/carrito.png" class="icono-carrito" alt="Carrito">
                </a>
            </li>
            <li><a href="nosotros.php">Encuéntranos</a></li>
        </ul>
    </nav>
</header>

<section class="productos-section">
    <h2 class="titulo-productos">Nuestros Productos</h2>

    <!-- BARRA SUPERIOR: FILTRO Y MONEDAS -->
    <div class="barra-filtros-moneda">
        <!-- SELECTOR DE CATEGORÍAS (Usamos valores genéricos en minúscula para matchear con JS) -->
        <div class="filtro-categorias">
            <select id="selectCategoria" onchange="filtrarProductos()">
                <option value="TODAS">Todas las categorías</option>
                <option value="accesorios">Accesorios</option>
                <option value="camas">Camas</option>
                <option value="comida">Comida</option>
                <option value="juguetes">Juguetes</option>
                <option value="ropa">Ropa</option>
                <option value="conejos">Conejos / Pequeñas Mascotas</option>
            </select>
        </div>

        <!-- BOTONES Y DESPLEGABLE DE MONEDAS -->
        <div class="botones-moneda" style="display: flex; align-items: center; gap: 10px; flex-wrap:">
            <button onclick="cambiarMoneda('PEN', this)" class="btn-moneda active" id="btnPEN">Soles</button>
            <button onclick="cambiarMoneda('USD', this)" class="btn-moneda" id="btnUSD">Dólares</button>
            <button onclick="cambiarMoneda('EUR', this)" class="btn-moneda" id="btnEUR">Euros</button>
            
            <select id="selectOtrasMonedas" class="select-otras-monedas" onchange="cambiarMonedaOtras(this)" style="padding: 8px 15px; border-radius: 50px; border: 2px solid #ddd; background-color: #fff; font-size: 0.9rem; cursor: pointer; outline: none;">
                <option value="" disabled selected>otras monedas</option>
                <option value="COP">Peso Colombiano (COP)</option>
                <option value="MXN">Peso Mexicano (MXN)</option>
                <option value="ARS">Peso Argentino (ARS)</option>
                <option value="CLP">Peso Chileno (CLP)</option>
                <option value="BRL">Real Brasileño (BRL)</option>
                <option value="BOB">Boliviano (BOB)</option>
                <option value="UYU">Peso Uruguayo (UYU)</option>
                <option value="PYG">Guaraní Paraguayo (PYG)</option>
                <option value="VES">Bolívar Venezolano (VES)</option>
                <option value="CRC">Colón Costarricense (CRC)</option>
                <option value="DOP">Peso Dominicano (DOP)</option>
                <option value="GTQ">Quetzal Guatemalteco (GTQ)</option>
                <option value="HNL">Lempira Hondureño (HNL)</option>
                <option value="NIO">Córdoba Nicaragüense (NIO)</option>
                <option value="PAB">Balboa Panameño (PAB)</option>
                <option value="GBP">Libra Esterlina (GBP)</option>
                <option value="CAD">Dólar Canadiense (CAD)</option>
                <option value="AUD">Dólar Australiano (AUD)</option>
                <option value="JPY">Yen Japonés (JPY)</option>
                <option value="CNY">Yuan Chino (CNY)</option>
                <option value="CHF">Franco Suizo (CHF)</option>
            </select>
        </div>
    </div>

    <!-- GRID DE PRODUCTOS -->
    <div class="grid-productos" id="gridProductos">
        <!-- Los productos se cargarán dinámicamente con JavaScript -->
    </div>
</section>

<!-- FOOTER -->
<footer class="footer">
    <div class="footer-contenido">
        <div class="footer-col">
            <img src="imagenes/otras_imagenes/logo2.jpg" class="footer-logo" alt="Logo Patitas Felices">
            <p>&copy; <?php echo date("Y"); ?> Patitas Felices</p> 
        </div>
        <div class="footer-col">
            <h3>Contacto</h3>
            <p>Email: patitasfelices@gmail.com</p>
            <p>Teléfono: 923258007</p>
            <p>Dirección: Av. Principal #123</p>
        </div>
        <div class="footer-col">
            <h3>Enlaces</h3>
            <ul class="footer-links">
                <li><a href="home.php">Inicio</a></li>
                <li><a href="productos.php">Productos</a></li>
                <li><a href="carrito.php">Carrito</a></li>
                <li><a href="nosotros.php">Encuéntranos</a></li>
            </ul>
        </div>
        <div class="footer-col redes-sociales-col">
            <h3>Síguenos</h3>
            <div class="redes-iconos">
                <a href="https://www.facebook.com" target="_blank"><img src="imagenes/otras_imagenes/faceboock.png" alt="Facebook"> Facebook</a>
                <a href="https://www.instagram.com" target="_blank"><img src="imagenes/otras_imagenes/instagran.png" alt="Instagram"> Instagram</a>
                <a href="https://www.twitter.com" target="_blank"><img src="imagenes/otras_imagenes/x.png" alt="Twitter"> Twitter</a>
            </div>
        </div>
    </div>
    <div class="copyright-mobile">
        <p>&copy; <?php echo date("Y"); ?> Patitas Felices</p>
    </div>
</footer>

<script>
    let monedaActual = 'PEN';
    let simboloMoneda = 'S/ ';
    
    const tasasCambio = {
        PEN: 1.00, USD: 0.27, EUR: 0.25, COP: 1100.50, MXN: 4.65,
        ARS: 280.00, CLP: 250.00, BRL: 1.35, BOB: 1.85, UYU: 10.50,
        PYG: 2000.00, VES: 9.80, CRC: 140.00, DOP: 15.80, GTQ: 2.10,
        HNL: 6.70, NIO: 9.90, PAB: 0.27, GBP: 0.21, CAD: 0.37,
        AUD: 0.41, JPY: 41.50, CNY: 1.95, CHF: 0.24
    };

    const simbolos = {
        PEN: 'S/ ', USD: '$ ', EUR: '€ ', COP: '$ ', MXN: '$ ',
        ARS: '$ ', CLP: '$ ', BRL: 'R$ ', BOB: 'Bs. ', UYU: '$U ',
        PYG: '₲ ', VES: 'Bs.S ', CRC: '₡ ', DOP: 'RD$ ', GTQ: 'Q ',
        HNL: 'L ', NIO: 'C$ ', PAB: 'B/. ', GBP: '£ ', CAD: 'CA$ ',
        AUD: 'AU$ ', JPY: '¥ ', CNY: 'CN¥ ', CHF: 'CHF '
    };

    // Inyectamos de forma segura los productos desde PHP hacia JavaScript
    const listaProductos = <?php echo json_encode($productos_db, JSON_UNESCAPED_UNICODE); ?>;

    document.addEventListener("DOMContentLoaded", () => {
        console.log("Productos cargados desde PHP:", listaProductos); // Útil para depurar en la consola del navegador
        
        const urlParams = new URLSearchParams(window.location.search);
        const query = urlParams.get("busqueda");
        
        if (query && document.getElementById("inputBuscar")) {
            document.getElementById("inputBuscar").value = query;
        }
        
        filtrarProductos();
    });

    function cambiarMoneda(moneda, elementoBtn) {
        monedaActual = moneda;
        simboloMoneda = simbolos[moneda];

        document.querySelectorAll('.btn-moneda').forEach(btn => btn.classList.remove('active'));
        if (elementoBtn) elementoBtn.classList.add('active');

        const selectOtras = document.getElementById("selectOtrasMonedas");
        if (selectOtras) selectOtras.selectedIndex = 0;

        filtrarProductos();
    }

    function cambiarMonedaOtras(selectElement) {
        const moneda = selectElement.value;
        if (!moneda) return;

        monedaActual = moneda;
        simboloMoneda = simbolos[moneda];

        document.querySelectorAll('.btn-moneda').forEach(btn => btn.classList.remove('active'));
        filtrarProductos();
    }

    function filtrarProductos() {
        const categoriaSeleccionada = document.getElementById("selectCategoria").value.toLowerCase();
        const inputBuscar = document.getElementById("inputBuscar");
        const query = inputBuscar ? inputBuscar.value.toLowerCase().trim() : "";

        let productosFiltrados = listaProductos;

        // 1. Filtrar por categoría (si no es TODAS)
        if (categoriaSeleccionada !== "todas") {
            productosFiltrados = productosFiltrados.filter(p => {
                return p.categoria && p.categoria.toLowerCase() === categoriaSeleccionada;
            });
        }

        // 2. Filtrar por texto de búsqueda
        if (query !== "") {
            productosFiltrados = productosFiltrados.filter(p => 
                (p.nombre && p.nombre.toLowerCase().includes(query)) || 
                (p.descripcion && p.descripcion.toLowerCase().includes(query))
            );
        }

        mostrarProductos(productosFiltrados);
    }

    function mostrarProductos(productos) {
        const grid = document.getElementById("gridProductos");
        grid.innerHTML = "";

        if (productos.length === 0) {
            grid.innerHTML = `<p style="grid-column: 1/-1; text-align:center; color:#666; padding: 20px;">No hay productos disponibles o que coincidan con tu búsqueda.</p>`;
            return;
        }

        productos.forEach(p => {
            const tasa = tasasCambio[monedaActual] || 1;
            const precioConvertido = (parseFloat(p.precio) * tasa).toFixed(2);
            
            // Ruta de la imagen considerando que la carpeta imagenes está en la raíz
            const rutaImagen = p.imagen ? `imagenes/productos/${p.imagen}` : `imagenes/otras_imagenes/logo2.jpg`;

            let card = document.createElement("div");
            card.className = "card-producto";
            card.innerHTML = `
                <img src="${rutaImagen}" alt="${p.nombre}">
                <h3>${p.nombre}</h3>
                <p class="precio">${simboloMoneda}${precioConvertido}</p>
                <p class="descripcion">${p.descripcion ? p.descripcion : ''}</p>
                <button onclick="agregarAlCarrito(${p.producto_id}, '${p.nombre.replace(/'/g, "\\'")}', ${p.precio}, '${rutaImagen}')" class="btn-carrito">🛒</button>
            `;
            grid.appendChild(card);
        });
    }

    function agregarAlCarrito(id, nombre, precio, imagen) {
        let carrito = JSON.parse(localStorage.getItem('carrito')) || [];
        
        let existente = carrito.find(item => item.id === id);
        if (existente) {
            existente.cantidad += 1;
        } else {
            carrito.push({
                id: id,
                nombre: nombre,
                precio: precio,
                imagen: imagen,
                cantidad: 1
            });
        }

        localStorage.setItem('carrito', JSON.stringify(carrito));
        mostrarMensaje("Producto agregado 🐾");
    }

    function mostrarMensaje(texto) {
        const aviso = document.createElement("div");
        aviso.className = "mensaje-carrito";
        aviso.innerText = texto;
        aviso.style.cssText = "position: fixed; bottom: 20px; right: 20px; background: #333; color: #fff; padding: 12px 20px; border-radius: 8px; z-index: 1000; box-shadow: 0 4px 6px rgba(0,0,0,0.1);";

        document.body.appendChild(aviso);
        setTimeout(() => aviso.remove(), 2000);
    }
</script>
<script src="js/script.js"></script>

</body>
</html>