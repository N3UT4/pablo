document.addEventListener('DOMContentLoaded', function () {

  /* ---- Fade-in de la tarjeta principal al cargar ---- */
  document.querySelectorAll('.card').forEach((card, i) => {
    card.classList.add('card-enter');
    setTimeout(() => card.classList.add('card-enter-visible'), 60 + i * 80);
  });

  /* ---- Ripple en botones ---- */
  document.querySelectorAll('.submit-btn, .steps-nav a, .unlockBtn, #unlockBtn').forEach(btn => {
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

  /* ---- Pastillas de método de pago: pequeño "pop" al elegir ---- */
  document.querySelectorAll('.pay-pill input').forEach(input => {
    input.addEventListener('change', () => {
      const label = input.nextElementSibling;
      if (!label) return;
      label.style.animation = 'none';
      void label.offsetWidth; // reinicia la animación
      label.style.animation = 'popSelect .3s ease';
    });
  });

  /* ---- Campos: leve elevación al enfocar ---- */
  document.querySelectorAll('.field input, .field select, .field textarea').forEach(el => {
    el.addEventListener('focus', () => el.closest('.field').classList.add('field-focus'));
    el.addEventListener('blur', () => el.closest('.field').classList.remove('field-focus'));
  });

});
