document.addEventListener('DOMContentLoaded', function () {
  const STAFF_CODE = 'ITZA-STAFF-2026';
  const accessGate = document.getElementById('accessGate');
  const consentCard = document.getElementById('consentCard');
  const unlockBtn = document.getElementById('unlockBtn');

  function unlock() {
    accessGate.style.display = 'none';
    consentCard.style.display = 'block';
    sessionStorage.setItem('itza_staff_access', 'true');
  }

  // Si ya se validó el acceso en esta sesión del navegador, saltar el gate
  if (sessionStorage.getItem('itza_staff_access') === 'true') {
    unlock();
  }

  unlockBtn.addEventListener('click', function () {
    Swal.fire({
      title: 'Código de acceso',
      input: 'password',
      inputPlaceholder: 'Código de staff',
      background: '#161616',
      color: '#F7F3EC',
      confirmButtonColor: '#D4123A',
      confirmButtonText: 'Validar',
      showCancelButton: true,
      cancelButtonText: 'Cancelar',
      cancelButtonColor: '#333'
    }).then(function (result) {
      if (result.isConfirmed) {
        if (result.value === STAFF_CODE) {
          itzaSuccess('Acceso concedido. Bienvenido(a) al panel de staff.').then(unlock);
        } else {
          itzaError('Código incorrecto. Este contenido es solo para staff autorizado.');
        }
      }
    });
  });

  const form = document.getElementById('consentimientoForm');
  const fechaNacimiento = document.getElementById('fecha_nacimiento_cliente');
  const guardianBox = document.getElementById('guardianBox');
  const guardianNote = document.getElementById('guardianNote');

  function setInvalid(input, errEl, isInvalid) {
    input.classList.toggle('invalid', isInvalid);
    if (errEl) errEl.classList.toggle('show', isInvalid);
  }

  function calcularEdad(fechaStr) {
    const nacimiento = new Date(fechaStr);
    const hoy = new Date();
    let edad = hoy.getFullYear() - nacimiento.getFullYear();
    const m = hoy.getMonth() - nacimiento.getMonth();
    if (m < 0 || (m === 0 && hoy.getDate() < nacimiento.getDate())) edad--;
    return edad;
  }

  let esMenor = false;

  fechaNacimiento.addEventListener('change', function () {
    if (!fechaNacimiento.value) return;
    const edad = calcularEdad(fechaNacimiento.value);
    esMenor = edad < 18;
    guardianBox.classList.toggle('show', esMenor);
    guardianNote.style.display = esMenor ? 'flex' : 'none';
    document.querySelectorAll('#guardianBox input').forEach(inp => {
      inp.required = esMenor;
    });
  });

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    let ok = true;

    const nombre = document.getElementById('nombre_cliente_consent');
    const nombreValid = nombre.value.trim().split(' ').filter(Boolean).length >= 2;
    setInvalid(nombre, document.getElementById('nombreConsentErr'), !nombreValid);
    if (!nombreValid) ok = false;

    const documento = document.getElementById('documento_consent');
    const docValid = documento.value.trim().replace(/\D/g, '').length >= 6;
    setInvalid(documento, document.getElementById('documentoConsentErr'), !docValid);
    if (!docValid) ok = false;

    const fnValid = !!fechaNacimiento.value;
    setInvalid(fechaNacimiento, document.getElementById('fechaNacimientoConsentErr'), !fnValid);
    if (!fnValid) ok = false;

    const procedimiento = document.getElementById('procedimiento');
    const procValid = procedimiento.value.trim().length >= 5;
    setInvalid(procedimiento, document.getElementById('procedimientoErr'), !procValid);
    if (!procValid) ok = false;

    const aceptaRiesgos = document.getElementById('acepta_riesgos');
    const riesgosError = document.getElementById('riesgosErr');
    if (!aceptaRiesgos.checked) {
      riesgosError.classList.add('show');
      ok = false;
    } else {
      riesgosError.classList.remove('show');
    }

    const firmaCliente = document.getElementById('firma_cliente');
    const firmaValid = firmaCliente.value.trim().length >= 3;
    setInvalid(firmaCliente, document.getElementById('firmaClienteErr'), !firmaValid);
    if (!firmaValid) ok = false;

    if (esMenor) {
      const acudienteNombre = document.getElementById('acudiente_nombre');
      const acudienteNombreValid = acudienteNombre.value.trim().split(' ').filter(Boolean).length >= 2;
      setInvalid(acudienteNombre, document.getElementById('acudienteNombreErr'), !acudienteNombreValid);
      if (!acudienteNombreValid) ok = false;

      const acudienteDocumento = document.getElementById('acudiente_documento');
      const acudienteDocValid = acudienteDocumento.value.trim().replace(/\D/g, '').length >= 6;
      setInvalid(acudienteDocumento, document.getElementById('acudienteDocumentoErr'), !acudienteDocValid);
      if (!acudienteDocValid) ok = false;

      const parentesco = document.getElementById('parentesco');
      const parentescoValid = parentesco.value !== '';
      setInvalid(parentesco, document.getElementById('parentescoErr'), !parentescoValid);
      if (!parentescoValid) ok = false;

      const firmaAcudiente = document.getElementById('firma_acudiente');
      const firmaAcudienteValid = firmaAcudiente.value.trim().length >= 3;
      setInvalid(firmaAcudiente, document.getElementById('firmaAcudienteErr'), !firmaAcudienteValid);
      if (!firmaAcudienteValid) ok = false;
    }

    if (!ok) {
      itzaError('Completa todos los campos requeridos del consentimiento antes de firmar.');
      return;
    }

    itzaSuccess('Consentimiento informado registrado y firmado con éxito.').then(() => {
      form.reset();
      guardianBox.classList.remove('show');
      guardianNote.style.display = 'none';
    });
  });

  document.querySelectorAll('input, select, textarea').forEach(inp => {
    inp.addEventListener('input', () => inp.classList.remove('invalid'));
  });
});
