<?php
// Recurso JavaScript servido por PHP.
header('Content-Type: application/javascript; charset=utf-8');
?>
document.addEventListener('DOMContentLoaded', function () {
  const carousels = document.querySelectorAll('[data-gallery-carousel]');

  carousels.forEach(function (carousel) {
    const viewport = carousel.querySelector('.gallery-viewport');
    const track = carousel.querySelector('.gallery-track');
    const slides = Array.from(carousel.querySelectorAll('.gallery-slide'));
    const previousButton = carousel.querySelector('[data-gallery-prev]');
    const nextButton = carousel.querySelector('[data-gallery-next]');
    const indicatorsContainer = carousel.querySelector('.gallery-indicators');
    const status = carousel.querySelector('.gallery-status');

    if (!viewport || !track || slides.length === 0 || !indicatorsContainer || !previousButton || !nextButton) {
      return;
    }

    let activeIndex = 0;
    let touchStartX = null;

    function focusSlide(index) {
      const photo = slides[index].querySelector('.gallery-photo');
      if (photo) {
        photo.focus({ preventScroll: true });
      }
    }

    function goTo(index, moveFocus = false) {
      activeIndex = (index + slides.length) % slides.length;
      track.style.transform = 'translateX(-' + (activeIndex * 100) + '%)';
      updateCarousel();

      const shouldMoveFocus = moveFocus || (
        document.activeElement
        && document.activeElement.closest('.gallery-slide')
        && carousel.contains(document.activeElement)
      );
      if (shouldMoveFocus) {
        window.requestAnimationFrame(function () {
          focusSlide(activeIndex);
        });
      }
    }

    function updateCarousel() {
      const indicators = Array.from(indicatorsContainer.querySelectorAll('.gallery-indicator'));

      indicators.forEach(function (indicator, index) {
        if (index === activeIndex) {
          indicator.setAttribute('aria-current', 'true');
        } else {
          indicator.removeAttribute('aria-current');
        }
      });

      slides.forEach(function (slide, index) {
        const isActive = index === activeIndex;
        slide.setAttribute('aria-hidden', isActive ? 'false' : 'true');
        const photo = slide.querySelector('.gallery-photo');
        if (photo) {
          photo.tabIndex = isActive ? 0 : -1;
        }
      });

      if (status) {
        status.textContent = 'Foto ' + (activeIndex + 1) + ' de ' + slides.length;
      }
    }

    function createIndicators() {
      slides.forEach(function (slide, index) {
        const indicator = document.createElement('button');

        indicator.type = 'button';
        indicator.className = 'gallery-indicator';
        indicator.setAttribute('aria-label', 'Ir a la foto ' + (index + 1));
        indicator.addEventListener('click', function () {
          goTo(index);
        });
        indicatorsContainer.appendChild(indicator);
      });
    }

    createIndicators();
    updateCarousel();

    previousButton.addEventListener('click', function () {
      goTo(activeIndex - 1);
    });

    nextButton.addEventListener('click', function () {
      goTo(activeIndex + 1);
    });

    viewport.addEventListener('keydown', function (event) {
      if (event.key === 'ArrowLeft') {
        event.preventDefault();
        goTo(activeIndex - 1, true);
      }

      if (event.key === 'ArrowRight') {
        event.preventDefault();
        goTo(activeIndex + 1, true);
      }
    });

    viewport.addEventListener('touchstart', function (event) {
      touchStartX = event.changedTouches[0].clientX;
    }, { passive: true });

    viewport.addEventListener('touchend', function (event) {
      if (touchStartX === null) {
        return;
      }

      const distance = event.changedTouches[0].clientX - touchStartX;
      if (Math.abs(distance) > 40) {
        goTo(activeIndex + (distance < 0 ? 1 : -1));
      }
      touchStartX = null;
    }, { passive: true });

    viewport.addEventListener('touchcancel', function () {
      touchStartX = null;
    }, { passive: true });

    window.addEventListener('resize', function () {
      track.style.transform = 'translateX(-' + (activeIndex * 100) + '%)';
    });
  });

  // Modal Lightbox con efecto de fondo desenfocado (backdrop-filter)
  const lightbox = document.getElementById('galleryLightbox');
  const lightboxImage = lightbox ? lightbox.querySelector('.gallery-lightbox-image') : null;
  const lightboxCaption = lightbox ? lightbox.querySelector('.gallery-lightbox-caption') : null;
  const lightboxBackdrop = lightbox ? lightbox.querySelector('.gallery-lightbox-backdrop') : null;
  const lightboxClose = lightbox ? lightbox.querySelector('[data-lightbox-close]:not(.gallery-lightbox-backdrop)') : null;
  const lightboxPrevious = lightbox ? lightbox.querySelector('[data-lightbox-prev]') : null;
  const lightboxNext = lightbox ? lightbox.querySelector('[data-lightbox-next]') : null;
  const pageRegions = Array.from(document.body.children).filter(function (element) {
    return element !== lightbox && !['SCRIPT', 'STYLE', 'LINK'].includes(element.tagName);
  });
  const pageRegionState = new Map();
  let lastFocusedElement = null;
  let lightboxItems = [];
  let lightboxIndex = 0;
  let imageRequestId = 0;

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
        const state = pageRegionState.get(element);
        if (!state) {
          return;
        }

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
    if (!lightboxImage || lightboxItems.length === 0) {
      return;
    }

    lightboxIndex = (index + lightboxItems.length) % lightboxItems.length;
    const trigger = lightboxItems[lightboxIndex];
    const source = trigger.getAttribute('data-lightbox-src') || '';
    const alt = trigger.getAttribute('data-lightbox-alt') || 'Imagen de la galería';
    const caption = trigger.getAttribute('data-lightbox-caption') || '';
    const requestId = ++imageRequestId;

    lightboxImage.classList.add('is-loading');
    lightboxImage.classList.remove('has-error');
    lightboxImage.setAttribute('aria-busy', 'true');
    lightboxImage.alt = alt;
    if (lightboxCaption) {
      lightboxCaption.textContent = caption;
    }

    lightboxImage.onload = function () {
      if (requestId !== imageRequestId) {
        return;
      }
      lightboxImage.classList.remove('is-loading');
      lightboxImage.removeAttribute('aria-busy');
    };
    lightboxImage.onerror = function () {
      if (requestId !== imageRequestId) {
        return;
      }
      lightboxImage.classList.remove('is-loading');
      lightboxImage.classList.add('has-error');
      lightboxImage.removeAttribute('aria-busy');
      lightboxImage.alt = 'No se pudo cargar la imagen.';
      if (lightboxCaption) {
        lightboxCaption.textContent = 'No se pudo cargar la imagen.';
      }
    };
    lightboxImage.src = source;
  }

  function openLightbox(trigger) {
    if (!lightbox || !lightboxImage || !trigger || !lightbox.hidden) {
      return;
    }

    const carousel = trigger.closest('[data-gallery-carousel]');
    lightboxItems = carousel
      ? Array.from(carousel.querySelectorAll('[data-lightbox-src]'))
      : Array.from(document.querySelectorAll('[data-lightbox-src]'));
    lightboxIndex = Math.max(0, lightboxItems.indexOf(trigger));

    if (lightboxItems.length === 0) {
      return;
    }

    lastFocusedElement = document.activeElement;
    setPageHidden(true);
    lightbox.hidden = false;
    document.body.classList.add('gallery-lightbox-open');
    showLightboxImage(lightboxIndex);

    window.requestAnimationFrame(function () {
      if (lightboxClose) {
        lightboxClose.focus();
      }
    });
  }

  function closeLightbox() {
    if (!lightbox || lightbox.hidden) {
      return;
    }

    imageRequestId += 1;
    lightbox.hidden = true;
    document.body.classList.remove('gallery-lightbox-open');
    setPageHidden(false);
    if (lightboxImage) {
      lightboxImage.removeAttribute('src');
      lightboxImage.classList.remove('is-loading', 'has-error');
      lightboxImage.removeAttribute('aria-busy');
    }
    if (lightboxCaption) {
      lightboxCaption.textContent = '';
    }
    if (lastFocusedElement && typeof lastFocusedElement.focus === 'function') {
      lastFocusedElement.focus();
    }
  }

  function navigateLightbox(direction) {
    if (!lightbox || lightbox.hidden || lightboxItems.length === 0) {
      return;
    }
    showLightboxImage(lightboxIndex + direction);
  }

  document.querySelectorAll('[data-lightbox-src]').forEach(function (trigger) {
    trigger.addEventListener('click', function () {
      openLightbox(trigger);
    });
  });

  // Evento en JavaScript para cerrar el modal al hacer clic en el backdrop fuera de la foto
  if (lightboxBackdrop) {
    lightboxBackdrop.addEventListener('click', closeLightbox);
  }

  if (lightboxClose) {
    lightboxClose.addEventListener('click', closeLightbox);
  }

  if (lightboxPrevious) {
    lightboxPrevious.addEventListener('click', function () {
      navigateLightbox(-1);
    });
  }

  if (lightboxNext) {
    lightboxNext.addEventListener('click', function () {
      navigateLightbox(1);
    });
  }

  document.addEventListener('keydown', function (event) {
    if (!lightbox || lightbox.hidden) {
      return;
    }

    if (event.key === 'Escape') {
      event.preventDefault();
      closeLightbox();
      return;
    }

    if (event.key === 'ArrowLeft') {
      event.preventDefault();
      navigateLightbox(-1);
      return;
    }

    if (event.key === 'ArrowRight') {
      event.preventDefault();
      navigateLightbox(1);
      return;
    }

    if (event.key === 'Tab') {
      const focusableElements = getLightboxFocusableElements();
      if (focusableElements.length === 0) {
        event.preventDefault();
        lightbox.focus();
        return;
      }

      const firstElement = focusableElements[0];
      const lastElement = focusableElements[focusableElements.length - 1];
      if (event.shiftKey && document.activeElement === firstElement) {
        event.preventDefault();
        lastElement.focus();
      } else if (!event.shiftKey && document.activeElement === lastElement) {
        event.preventDefault();
        firstElement.focus();
      } else if (!lightbox.contains(document.activeElement)) {
        event.preventDefault();
        firstElement.focus();
      }
    }
  });

  document.addEventListener('focusin', function (event) {
    if (lightbox && !lightbox.hidden && !lightbox.contains(event.target)) {
      event.preventDefault();
      const focusableElements = getLightboxFocusableElements();
      if (focusableElements.length > 0) {
        focusableElements[0].focus();
      }
    }
  });
});
