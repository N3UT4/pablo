<?php
$profile = $profile ?? null;
$artist = $artist ?? null;
?>

<div class="container container-narrow">
  <div class="welcome-banner">
    <h2>Mi Perfil</h2>
    <p>Datos de artista</p>
  </div>

  <div class="card">
    <div class="card-head">
      <span class="eyebrow">Perfil</span>
      <h1>Información del Tatuador</h1>
    </div>

    <div class="profile-grid">
      <div class="profile-section">
        <h3 class="section-title">Datos del Artista</h3>
        <?php if ($artist): ?>
          <div class="profile-row">
            <label>Nombre artístico</label>
            <span><?= htmlspecialchars($artist['nombre']) ?></span>
          </div>
          <div class="profile-row">
            <label>Especialidad</label>
            <span><?= htmlspecialchars($artist['bio'] ?? 'No especificada') ?></span>
          </div>
          <div class="profile-row">
            <label>Estado</label>
            <span><?= (int)($artist['activo'] ?? 0) === 1 ? 'Activo' : 'Inactivo' ?></span>
          </div>
        <?php endif; ?>
      </div>

      <div class="profile-section">
        <h3 class="section-title">Datos de Usuario</h3>
        <?php if ($profile): ?>
          <div class="profile-row">
            <label>Nombre completo</label>
            <span><?= htmlspecialchars($profile['nombre']) ?></span>
          </div>
          <div class="profile-row">
            <label>Correo electrónico</label>
            <span><?= htmlspecialchars($profile['email']) ?></span>
          </div>
          <div class="profile-row">
            <label>Teléfono</label>
            <span><?= htmlspecialchars($profile['telefono'] ?? 'No registrado') ?></span>
          </div>
          <div class="profile-row">
            <label>Documento (cédula colombiana)</label>
            <span><?= htmlspecialchars($profile['documento'] ?? 'No registrada') ?></span>
          </div>
          <div class="profile-row">
            <label>Fecha de nacimiento</label>
            <span><?= htmlspecialchars($profile['fecha_nacimiento'] ?? 'No registrada') ?></span>
          </div>
          <div class="profile-row">
            <label>Rol</label>
            <span style="text-transform: capitalize;"><?= htmlspecialchars($user['rol'] ?? '') ?></span>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <div class="cta-buttons" style="margin-top: 28px;">
      <a href="<?= BASE_URL ?>index.php?action=artist-agenda" class="cta-btn btn-primary">Ver Mis Citas</a>
      <a href="<?= BASE_URL ?>index.php?action=artist-horarios" class="cta-btn">Editar Horarios</a>
      <a href="<?= BASE_URL ?>index.php?action=logout" class="cta-btn secondary">Cerrar Sesión</a>
    </div>
  </div>
</div>
