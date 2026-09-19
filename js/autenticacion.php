<?php
// Recurso JavaScript servido por PHP.
// Valida el formulario de login en el navegador.
// Muestra/oculta errores y guarda datos de sesión en localStorage.
header('Content-Type: application/javascript; charset=utf-8');
?>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('loginForm');
  if (!form) return;

  const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  // Alterna la clase 'invalid' en el input y muestra/oculta el error.
  function setInvalid(input, errEl, isInvalid) {
    input.classList.toggle('invalid', isInvalid);
    if (errEl) errEl.classList.toggle('show', isInvalid);
  }

  form.addEventListener('submit', function (e) {
    const email = document.getElementById('loginEmail');
    const pass = document.getElementById('loginPass');
    let ok = true;

    const emailValid = emailRe.test(email.value.trim());
    setInvalid(email, document.getElementById('loginEmailErr'), !emailValid);
    if (!emailValid) ok = false;

    const passValid = pass.value.length >= 6;
    setInvalid(pass, document.getElementById('loginPassErr'), !passValid);
    if (!passValid) ok = false;

    if (!ok) {
      e.preventDefault();
      itzaError('Revisa los campos marcados en rojo antes de continuar.');
      return;
    }

    // Guarda datos de sesión en localStorage para uso de la app
    localStorage.setItem('itza_session', JSON.stringify({ email: email.value.trim(), loggedAt: Date.now() }));
  });

  // Limpia el estado de error al empezar a escribir
  document.querySelectorAll('input').forEach(inp => {
    inp.addEventListener('input', () => inp.classList.remove('invalid'));
  });
});
