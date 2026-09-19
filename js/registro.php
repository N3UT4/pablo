<?php
// Recurso JavaScript servido por PHP.
// Valida el formulario de registro en el navegador.
// Verifica nombre, email, teléfono, documento, fecha de nacimiento y contraseñas.
header('Content-Type: application/javascript; charset=utf-8');
?>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('registroForm');
  if (!form) return;

  const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  // Alterna la clase 'invalid' en el input y muestra/oculta el error.
  function setInvalid(input, errEl, isInvalid) {
    input.classList.toggle('invalid', isInvalid);
    if (errEl) errEl.classList.toggle('show', isInvalid);
  }

  form.addEventListener('submit', function (e) {
    let ok = true;

    // Validación del nombre completo (mínimo 2 palabras)
    const nombre = document.getElementById('nombre_completo');
    const nombreValid = nombre.value.trim().split(' ').filter(Boolean).length >= 2;
    setInvalid(nombre, document.getElementById('nombreErr'), !nombreValid);
    if (!nombreValid) ok = false;

    // Validación del email
    const email = document.getElementById('email');
    const emailValid = emailRe.test(email.value.trim());
    setInvalid(email, document.getElementById('emailErr'), !emailValid);
    if (!emailValid) ok = false;

    // Validación del teléfono (10 dígitos)
    const telefono = document.getElementById('telefono');
    const telDigits = telefono.value.replace(/\D/g, '');
    const telValid = telDigits.length === 10;
    setInvalid(telefono, document.getElementById('telefonoErr'), !telValid);
    if (!telValid) ok = false;

    // Validación del documento (mínimo 6 dígitos)
    const documento = document.getElementById('documento');
    const docValid = documento.value.trim().replace(/\D/g, '').length >= 6;
    setInvalid(documento, document.getElementById('documentoErr'), !docValid);
    if (!docValid) ok = false;

    // Validación de la fecha de nacimiento
    const fechaNacimiento = document.getElementById('fecha_nacimiento');
    const fnValid = !!fechaNacimiento.value;
    setInvalid(fechaNacimiento, document.getElementById('fechaNacimientoErr'), !fnValid);
    if (!fnValid) ok = false;

    // Validación de la contraseña (mínimo 6 caracteres)
    const password = document.getElementById('password_hash');
    const passValid = password.value.length >= 6;
    setInvalid(password, document.getElementById('passwordErr'), !passValid);
    if (!passValid) ok = false;

    // Validación de confirmación de contraseña
    const password2 = document.getElementById('password2');
    const pass2Valid = password2.value === password.value && password.value.length > 0;
    setInvalid(password2, document.getElementById('password2Err'), !pass2Valid);
    if (!pass2Valid) ok = false;

    if (!ok) {
      e.preventDefault();
      itzaError('Hay campos incompletos o inválidos. Corrígelos para continuar.');
      return;
    }

    // Guarda datos de sesión en localStorage
    localStorage.setItem('itza_session', JSON.stringify({ email: email.value.trim(), nombre: nombre.value.trim(), loggedAt: Date.now() }));
  });

  // Limpia el estado de error al empezar a escribir
  document.querySelectorAll('input, select').forEach(inp => {
    inp.addEventListener('input', () => inp.classList.remove('invalid'));
  });
});
