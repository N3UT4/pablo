<div class="container container-narrow">
  <div class="welcome-banner">
    <h2>¡Bienvenido a ITZA TATTOO!</h2>
    <p>Gracias por confiar en nosotros para tu próxima pieza. Aquí encontrarás toda la información para agendar tu cita, revisar promociones exclusivas y mucho más.</p>
  </div>

  <div class="user-info">
    <strong>Sesión iniciada como:</strong>
    <div id="userEmail" style="font-size: 14px; margin-top: 6px;">
      <?= htmlspecialchars($user['email'] ?? '') ?>
    </div>
  </div>

  <div class="section-title">Horarios de atención</div>
  <div class="hours-grid">
    <div class="hours-item">
      <div><strong>Primer horario</strong></div>
      <div><span class="time"><?= htmlspecialchars(STUDIO_HOURS[0]) ?></span> <span class="status-badge">Atención</span></div>
    </div>
    <div class="hours-item">
      <div><strong>Segundo horario</strong></div>
      <div><span class="time"><?= htmlspecialchars(STUDIO_HOURS[1]) ?></span> <span class="status-badge">Atención</span></div>
    </div>
    <div class="hours-item">
      <div><strong>Tercer horario</strong></div>
      <div><span class="time"><?= htmlspecialchars(STUDIO_HOURS[2]) ?></span> <span class="status-badge">Atención</span></div>
    </div>
  </div>

  <div class="exclusive-section">
    <span class="exclusive-label">Exclusivo</span>
    <h3>Promociones activas</h3>
    <p>Accede a descuentos para clientes y aprovecha tu primera cita con beneficios especiales.</p>
    <div class="cta-buttons">
      <a href="index.php?action=home#servicios" class="btn-primary cta-btn">Ver servicios</a>
      <a href="index.php?action=logout" class="btn-ghost cta-btn secondary">Cerrar sesión</a>
    </div>
  </div>

  <div class="card" style="margin-top: 24px; border: 1px solid #6f2d2d;">
    <div class="section-title">Zona de cuenta</div>
    <p>Eliminar tu cuenta borrará tus datos de acceso de forma permanente.</p>
    <form method="POST" action="index.php?action=delete-account"
          onsubmit="return confirm('¿Seguro que deseas eliminar tu cuenta? Esta acción no se puede deshacer.');">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
      <button type="submit" class="btn-ghost" style="color: #ffb3b3; border-color: #6f2d2d;">
        Eliminar mi cuenta
      </button>
    </form>
  </div>
</div>
