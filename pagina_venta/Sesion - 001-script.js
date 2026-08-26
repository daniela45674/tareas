
    let currentSlide = 0;
    const slides = document.querySelectorAll('.carousel-item');
    const indicators = document.querySelectorAll('.indicator');
    const totalSlides = slides.length;
    let autoSlideInterval;

    function showSlide(index) {
      if (index >= totalSlides) currentSlide = 0;
      else if (index < 0) currentSlide = totalSlides - 1;
      else currentSlide = index;

      const offset = -currentSlide * 100;
      document.querySelector('.carousel-inner').style.transform = `translateX(${offset}%)`;

      indicators.forEach((ind, i) => {
        ind.classList.toggle('active', i === currentSlide);
      });
    }

    function nextSlide() {
      showSlide(currentSlide + 1);
      resetAutoSlide();
    }

    function previousSlide() {
      showSlide(currentSlide - 1);
      resetAutoSlide();
    }

    function goToSlide(index) {
      showSlide(index);
      resetAutoSlide();
    }

    function startAutoSlide() {
      autoSlideInterval = setInterval(() => {
        nextSlide();
      }, 5000);
    }

    function resetAutoSlide() {
      clearInterval(autoSlideInterval);
      startAutoSlide();
    }

    startAutoSlide();

    // ========== BOTÓN FLOTANTE PARA IR AL INICIO ==========
    const btnInicio = document.getElementById('btnInicio');

    // Mostrar/ocultar botón según scroll
    window.addEventListener('scroll', () => {
      if (window.pageYOffset > 300) {
        btnInicio.classList.add('visible');
      } else {
        btnInicio.classList.remove('visible');
      }
    });

    // Función para ir al inicio con smooth scroll
    function irAlInicio() {
      const target = document.querySelector('#inicio');
      if (target) {
        const navHeight = document.querySelector('nav').offsetHeight;
        const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - navHeight;
        
        window.scrollTo({
          top: targetPosition,
          behavior: 'smooth'
        });
      }
    }

    // Smooth scroll para navegación
    document.querySelectorAll('nav a').forEach(anchor => {
      anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const targetId = this.getAttribute('href');
        const target = document.querySelector(targetId);
        
        if (target) {
          const navHeight = document.querySelector('nav').offsetHeight;
          const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - navHeight;
          
          window.scrollTo({
            top: targetPosition,
            behavior: 'smooth'
          });
        }
      });
    });

    // ========== AUTO-PLAY PARA CARRUSELES DE CARDS ==========
    const cardCarousels = new Map(); // Almacenar intervalos de cada card

    // Función para cambiar slide de card (manual o automático)
    function changeCardSlide(button, direction) {
      const card = button.closest('.card');
      changeCardSlideLogic(card, direction);
      
      // Reiniciar auto-play después de interacción manual
      resetCardAutoPlay(card);
    }

    // Lógica principal del cambio de slide
    function changeCardSlideLogic(card, direction) {
      const carousel = card.querySelector('.card-carousel');
      const inner = carousel.querySelector('.card-carousel-inner');
      const items = carousel.querySelectorAll('.card-carousel-item');
      const dots = carousel.querySelectorAll('.card-carousel-dot');
      const infoItems = card.querySelectorAll('.card-info-item');
      const totalItems = items.length;
      
      // Obtener el índice actual
      let currentIndex = 0;
      dots.forEach((dot, index) => {
        if (dot.classList.contains('active')) {
          currentIndex = index;
        }
      });
      
      // Calcular nuevo índice
      let newIndex = currentIndex + direction;
      if (newIndex >= totalItems) newIndex = 0;
      if (newIndex < 0) newIndex = totalItems - 1;
      
      // Actualizar posición de imágenes
      const offset = -newIndex * 100;
      inner.style.transform = `translateX(${offset}%)`;
      
      // Actualizar dots
      dots.forEach((dot, index) => {
        dot.classList.toggle('active', index === newIndex);
      });
      
      // Actualizar información del producto
      infoItems.forEach((info, index) => {
        info.classList.toggle('active', index === newIndex);
      });
    }

    // Iniciar auto-play para una card específica
    function startCardAutoPlay(card) {
      const items = card.querySelectorAll('.card-carousel-item');
      
      // Solo iniciar si tiene más de 1 imagen
      if (items.length > 1) {
        const interval = setInterval(() => {
          changeCardSlideLogic(card, 1); // Avanzar automáticamente
        }, 4000); // Cambia cada 4 segundos
        
        cardCarousels.set(card, interval);
      }
    }

    // Detener auto-play de una card
    function stopCardAutoPlay(card) {
      if (cardCarousels.has(card)) {
        clearInterval(cardCarousels.get(card));
        cardCarousels.delete(card);
      }
    }

    // Reiniciar auto-play después de interacción manual
    function resetCardAutoPlay(card) {
      stopCardAutoPlay(card);
      startCardAutoPlay(card);
    }

    // Inicializar auto-play para todas las cards al cargar la página
    document.addEventListener('DOMContentLoaded', () => {
      const allCards = document.querySelectorAll('.card');
      
      allCards.forEach(card => {
        startCardAutoPlay(card);
        
        // Pausar auto-play cuando el mouse está sobre la card
        card.addEventListener('mouseenter', () => {
          stopCardAutoPlay(card);
        });
        
        // Reanudar auto-play cuando el mouse sale de la card
        card.addEventListener('mouseleave', () => {
          startCardAutoPlay(card);
        });
      });
    });

    // También hacer que los dots sean clicables
    document.addEventListener('click', (e) => {
      if (e.target.classList.contains('card-carousel-dot')) {
        const card = e.target.closest('.card');
        const dots = card.querySelectorAll('.card-carousel-dot');
        const clickedIndex = Array.from(dots).indexOf(e.target);
        const currentIndex = Array.from(dots).findIndex(dot => dot.classList.contains('active'));
        const direction = clickedIndex - currentIndex;
        
        changeCardSlideLogic(card, direction);
        resetCardAutoPlay(card);
      }
    });
    // ========== FUNCIÓN PARA ENVIAR A WHATSAPP ==========
    function enviarWhatsApp(event, nombreProducto) {
      event.preventDefault();

      // Número de WhatsApp de Grafiluz (usa el formato internacional sin + ni espacios)
      const numeroWhatsApp = '51930910829'; // Cambia este número según corresponda

      // Mensaje personalizado
      const mensaje = `Hola, estoy interesado en: *${nombreProducto}*. ¿Podrías darme más información?`;

      // Codificar el mensaje para URL
      const mensajeCodificado = encodeURIComponent(mensaje);

      // Crear URL de WhatsApp
      const urlWhatsApp = `https://wa.me/${numeroWhatsApp}?text=${mensajeCodificado}`;

      // Abrir en nueva ventana
      window.open(urlWhatsApp, '_blank');
    }
