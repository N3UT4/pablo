<?php
$profile = $profile ?? null;
$artist = $artist ?? null;
?>
<div class="container container-narrow" style="padding-top:24px;">

    <div class="glass-card">
        <div class="dash-section-title">
            <i class="fa-solid fa-id-card"></i>
            <h2>Información del Tatuador</h2>
        </div>

        <div class="dash-grid-2" style="margin-bottom:24px;">
            <div class="glass-card">
                <h3 class="dash-section-title" style="border:none;padding:0;margin:0;"><i class="fa-solid fa-user"></i> Datos del Artista</h3>
                <?php if ($artist): ?>
                    <?php if ($artist['foto']): ?>
                        <img src="<?= BASE_URL . htmlspecialchars($artist['foto']) ?>" alt="" style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:2px solid var(--blood-bright);margin-bottom:12px;">
                    <?php else: ?>
                        <div style="width:80px;height:80px;border-radius:50%;background:var(--panel-2);border:2px solid var(--blood-bright);margin-bottom:12px;display:flex;align-items:center;justify-content:center;">
                            <i class="fa-solid fa-user" style="font-size:28px;color:var(--blood-bright);"></i>
                        </div>
                    <?php endif; ?>
                    <div class="profile-row" style="margin-bottom:8px;">
                        <label>Nombre artístico</label>
                        <span style="display:block;font-size:14px;"><?= htmlspecialchars($artist['nombre']) ?></span>
                    </div>
                    <div class="profile-row" style="margin-bottom:8px;">
                        <label>Especialidad</label>
                        <span style="display:block;font-size:14px;color:var(--bone-dim);"><?= htmlspecialchars($artist['bio'] ?? 'No especificada') ?></span>
                    </div>
                    <div class="profile-row">
                        <label>Estado</label>
                        <span style="display:block;font-size:14px;"><?= (int)($artist['activo'] ?? 0) === 1 ? '<span class="badge badge-completada">Activo</span>' : '<span class="badge badge-cancelada">Inactivo</span>' ?></span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="glass-card">
                <h3 class="dash-section-title" style="border:none;padding:0;margin:0;"><i class="fa-solid fa-user"></i> Datos de Usuario</h3>
                <?php if ($profile): ?>
                    <div class="profile-row" style="margin-bottom:8px;">
                        <label>Nombre completo</label>
                        <span style="display:block;font-size:14px;"><?= htmlspecialchars($profile['nombre']) ?></span>
                    </div>
                    <div class="profile-row" style="margin-bottom:8px;">
                        <label>Correo electrónico</label>
                        <span style="display:block;font-size:14px;color:var(--bone-dim);"><?= htmlspecialchars($profile['email']) ?></span>
                    </div>
                    <div class="profile-row" style="margin-bottom:8px;">
                        <label>Teléfono</label>
                        <span style="display:block;font-size:14px;"><?= htmlspecialchars($profile['telefono'] ?? 'No registrado') ?></span>
                    </div>
                    <div class="profile-row" style="margin-bottom:8px;">
                        <label>Documento</label>
                        <span style="display:block;font-size:14px;"><?= htmlspecialchars($profile['documento'] ?? 'No registrada') ?></span>
                    </div>
                    <div class="profile-row">
                        <label>Rol</label>
                        <span style="display:block;font-size:14px;text-transform:capitalize;"><?= htmlspecialchars($user['rol'] ?? '') ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <a href="<?= BASE_URL ?>index.php?action=artist-agenda" class="btn-glow">Ver Mis Citas</a>
            <a href="<?= BASE_URL ?>index.php?action=artist-horarios" class="btn-outline">Editar Horarios</a>
            <a href="<?= BASE_URL ?>index.php?action=logout" class="btn-outline">Cerrar Sesión</a>
        </div>
    </div>
</div>
