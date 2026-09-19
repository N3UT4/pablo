<?php
$metrics = $metrics ?? [
    'total_citas' => 0,
    'pendientes' => 0,
    'confirmadas' => 0,
    'completadas' => 0,
    'canceladas' => 0,
    'citas_mes' => 0,
    'total_abonos' => 0,
    'ultimas_citas' => [],
];
?>
<div class="container container-narrow" style="padding-top:24px;">
  <div style="margin-bottom:16px;">
    <a href="javascript:history.back()" class="btn-outline btn-sm" style="text-decoration:none;"><i class="fa-solid fa-arrow-left"></i> Regresar</a>
  </div>

    <div class="dash-stats">
        <div class="dash-stat-card stat-success">
            <div class="stat-icon"><i class="fa-solid fa-dollar-sign"></i></div>
            <div class="stat-value">$<?= number_format($metrics['total_abonos'], 0, ',', '.') ?></div>
            <div class="stat-label">Ingresos por Abonos</div>
        </div>
        <div class="dash-stat-card">
            <div class="stat-icon"><i class="fa-solid fa-calendar"></i></div>
            <div class="stat-value"><?= $metrics['citas_mes'] ?></div>
            <div class="stat-label">Citas este Mes</div>
        </div>
        <div class="dash-stat-card stat-info">
            <div class="stat-icon"><i class="fa-solid fa-clipboard-list"></i></div>
            <div class="stat-value"><?= $metrics['total_citas'] ?></div>
            <div class="stat-label">Citas Totales</div>
        </div>
        <div class="dash-stat-card stat-warning">
            <div class="stat-icon"><i class="fa-solid fa-hourglass-half"></i></div>
            <div class="stat-value"><?= $metrics['pendientes'] ?></div>
            <div class="stat-label">Pendientes</div>
        </div>
    </div>

    <div class="dash-grid-2">
        <div class="glass-card">
            <div class="dash-section-title">
                <i class="fa-solid fa-chart-bar"></i>
                <h2>Resumen</h2>
            </div>
            <div style="display:flex;flex-direction:column;gap:12px;">
                <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid rgba(247,243,236,0.06);">
                    <span style="color:var(--bone-dim);font-size:13px;">Confirmadas</span>
                    <strong style="color:#60a5fa;"><?= $metrics['confirmadas'] ?></strong>
                </div>
                <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid rgba(247,243,236,0.06);">
                    <span style="color:var(--bone-dim);font-size:13px;">Completadas</span>
                    <strong style="color:#22c55e;"><?= $metrics['completadas'] ?></strong>
                </div>
                <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid rgba(247,243,236,0.06);">
                    <span style="color:var(--bone-dim);font-size:13px;">Canceladas</span>
                    <strong style="color:#ef4444;"><?= $metrics['canceladas'] ?></strong>
                </div>
                <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid rgba(247,243,236,0.06);">
                    <span style="color:var(--bone-dim);font-size:13px;">Abono Total</span>
                    <strong style="color:var(--blood-bright);">$<?= number_format($metrics['total_abonos'], 0, ',', '.') ?></strong>
                </div>
                <div style="display:flex;justify-content:space-between;padding:10px 0;">
                    <span style="color:var(--bone-dim);font-size:13px;">Citas este Mes</span>
                    <strong><?= $metrics['citas_mes'] ?></strong>
                </div>
            </div>
        </div>

        <div class="glass-card">
            <div class="dash-section-title">
                <i class="fa-solid fa-clock-rotate-left"></i>
                <h2>Últimas 5 Citas</h2>
            </div>
            <?php if (!empty($metrics['ultimas_citas'])): ?>
                <div style="overflow-x:auto;">
                    <table style="width:100%;border-collapse:collapse;font-size:13px;">
                        <thead>
                            <tr style="text-align:left;color:var(--bone-dim);border-bottom:1px solid rgba(247,243,236,0.1);">
                                <th style="padding:8px;">Cliente</th>
                                <th style="padding:8px;">Tatuador</th>
                                <th style="padding:8px;">Servicio</th>
                                <th style="padding:8px;">Fecha / Hora</th>
                                <th style="padding:8px;">Estado</th>
                                <th style="padding:8px;">Abono</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($metrics['ultimas_citas'] as $cita): ?>
                                <?php
                                $estado = $cita['estado'];
                                $estadoClass = match($estado) {
                                    'pendiente' => 'status-pendiente',
                                    'confirmada' => 'status-confirmada',
                                    'completada' => 'status-completada',
                                    'cancelada' => 'status-cancelada',
                                    default => ''
                                };
                                $estadoLabel = match($estado) {
                                    'pendiente' => 'Pendiente',
                                    'confirmada' => 'Confirmada',
                                    'completada' => 'Completada',
                                    'cancelada' => 'Cancelada',
                                    default => ucfirst($estado)
                                };
                                $abono = $cita['monto'] ? '$' . number_format($cita['monto'], 0, ',', '.') . ' (' . ($cita['estado_pago'] ?? 'pendiente') . ')' : '—';
                                ?>
                                <tr style="border-bottom:1px solid rgba(247,243,236,0.04);">
                                    <td style="padding:8px;"><?= htmlspecialchars($cita['cliente']) ?></td>
                                    <td style="padding:8px;"><?= htmlspecialchars($cita['tatuador']) ?></td>
                                    <td style="padding:8px;"><?= htmlspecialchars($cita['servicio']) ?></td>
                                    <td style="padding:8px;"><?= date('d/m/Y', strtotime($cita['fecha_cita'])) ?> <?= date('H:i', strtotime($cita['hora_cita'])) ?></td>
                                    <td style="padding:8px;"><span class="status-badge <?= $estadoClass ?>"><?= $estadoLabel ?></span></td>
                                    <td style="padding:8px;"><?= $abono ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div style="text-align:center;padding:24px;color:var(--bone-dim);">
                    <i class="fa-solid fa-calendar-xmark" style="font-size:32px;margin-bottom:8px;display:block;opacity:0.4;"></i>
                    <p>No hay citas registradas aún.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="dash-grid-2" style="margin-top:24px;">
        <div class="glass-card">
            <div class="dash-section-title">
                <i class="fa-solid fa-bolt"></i>
                <h2>Acceso Rápido</h2>
            </div>
            <div class="panel-grid-dash" style="margin-bottom:0;grid-template-columns:1fr;">
                <a href="<?= BASE_URL ?>index.php?action=admin-usuarios" class="panel-item">
                    <i class="fa-solid fa-users"></i>
                    <h3>Gestión de Usuarios</h3>
                    <p>Crear, editar y asignar roles</p>
                </a>
                <a href="<?= BASE_URL ?>index.php?action=admin-servicios" class="panel-item">
                    <i class="fa-solid fa-scissors"></i>
                    <h3>Servicios y Precios</h3>
                    <p>CRUD de estilos ofertados</p>
                </a>
                <a href="<?= BASE_URL ?>index.php?action=admin-transacciones" class="panel-item">
                    <i class="fa-solid fa-receipt"></i>
                    <h3>Transacciones</h3>
                    <p>Historial de abonos y citas</p>
                </a>
            </div>
        </div>
    </div>
</div>
