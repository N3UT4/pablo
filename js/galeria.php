<?php
// Recurso JavaScript servido por PHP.
// Controla el acceso al panel de staff de galería,
// permite subir fotos con drag & drop y validación de campos.
header('Content-Type: application/javascript; charset=utf-8');
?>
document.addEventListener('DOMContentLoaded', function () {
  const STAFF_CODE = 'ITZA-STAFF-2026';
  const accessGate = document.getElementById('accessGate');
  const galeriaCard = document.getElementById('galeriaCard');
  const unlockBtn = document.getElementById('unlockBtn');
  const form = document.getElementById('galeriaForm');
  const uploadBox = document.getElementById('uploadBox');
  const fotosInput = document.getElementById('fotos_trabajo');
  const previewContainer = document.getElementById('previewContainer');
  const preview = document.getElementById('preview');

  // Desbloquea el panel de galería y guarda el código en sesión.
  function unlock(code) {
    accessGate.style.display = 'none';
    galeriaCard.style.display = 'block';
    sessionStorage.setItem('itza_staff_code', code || STAFF_CODE);
  }

  // Si ya se desbloqueó antes, muestra el panel directamente.
  const savedCode = sessionStorage.getItem('itza_staff_code');
  if (savedCode === STAFF_CODE) {
    unlock(savedCode);
  }

  // Botón para desbloquear con código de staff (SweetAlert2)
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
          itzaSuccess('Acceso concedido. Bienvenido(a) al panel de staff.').then(() => unlock(result.value));
        } else {
          itzaError('Código incorrecto. Este contenido es solo para staff autorizado.');
        }
      }
    });
  });

  // Drag and drop: arrastrar fotos al área de subida
  uploadBox.addEventListener('dragover', (e) => {
    e.preventDefault();
    uploadBox.style.background = 'rgba(212, 18, 58, 0.1)';
  });

  uploadBox.addEventListener('dragleave', () => {
    uploadBox.style.background = '';
  });

  uploadBox.addEventListener('drop', (e) => {
    e.preventDefault();
    uploadBox.style.background = '';
    fotosInput.files = e.dataTransfer.files;
    mostrarPreview();
  });

  uploadBox.addEventListener('click', () => fotosInput.click());

  fotosInput.addEventListener('change', mostrarPreview);

  // Muestra vista previa de las fotos seleccionadas.
  function mostrarPreview() {
    preview.innerHTML = '';
    const files = fotosInput.files;

    if (files.length === 0) {
      previewContainer.style.display = 'none';
      return;
    }

    previewContainer.style.display = 'block';

    Array.from(files).forEach((file, index) => {
      const reader = new FileReader();
      reader.onload = (e) => {
        const div = document.createElement('div');
        div.style.position = 'relative';
        div.style.width = '80px';
        div.style.height = '80px';
        div.style.borderRadius = '3px';
        div.style.overflow = 'hidden';
        div.innerHTML = `
          <img src="${e.target.result}" style="width:100%; height:100%; object-fit:cover;">
          <button type="button" style="position:absolute; top:4px; right:4px; background:#D4123A; border:none; color:#fff; width:20px; height:20px; border-radius:50%; cursor:pointer; font-size:12px; display:flex; align-items:center; justify-content:center;" onclick="this.parentElement.remove();">×</button>
        `;
        preview.appendChild(div);
      };
      reader.readAsDataURL(file);
    });
  }

  // Alterna la clase 'invalid' en el input y muestra/oculta el error.
  function setInvalid(input, errEl, isInvalid) {
    if (input.tagName === 'INPUT' || input.tagName === 'SELECT' || input.tagName === 'TEXTAREA') {
      input.classList.toggle('invalid', isInvalid);
    }
    if (errEl) errEl.classList.toggle('show', isInvalid);
  }

  form.addEventListener('submit', async function (e) {
    e.preventDefault();
    let ok = true;

    const titulo = document.getElementById('titulo_trabajo');
    const tituloValid = titulo.value.trim().length >= 3;
    setInvalid(titulo, document.getElementById('tituloTrabajoErr'), !tituloValid);
    if (!tituloValid) ok = false;

    const tatuador = document.getElementById('tatuador_trabajo');
    const tatuadorValid = tatuador.value !== '';
    setInvalid(tatuador, document.getElementById('tatuadorTrabajoErr'), !tatuadorValid);
    if (!tatuadorValid) ok = false;

    const estilo = document.getElementById('estilo_trabajo');
    const estiloValid = estilo.value !== '';
    setInvalid(estilo, document.getElementById('estiloTrabajoErr'), !estiloValid);
    if (!estiloValid) ok = false;

    const fotos = document.getElementById('fotos_trabajo');
    const fotosValid = fotos.files.length > 0;
    if (!fotosValid) {
      document.getElementById('fotosErr').style.display = 'block';
      ok = false;
    } else {
      document.getElementById('fotosErr').style.display = 'none';
    }

    const autoriza = document.getElementById('autoriza_publicacion');
    const autorizaErr = document.getElementById('autorizaErr');
    if (!autoriza.checked) {
      autorizaErr.classList.add('show');
      ok = false;
    } else {
      autorizaErr.classList.remove('show');
    }

    if (!ok) {
      itzaError('Revisa los campos marcados en rojo antes de continuar.');
      return;
    }

    const submitBtn = form.querySelector('button[type="submit"]');
    if (submitBtn) submitBtn.disabled = true;

    const formData = new FormData(form);
    formData.set('staff_code', sessionStorage.getItem('itza_staff_code') || '');

    const result = await ItzaAPI.postForm('gallery-upload', formData);
    if (submitBtn) submitBtn.disabled = false;

    if (!result.ok) {
      itzaError(result.message || 'No fue posible subir las fotos.');
      return;
    }

    itzaSuccess(result.message || '¡Fotos subidas y publicadas!', 'Galería actualizada').then(() => {
      form.reset();
      previewContainer.style.display = 'none';
      preview.innerHTML = '';
    });
  });

  document.querySelectorAll('input, select, textarea').forEach(inp => {
    inp.addEventListener('input', () => inp.classList.remove('invalid'));
  });
});
