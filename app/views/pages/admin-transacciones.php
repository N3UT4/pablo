<?php
$transacciones = $transacciones ?? [];
?>
<div class="container container-narrow" style="padding-top:24px;">

    <div class="glass-card">
        <div class="dash-section-title">
            <i class="fa-solid fa-receipt"></i>
            <h2>Registro Completo de Transacciones</h2>
        </div>

        <div class="dash-table-wrapper">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>Cita #</th>
                        <th>Cliente</th>
                        <th>Tatuador</th>
                        <th>Servicio</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Estado Cita</th>
                        <th>Monto</th>
                        <th>Método</th>
                        <th>Estado Pago</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transacciones as $t): ?>
                        <tr>
                            <td>#<?= (int) $t['cita_id'] ?></td>
                            <td><?= htmlspecialchars($t['cliente'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($t['tatuador'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($t['servicio'] ?? '-') ?></td>
                            <td style="font-size:12px;"><?= htmlspecialchars(date('d/m/Y', strtotime($t['fecha_cita']))) ?></td>
                            <td style="font-size:12px;"><?= htmlspecialchars(date('g:i a', strtotime($t['hora_cita']))) ?></td>
                            <td>
                                <span class="badge badge-<?= htmlspecialchars($t['estado'] ?? 'pendiente') ?>">
                                    <?= htmlspecialchars(ucfirst($t['estado'] ?? 'pendiente')) ?>
                                </span>
                            </td>
                            <td style="font-weight:600;">$<?= number_format((float) ($t['monto'] ?? 0), 0, ',', '.') ?></td>
                            <td><?= htmlspecialchars(ucfirst($t['metodo'] ?? '-')) ?></td>
                            <td>
                                <?php $ep = htmlspecialchars($t['estado_pago'] ?? 'pendiente'); ?>
                                <span class="badge <?= $ep === 'verificado' || $ep === 'pagado' ? 'badge-completada' : 'badge-pendiente' ?>">
                                    <?= htmlspecialchars(ucfirst($ep)) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($transacciones)): ?>
                        <tr>
                            <td colspan="10" style="text-align:center;padding:40px;color:var(--bone-dim);">
                                No hay transacciones registradas.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div style="margin-top:16px;text-align:right;">
            <button type="button" class="btn-outline btn-sm" onclick="window.print()">
                <i class="fa-solid fa-print"></i> Imprimir
            </button>
        </div>
    </div>
</div>
