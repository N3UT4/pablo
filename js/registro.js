document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('registroForm');
  const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  function setInvalid(input, errEl, isInvalid) {
    input.classList.toggle('invalid', isInvalid);
    if (errEl) errEl.classList.toggle('show', isInvalid);
  }

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    let ok = true;

    const nombre = document.getElementById('nombre_completo');
    const nombreValid = nombre.value.trim().split(' ').filter(Boolean).length >= 2;
    setInvalid(nombre, document.getElementById('nombreErr'), !nombreValid);
    if (!nombreValid) ok = false;

    const email = document.getElementById('email');
    const emailValid = emailRe.test(email.value.trim());
    setInvalid(email, document.getElementById('emailErr'), !emailValid);
    if (!emailValid) ok = false;

    const telefono = document.getElementById('telefono');
    const telDigits = telefono.value.replace(/\D/g, '');
    const telValid = telDigits.length === 10;
    setInvalid(telefono, document.getElementById('telefonoErr'), !telValid);
    if (!telValid) ok = false;

    const documento = document.getElementById('documento');
    const docValid = documento.value.trim().replace(/\D/g, '').length >= 6;
    setInvalid(documento, document.getElementById('documentoErr'), !docValid);
    if (!docValid) ok = false;

    const fechaNacimiento = document.getElementById('fecha_nacimiento');
    const fnValid = !!fechaNacimiento.value;
    setInvalid(fechaNacimiento, document.getElementById('fechaNacimientoErr'), !fnValid);
    if (!fnValid) ok = false;

    const password = document.getElementById('password_hash');
    const passValid = password.value.length >= 6;
    setInvalid(password, document.getElementById('passwordErr'), !passValid);
    if (!passValid) ok = false;

    const password2 = document.getElementById('password2');
    const pass2Valid = password2.value === password.value && password.value.length > 0;
    setInvalid(password2, document.getElementById('password2Err'), !pass2Valid);
    if (!pass2Valid) ok = false;

    if (!ok) {
      itzaError('Hay campos incompletos o inválidos. Corrígelos para continuar.');
      return;
    }

    localStorage.setItem('itza_session', JSON.stringify({ email: email.value.trim(), nombre: nombre.value.trim(), loggedAt: Date.now() }));

    itzaSuccess('Cuenta creada con éxito. Ya puedes agendar tu cita.').then(() => {
      window.location.href = 'abono.html';
    });
  });

  document.querySelectorAll('input, select').forEach(inp => {
    inp.addEventListener('input', () => inp.classList.remove('invalid'));
  });
});
