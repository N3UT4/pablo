<?php
// Partial: Sidebar del dashboard (usuarios logueados).
// Requiere: $user, $rol, $isArtist, $currentPage, $currentAction, $artistMode
?>
<div class="dashboard-shell">
    <aside class="dashboard-sidebar" id="dashSidebar">
        <div class="sidebar-brand">
            <a href="<?= BASE_URL ?>index.php?action=dashboard">
                <span class="brand-dot"></span>
                ITZA <span style="color:var(--blood-bright)">TATTOO</span>
            </a>
        </div>

        <!-- Sección: Principal -->
        <div class="sidebar-section-label">Principal</div>
        <nav>
            <a href="<?= BASE_URL ?>index.php?action=dashboard" class="<?= $currentAction === 'dashboard' ? 'active' : '' ?>">
                <i class="fa-solid fa-gauge-high"></i> Dashboard
            </a>
        </nav>

        <?php if ($rol === 'cliente'): ?>
            <!-- Sección: Tu Cuenta (cliente) -->
            <div class="sidebar-section-label">Tu Cuenta</div>
            <nav>
                <a href="<?= BASE_URL ?>index.php?action=cliente-citas" class="<?= $currentAction === 'cliente-citas' ? 'active' : '' ?>">
                    <i class="fa-solid fa-calendar-check"></i> Mis Citas
                </a>
                <a href="<?= BASE_URL ?>index.php?action=cliente-abonos" class="<?= $currentAction === 'cliente-abonos' ? 'active' : '' ?>">
                    <i class="fa-solid fa-credit-card"></i> Abonos
                </a>
                <a href="<?= BASE_URL ?>index.php?action=cliente-consentimiento" class="<?= $currentAction === 'cliente-consentimiento' ? 'active' : '' ?>">
                    <i class="fa-solid fa-file-signature"></i> Consentimiento
                </a>
                <a href="<?= BASE_URL ?>index.php?action=cliente-agendar" class="<?= $currentAction === 'cliente-agendar' ? 'active' : '' ?>">
                    <i class="fa-solid fa-plus"></i> Agendar Cita
                </a>
                <a href="<?= BASE_URL ?>index.php?action=profile" class="<?= $currentPage === 'profile' ? 'active' : '' ?>">
                    <i class="fa-solid fa-user"></i> Perfil
                </a>
            </nav>
        <?php elseif ($rol === 'tatuador'): ?>
            <!-- Sección: Panel Tatuador -->
            <div class="sidebar-section-label">Panel Tatuador</div>
            <nav>
                <a href="<?= BASE_URL ?>index.php?action=artist-panel" class="<?= $currentAction === 'artist-panel' ? 'active' : '' ?>">
                    <i class="fa-solid fa-gauge-high"></i> Panel
                </a>
                <a href="<?= BASE_URL ?>index.php?action=artist-agenda" class="<?= $currentAction === 'artist-agenda' ? 'active' : '' ?>">
                    <i class="fa-solid fa-calendar-days"></i> Agenda
                </a>
                <a href="<?= BASE_URL ?>index.php?action=artist-horarios" class="<?= $currentAction === 'artist-horarios' ? 'active' : '' ?>">
                    <i class="fa-solid fa-clock"></i> Horarios
                </a>
                <a href="<?= BASE_URL ?>index.php?action=artist-perfil" class="<?= $currentAction === 'artist-perfil' ? 'active' : '' ?>">
                    <i class="fa-solid fa-id-card"></i> Perfil
                </a>
            </nav>
        <?php elseif ($rol === 'admin'): ?>
            <!-- Sección: Panel Administrador -->
            <div class="sidebar-section-label">Panel Administrador</div>
            <nav>
                <a href="<?= BASE_URL ?>index.php?action=admin" class="<?= in_array($currentAction, ['admin', 'dashboard']) ? 'active' : '' ?>">
                    <i class="fa-solid fa-chart-line"></i> Métricas
                </a>
                <a href="<?= BASE_URL ?>index.php?action=admin-usuarios" class="<?= $currentAction === 'admin-usuarios' ? 'active' : '' ?>">
                    <i class="fa-solid fa-users"></i> Usuarios
                </a>
                <a href="<?= BASE_URL ?>index.php?action=admin-servicios" class="<?= $currentAction === 'admin-servicios' ? 'active' : '' ?>">
                    <i class="fa-solid fa-scissors"></i> Servicios
                </a>
                <a href="<?= BASE_URL ?>index.php?action=admin-transacciones" class="<?= $currentAction === 'admin-transacciones' ? 'active' : '' ?>">
                    <i class="fa-solid fa-receipt"></i> Transacciones
                </a>
            </nav>
            <!-- Sección: Modo Tatuador (admin) -->
            <div class="sidebar-section-label">Modo Tatuador</div>
            <nav>
                <a href="<?= BASE_URL ?>index.php?action=artist-switch-mode" class="<?= $currentAction === 'artist-switch-mode' ? 'active' : '' ?>">
                    <i class="fa-solid fa-user-tie"></i> Entrar Modo Tatuador
                </a>
            </nav>
        <?php endif; ?>

        <!-- Info del usuario y botón de cerrar sesión -->
        <div class="sidebar-user">
            <div class="user-name"><?= htmlspecialchars($user['nombre'] ?? $user['email'] ?? 'Usuario') ?></div>
            <div class="user-role"><?= htmlspecialchars(ucfirst($rol)) ?></div>
            <a href="<?= BASE_URL ?>index.php?action=logout">
                <i class="fa-solid fa-right-from-bracket"></i> Cerrar sesión
            </a>
        </div>
    </aside>

    <main class="dashboard-main">
        <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Menú">
            <i class="fa-solid fa-bars"></i>
        </button>
        <div class="dashboard-header">
            <h1><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?></h1>
            <div class="header-meta">
                <span style="color:var(--bone-dim);font-size:12px;">
                    <?= date('d/m/Y') ?> · <?= date('g:i a') ?>
                </span>
            </div>
        </div>
