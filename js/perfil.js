document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('perfilForm');
  const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  function setInvalid(input, errEl, isInvalid) {
    input.classList.toggle('invalid', isInvalid);
    if (errEl) errEl.classList.toggle('show', isInvalid);
  }

  // Cargar datos guardados en localStorage
  function cargarDatos() {
    const session = JSON.parse(localStorage.getItem('itza_session') || '{}');
    if (session.email) document.getElementById('email_perfil').value = session.email;
    if (session.nombre) document.getElementById('nombre_perfil').value = session.nombre;
  }
  cargarDatos();

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    let ok = true;

    const nombre = document.getElementById('nombre_perfil');
    const nombreValid = nombre.value.trim().split(' ').filter(Boolean).length >= 2;
    setInvalid(nombre, document.getElementById('nombrePerfilErr'), !nombreValid);
    if (!nombreValid) ok = false;

    const email = document.getElementById('email_perfil');
    const emailValid = emailRe.test(email.value.trim());
    setInvalid(email, document.getElementById('emailPerfilErr'), !emailValid);
    if (!emailValid) ok = false;

    const telefono = document.getElementById('telefono_perfil');
    const telDigits = telefono.value.replace(/\D/g, '');
    const telValid = telDigits.length === 10;
    setInvalid(telefono, document.getElementById('telefonoPerfilErr'), !telValid);
    if (!telValid) ok = false;

    const documento = document.getElementById('documento_perfil');
    const docValid = documento.value.trim().replace(/\D/g, '').length >= 6;
    setInvalid(documento, document.getElementById('documentoPerfilErr'), !docValid);
    if (!docValid) ok = false;

    const fechaNacimiento = document.getElementById('fecha_nacimiento_perfil');
    const fnValid = !!fechaNacimiento.value;
    setInvalid(fechaNacimiento, document.getElementById('fechaNacimientoPerfilErr'), !fnValid);
    if (!fnValid) ok = false;

    const ciudad = document.getElementById('ciudad');
    const ciudadValid = ciudad.value.trim().length >= 2;
    setInvalid(ciudad, document.getElementById('ciudadErr'), !ciudadValid);
    if (!ciudadValid) ok = false;

    const direccion = document.getElementById('direccion');
    const direccionValid = direccion.value.trim().length >= 5;
    setInvalid(direccion, document.getElementById('direccionErr'), !direccionValid);
    if (!direccionValid) ok = false;

    // Validar cambio de contraseña si se intenta cambiar
    const passActual = document.getElementById('password_actual');
    const passNueva = document.getElementById('password_nueva');
    const passConfirm = document.getElementById('password_confirm');

    if (passNueva.value || passActual.value || passConfirm.value) {
      // Si hay algo, validar todo
      const passNuevaValid = passNueva.value.length >= 6;
      setInvalid(passNueva, document.getElementById('passwordNuevaErr'), !passNuevaValid);
      if (!passNuevaValid) ok = false;

      const passConfirmValid = passConfirm.value === passNueva.value && passNueva.value.length > 0;
      setInvalid(passConfirm, document.getElementById('passwordConfirmErr'), !passConfirmValid);
      if (!passConfirmValid) ok = false;
    }

    if (!ok) {
      itzaError('Hay campos incompletos o inválidos. Corrígelos para continuar.');
      return;
    }

    // Guardar datos en localStorage
    localStorage.setItem('itza_session', JSON.stringify({
      email: email.value.trim(),
      nombre: nombre.value.trim(),
      telefono: telefono.value,
      documento: documento.value,
      ciudad: ciudad.value,
      recibePromo: document.getElementById('recibir_promo').checked,
      updatedAt: Date.now()
    }));

    itzaSuccess('Tu perfil ha sido actualizado correctamente.', 'Cambios guardados').then(() => {
      // Limpiar solo las contraseñas
      passActual.value = '';
      passNueva.value = '';
      passConfirm.value = '';
    });
  });

  document.querySelectorAll('input, select, textarea').forEach(inp => {
    inp.addEventListener('input', () => inp.classList.remove('invalid'));
  });
});
