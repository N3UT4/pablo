<?php
$citas = $citas ?? [];
$estadoLabels = [
    'pendiente' => 'Pendiente',
    'confirmada' => 'Confirmada',
    'completada' => 'Finalizada',
    'cancelada' => 'Cancelada',
];
$counts = ['total' => count($citas), 'pendiente' => 0, 'confirmada' => 0, 'completada' => 0, 'cancelada' => 0];
foreach ($citas as $cita) {
    $est = $cita['estado'] ?? 'pendiente';
    if (isset($counts[$est])) $counts[$est]++;
}
?>
<div class="container container-narrow" style="padding-top:24px;">
  <div style="margin-bottom:16px;">
    <a href="javascript:history.back()" class="btn-outline btn-sm" style="text-decoration:none;"><i class="fa-solid fa-arrow-left"></i> Regresar</a>
  </div>

    <div class="dash-stats">
        <div class="dash-stat-card">
            <div class="stat-icon"><i class="fa-solid fa-calendar"></i></div>
            <div class="stat-value"><?= $counts['total'] ?></div>
            <div class="stat-label">Total Citas</div>
        </div>
        <div class="dash-stat-card stat-warning">
            <div class="stat-icon"><i class="fa-solid fa-hourglass-half"></i></div>
            <div class="stat-value"><?= $counts['pendiente'] ?></div>
            <div class="stat-label">Pendientes</div>
        </div>
        <div class="dash-stat-card stat-info">
            <div class="stat-icon"><i class="fa-solid fa-circle-check"></i></div>
            <div class="stat-value"><?= $counts['confirmada'] ?></div>
            <div class="stat-label">Confirmadas</div>
        </div>
        <div class="dash-stat-card stat-success">
            <div class="stat-icon"><i class="fa-solid fa-flag-checkered"></i></div>
            <div class="stat-value"><?= $counts['completada'] + $counts['cancelada'] ?></div>
            <div class="stat-label">Finalizadas</div>
        </div>
    </div>

    <div class="glass-card">
        <div class="dash-section-title">
            <i class="fa-solid fa-calendar-check"></i>
            <h2>Mis Citas</h2>
        </div>

        <?php if (empty($citas)): ?>
            <div class="dash-empty">
                <i class="fa-solid fa-calendar-xmark"></i>
                <h3>Sin citas agendadas</h3>
                <p>No tienes citas en este momento.</p>
                <a href="<?= BASE_URL ?>index.php?action=cliente-agendar" class="btn-glow">Agendar Cita</a>
            </div>
        <?php else: ?>
            <div class="dash-table-wrapper">
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Servicio</th>
                            <th>Tatuador</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($citas as $cita): ?>
                            <tr>
                                <td><?= htmlspecialchars(date('d/m/Y', strtotime($cita['fecha_cita']))) ?></td>
                                <td><?= htmlspecialchars(date('g:i a', strtotime($cita['hora_cita']))) ?></td>
                                <td><?= htmlspecialchars($cita['servicio'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($cita['tatuador'] ?? '-') ?></td>
                                <td>
                                    <span class="badge badge-<?= htmlspecialchars($cita['estado'] ?? 'pendiente') ?>">
                                        <?= htmlspecialchars($estadoLabels[$cita['estado'] ?? 'pendiente']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
