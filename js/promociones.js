document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('promocionesForm');
  const validarBtn = document.getElementById('validarBtn');
  const cuponValidoDiv = document.getElementById('cuponValido');
  const formAplicacion = document.getElementById('formAplicacion');
  const sinCuponDiv = document.getElementById('sinCupon');
  const codigoCuponInput = document.getElementById('codigo_cupon');
  const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  // Base de datos de cupones (en producción sería desde backend)
  const cupones = {
    'WELCOME20': {
      nombre: 'Bienvenida 20%',
      descuento: '20%',
      monto: 50000,
      descripcion: 'Válido para clientes nuevos en tatuajes desde $150.000',
      vigencia: '31 dic 2026'
    },
    'ITZA2026': {
      nombre: 'Descuento ITZA 2026',
      descuento: '15%',
      monto: 20000,
      descripcion: 'Válido para tatuajes desde $100.000',
      vigencia: '31 dic 2026'
    },
    'REFERIDOS': {
      nombre: 'Regalo por referir',
      descuento: '$30.000',
      monto: 30000,
      descripcion: 'Por cada amigo que refiera y se tatúe',
      vigencia: '31 dic 2026'
    },
    'LOYALTY': {
      nombre: 'Cliente leal',
      descuento: '25%',
      monto: 75000,
      descripcion: 'Válido para clientes con más de 3 sesiones',
      vigencia: '31 dic 2026'
    }
  };

  function setInvalid(input, errEl, isInvalid) {
    input.classList.toggle('invalid', isInvalid);
    if (errEl) errEl.classList.toggle('show', isInvalid);
  }

  function mostrarCupon(codigo) {
    const cupon = cupones[codigo.toUpperCase()];
    if (!cupon) {
      cuponValidoDiv.style.display = 'none';
      formAplicacion.style.display = 'none';
      sinCuponDiv.style.display = 'block';
      return;
    }

    document.getElementById('nombrePromo').textContent = cupon.nombre;
    document.getElementById('montoPromo').textContent = `-$${cupon.monto.toLocaleString('es-CO')}`;
    document.getElementById('descripcionPromo').textContent = cupon.descripcion;
    document.getElementById('vigenciaPromo').textContent = `Válido hasta: ${cupon.vigencia}`;

    cuponValidoDiv.style.display = 'block';
    formAplicacion.style.display = 'block';
    sinCuponDiv.style.display = 'none';
  }

  validarBtn.addEventListener('click', function (e) {
    e.preventDefault();
    const codigo = codigoCuponInput.value.trim();

    if (!codigo) {
      setInvalid(codigoCuponInput, document.getElementById('codigoCuponErr'), true);
      return;
    }

    mostrarCupon(codigo);
    codigoCuponInput.classList.remove('invalid');
  });

  codigoCuponInput.addEventListener('keypress', function (e) {
    if (e.key === 'Enter') {
      validarBtn.click();
    }
  });

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    const codigo = codigoCuponInput.value.trim();

    if (!codigo || !cupones[codigo.toUpperCase()]) {
      itzaError('Valida un cupón antes de continuar.');
      return;
    }

    let ok = true;

    const email = document.getElementById('email_cupon');
    const emailValid = emailRe.test(email.value.trim());
    setInvalid(email, document.getElementById('emailCuponErr'), !emailValid);
    if (!emailValid) ok = false;

    const fecha = document.getElementById('fecha_uso');
    const hoy = new Date().toISOString().split('T')[0];
    const fechaValid = fecha.value && fecha.value >= hoy;
    setInvalid(fecha, document.getElementById('fechaUsoErr'), !fechaValid);
    if (!fechaValid) ok = false;

    const acepta = document.getElementById('acepta_terminos');
    const aceptaErr = document.getElementById('aceptaTerminosErr');
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

    const cupon = cupones[codigo.toUpperCase()];
    itzaSuccess(
      `Cupón "${cupon.nombre}" registrado en tu cuenta. Úsalo en tu próxima cita.`,
      '¡Cupón guardado!'
    ).then(() => {
      form.reset();
      cuponValidoDiv.style.display = 'none';
      formAplicacion.style.display = 'none';
      sinCuponDiv.style.display = 'none';
    });
  });

  document.querySelectorAll('input, textarea').forEach(inp => {
    inp.addEventListener('input', () => inp.classList.remove('invalid'));
  });
});
