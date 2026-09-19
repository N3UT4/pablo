<?php
$artist = $artist ?? null;
$artistName = $artist['nombre'] ?? ($_SESSION['user']['nombre'] ?? 'Tatuador');
$artistId = $artistId ?? 0;
?>
<div class="container container-narrow" style="padding-top:24px;">
  <div style="margin-bottom:16px;">
    <a href="javascript:history.back()" class="btn-outline btn-sm" style="text-decoration:none;"><i class="fa-solid fa-arrow-left"></i> Regresar</a>
  </div>
  <div class="glass-card glow-hover">
    <div class="welcome-banner" style="margin-bottom:24px;">
      <h2><?= htmlspecialchars($artistName) ?></h2>
      <p>Panel operativo de tatuador — Itza Tattoo Studio</p>
    </div>

    <div class="dash-stats">
      <div class="dash-stat-card">
        <div class="stat-icon"><i class="fa-solid fa-calendar"></i></div>
        <div class="stat-value">0</div>
        <div class="stat-label">Total Citas</div>
      </div>
      <div class="dash-stat-card stat-warning">
        <div class="stat-icon"><i class="fa-solid fa-hourglass-half"></i></div>
        <div class="stat-value">0</div>
        <div class="stat-label">Pendientes</div>
      </div>
      <div class="dash-stat-card stat-info">
        <div class="stat-icon"><i class="fa-solid fa-circle-check"></i></div>
        <div class="stat-value">0</div>
        <div class="stat-label">Confirmadas</div>
      </div>
      <div class="dash-stat-card stat-success">
        <div class="stat-icon"><i class="fa-solid fa-flag-checkered"></i></div>
        <div class="stat-value">0</div>
        <div class="stat-label">Finalizadas</div>
      </div>
    </div>

    <div class="dash-section-title">
      <i class="fa-solid fa-bolt"></i>
      <h2>Panel de Control</h2>
    </div>

    <div class="panel-grid-dash" style="margin-bottom:24px;">
      <a href="<?= BASE_URL ?>index.php?action=artist-agenda" class="panel-item">
        <i class="fa-solid fa-calendar-days"></i>
        <h3>Agenda</h3>
        <p>Ver y gestionar tus citas asignadas</p>
      </a>
      <a href="<?= BASE_URL ?>index.php?action=artist-horarios" class="panel-item">
        <i class="fa-solid fa-clock"></i>
        <h3>Horarios</h3>
        <p>Gestiona tu disponibilidad semanal</p>
      </a>
      <a href="<?= BASE_URL ?>index.php?action=artist-perfil" class="panel-item">
        <i class="fa-solid fa-id-card"></i>
        <h3>Perfil</h3>
        <p>Ficha del artista y datos</p>
      </a>
    </div>

    <?php if ($rol === 'admin'): ?>
      <div class="exclusive-section">
        <span class="exclusive-label">Modo de prueba</span>
        <h3>Has entrado como administrador</h3>
        <p>Estás visualizando el panel de tatuador para probar las funcionalidades. Puedes salir del modo tatuador desde el menú desplegable.</p>
      </div>
    <?php endif; ?>
  </div>
</div>
