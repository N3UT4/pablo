<?php
// Recurso JavaScript servido por PHP.
// Controla el acceso al panel de tatuador desde el navegador.
// Valida el código de staff y envía formularios al backend.
header('Content-Type: application/javascript; charset=utf-8');
?>
window.APP_BASE_URL = <?= json_encode(BASE_URL) ?>;

document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('tatuadorForm');
  if (!form) return;

  // Alterna la clase 'invalid' en el input y muestra/oculta el error.
  function setInvalid(input, errEl, isInvalid) {
    input.classList.toggle('invalid', isInvalid);
    if (errEl) errEl.classList.toggle('show', isInvalid);
  }

  form.addEventListener('submit', async function (e) {
    e.preventDefault();
    let ok = true;

    const submitBtn = form.querySelector('button[type="submit"]');
    if (submitBtn) submitBtn.disabled = true;

    const result = await ItzaAPI.postForm('artist-update-estado', form);
    if (submitBtn) submitBtn.disabled = false;

    if (!result.ok) {
      itzaError(result.message || 'No fue posible actualizar el estado.');
      return;
    }

    itzaSuccess(result.message || 'Estado actualizado correctamente.').then(() => {
      form.reset();
    });
  });

  document.querySelectorAll('input, select, textarea').forEach(inp => {
    inp.addEventListener('input', () => inp.classList.remove('invalid'));
  });
});
