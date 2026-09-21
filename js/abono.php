<?php
require_once dirname(__DIR__) . '/config/config.php';
// Recurso JavaScript servido por PHP.
header('Content-Type: application/javascript; charset=utf-8');
?>
window.APP_BASE_URL = <?= json_encode(BASE_URL) ?>;

document.addEventListener('DOMContentLoaded', function () {
  const form = document.querySelector('form.dash-form[action*="book-appointment"]');
  const fechaInput = document.querySelector('input[name="fecha_cita"]');
  const horaInput = document.querySelector('input[name="hora_cita"]');
  const tipoServicio = document.querySelector('select[name="tipo_servicio"]');
  const tamanoSelect = document.querySelector('select[name="tamano"]');
  const personalizadoBox = document.getElementById('personalizadoBox');
  const qrBox = document.getElementById('qrBox');
  const metodoPills = document.querySelectorAll('input[name="metodo_pago"]');

  // Alterna la clase 'invalid' en el input y muestra/oculta el error.
  function setInvalid(input, errEl, isInvalid) {
    input.classList.toggle('invalid', isInvalid);
    if (errEl) errEl.classList.toggle('show', isInvalid);
  }

  // Fecha mínima permitida: hoy
  const hoy = new Date();
  const yyyy = hoy.getFullYear();
  const mm = String(hoy.getMonth() + 1).padStart(2, '0');
  const dd = String(hoy.getDate()).padStart(2, '0');
  const hoyStr = `${yyyy}-${mm}-${dd}`;
  if (fechaInput) fechaInput.setAttribute('min', hoyStr);

  // Mostrar/ocultar campo de diseño personalizado según el servicio seleccionado
  if (tipoServicio) {
    tipoServicio.addEventListener('change', function () {
      const esPersonalizado = tipoServicio.value === 'personalizado';
      if (personalizadoBox) personalizadoBox.style.display = esPersonalizado ? 'flex' : 'none';
      const detalleInput = document.getElementById('detalle_personalizado');
      if (detalleInput) detalleInput.required = esPersonalizado;
    });
  }

  // Mostrar QR solo si el método de pago es Nequi
  metodoPills.forEach(pill => {
    pill.addEventListener('change', () => {
      if (qrBox) qrBox.classList.toggle('show', pill.value === 'nequi' && pill.checked);
    });
  });

  // Verifica que la hora esté entre 08:00 y 21:00
  function horaEnRango(horaStr) {
    if (!horaStr) return false;
    const [h, m] = horaStr.split(':').map(Number);
    const minutos = h * 60 + m;
    return minutos >= (8 * 60) && minutos <= (21 * 60);
  }

  if (form) {
    form.addEventListener('submit', async function (e) {
      e.preventDefault();
      let ok = true;

      const tatuador = document.querySelector('input[name="id_tatuador"]:checked');
      const tatuadorValid = !!tatuador;
      setInvalid(tatuador, document.getElementById('tatuadorErr'), !tatuadorValid);
      if (!tatuadorValid) ok = false;

      if (tipoServicio) {
        const tipoValid = tipoServicio.value !== '';
        setInvalid(tipoServicio, document.getElementById('tipoServicioErr'), !tipoValid);
        if (!tipoValid) ok = false;
      }

      if (tipoServicio && tipoServicio.value === 'personalizado') {
        const detalle = document.getElementById('detalle_personalizado');
        const detalleValid = detalle && detalle.value.trim().length >= 10;
        setInvalid(detalle, document.getElementById('personalizadoErr'), !detalleValid);
        if (!detalleValid) ok = false;
      }

      // Validar tamaño del tatuaje
      if (tamanoSelect) {
        const tamanoValid = tamanoSelect.value !== '';
        setInvalid(tamanoSelect, document.getElementById('tamanoErr'), !tamanoValid);
        if (!tamanoValid) ok = false;
      }

      if (fechaInput) {
        let fechaValid = !!fechaInput.value && fechaInput.value >= hoyStr;
        setInvalid(fechaInput, document.getElementById('fechaErr'), !fechaValid);
        if (!fechaValid) ok = false;
      }

      if (horaInput) {
        let horaValid = horaEnRango(horaInput.value);
        if (horaValid && fechaInput && fechaInput.value === hoyStr) {
          const ahora = new Date();
          const [h, m] = horaInput.value.split(':').map(Number);
          const horaSeleccionada = new Date();
          horaSeleccionada.setHours(h, m, 0, 0);
          if (horaSeleccionada < ahora) horaValid = false;
        }
        setInvalid(horaInput, document.getElementById('horaErr'), !horaValid);
        if (!horaValid) ok = false;
      }

      const montoInput = document.querySelector('input[name="monto"]');
      if (montoInput) {
        const montoValid = Number(montoInput.value) > 0;
        setInvalid(montoInput, document.getElementById('montoErr'), !montoValid);
        if (!montoValid) ok = false;
      }

      const metodoSeleccionado = document.querySelector('input[name="metodo_pago"]:checked');
      const metodoError = document.getElementById('metodoErr');
      if (!metodoSeleccionado) {
        if (metodoError) metodoError.classList.add('show');
        ok = false;
      } else {
        if (metodoError) metodoError.classList.remove('show');
      }

      if (metodoSeleccionado && metodoSeleccionado.value === 'nequi') {
        const comprobante = document.querySelector('input[name="comprobante"]');
        const compValid = comprobante && comprobante.value.trim().length >= 4;
        setInvalid(comprobante, document.getElementById('comprobanteErr'), !compValid);
        if (!compValid) ok = false;
      }

      if (!ok) {
        itzaError('Revisa los campos marcados en rojo antes de continuar.');
        return;
      }

      const submitBtn = form.querySelector('button[type="submit"]');
      if (submitBtn) submitBtn.disabled = true;

      const result = await ItzaAPI.postForm('book-appointment', form);
      if (submitBtn) submitBtn.disabled = false;

      if (!result.ok) {
        if (result.redirect) {
          const redirectUrl = /^https?:\/\//i.test(result.redirect) || result.redirect.startsWith('/')
            ? result.redirect
            : window.APP_BASE_URL + result.redirect.replace(/^\.?\//, '');
          itzaError(result.message).then(() => { window.location.href = redirectUrl; });
        } else {
          itzaError(result.message || 'No fue posible agendar la cita.');
        }
        return;
      }

      itzaSuccess(result.message || 'Tu cita quedó agendada y tu abono registrado. ¡Te esperamos!').then(() => {
        form.reset();
        if (personalizadoBox) personalizadoBox.style.display = 'none';
        if (qrBox) qrBox.classList.remove('show');
      });
    });
  }

  document.querySelectorAll('input, select, textarea').forEach(inp => {
    inp.addEventListener('input', () => inp.classList.remove('invalid'));
  });
});
