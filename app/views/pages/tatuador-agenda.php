<?php
$citas = $citas ?? [];
$estadoLabels = [
    'pendiente' => 'Pendiente',
    'confirmada' => 'Confirmada',
    'completada' => 'Finalizada',
    'cancelada' => 'Cancelada',
];
$counts = [
    'total' => count($citas),
    'pendiente' => 0,
    'confirmada' => 0,
    'completada' => 0,
    'cancelada' => 0,
];
foreach ($citas as $cita) {
    $est = $cita['estado'] ?? 'pendiente';
    if (isset($counts[$est])) $counts[$est]++;
}
?>
<div class="container container-narrow" style="padding-top:24px;">

    <?php if (!empty($citas)): ?>
        <div class="dash-stats" style="margin-bottom:20px;">
            <div class="dash-stat-card">
                <div class="stat-value"><?= $counts['total'] ?></div>
                <div class="stat-label">Total Citas</div>
            </div>
            <div class="dash-stat-card stat-warning">
                <div class="stat-value"><?= $counts['pendiente'] ?></div>
                <div class="stat-label">Pendientes</div>
            </div>
            <div class="dash-stat-card stat-info">
                <div class="stat-value"><?= $counts['confirmada'] ?></div>
                <div class="stat-label">Confirmadas</div>
            </div>
            <div class="dash-stat-card stat-success">
                <div class="stat-value"><?= $counts['completada'] + $counts['cancelada'] ?></div>
                <div class="stat-label">Resueltas</div>
            </div>
        </div>
    <?php endif; ?>

    <div class="glass-card">
        <div class="dash-section-title">
            <i class="fa-solid fa-calendar-days"></i>
            <h2>Citas Asignadas</h2>
        </div>

        <?php if (empty($citas)): ?>
            <div class="dash-empty">
                <i class="fa-solid fa-calendar-xmark"></i>
                <h3>Sin citas asignadas</h3>
                <p>No tienes citas en este momento.</p>
            </div>
        <?php else: ?>
            <div class="dash-table-wrapper">
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Cliente</th>
                            <th>Servicio</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($citas as $cita): ?>
                            <tr data-cita-id="<?= $cita['id'] ?>">
                                <td class="fecha-cell"><?= htmlspecialchars(date('d/m/Y', strtotime($cita['fecha_cita']))) ?></td>
                                <td class="hora-cell"><?= htmlspecialchars(date('g:i a', strtotime($cita['hora_cita']))) ?></td>
                                <td class="cliente-cell">
                                    <?= htmlspecialchars($cita['cliente'] ?? '-') ?>
                                    <span class="hint small" style="display:block;color:var(--bone-dim);font-size:11px;"><?= htmlspecialchars($cita['email_cliente'] ?? '') ?></span>
                                </td>
                                <td class="servicio-cell"><?= htmlspecialchars($cita['servicio'] ?? '-') ?></td>
                                <td class="estado-cell">
                                    <select class="estado-select" data-cita-id="<?= $cita['id'] ?>" style="min-width:100px;">
                                        <?php foreach ($estadoLabels as $val => $label): ?>
                                            <option value="<?= $val ?>" <?= ($cita['estado'] ?? '') === $val ? 'selected' : '' ?>><?= $label ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <span class="badge badge-<?= htmlspecialchars($cita['estado'] ?? 'pendiente') ?>" style="display:block;margin-top:4px;">
                                        <?= htmlspecialchars($estadoLabels[$cita['estado'] ?? 'pendiente']) ?>
                                    </span>
                                </td>
                                <td class="acciones-cell">
                                    <a href="<?= BASE_URL ?>index.php?action=artist-agenda&view=cliente&id=<?= $cita['id'] ?>" class="btn-outline btn-sm">Expediente</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <div class="hint small" style="margin-top:16px;color:var(--bone-dim);">
            Cambia el estado de una cita usando el menú desplegable en cada fila.
        </div>
    </div>
</div>
