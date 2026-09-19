<?php
header('Content-Type: application/javascript; charset=utf-8');
?>
document.addEventListener('DOMContentLoaded', function () {
  var carousels = document.querySelectorAll('[data-galeria-carousel]');

  function initLightbox() {
    var lightbox = document.getElementById('galeriaLightbox');
    var lightboxImage = lightbox ? lightbox.querySelector('.galeria-lightbox-image') : null;
    var lightboxCaption = lightbox ? lightbox.querySelector('.galeria-lightbox-caption') : null;
    var lightboxBackdrop = lightbox ? lightbox.querySelector('.galeria-lightbox-backdrop') : null;
    var lightboxClose = lightbox ? lightbox.querySelector('[data-lightbox-close]:not(.galeria-lightbox-backdrop)') : null;
    var lightboxPrevious = lightbox ? lightbox.querySelector('[data-lightbox-prev]') : null;
    var lightboxNext = lightbox ? lightbox.querySelector('[data-lightbox-next]') : null;
    var lightboxAncestors = [];
    var ancestor = lightbox.parentElement;
    while (ancestor && ancestor !== document.body) {
      lightboxAncestors.push(ancestor);
      ancestor = ancestor.parentElement;
    }
    var pageRegions = Array.from(document.body.children).filter(function (element) {
      return element !== lightbox && !['SCRIPT', 'STYLE', 'LINK'].includes(element.tagName) && lightboxAncestors.indexOf(element) === -1;
    });
    var pageRegionState = new Map();
    var lastFocusedElement = null;
    var lightboxItems = [];
    var lightboxIndex = 0;
    var imageRequestId = 0;

    function setPageHidden(hidden) {
      pageRegions.forEach(function (element) {
        if (hidden) {
          pageRegionState.set(element, {
            ariaHidden: element.getAttribute('aria-hidden'),
            inert: element.inert
          });
          element.setAttribute('aria-hidden', 'true');
          element.inert = true;
        } else {
          var state = pageRegionState.get(element);
          if (!state) return;
          if (state.ariaHidden === null) {
            element.removeAttribute('aria-hidden');
          } else {
            element.setAttribute('aria-hidden', state.ariaHidden);
          }
          element.inert = state.inert;
          pageRegionState.delete(element);
        }
      });
    }

    function getLightboxFocusableElements() {
      return Array.from(lightbox.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'))
        .filter(function (element) {
          return !element.disabled && element.getClientRects().length > 0;
        });
    }

    function showLightboxImage(index) {
      if (!lightboxImage || lightboxItems.length === 0) return;
      lightboxIndex = (index + lightboxItems.length) % lightboxItems.length;
      var trigger = lightboxItems[lightboxIndex];
      var source = trigger.getAttribute('data-lightbox-src') || '';
      var alt = trigger.getAttribute('data-lightbox-alt') || 'Imagen de la galería';
      var caption = trigger.getAttribute('data-lightbox-caption') || '';
      var requestId = ++imageRequestId;

      lightboxImage.classList.add('is-loading');
      lightboxImage.classList.remove('has-error');
      lightboxImage.setAttribute('aria-busy', 'true');
      lightboxImage.alt = alt;
      if (lightboxCaption) lightboxCaption.textContent = caption;

      lightboxImage.onload = function () {
        if (requestId !== imageRequestId) return;
        lightboxImage.classList.remove('is-loading');
        lightboxImage.removeAttribute('aria-busy');
      };
      lightboxImage.onerror = function () {
        if (requestId !== imageRequestId) return;
        lightboxImage.classList.remove('is-loading');
        lightboxImage.classList.add('has-error');
        lightboxImage.removeAttribute('aria-busy');
        lightboxImage.alt = 'No se pudo cargar la imagen.';
        if (lightboxCaption) lightboxCaption.textContent = 'No se pudo cargar la imagen.';
      };
      lightboxImage.src = source;
    }

    function openLightbox(trigger) {
      if (!lightbox || !lightboxImage || !trigger || !lightbox.hidden) return;
      var carousel = trigger.closest('[data-galeria-carousel]');
      var allItems = carousel
        ? Array.from(carousel.querySelectorAll('[data-lightbox-src]'))
        : Array.from(document.querySelectorAll('[data-lightbox-src]'));
      lightboxItems = allItems.filter(function (el) {
        var slide = el.closest('.swiper-slide');
        if (!slide) return true;
        return slide.style.display !== 'none';
      });
      lightboxIndex = Math.max(0, lightboxItems.indexOf(trigger));
      if (lightboxItems.length === 0) return;

      lastFocusedElement = document.activeElement;
      setPageHidden(true);
      lightbox.hidden = false;
      lightbox.inert = false;
      document.body.classList.add('galeria-lightbox-open');
      showLightboxImage(lightboxIndex);

      window.requestAnimationFrame(function () {
        if (lightboxClose) lightboxClose.focus();
      });
    }

    function closeLightbox() {
      if (!lightbox || lightbox.hidden) return;
      imageRequestId += 1;
      lightbox.hidden = true;
      document.body.classList.remove('galeria-lightbox-open');
      setPageHidden(false);
      if (lightboxImage) {
        lightboxImage.removeAttribute('src');
        lightboxImage.classList.remove('is-loading', 'has-error');
        lightboxImage.removeAttribute('aria-busy');
      }
      if (lightboxCaption) lightboxCaption.textContent = '';
      if (lastFocusedElement && typeof lastFocusedElement.focus === 'function') {
        lastFocusedElement.focus();
      }
    }

    function navigateLightbox(direction) {
      if (!lightbox || lightbox.hidden || lightboxItems.length === 0) return;
      showLightboxImage(lightboxIndex + direction);
    }

    document.querySelectorAll('[data-lightbox-src]').forEach(function (trigger) {
      trigger.addEventListener('click', function () { openLightbox(trigger); });
    });

    if (lightboxBackdrop) lightboxBackdrop.addEventListener('click', closeLightbox);
    if (lightboxClose) lightboxClose.addEventListener('click', closeLightbox);
    if (lightboxImage) lightboxImage.addEventListener('click', closeLightbox);
    if (lightboxPrevious) lightboxPrevious.addEventListener('click', function () { navigateLightbox(-1); });
    if (lightboxNext) lightboxNext.addEventListener('click', function () { navigateLightbox(1); });

    document.addEventListener('keydown', function (event) {
      if (!lightbox || lightbox.hidden) return;
      if (event.key === 'Escape') { event.preventDefault(); closeLightbox(); return; }
      if (event.key === 'ArrowLeft') { event.preventDefault(); navigateLightbox(-1); return; }
      if (event.key === 'ArrowRight') { event.preventDefault(); navigateLightbox(1); return; }
      if (event.key === 'Tab') {
        var focusableElements = getLightboxFocusableElements();
        if (focusableElements.length === 0) { event.preventDefault(); lightbox.focus(); return; }
        var firstElement = focusableElements[0];
        var lastElement = focusableElements[focusableElements.length - 1];
        if (event.shiftKey && document.activeElement === firstElement) { event.preventDefault(); lastElement.focus(); }
        else if (!event.shiftKey && document.activeElement === lastElement) { event.preventDefault(); firstElement.focus(); }
        else if (!lightbox.contains(document.activeElement)) { event.preventDefault(); firstElement.focus(); }
      }
    });

    document.addEventListener('focusin', function (event) {
      if (lightbox && !lightbox.hidden && !lightbox.contains(event.target)) {
        event.preventDefault();
        var focusableElements = getLightboxFocusableElements();
        if (focusableElements.length > 0) focusableElements[0].focus();
      }
    });
  }

  initLightbox();

  carousels.forEach(function (carousel) {
    var swiperContainer = carousel.querySelector('.galeria-swiper');
    var filtersContainer = carousel.querySelector('.galeria-filters');
    var status = carousel.querySelector('.galeria-counter');

    if (!swiperContainer) return;

    var allSlides = Array.from(swiperContainer.querySelectorAll('.swiper-slide'));
    var swiperInstance = null;

    function getVisibleSlides() {
      return allSlides.filter(function (s) { return s.style.display !== 'none'; });
    }

    function getActiveVisibleIndex() {
      if (!swiperInstance) return 0;
      var visibleSlides = getVisibleSlides();
      var activeSlide = swiperInstance.slides[swiperInstance.activeIndex];
      if (!activeSlide || activeSlide.style.display === 'none') {
        var firstVisible = visibleSlides[0];
        if (firstVisible) {
          var idx = allSlides.indexOf(firstVisible);
          return idx >= 0 ? idx : 0;
        }
        return 0;
      }
      return swiperInstance.activeIndex;
    }

    function updateStatus() {
      if (!status) return;
      var visibleSlides = getVisibleSlides();
      var activeIdx = getActiveVisibleIndex();
      var visibleActiveIndex = visibleSlides.indexOf(allSlides[activeIdx]);
      if (visibleActiveIndex < 0) visibleActiveIndex = 0;
      status.textContent = 'Foto ' + (visibleActiveIndex + 1) + ' de ' + visibleSlides.length;
    }

    function buildSwiper() {
      if (swiperInstance) {
        swiperInstance.destroy(true, true);
        swiperInstance = null;
      }

      var visibleSlides = getVisibleSlides();
      if (visibleSlides.length === 0) return;

      var useCoverflow = visibleSlides.length > 1;

      var swiperConfig = {
        slidesPerView: useCoverflow ? 3 : 1,
        spaceBetween: 24,
        centeredSlides: useCoverflow,
        loop: true,
        grabCursor: useCoverflow,
        allowTouchMove: true,
        autoplay: {
          delay: 3500,
          disableOnInteraction: false,
        },
        pagination: {
          el: carousel.querySelector('.galeria-dots'),
          clickable: true,
          renderBullet: function (index, className) {
            return '<span class="' + className + '" role="listitem" aria-label="Ir a foto ' + (index + 1) + '"></span>';
          },
        },
        navigation: {
          nextEl: carousel.querySelector('.galeria-nav-next'),
          prevEl: carousel.querySelector('.galeria-nav-prev'),
        },
      };

      if (useCoverflow) {
        swiperConfig.effect = 'coverflow';
        swiperConfig.coverflowEffect = {
          rotate: 0,
          stretch: 0,
          depth: 200,
          modifier: 1,
          slideShadows: true,
        };
      }

      try {
        swiperInstance = new Swiper(swiperContainer, swiperConfig);
      } catch (e) {
        console.warn('Swiper init error:', e);
      }
    }

    buildSwiper();

    if (swiperInstance) {
      swiperInstance.on('slideChange', function () {
        updateStatus();
      });
      swiperInstance.on('activeIndexChange', function () {
        updateStatus();
      });
      swiperInstance.on('init', function () {
        updateStatus();
      });
    }

    setTimeout(updateStatus, 200);

    if (filtersContainer) {
      filtersContainer.addEventListener('click', function (event) {
        var btn = event.target.closest('.galeria-filter');
        if (!btn) return;

        var filter = btn.getAttribute('data-filter');

        filtersContainer.querySelectorAll('.galeria-filter').forEach(function (f) {
          f.classList.remove('active');
          f.setAttribute('aria-pressed', 'false');
        });
        btn.classList.add('active');
        btn.setAttribute('aria-pressed', 'true');

        allSlides.forEach(function (slide) {
          var category = slide.getAttribute('data-category');
          if (filter === 'all' || category === filter) {
            slide.style.display = '';
            slide.style.opacity = '';
            slide.style.pointerEvents = '';
            slide.style.visibility = '';
          } else {
            slide.style.display = 'none';
            slide.style.opacity = '0';
            slide.style.pointerEvents = 'none';
            slide.style.visibility = 'hidden';
          }
        });

        setTimeout(function () {
          buildSwiper();
          updateStatus();
        }, 100);
      });
    }

    var quoteDebounce = {};
    carousel.addEventListener('click', function (event) {
      var quoteBtn = event.target.closest('.slide-quote-btn');
      if (!quoteBtn) return;
      event.preventDefault();
      event.stopPropagation();

      var style = quoteBtn.getAttribute('data-style') || '';
      var piece = quoteBtn.getAttribute('data-piece') || '';
      var slug = piece.toLowerCase()
        .replace(/[^a-z0-9]+/g, '_')
        .replace(/^_+|_+$/g, '');

      var url = 'index.php?action=agendar_cita&estilo=' + encodeURIComponent(style) + '&pieza=' + encodeURIComponent(slug);

      if (quoteDebounce[style]) return;
      quoteDebounce[style] = true;
      setTimeout(function () { quoteDebounce[style] = false; }, 1000);

      window.location.href = url;
    });

    window.addEventListener('resize', function () {
      if (swiperInstance) {
        swiperInstance.update();
      }
    });
  });
});
