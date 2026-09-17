<?php
$artist = $artist ?? null;
$artistName = $artist['nombre'] ?? ($_SESSION['user']['nombre'] ?? 'Tatuador');
$artistId = $artistId ?? 0;
?>

<div class="container container-narrow">
  <div class="welcome-banner">
    <h2><?= htmlspecialchars($artistName) ?></h2>
    <p>Panel operativo de tatuador — Itza Tattoo Studio</p>
  </div>

  <div class="card">
    <div class="card-head">
      <span class="eyebrow">Modo Tatuador</span>
      <h1>Mi Panel</h1>
      <p class="hint">Desde aquí gestionas tus citas, horarios y el expediente de tus clientes. No puedes modificar datos globales del sitio.</p>
    </div>

    <div class="cta-buttons">
      <a href="<?= BASE_URL ?>index.php?action=artist-agenda" class="cta-btn btn-primary">Mis Citas Asignadas</a>
      <a href="<?= BASE_URL ?>index.php?action=artist-horarios" class="cta-btn">Mis Horarios</a>
      <a href="<?= BASE_URL ?>index.php?action=artist-perfil" class="cta-btn">Mi Perfil</a>
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
