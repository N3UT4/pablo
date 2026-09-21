<?php
$cuidados = require DIR_PATH . 'cuidados_data.php';
$antes = $cuidados['antes'] ?? [];
$despues = $cuidados['despues'] ?? [];
?>

<div class="container container-narrow">
  <div class="card-head-cta">
    <a href="javascript:history.back()" class="btn-outline btn-sm">
      <i class="fa-solid fa-arrow-left"></i> Regresar
    </a>
  </div>

  <div class="card">
    <div class="card-head">
      <span class="eyebrow">Guía del estudio</span>
      <h1>Cuidados del tatuaje</h1>
      <p>Todo lo que debes saber antes y después de tu cita.</p>
    </div>

    <div class="recomendacion-grid">
      <div class="info-card">
        <div class="section-title">Antes de la cita</div>
        <div class="recomendacion-list">
          <?php foreach ($antes as $punto): ?>
            <div class="recomendacion-item">
              <span class="item-icon" aria-hidden="true">
                <i class="fa-solid fa-circle"></i>
              </span>
              <div class="item-body">
                <p class="item-text"><?= htmlspecialchars($punto) ?></p>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="info-card">
        <div class="section-title">Después del tatuaje</div>
        <div class="recomendacion-list">
          <?php foreach ($despues as $punto): ?>
            <div class="recomendacion-item">
              <span class="item-icon" aria-hidden="true">
                <i class="fa-solid fa-circle"></i>
              </span>
              <div class="item-body">
                <p class="item-text"><?= htmlspecialchars($punto) ?></p>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>

  <div class="info-card">
    <div class="info-card-body">
      <div class="section-title">¿Tienes dudas?</div>
      <p>Si notas señales de infección (hinchazón, calor, pus o fiebre) consulta a un profesional inmediatamente.</p>
      <div class="cta-buttons">
        <a href="<?= BASE_URL ?>index.php?action=contact" class="cta-btn btn-primary">
          <i class="fa-solid fa-envelope"></i> Contáctanos
        </a>
        <a href="javascript:history.back()" class="cta-btn secondary">
          <i class="fa-solid fa-arrow-left"></i> Volver
        </a>
      </div>
    </div>
  </div>
</div>
