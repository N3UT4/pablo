<?php
$citas = $citas ?? [];
$metrics = $metrics ?? [
    'total_citas' => 0,
    'pendientes' => 0,
    'confirmadas' => 0,
    'completadas' => 0,
    'canceladas' => 0,
    'citas_mes' => 0,
    'total_abonos' => 0,
];
$pagos = $pagos ?? [];
?>
<div id="dashboardRoot" style="position:relative;">
  <div id="dashboardSkeleton" style="display:block;">
    <div class="dash-stats" style="margin-bottom:24px;">
      <div style="height:120px;background:rgba(255,255,255,0.04);border-radius:10px;animation:pulse 1.5s ease-in-out infinite;"></div>
      <div style="height:120px;background:rgba(255,255,255,0.04);border-radius:10px;animation:pulse 1.5s ease-in-out infinite;animation-delay:0.15s;"></div>
      <div style="height:120px;background:rgba(255,255,255,0.04);border-radius:10px;animation:pulse 1.5s ease-in-out infinite;animation-delay:0.3s;"></div>
      <div style="height:120px;background:rgba(255,255,255,0.04);border-radius:10px;animation:pulse 1.5s ease-in-out infinite;animation-delay:0.45s;"></div>
    </div>
    <div class="glass-card" style="margin-bottom:24px;padding:24px;">
      <div style="height:24px;width:200px;background:rgba(255,255,255,0.06);border-radius:4px;margin-bottom:20px;animation:pulse 1.5s ease-in-out infinite;"></div>
      <?php for ($i = 0; $i < 5; $i++): ?>
        <div style="display:flex;gap:16px;margin-bottom:16px;align-items:center;">
          <div style="width:40px;height:40px;background:rgba(255,255,255,0.04);border-radius:8px;animation:pulse 1.5s ease-in-out infinite;"></div>
          <div style="flex:1;">
            <div style="height:14px;width:60%;background:rgba(255,255,255,0.06);border-radius:4px;margin-bottom:8px;animation:pulse 1.5s ease-in-out infinite;"></div>
            <div style="height:10px;width:40%;background:rgba(255,255,255,0.04);border-radius:4px;animation:pulse 1.5s ease-in-out infinite;"></div>
          </div>
          <div style="width:80px;height:24px;background:rgba(255,255,255,0.04);border-radius:3px;animation:pulse 1.5s ease-in-out infinite;"></div>
        </div>
      <?php endfor; ?>
    </div>
  </div>

  <div id="dashboardContent" style="display:none;">
    <div class="dash-stats" style="margin-bottom:24px;">
      <div class="dash-stat-card">
        <div class="stat-icon"><i class="fa-solid fa-calendar"></i></div>
        <div class="stat-value" id="statTotal"><?= (int) $metrics['total_citas'] ?></div>
        <div class="stat-label">Total Citas</div>
      </div>
      <div class="dash-stat-card stat-success">
        <div class="stat-icon"><i class="fa-solid fa-coins"></i></div>
        <div class="stat-value">$<?= number_format((float) $metrics['total_abonos'], 0, ',', '.') ?></div>
        <div class="stat-label">Saldo Recaudado</div>
      </div>
      <div class="dash-stat-card stat-warning">
        <div class="stat-icon"><i class="fa-solid fa-hourglass-half"></i></div>
        <div class="stat-value" id="statPendientes"><?= (int) $metrics['pendientes'] ?></div>
        <div class="stat-label">Citas Pendientes</div>
      </div>
      <div class="dash-stat-card stat-info">
        <div class="stat-icon"><i class="fa-solid fa-clipboard-check"></i></div>
        <div class="stat-value" id="statMes"><?= (int) $metrics['citas_mes'] ?></div>
        <div class="stat-label">Citas este Mes</div>
      </div>
    </div>

    <div class="dash-grid-2" style="margin-bottom:24px;">
      <div class="glass-card">
        <div class="dash-section-title">
          <i class="fa-solid fa-chart-pie"></i>
          <h2>Resumen por Estado</h2>
        </div>
        <div style="display:flex;flex-direction:column;gap:14px;">
          <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid rgba(247,243,236,0.06);">
            <span style="display:flex;align-items:center;gap:8px;color:var(--bone-dim);font-size:13px;">
              <span style="width:10px;height:10px;border-radius:50%;background:var(--gold);display:inline-block;"></span>
              Pendientes
            </span>
            <strong style="color:var(--gold);font-size:18px;"><?= (int) $metrics['pendientes'] ?></strong>
          </div>
          <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid rgba(247,243,236,0.06);">
            <span style="display:flex;align-items:center;gap:8px;color:var(--bone-dim);font-size:13px;">
              <span style="width:10px;height:10px;border-radius:50%;background:#60a5fa;display:inline-block;"></span>
              Confirmadas
            </span>
            <strong style="color:#60a5fa;font-size:18px;"><?= (int) $metrics['confirmadas'] ?></strong>
          </div>
          <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid rgba(247,243,236,0.06);">
            <span style="display:flex;align-items:center;gap:8px;color:var(--bone-dim);font-size:13px;">
              <span style="width:10px;height:10px;border-radius:50%;background:#34d399;display:inline-block;"></span>
              Completadas
            </span>
            <strong style="color:#34d399;font-size:18px;"><?= (int) $metrics['completadas'] ?></strong>
          </div>
          <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;">
            <span style="display:flex;align-items:center;gap:8px;color:var(--bone-dim);font-size:13px;">
              <span style="width:10px;height:10px;border-radius:50%;background:#f87171;display:inline-block;"></span>
              Canceladas
            </span>
            <strong style="color:#f87171;font-size:18px;"><?= (int) $metrics['canceladas'] ?></strong>
          </div>
        </div>
      </div>

      <div class="glass-card">
        <div class="dash-section-title">
          <i class="fa-solid fa-money-bill-wave"></i>
          <h2>Últimos Abonos</h2>
        </div>
        <?php if (!empty($pagos)): ?>
          <div style="display:flex;flex-direction:column;gap:10px;">
            <?php foreach ($pagos as $pago): ?>
              <div style="display:flex;justify-content:space-between;align-items:center;padding:12px;background:rgba(247,243,236,0.03);border-radius:8px;border:1px solid rgba(247,243,236,0.04);">
                <div>
                  <div style="font-size:13px;font-weight:600;color:var(--bone);">Cita #<?= (int) $pago['cita_id'] ?></div>
                  <div style="font-size:11px;color:var(--bone-dim);">
                    <?= htmlspecialchars($pago['cliente'] ?? '-') ?> · <?= htmlspecialchars(ucfirst($pago['metodo'] ?? '')) ?>
                  </div>
                </div>
                <div style="text-align:right;">
                  <div style="font-size:15px;font-weight:700;color:#34d399;">$<?= number_format((float) $pago['monto'], 0, ',', '.') ?></div>
                  <span class="badge <?= ($pago['estado_pago'] ?? 'pendiente') === 'verificado' ? 'badge-completada' : 'badge-pendiente' ?>">
                    <?= htmlspecialchars(ucfirst($pago['estado_pago'] ?? 'pendiente')) ?>
                  </span>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <div class="dash-empty" style="padding:32px;">
            <i class="fa-solid fa-coins"></i>
            <p>Sin abonos registrados aún.</p>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <div class="glass-card">
      <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:20px;">
        <div class="dash-section-title" style="margin-bottom:0;border-bottom:none;padding-bottom:0;">
          <i class="fa-solid fa-list"></i>
          <h2>Historial de Citas</h2>
        </div>
      </div>

      <div id="tableSkeleton" style="display:none;">
        <div style="display:flex;flex-direction:column;gap:12px;padding:16px;">
          <?php for ($i = 0; $i < 4; $i++): ?>
            <div style="height:48px;background:rgba(255,255,255,0.03);border-radius:6px;animation:pulse 1.5s ease-in-out infinite;"></div>
          <?php endfor; ?>
        </div>
      </div>

      <div id="tableContent">
        <div class="dash-table-wrapper">
          <table class="dash-table" id="citasTable">
            <thead>
              <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Tatuador</th>
                <th>Servicio</th>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Estado</th>
                <th>Abono</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody id="citasBody">
              <?php foreach ($citas as $cita): ?>
                <tr data-cita-id="<?= (int) $cita['id'] ?>">
                  <td>#<?= (int) $cita['id'] ?></td>
                  <td><?= htmlspecialchars($cita['cliente'] ?? '-') ?></td>
                  <td><?= htmlspecialchars($cita['tatuador'] ?? '-') ?></td>
                  <td><?= htmlspecialchars($cita['servicio'] ?? '-') ?></td>
                  <td style="font-size:12px;white-space:nowrap;"><?= htmlspecialchars(date('d/m/Y', strtotime($cita['fecha_cita']))) ?></td>
                  <td style="font-size:12px;white-space:nowrap;"><?= htmlspecialchars(date('H:i', strtotime($cita['hora_cita']))) ?></td>
                  <td>
                    <span class="badge badge-<?= htmlspecialchars($cita['estado'] ?? 'pendiente') ?>" id="estado-badge-<?= (int) $cita['id'] ?>">
                      <?= htmlspecialchars(ucfirst($cita['estado'] ?? 'pendiente')) ?>
                    </span>
                  </td>
                  <td style="font-weight:600;">
                    <?php if (isset($cita['monto']) && (float) $cita['monto'] > 0): ?>
                      $<?= number_format((float) $cita['monto'], 0, ',', '.') ?>
                    <?php else: ?>
                      <span style="color:var(--bone-subtle);">—</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <div style="display:flex;gap:6px;flex-wrap:wrap;">
                      <button type="button" class="btn-outline btn-sm btn-estado" data-cita-id="<?= (int) $cita['id'] ?>" title="Cambiar Estado">
                        <i class="fa-solid fa-arrow-right-arrow-left"></i>
                      </button>
                      <button type="button" class="btn-outline btn-sm btn-pago" data-cita-id="<?= (int) $cita['id'] ?>" data-cliente="<?= htmlspecialchars($cita['cliente'] ?? '') ?>" title="Registrar Abono" style="color:#34d399;border-color:rgba(50,211,153,0.3);">
                        <i class="fa-solid fa-money-bill"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
              <?php if (empty($citas)): ?>
                <tr>
                  <td colspan="9" style="text-align:center;padding:40px;color:var(--bone-dim);">
                    <i class="fa-solid fa-calendar-xmark" style="font-size:28px;display:block;margin-bottom:8px;opacity:0.4;"></i>
                    No hay citas registradas.
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="modalEstado" class="modal-overlay" hidden>
  <div class="modal-card">
    <div class="modal-head">
      <h2 id="estadoModalTitle">Cambiar Estado</h2>
      <button type="button" class="modal-close" data-close-modal="modalEstado" aria-label="Cerrar">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>
    <form id="formEstado" method="POST" action="<?= BASE_URL ?>index.php?action=admin-update-estado">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
      <input type="hidden" name="cita_id" id="estadoCitaId" value="">
      <div style="margin-bottom:20px;">
        <label for="nuevoEstado">Nuevo estado</label>
        <select name="nuevo_estado" id="nuevoEstado" required>
          <option value="pendiente">Pendiente</option>
          <option value="confirmada">Confirmada</option>
          <option value="completada">Completada</option>
          <option value="cancelada">Cancelada</option>
        </select>
      </div>
      <div style="display:flex;gap:10px;justify-content:flex-end;">
        <button type="button" class="btn-outline" data-close-modal="modalEstado">Cancelar</button>
        <button type="submit" class="btn-glow" id="btnGuardarEstado">
          <span id="btnEstadoSpinner" class="btn-spinner" style="display:none;"><i class="fa-solid fa-circle-notch fa-spin"></i></span>
          <span id="btnEstadoText">Guardar</span>
        </button>
      </div>
    </form>
  </div>
