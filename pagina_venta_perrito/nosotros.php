<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patitas Felices - Encuéntranos</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/style_footer.css">
    <link rel="stylesheet" href="css/style_nosotros.css">
</head>
<body>

<!-- HEADER -->
<header class="header">
    <div class="logo">
        <img src="imagenes/otras_imagenes/logo2.jpg" alt="Logo Patitas Felices">
        <span>Patitas Felices</span>
    </div>

    <div id="hamburger" class="hamburger">
        <span></span>
        <span></span>
        <span></span>
    </div>

    <nav id="menu" class="menu">
        <ul>
            <li><a href="home.php">Inicio</a></li>
            <li><a href="productos.php">Productos</a></li>
            <li>
                <a href="carrito.php">
                    <img src="imagenes/otras_imagenes/carrito.png" class="icono-carrito" alt="Carrito">
                </a>
            </li>
            <li><a href="nosotros.php" style="color: #e11d48; font-weight: bold;">Encuéntranos</a></li>
        </ul>
    </nav>
</header>

<div class="seccion-contacto">
    <h2>Encuéntranos</h2>
    
    <div class="info-contacto">
        <div class="info-item">
            <span>📍</span>
            <div>
                <div>av. dos de mayo 311, arequipa 04406</div>
                <small>Dirección</small>
            </div>
        </div>

        <div class="info-item">
            <span>📞</span>
            <div>
                <div><a href="tel:+51901023589">+51 901023589</a></div>
                <small>Teléfono</small>
            </div>
        </div>

        <div class="info-item">
            <span>✉</span>
            <div>
                <div><a href="mailto:perritosfelices@gmail.com">perritosfelices@gmail.com</a></div>
                <small>Correo electrónico</small>
            </div>
        </div>
    </div>
    
    <!-- Mapa incrustado -->
    <div class="mapa-container">
        <iframe 
            src="https://www.google.com/maps?q=-16.409047,-71.537451&output=embed"
            allowfullscreen
            loading="lazy">
        </iframe>
    </div>

    <div class="meta-row">
        <a href="https://maps.app.goo.gl/crPzDUUfpUiAGFoq6" target="_blank" rel="noopener noreferrer" class="enlace-mapa">
            🗺 Abrir en Google Maps
        </a>

        <a href="https://www.google.com/maps/dir/?api=1&destination=-16.409047,-71.537451" 
           target="_blank" rel="noopener noreferrer" 
           class="enlace-mapa" 
           style="background:#2E7D32;">
            ➜ Cómo llegar
        </a>
    </div>

</div>

<!-- SEO local -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "LocalBusiness",
    "name": "Perritos Felices",
    "telephone": "+51 960319422",
    "email": "perritosfelices@gmail.com",
    "address": {
        "@type": "PostalAddress",
        "streetAddress": "av. dos de mayo 311, arequipa 04406",
        "addressLocality": "Arequipa",
        "addressRegion": "Arequipa",
        "postalCode": "04406",
        "addressCountry": "PE"
    },
    "geo": {
        "@type": "GeoCoordinates",
        "latitude": -16.409047,
        "longitude": -71.537451
    },
    "url": "https://maps.app.goo.gl/crPzDUUfpUiAGFoq6"
}
</script>

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
                <a href="https://www.facebook.com/tucuennnnta" target="_blank">
                    <img src="imagenes/otras_imagenes/faceboock.png" alt="Facebook"> Facebook
                </a>
                <a href="https://www.instagram.com/tucuennnnta" target="_blank">
                    <img src="imagenes/otras_imagenes/instagran.png" alt="Instagram"> Instagram
                </a>
                <a href="https://www.twitter.com/tucuennnnta" target="_blank">
                    <img src="imagenes/otras_imagenes/x.png" alt="Twitter"> Twitter
                </a>
            </div>
        </div>

    </div>
    
    <div class="copyright-mobile">
        <p>&copy; <?php echo date("Y"); ?> Patitas Felices</p>
    </div>
</footer>

</body>
</html>