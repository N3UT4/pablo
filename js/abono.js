document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('abonoForm');
  const fechaInput = document.getElementById('fecha_cita');
  const horaInput = document.getElementById('hora_cita');
  const tipoServicio = document.getElementById('tipo_servicio');
  const personalizadoBox = document.getElementById('personalizadoBox');
  const qrBox = document.getElementById('qrBox');
  const metodoPills = document.querySelectorAll('input[name="metodo_pago"]');

  function setInvalid(input, errEl, isInvalid) {
    input.classList.toggle('invalid', isInvalid);
    if (errEl) errEl.classList.toggle('show', isInvalid);
  }

  // Fecha mínima: hoy
  const hoy = new Date();
  const yyyy = hoy.getFullYear();
  const mm = String(hoy.getMonth() + 1).padStart(2, '0');
  const dd = String(hoy.getDate()).padStart(2, '0');
  const hoyStr = `${yyyy}-${mm}-${dd}`;
  fechaInput.setAttribute('min', hoyStr);

  // Mostrar/ocultar campo de diseño personalizado
  tipoServicio.addEventListener('change', function () {
    const esPersonalizado = tipoServicio.value === 'personalizado';
    personalizadoBox.style.display = esPersonalizado ? 'flex' : 'none';
    document.getElementById('detalle_personalizado').required = esPersonalizado;
  });

  // Mostrar QR solo si el método es Nequi
  metodoPills.forEach(pill => {
    pill.addEventListener('change', () => {
      qrBox.classList.toggle('show', pill.value === 'nequi' && pill.checked);
    });
  });

  function horaEnRango(horaStr) {
    if (!horaStr) return false;
    const [h, m] = horaStr.split(':').map(Number);
    const minutos = h * 60 + m;
    return minutos >= (8 * 60) && minutos <= (21 * 60);
  }

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    let ok = true;

    const tatuador = document.getElementById('id_tatuador');
    const tatuadorValid = tatuador.value !== '';
    setInvalid(tatuador, document.getElementById('tatuadorErr'), !tatuadorValid);
    if (!tatuadorValid) ok = false;

    const tipoValid = tipoServicio.value !== '';
    setInvalid(tipoServicio, document.getElementById('tipoServicioErr'), !tipoValid);
    if (!tipoValid) ok = false;

    if (tipoServicio.value === 'personalizado') {
      const detalle = document.getElementById('detalle_personalizado');
      const detalleValid = detalle.value.trim().length >= 10;
      setInvalid(detalle, document.getElementById('personalizadoErr'), !detalleValid);
      if (!detalleValid) ok = false;
    }

    let fechaValid = !!fechaInput.value && fechaInput.value >= hoyStr;
    setInvalid(fechaInput, document.getElementById('fechaErr'), !fechaValid);
    if (!fechaValid) ok = false;

    let horaValid = horaEnRango(horaInput.value);
    if (horaValid && fechaInput.value === hoyStr) {
      const ahora = new Date();
      const [h, m] = horaInput.value.split(':').map(Number);
      const horaSeleccionada = new Date();
      horaSeleccionada.setHours(h, m, 0, 0);
      if (horaSeleccionada < ahora) horaValid = false;
    }
    setInvalid(horaInput, document.getElementById('horaErr'), !horaValid);
    if (!horaValid) ok = false;

    const monto = document.getElementById('monto');
    const montoValid = Number(monto.value) > 0;
    setInvalid(monto, document.getElementById('montoErr'), !montoValid);
    if (!montoValid) ok = false;

    const metodoSeleccionado = document.querySelector('input[name="metodo_pago"]:checked');
    const metodoError = document.getElementById('metodoErr');
    if (!metodoSeleccionado) {
      metodoError.classList.add('show');
      ok = false;
    } else {
      metodoError.classList.remove('show');
    }

    if (metodoSeleccionado && metodoSeleccionado.value === 'nequi') {
      const comprobante = document.getElementById('comprobante');
      const compValid = comprobante.value.trim().length >= 4;
      setInvalid(comprobante, document.getElementById('comprobanteErr'), !compValid);
      if (!compValid) ok = false;
    }

    if (!ok) {
      itzaError('Revisa los campos marcados en rojo antes de continuar.');
      return;
    }

    itzaSuccess('Tu cita quedó agendada y tu abono registrado. ¡Te esperamos!').then(() => {
      form.reset();
      personalizadoBox.style.display = 'none';
      qrBox.classList.remove('show');
    });
  });

  document.querySelectorAll('input, select, textarea').forEach(inp => {
    inp.addEventListener('input', () => inp.classList.remove('invalid'));
  });
});
