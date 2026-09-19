<?php
$user = $user ?? null;
?>
<div class="container container-narrow" style="padding-top:24px;">
  <div class="glass-card glow-hover">
    <div class="welcome-banner" style="margin-bottom:24px;">
      <h2>¡Bienvenido<?= ($user['rol'] ?? '') === 'tatuador' ? '' : '' ?> a ITZA TATTOO!</h2>
      <p>Gracias por confiar en nosotros para tu próxima pieza. Aquí encontrarás toda la información para agendar tu cita, revisar promociones exclusivas y mucho más.</p>
    </div>

    <div class="panel-grid-dash">
      <a href="<?= BASE_URL ?>index.php?action=cliente-citas" class="panel-item">
        <i class="fa-solid fa-calendar-check"></i>
        <h3>Mis Citas</h3>
        <p>Revisa el estado de tus citas agendadas</p>
      </a>
      <a href="<?= BASE_URL ?>index.php?action=cliente-abonos" class="panel-item">
        <i class="fa-solid fa-credit-card"></i>
        <h3>Abonos</h3>
        <p>Estado de pagos y saldo pendiente</p>
      </a>
      <a href="<?= BASE_URL ?>index.php?action=cliente-consentimiento" class="panel-item">
        <i class="fa-solid fa-file-signature"></i>
        <h3>Consentimiento</h3>
        <p>Firma digital y consentimiento informado</p>
      </a>
      <a href="<?= BASE_URL ?>index.php?action=cliente-agendar" class="panel-item">
        <i class="fa-solid fa-plus"></i>
        <h3>Agendar</h3>
        <p>Reserva tu próxima sesión</p>
      </a>
    </div>

    <div class="dash-section">
      <div class="dash-section-title">
        <i class="fa-solid fa-clock"></i>
        <h2>Horarios de Atención</h2>
      </div>
      <div class="dash-grid-3">
        <div class="glass-card" style="text-align:center;padding:16px;">
          <div style="color:var(--blood-bright);font-size:20px;margin-bottom:6px;">🕐</div>
          <strong style="font-size:13px;">Primer Horario</strong>
          <div style="color:var(--bone-dim);font-size:12px;margin-top:4px;"><?= htmlspecialchars(STUDIO_HOURS[0]) ?></div>
          <span class="badge badge-confirmada" style="margin-top:6px;">Atención</span>
        </div>
        <div class="glass-card" style="text-align:center;padding:16px;">
          <div style="color:var(--blood-bright);font-size:20px;margin-bottom:6px;">🕐</div>
          <strong style="font-size:13px;">Segundo Horario</strong>
          <div style="color:var(--bone-dim);font-size:12px;margin-top:4px;"><?= htmlspecialchars(STUDIO_HOURS[1]) ?></div>
          <span class="badge badge-confirmada" style="margin-top:6px;">Atención</span>
        </div>
        <div class="glass-card" style="text-align:center;padding:16px;">
          <div style="color:var(--blood-bright);font-size:20px;margin-bottom:6px;">🕐</div>
          <strong style="font-size:13px;">Tercer Horario</strong>
          <div style="color:var(--bone-dim);font-size:12px;margin-top:4px;"><?= htmlspecialchars(STUDIO_HOURS[2]) ?></div>
          <span class="badge badge-confirmada" style="margin-top:6px;">Atención</span>
        </div>
      </div>
    </div>

    <div class="exclusive-section" style="margin-top:24px;">
      <span class="exclusive-label">Exclusivo</span>
      <h3>Promociones activas</h3>
      <p>Accede a descuentos para clientes y aprovecha tu primera cita con beneficios especiales.</p>
      <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:12px;">
        <a href="<?= BASE_URL ?>index.php?action=cliente-agendar" class="btn-glow">Agendar Cita</a>
        <a href="<?= BASE_URL ?>index.php?action=promotions" class="btn-outline">Ver Promociones</a>
      </div>
    </div>

    <div class="glass-card" style="margin-top:24px;border:1px solid rgba(239,68,68,0.2);">
      <div class="dash-section-title">
        <i class="fa-solid fa-triangle-exclamation" style="color:#ef4444;"></i>
        <h2>Zona de Cuenta</h2>
      </div>
      <p style="color:var(--bone-dim);font-size:13px;margin-bottom:12px;">Eliminar tu cuenta borrará tus datos de acceso de forma permanente.</p>
      <form method="POST" action="<?= BASE_URL ?>index.php?action=delete-account"
            onsubmit="return confirm('¿Seguro que deseas eliminar tu cuenta? Esta acción no se puede deshacer.');">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
        <button type="submit" class="btn-outline" style="color:#ffb3b3;border-color:rgba(239,68,68,0.3);">
          Eliminar mi cuenta
        </button>
      </form>
    </div>
  </div>
</div>
