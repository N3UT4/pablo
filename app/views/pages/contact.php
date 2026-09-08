<div class="container container-narrow">
  <div class="card">
    <div class="card-head">
      <span class="eyebrow">Contacto</span>
      <h1>Escríbenos</h1>
      <p>Estamos listos para responder tus dudas, cotizar un diseño o ayudarte a reservar tu próxima cita.</p>
    </div>

    <form method="POST" action="index.php?action=contact" novalidate>
      <div class="row2">
        <div class="field">
          <label for="contactName">Nombre</label>
          <input type="text" id="contactName" name="contactName" placeholder="Tu nombre" required>
        </div>
        <div class="field">
          <label for="contactEmail">Correo</label>
          <input type="email" id="contactEmail" name="contactEmail" placeholder="tu@correo.com" required>
        </div>

      </div>
      <div class="field">
        <label for="contactSubject">Asunto</label>
        <input type="text" id="contactSubject" name="contactSubject" placeholder="¿Qué necesitas?" required>
      </div>
      <div class="field">
        <label for="contactMessage">Mensaje</label>
        <textarea id="contactMessage" name="contactMessage" placeholder="Cuéntanos tu idea..." required></textarea>
      </div>
      <button type="submit" class="submit-btn">Enviar consulta</button>
    </form>
  </div>

  <div class="card studio-info-card">
    <div class="section-title">Información del estudio</div>
    <div class="studio-info-grid">
      <div>
        <span class="info-label">Ubicación</span>
        <strong><?= htmlspecialchars(STUDIO_CITY) ?></strong>
        <span><?= htmlspecialchars(STUDIO_ADDRESS) ?></span>
      </div>
      <div>
        <span class="info-label">Horarios</span>
        <?php foreach (STUDIO_HOURS as $hour): ?>
          <span><?= htmlspecialchars($hour) ?></span>
        <?php endforeach; ?>
      </div>
      <div>
        <span class="info-label">WhatsApp</span>
        <a href="https://wa.me/<?= STUDIO_WHATSAPP ?>" target="_blank" rel="noopener noreferrer">
          <?= htmlspecialchars(STUDIO_PHONE) ?>
        </a>
        <span>Atención y reservas</span>
      </div>
    </div>
  </div>

  <div class="card studio-info-card">
    <div class="section-title">Síguenos y visítanos</div>
    <div class="social-contact-grid">
      <a href="<?= htmlspecialchars(STUDIO_INSTAGRAM) ?>" target="_blank" rel="noopener noreferrer">
        <i class="fa-brands fa-instagram social-icon" aria-hidden="true"></i>
        <span>Instagram</span><small>@itza.tattoo</small>
      </a>
      <a href="<?= htmlspecialchars(STUDIO_FACEBOOK) ?>" target="_blank" rel="noopener noreferrer">
        <i class="fa-brands fa-facebook-f social-icon" aria-hidden="true"></i>
        <span>Facebook</span><small>ITZA TATTOO</small>
      </a>
      <a href="<?= htmlspecialchars(STUDIO_TIKTOK) ?>" target="_blank" rel="noopener noreferrer">
        <i class="fa-brands fa-tiktok social-icon" aria-hidden="true"></i>
        <span>TikTok</span><small>@itza_tattoo</small>
      </a>
      <a href="<?= htmlspecialchars(STUDIO_MAPS) ?>" target="_blank" rel="noopener noreferrer">
        <i class="fa-solid fa-location-dot social-icon" aria-hidden="true"></i>
        <span>Google Maps</span><small>Ver ubicación</small>
      </a>
    </div>
  </div>
</div>
