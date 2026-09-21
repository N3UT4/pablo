<?php
$rec = require DIR_PATH . 'app/data/recomendaciones_tatuaje.php';
$secciones = $rec['secciones'] ?? [];
$totalSecciones = count($secciones);
$indiceSeccion = 0;
?>

<div class="container container-narrow">
  <div class="card-head-cta">
    <a href="javascript:history.back()" class="btn-outline btn-sm">
      <i class="fa-solid fa-arrow-left"></i> Regresar
    </a>
  </div>

  <div class="card">
    <div class="card-head">
      <span class="eyebrow">Cuida tu piel</span>
      <h1>Recomendaciones y cuidados</h1>
      <p class="hint">Antes y después de tu sesión, sigue estas pautas para una cicatrización saludable y un resultado impecable.</p>
    </div>

    <?php foreach ($secciones as $seccion): ?>
      <div class="section-title" id="<?= $seccion['id'] ?? '' ?>">
        <i class="<?= $seccion['icono'] ?? '' ?>" aria-hidden="true"></i>
        <?= htmlspecialchars($seccion['titulo'] ?? '') ?>
      </div>

      <?php if (!empty($seccion['subtitulo'])): ?>
        <p class="hint"><?= htmlspecialchars($seccion['subtitulo']) ?></p>
      <?php endif; ?>

      <div class="recomendacion-grid">
        <?php foreach (($seccion['items'] ?? []) as $item): ?>
          <div class="recomendacion-item">
            <span class="item-icon" aria-hidden="true">
              <i class="<?= $item['icono'] ?? '' ?>"></i>
            </span>
            <div class="item-body">
              <strong class="item-title"><?= htmlspecialchars($item['titulo'] ?? '') ?></strong>
              <p class="item-text"><?= htmlspecialchars($item['descripcion'] ?? '') ?></p>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <?php $indiceSeccion++; ?>
      <?php if ($indiceSeccion < $totalSecciones): ?>
        <hr class="section-divider">
      <?php endif; ?>
    <?php endforeach; ?>
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