</div>

<div id="modalPago" class="modal-overlay" hidden>
  <div class="modal-card">
    <div class="modal-head">
      <h2>Registrar Abono</h2>
      <button type="button" class="modal-close" data-close-modal="modalPago" aria-label="Cerrar">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>
    <form id="formPago" method="POST" action="<?= BASE_URL ?>index.php?action=admin-registrar-pago">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
      <input type="hidden" name="cita_id" id="pagoCitaId" value="">
      <div style="margin-bottom:16px;padding:12px;background:rgba(247,243,236,0.04);border-radius:8px;">
        <div style="font-size:11px;color:var(--bone-dim);text-transform:uppercase;letter-spacing:0.5px;">Cita</div>
        <div style="font-size:14px;color:var(--bone);font-weight:600;" id="pagoInfoCliente"></div>
      </div>
      <div style="margin-bottom:16px;">
        <label for="montoPago">Monto (COP)</label>
        <input type="number" name="monto" id="montoPago" min="1" step="1000" required placeholder="Ej: 50000">
      </div>
      <div style="margin-bottom:16px;">
        <label for="metodoPago">Método de pago</label>
        <select name="metodo" id="metodoPago" required>
          <option value="nequi">Nequi</option>
          <option value="transferencia">Transferencia</option>
          <option value="efectivo">Efectivo</option>
          <option value="tarjeta">Tarjeta</option>
        </select>
      </div>
      <div style="margin-bottom:16px;">
        <label for="comprobantePago">Comprobante / Referencia</label>
        <input type="text" name="comprobante" id="comprobantePago" placeholder="Ej: comp_00123.png">
      </div>
      <div style="display:flex;gap:10px;justify-content:flex-end;">
        <button type="button" class="btn-outline" data-close-modal="modalPago">Cancelar</button>
        <button type="submit" class="btn-glow" id="btnGuardarPago">
          <span id="btnPagoSpinner" class="btn-spinner" style="display:none;"><i class="fa-solid fa-circle-notch fa-spin"></i></span>
          <span id="btnPagoText">Registrar</span>
        </button>
      </div>
    </form>
  </div>
