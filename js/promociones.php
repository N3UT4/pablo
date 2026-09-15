<?php
// Recurso JavaScript servido por PHP.
header('Content-Type: application/javascript; charset=utf-8');
?>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('promocionesForm');
  const validarBtn = document.getElementById('validarBtn');
  const cuponValidoDiv = document.getElementById('cuponValido');
  const formAplicacion = document.getElementById('formAplicacion');
  const sinCuponDiv = document.getElementById('sinCupon');
  const codigoCuponInput = document.getElementById('codigo_cupon');
  const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  function setInvalid(input, errEl, isInvalid) {
    input.classList.toggle('invalid', isInvalid);
    if (errEl) errEl.classList.toggle('show', isInvalid);
  }

  function mostrarCupon(promo) {
    document.getElementById('nombrePromo').textContent = promo.nombre;
    document.getElementById('montoPromo').textContent = promo.descuento ? `-${promo.descuento}` : '';
    document.getElementById('descripcionPromo').textContent = promo.descripcion || '';
    document.getElementById('vigenciaPromo').textContent = promo.vigencia || '';

    cuponValidoDiv.style.display = 'block';
    formAplicacion.style.display = 'block';
    sinCuponDiv.style.display = 'none';
  }

  function ocultarCupon() {
    cuponValidoDiv.style.display = 'none';
    formAplicacion.style.display = 'none';
    sinCuponDiv.style.display = 'block';
  }

  let cuponVigente = null;

  validarBtn.addEventListener('click', async function (e) {
    e.preventDefault();
    const codigo = codigoCuponInput.value.trim();

    if (!codigo) {
      setInvalid(codigoCuponInput, document.getElementById('codigoCuponErr'), true);
      return;
    }
    codigoCuponInput.classList.remove('invalid');

    validarBtn.disabled = true;
    const formData = new FormData();
    formData.set('codigo_cupon', codigo);
    const result = await ItzaAPI.postForm('validate-promo', formData);
    validarBtn.disabled = false;

    if (!result.ok) {
      cuponVigente = null;
      ocultarCupon();
      return;
    }

    cuponVigente = codigo.toUpperCase();
    mostrarCupon(result.promo);
  });

  codigoCuponInput.addEventListener('keypress', function (e) {
    if (e.key === 'Enter') {
      validarBtn.click();
    }
  });

  form.addEventListener('submit', async function (e) {
    e.preventDefault();
    const codigo = codigoCuponInput.value.trim();

    if (!codigo || codigo.toUpperCase() !== cuponVigente) {
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

    const submitBtn = form.querySelector('button[type="submit"]');
    if (submitBtn) submitBtn.disabled = true;

    const result = await ItzaAPI.postForm('redeem-promo', form);
    if (submitBtn) submitBtn.disabled = false;

    if (!result.ok) {
      if (result.redirect) {
        itzaError(result.message).then(() => { window.location.href = result.redirect; });
      } else {
        itzaError(result.message || 'No fue posible guardar el cupón.');
      }
      return;
    }

    itzaSuccess(result.message, '¡Cupón guardado!').then(() => {
      form.reset();
      cuponVigente = null;
      cuponValidoDiv.style.display = 'none';
      formAplicacion.style.display = 'none';
      sinCuponDiv.style.display = 'none';
    });
  });

  document.querySelectorAll('input, textarea').forEach(inp => {
    inp.addEventListener('input', () => inp.classList.remove('invalid'));
  });
});
