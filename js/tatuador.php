<?php
require_once dirname(__DIR__) . '/config/config.php';
// Recurso JavaScript servido por PHP.
header('Content-Type: application/javascript; charset=utf-8');
?>
window.APP_BASE_URL = <?= json_encode(BASE_URL) ?>;

(() => {
    'use strict';

    const csrf = window.__CSRF_TOKEN__ || '<?php echo csrf_token(); ?>';

    // --- Dropdown del menú de tatuador ---
    const dropdownBtn = document.querySelector('.artist-dropdown-btn');
    const dropdownMenu = document.querySelector('.artist-dropdown-menu');

    if (dropdownBtn && dropdownMenu) {
        dropdownBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
            dropdownBtn.setAttribute('aria-expanded', dropdownMenu.style.display === 'block');
        });

        document.addEventListener('click', function () {
            dropdownMenu.style.display = 'none';
            dropdownBtn.setAttribute('aria-expanded', 'false');
        });
    }

    // --- Cambio de estado de cita ---
    const estadoSelects = document.querySelectorAll('.estado-select');
    estadoSelects.forEach(function (select) {
        select.addEventListener('change', async function () {
            const citaId = this.getAttribute('data-cita-id');
            const nuevoEstado = this.value;
            const row = this.closest('tr');
            const badge = row.querySelector('.status-badge');
            const estadoLabels = {
                'pendiente': 'Pendiente',
                'confirmada': 'Confirmada',
                'completada': 'Finalizada',
                'cancelada': 'Cancelada'
            };
            const estadoColors = {
                'pendiente': 'var(--gold)',
                'confirmada': '#3b82f6',
                'completada': '#10b981',
                'cancelada': '#ef4444'
            };

            const formData = new FormData();
            formData.append('csrf_token', csrf);
            formData.append('cita_id', citaId);
            formData.append('estado', nuevoEstado);

            try {
                const r = await window.ItzaAPI.postForm('artist-update-estado', formData);
                if (r.ok) {
                    badge.textContent = estadoLabels[nuevoEstado] || nuevoEstado;
                    badge.style.background = estadoColors[nuevoEstado] + '1A';
                    badge.style.color = estadoColors[nuevoEstado];
                } else {
                    Swal.fire('Error', r.message || 'No se pudo actualizar el estado.', 'error');
                    // Revertir el select al estado anterior
                    select.value = badge.dataset.estado || select.options[0].value;
                }
            } catch (e) {
                Swal.fire('Error', 'No se pudo conectar al servidor.', 'error');
            }
        });
    });

    // --- Guardar horario por día ---
    const saveButtons = document.querySelectorAll('.save-dia');
    saveButtons.forEach(function (btn) {
        btn.addEventListener('click', async function () {
            const dia = parseInt(this.getAttribute('data-dia'), 10);
            const row = this.closest('.schedule-row');
            const chkDisponible = row.querySelector('.dia-disponible');
            const inputInicio = row.querySelector('.hora-inicio');
            const inputFin = row.querySelector('.hora-fin');

            const disponible = chkDisponible.checked ? 1 : 0;
            let horaInicio = inputInicio ? inputInicio.value + ':00' : '09:00:00';
            let horaFin = inputFin ? inputFin.value + ':00' : '17:00:00';

            // Normalizar format HH:MM:SS
            if (!horaInicio.includes(':')) horaInicio = '09:00:00';
            if (!horaFin.includes(':')) horaFin = '17:00:00';

            const formData = new FormData();
            formData.append('csrf_token', csrf);
            formData.append('dia', dia);
            formData.append('hora_inicio', horaInicio);
            formData.append('hora_fin', horaFin);
            formData.append('disponible', disponible);

            const originalText = this.textContent;
            this.textContent = 'Guardando...';
            this.disabled = true;

            try {
                const r = await window.ItzaAPI.postForm('artist-save-schedule', formData);
                if (r.ok) {
                    this.textContent = 'Guardado';
                    setTimeout(() => { this.textContent = originalText; this.disabled = false; }, 1500);
                } else {
                    this.textContent = originalText;
                    this.disabled = false;
                    Swal.fire('Error', r.message || 'No se pudo guardar el horario.', 'error');
                }
            } catch (e) {
                this.textContent = originalText;
                this.disabled = false;
                Swal.fire('Error', 'No se pudo conectar al servidor.', 'error');
            }
        });
    });

    // --- Toggle inputs al marcar/desmarcar día ---
    const diaCheckboxes = document.querySelectorAll('.dia-disponible');
    diaCheckboxes.forEach(function (chk) {
        chk.addEventListener('change', function () {
            const row = this.closest('.schedule-row');
            const inputInicio = row.querySelector('.hora-inicio');
            const inputFin = row.querySelector('.hora-fin');
            if (inputInicio && inputFin) {
                inputInicio.disabled = !this.checked;
                inputFin.disabled = !this.checked;
            }
        });
    });

    // --- Salir del modo tatuador ---
    window.salirModoTatuador = function () {
        window.location.href = window.APP_BASE_URL + 'index.php?action=artist-switch-mode&exit=1';
    };
})();
