// ==========================================
// 1. MENÚ HAMBURGUESA
// ==========================================
const hamburger = document.getElementById('hamburger');
const menu = document.getElementById('menu');

if (hamburger && menu) {
    hamburger.addEventListener('click', function() {
        menu.classList.toggle('activo');
        menu.classList.toggle('mobile');
        hamburger.classList.toggle('active');
    });

    const menuLinks = menu.querySelectorAll('a');
    menuLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 900) {
                menu.classList.remove('activo', 'mobile');
                hamburger.classList.remove('active');
            }
        });
    });

    document.addEventListener('click', function(event) {
        const isClickInsideMenu = menu.contains(event.target);
        const isClickOnHamburger = hamburger.contains(event.target);
        
        if (!isClickInsideMenu && !isClickOnHamburger && menu.classList.contains('activo')) {
            menu.classList.remove('activo', 'mobile');
            hamburger.classList.remove('active');
        }
    });
}

// ==========================================
// 2. CARRUSEL PRINCIPAL
// ==========================================
document.addEventListener("DOMContentLoaded", () => {
    const items = document.querySelectorAll(".carousel-item");
    const indicators = document.querySelectorAll(".carousel-indicators .indicator");
    const nextBtn = document.getElementById("nextSlideBtn");
    const prevBtn = document.getElementById("prevSlideBtn");
    
    if (items.length > 0) {
        let currentIndex = 0;
        let autoSlideInterval;

        function showSlide(index) {
            if (index >= items.length) {
                currentIndex = 0;
            } else if (index < 0) {
                currentIndex = items.length - 1;
            } else {
                currentIndex = index;
            }

            items.forEach((item, i) => {
                item.classList.toggle("active", i === currentIndex);
            });

            indicators.forEach((ind, i) => {
                ind.classList.toggle("active", i === currentIndex);
            });
        }

        function nextSlide() {
            showSlide(currentIndex + 1);
        }

        function prevSlide() {
            showSlide(currentIndex - 1);
        }

        if (nextBtn) nextBtn.addEventListener("click", () => {
            nextSlide();
            resetTimer();
        });

        if (prevBtn) prevBtn.addEventListener("click", () => {
            prevSlide();
            resetTimer();
        });

        indicators.forEach((ind) => {
            ind.addEventListener("click", (e) => {
                const index = parseInt(e.target.getAttribute("data-index"));
                showSlide(index);
                resetTimer();
            });
        });

        function startTimer() {
            autoSlideInterval = setInterval(nextSlide, 5000);
        }

        function resetTimer() {
            clearInterval(autoSlideInterval);
            startTimer();
        }

        const carouselContainer = document.querySelector(".carousel");
        if (carouselContainer) {
            carouselContainer.addEventListener("mouseenter", () => clearInterval(autoSlideInterval));
            carouselContainer.addEventListener("mouseleave", startTimer);
        }

        startTimer();
    }
});

// ==========================================
// 3. CARRUSEL MULTIIMAGEN (OFERTAS)
// ==========================================
const contenedorMulti = document.getElementById("contenedorMulti");
if (contenedorMulti) {
    let indexMulti = 0;
    const slidesMulti = document.querySelectorAll(".slide-multi");
    const totalSlides = slidesMulti.length;

    function getSlidesVisibles() {
        const width = window.innerWidth;
        if (width > 980) {
            return 3;
        } else if (width > 620) {
            return 2;
        } else {
            return 1;
        }
    }

    function actualizarMulti() {
        const visibles = getSlidesVisibles();
        const maxIndex = totalSlides - visibles;
        
        if (indexMulti > maxIndex) {
            indexMulti = 0;
        } else if (indexMulti < 0) {
            indexMulti = maxIndex;
        }

        const desplazamiento = indexMulti * (100 / visibles);
        contenedorMulti.style.transform = `translateX(-${desplazamiento}%)`;
    }

    const nextBtnMulti = document.getElementById("next");
    const prevBtnMulti = document.getElementById("prev");

    if (nextBtnMulti) {
        nextBtnMulti.addEventListener("click", () => {
            const visibles = getSlidesVisibles();
            const maxIndex = totalSlides - visibles;
            indexMulti++;
            if (indexMulti > maxIndex) {
                indexMulti = 0;
            }
            actualizarMulti();
            reiniciarAutoplay();
        });
    }

    if (prevBtnMulti) {
        prevBtnMulti.addEventListener("click", () => {
            const visibles = getSlidesVisibles();
            const maxIndex = totalSlides - visibles;
            indexMulti--;
            if (indexMulti < 0) {
                indexMulti = maxIndex;
            }
            actualizarMulti();
            reiniciarAutoplay();
        });
    }

    let autoplayInterval = setInterval(() => {
        const visibles = getSlidesVisibles();
        const maxIndex = totalSlides - visibles;
        indexMulti++;
        if (indexMulti > maxIndex) {
            indexMulti = 0;
        }
        actualizarMulti();
    }, 4000);

    function reiniciarAutoplay() {
        clearInterval(autoplayInterval);
        autoplayInterval = setInterval(() => {
            const visibles = getSlidesVisibles();
            const maxIndex = totalSlides - visibles;
            indexMulti++;
            if (indexMulti > maxIndex) {
                indexMulti = 0;
            }
            actualizarMulti();
        }, 4000);
    }

    contenedorMulti.addEventListener("mouseenter", () => clearInterval(autoplayInterval));
    contenedorMulti.addEventListener("mouseleave", () => reiniciarAutoplay());

    let resizeTimer;
    window.addEventListener("resize", () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            actualizarMulti();
        }, 250);
    });

    actualizarMulti();
}

// ==========================================
// 4. API DE TIPO DE CAMBIO (MONEDAS)
// ==========================================
const API_TIPO_CAMBIO = "https://open.er-api.com/v6/latest/PEN";
let tasasCambio = {};
let monedaActual = 'PEN'; // Soles por defecto

// Cargar tasas al iniciar la página
document.addEventListener("DOMContentLoaded", async () => {
    try {
        let respuesta = await fetch(API_TIPO_CAMBIO);
        let datos = await respuesta.json();
        if (datos.result === "success") {
            tasasCambio = datos.rates;
        }
    } catch (error) {
        console.error("Error al obtener tipo de cambio:", error);
    }
});

// Función que se activa al hacer clic en los botones de moneda
function cambiarMoneda(moneda) {
    monedaActual = moneda;
    
   
    document.querySelectorAll('.btn-moneda').forEach(b => b.classList.remove('active'));
    let btnActivo = document.getElementById(`btn${moneda}`);
    if (btnActivo) btnActivo.classList.add('active');

    
    if (typeof listaProductos !== 'undefined' && typeof mostrarProductos === 'function') {
        mostrarProductos(listaProductos);
    }
}

// Función para calcular y formatear el precio según la moneda activa
function formatearPrecio(precioEnSoles) {
    let tasa = tasasCambio[monedaActual] || 1;
    let precioConvertido = precioEnSoles * tasa;
    
    if (monedaActual === 'USD') return `$ ${precioConvertido.toFixed(2)}`; //estos dos hacen que cambie a moneda no es necesario poner  los soles ya que estan por defecto
    if (monedaActual === 'EUR') return `€ ${precioConvertido.toFixed(2)}`;
    return `S/ ${precioConvertido.toFixed(2)}`;
}