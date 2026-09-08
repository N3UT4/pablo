document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('contactForm');
  const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  function setInvalid(input, errEl, isInvalid) {
    input.classList.toggle('invalid', isInvalid);
    if (errEl) errEl.classList.toggle('show', isInvalid);
  }

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    let ok = true;

    const nombre = document.getElementById('nombre_contacto');
    const nombreValid = nombre.value.trim().split(' ').filter(Boolean).length >= 2;
    setInvalid(nombre, document.getElementById('nombreContactoErr'), !nombreValid);
    if (!nombreValid) ok = false;

    const email = document.getElementById('email_contacto');
    const emailValid = emailRe.test(email.value.trim());
    setInvalid(email, document.getElementById('emailContactoErr'), !emailValid);
    if (!emailValid) ok = false;

    const telefono = document.getElementById('telefono_contacto');
    if (telefono.value.trim()) {
      const telDigits = telefono.value.replace(/\D/g, '');
      const telValid = telDigits.length >= 10;
      setInvalid(telefono, document.getElementById('telefonoContactoErr'), !telValid);
      if (!telValid) ok = false;
    } else {
      telefono.classList.remove('invalid');
    }

    const asunto = document.getElementById('asunto');
    const asuntoValid = asunto.value !== '';
    setInvalid(asunto, document.getElementById('asuntoErr'), !asuntoValid);
    if (!asuntoValid) ok = false;

    const mensaje = document.getElementById('mensaje');
    const mensajeValid = mensaje.value.trim().length >= 10;
    setInvalid(mensaje, document.getElementById('mensajeErr'), !mensajeValid);
    if (!mensajeValid) ok = false;

    const acepta = document.getElementById('acepta_contacto');
    const aceptaErr = document.getElementById('aceptaContactoErr');
    if (!acepta.checked) {
      aceptaErr.classList.add('show');
      ok = false;
    } else {
      aceptaErr.classList.remove('show');
    }

    if (!ok) {
      itzaError('Revisa los campos marcados en rojo antes de continuar.');
      return;
    }

    itzaSuccess('¡Tu consulta fue enviada! Nos pondremos en contacto pronto.', 'Consulta recibida').then(() => {
      form.reset();
    });
  });

  document.querySelectorAll('input, select, textarea').forEach(inp => {
    inp.addEventListener('input', () => inp.classList.remove('invalid'));
  });
});
