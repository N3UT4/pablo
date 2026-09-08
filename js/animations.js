document.addEventListener('DOMContentLoaded', function () {

  /* ---------------------------------------------------
     1. SCROLL REVEAL — las secciones aparecen al hacer scroll
  --------------------------------------------------- */
  const revealEls = document.querySelectorAll('.reveal');
  if (revealEls.length) {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.15, rootMargin: '0px 0px -60px 0px' });

    revealEls.forEach((el, i) => {
      el.style.transitionDelay = (i % 6) * 70 + 'ms';
      observer.observe(el);
    });
  }

  /* ---------------------------------------------------
     2. HEADER — sombra y blur al hacer scroll
  --------------------------------------------------- */
  const header = document.querySelector('header');
  if (header) {
    window.addEventListener('scroll', () => {
      header.classList.toggle('scrolled', window.scrollY > 12);
    }, { passive: true });
  }

  /* ---------------------------------------------------
     3. HERO — la línea de aguja se dibuja sola al cargar
  --------------------------------------------------- */
  const needlePath = document.querySelector('.needle-line path');
  if (needlePath) {
    const length = needlePath.getTotalLength();
    needlePath.style.strokeDasharray = length;
    needlePath.style.strokeDashoffset = length;
    needlePath.getBoundingClientRect(); // fuerza reflow
    needlePath.style.transition = 'stroke-dashoffset 1.8s ease-out';
    requestAnimationFrame(() => {
      needlePath.style.strokeDashoffset = '0';
    });
  }

  /* ---------------------------------------------------
     4. TILT 3D — tarjetas reaccionan al mouse
  --------------------------------------------------- */
  const tiltSelectors = '.service-card, .artist-card, .flash-item, .promo-card';
  document.querySelectorAll(tiltSelectors).forEach(card => {
    card.classList.add('tilt-card');
    card.addEventListener('mousemove', (e) => {
      const rect = card.getBoundingClientRect();
      const x = e.clientX - rect.left;
      const y = e.clientY - rect.top;
      const rotateX = ((y / rect.height) - 0.5) * -10;
      const rotateY = ((x / rect.width) - 0.5) * 10;
      card.style.transform = `perspective(600px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-6px)`;
    });
    card.addEventListener('mouseleave', () => {
      card.style.transform = '';
    });
  });

  /* ---------------------------------------------------
     5. RIPPLE — efecto de onda al hacer clic en botones
  --------------------------------------------------- */
  const rippleSelectors = '.btn-primary, .btn-ghost, .submit-btn, .nav-cta, .tab-btn';
  document.querySelectorAll(rippleSelectors).forEach(btn => {
    btn.classList.add('ripple-btn');
    btn.addEventListener('click', function (e) {
      const rect = btn.getBoundingClientRect();
      const ripple = document.createElement('span');
      ripple.className = 'ripple';
      const size = Math.max(rect.width, rect.height);
      ripple.style.width = ripple.style.height = size + 'px';
      ripple.style.left = (e.clientX - rect.left - size / 2) + 'px';
      ripple.style.top = (e.clientY - rect.top - size / 2) + 'px';
      btn.appendChild(ripple);
      setTimeout(() => ripple.remove(), 650);
    });
  });

  /* ---------------------------------------------------
     6. NAV LINKS — subrayado animado on hover
  --------------------------------------------------- */
  document.querySelectorAll('.nav-links a').forEach(link => {
    link.classList.add('underline-anim');
  });

  /* ---------------------------------------------------
     7. HERO PANEL — filas aparecen en cascada
  --------------------------------------------------- */
  document.querySelectorAll('.hero-panel .row').forEach((row, i) => {
    row.style.animation = `slideInRight .5s ease ${0.2 + i * 0.1}s both`;
  });

});