</div>

<div id="toastContainer" style="position:fixed;top:20px;right:20px;z-index:200;display:flex;flex-direction:column;gap:10px;pointer-events:none;"></div>

<style>
@keyframes pulse {
  0%, 100% { opacity: 0.4; }
  50% { opacity: 0.8; }
}

.btn-spinner {
  display: inline-flex;
  align-items: center;
  gap: 6px;
}

.btn-glow[disabled] {
  opacity: 0.6;
  pointer-events: none;
}

.toast {
  pointer-events: auto;
  display: flex;
  align-items: flex-start;
  gap: 12px;
  min-width: 320px;
  max-width: 420px;
  padding: 16px 20px;
  background: rgba(20, 20, 20, 0.95);
  backdrop-filter: blur(24px);
  -webkit-backdrop-filter: blur(24px);
  border: 1px solid rgba(247, 243, 236, 0.1);
  border-radius: 12px;
  box-shadow: 0 16px 48px rgba(0, 0, 0, 0.5);
  animation: toastIn 0.35s cubic-bezier(0.22, 0.61, 0.36, 1) forwards;
  font-family: 'Work Sans', sans-serif;
}

.toast.toast-out {
  animation: toastOut 0.25s ease-in forwards;
}

.toast-success { border-left: 3px solid #34d399; }
.toast-error { border-left: 3px solid #f87171; }
.toast-warning { border-left: 3px solid var(--gold); }
.toast-info { border-left: 3px solid #60a5fa; }

.toast-icon {
  font-size: 18px;
  flex-shrink: 0;
  margin-top: 2px;
}

.toast-success .toast-icon { color: #34d399; }
.toast-error .toast-icon { color: #f87171; }
.toast-warning .toast-icon { color: var(--gold); }
.toast-info .toast-icon { color: #60a5fa; }

.toast-body { flex: 1; }

.toast-title {
  font-size: 13px;
  font-weight: 700;
  color: var(--bone);
  margin-bottom: 4px;
  letter-spacing: 0.3px;
}

.toast-message {
  font-size: 12px;
  color: var(--bone-dim);
  line-height: 1.4;
}

.toast-close {
  background: none;
  border: none;
  color: var(--bone-subtle);
  cursor: pointer;
  font-size: 14px;
  padding: 0;
  flex-shrink: 0;
  transition: color 0.15s;
}

.toast-close:hover { color: var(--bone); }

@keyframes toastIn {
  from { opacity: 0; transform: translateX(100%); }
  to { opacity: 1; transform: translateX(0); }
}

@keyframes toastOut {
  from { opacity: 1; transform: translateX(0); }
  to { opacity: 0; transform: translateX(100%); }
}

.modal-overlay {
  transition: opacity 0.2s ease;
}

.modal-overlay[hidden] {
  display: none;
  opacity: 0;
}

.modal-overlay:not([hidden]) {
  animation: fadeIn 0.2s ease forwards;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}
</style>

<script>
(function() {
  'use strict';

  const citasData = <?php echo json_encode($citas); ?>;

  function showToast(type, title, message) {
    const icons = {
      success: 'fa-circle-check',
      error: 'fa-circle-xmark',
      warning: 'fa-triangle-exclamation',
      info: 'fa-circle-info'
    };
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.className = 'toast toast-' + type;
    toast.innerHTML =
      '<i class="fa-solid ' + (icons[type] || 'fa-circle-info') + ' toast-icon"></i>' +
      '<div class="toast-body">' +
        '<div class="toast-title">' + title + '</div>' +
        '<div class="toast-message">' + message + '</div>' +
      '</div>' +
      '<button class="toast-close" onclick="this.parentElement.remove()">&times;</button>';
    container.appendChild(toast);
    setTimeout(function() {
      toast.classList.add('toast-out');
      setTimeout(function() { toast.remove(); }, 300);
    }, 4000);
  }
  window.showToast = showToast;

  function openModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
      modal.hidden = false;
      document.body.style.overflow = 'hidden';
    }
  }
  window.openModal = openModal;

  function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
      modal.hidden = true;
      document.body.style.overflow = '';
    }
  }
  window.closeModal = closeModal;

  document.querySelectorAll('[data-close-modal]').forEach(function(btn) {
    btn.addEventListener('click', function() {
      closeModal(btn.dataset.closeModal);
    });
  });

  document.querySelectorAll('.modal-overlay').forEach(function(overlay) {
    overlay.addEventListener('click', function(e) {
      if (e.target === overlay) closeModal(overlay.id);
    });
  });

  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      document.querySelectorAll('.modal-overlay:not([hidden])').forEach(function(m) {
        closeModal(m.id);
      });
    }
  });

  document.querySelectorAll('.btn-estado').forEach(function(btn) {
    btn.addEventListener('click', function() {
      const citaId = parseInt(btn.dataset.citaId);
      const cita = citasData.find(function(c) { return c.id === citaId; });
      const estado = cita ? cita.estado : 'pendiente';
      document.getElementById('estadoCitaId').value = citaId;
      document.getElementById('nuevoEstado').value = estado;
      document.getElementById('estadoModalTitle').textContent = 'Cambiar Estado — Cita #' + citaId;
      openModal('modalEstado');
    });
  });

  document.getElementById('formEstado').addEventListener('submit', function(e) {
    e.preventDefault();
    const btnSpinner = document.getElementById('btnEstadoSpinner');
    const btnText = document.getElementById('btnEstadoText');
    const btn = document.getElementById('btnGuardarEstado');
    btn.disabled = true;
    btnSpinner.style.display = 'inline-flex';
    btnText.style.display = 'none';

    const formData = new FormData(e.target);
    fetch('<?= BASE_URL ?>index.php?action=admin-update-estado', {
      method: 'POST',
      body: formData,
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (data.ok) {
        const citaId = parseInt(formData.get('cita_id'));
        const nuevoEstado = formData.get('nuevo_estado');
        const badge = document.getElementById('estado-badge-' + citaId);
        if (badge) {
          badge.className = 'badge badge-' + nuevoEstado;
          badge.textContent = nuevoEstado.charAt(0).toUpperCase() + nuevoEstado.slice(1);
        }
        const row = document.querySelector('tr[data-cita-id="' + citaId + '"]');
        if (row) {
          row.style.transition = 'background 0.5s';
          row.style.background = 'rgba(45, 184, 92, 0.08)';
          setTimeout(function() { row.style.background = ''; }, 1500);
        }
        showToast('success', 'Estado actualizado', 'Cita #' + citaId + ' cambiada a ' + nuevoEstado + '.');
        closeModal('modalEstado');
      } else {
        showToast('error', 'Error', data.message || 'No se pudo actualizar el estado.');
      }
    })
    .catch(function() {
      showToast('error', 'Error', 'Error de conexión. Intenta de nuevo.');
    })
    .finally(function() {
      btn.disabled = false;
      btnSpinner.style.display = 'none';
      btnText.style.display = '';
    });
  });

  document.querySelectorAll('.btn-pago').forEach(function(btn) {
    btn.addEventListener('click', function() {
      const citaId = parseInt(btn.dataset.citaId);
      const cita = citasData.find(function(c) { return c.id === citaId; });
      document.getElementById('pagoCitaId').value = citaId;
      document.getElementById('pagoInfoCliente').textContent = cita
        ? cita.cliente + ' — ' + (cita.servicio || '')
        : 'Cita #' + citaId;
      document.getElementById('montoPago').value = '';
      document.getElementById('comprobantePago').value = '';
      openModal('modalPago');
    });
  });

  document.getElementById('formPago').addEventListener('submit', function(e) {
    e.preventDefault();
    const btnSpinner = document.getElementById('btnPagoSpinner');
    const btnText = document.getElementById('btnPagoText');
    const btn = document.getElementById('btnGuardarPago');
    btn.disabled = true;
    btnSpinner.style.display = 'inline-flex';
    btnText.style.display = 'none';

    const formData = new FormData(e.target);
    fetch('<?= BASE_URL ?>index.php?action=admin-registrar-pago', {
      method: 'POST',
      body: formData,
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (data.ok) {
        const citaId = parseInt(formData.get('cita_id'));
        const monto = parseFloat(formData.get('monto'));
        const row = document.querySelector('tr[data-cita-id="' + citaId + '"]');
        if (row) {
          const abonoCell = row.querySelector('td:nth-child(8)');
          if (abonoCell) abonoCell.innerHTML = '$' + monto.toLocaleString('es-CO', {maximumFractionDigits: 0});
          row.style.transition = 'background 0.5s';
          row.style.background = 'rgba(59, 130, 246, 0.08)';
          setTimeout(function() { row.style.background = ''; }, 1500);
        }
        showToast('success', 'Abono registrado', '$' + monto.toLocaleString('es-CO') + ' registrado para Cita #' + citaId + '.');
        closeModal('modalPago');
      } else {
        showToast('error', 'Error', data.message || 'No se pudo registrar el abono.');
      }
    })
    .catch(function() {
      showToast('error', 'Error', 'Error de conexión. Intenta de nuevo.');
    })
    .finally(function() {
      btn.disabled = false;
      btnSpinner.style.display = 'none';
      btnText.style.display = '';
    });
  });

  document.addEventListener('DOMContentLoaded', function() {
    const skeleton = document.getElementById('dashboardSkeleton');
    const content = document.getElementById('dashboardContent');
    const tableSkeleton = document.getElementById('tableSkeleton');
    const tableContent = document.getElementById('tableContent');

    setTimeout(function() {
      skeleton.style.display = 'none';
      content.style.display = 'block';

      setTimeout(function() {
        if (citasData.length > 0) {
          tableSkeleton.style.display = 'block';
          tableContent.style.display = 'none';
          setTimeout(function() {
            tableSkeleton.style.display = 'none';
            tableContent.style.display = 'block';
          }, 600);
        }
      }, 300);
    }, 1200);
  });
})();
</script>
