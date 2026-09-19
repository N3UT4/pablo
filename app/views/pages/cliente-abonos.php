<?php
$citas = $citas ?? [];
$estadoLabels = [
    'pendiente' => 'Pendiente',
    'confirmada' => 'Confirmada',
    'completada' => 'Finalizada',
    'cancelada' => 'Cancelada',
];
?>
<div class="container container-narrow" style="padding-top:24px;">
  <div style="margin-bottom:16px;">
    <a href="javascript:history.back()" class="btn-outline btn-sm" style="text-decoration:none;"><i class="fa-solid fa-arrow-left"></i> Regresar</a>
  </div>

    <div class="glass-card">
        <div class="dash-section-title">
            <i class="fa-solid fa-credit-card"></i>
            <h2>Mis Abonos</h2>
        </div>

        <?php if (empty($citas)): ?>
            <div class="dash-empty">
                <i class="fa-solid fa-credit-card"></i>
                <h3>Sin abonos registrados</h3>
                <p>Agendar una cita para ver tus abonos pendientes.</p>
            </div>
        <?php else: ?>
            <?php foreach ($citas as $cita):
                $est = $cita['estado'] ?? 'pendiente';
                $monto = isset($cita['monto']) && $cita['monto'] > 0 ? (float) $cita['monto'] : 0;
                $metodo = htmlspecialchars($cita['metodo'] ?? '');
                $estadoPago = htmlspecialchars($cita['estado_pago'] ?? 'pendiente');
                $saldo = max(0, 500000 - $monto);
            ?>
                <div class="dash-section" style="border-bottom:1px solid rgba(247,243,236,0.06);padding-bottom:20px;margin-bottom:20px;">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:12px;">
                        <div>
                            <h3 style="font-family:'Bebas Neue',sans-serif;font-size:20px;color:var(--bone);margin-bottom:4px;">
                                <?= htmlspecialchars($cita['servicio'] ?? 'Servicio') ?>
                            </h3>
                            <p style="color:var(--bone-dim);font-size:12px;">
                                <?= htmlspecialchars(date('d/m/Y', strtotime($cita['fecha_cita']))) ?> · <?= htmlspecialchars(date('g:i a', strtotime($cita['hora_cita']))) ?>
                            </p>
                            <p style="color:var(--bone-dim);font-size:12px;">
                                Tatuador: <?= htmlspecialchars($cita['tatuador'] ?? '-') ?>
                            </p>
                        </div>
                        <span class="badge badge-<?= htmlspecialchars($est) ?>">
                            <?= htmlspecialchars($estadoLabels[$est]) ?>
                        </span>
                    </div>

                    <div class="payment-status <?= $estadoPago === 'pendiente' ? 'pending' : 'paid' ?>" style="margin-top:12px;">
                        <i class="fa-solid <?= $estadoPago === 'pendiente' ? 'fa-clock' : 'fa-circle-check' ?>"></i>
                        <span>
                            Estado del abono: <?= ucfirst($estadoPago) ?>
                            <?php if ($monto > 0): ?> · $<?= number_format($monto, 0, ',', '.') ?> (<?= ucfirst($metodo) ?>) <?php endif; ?>
                        </span>
                    </div>

                    <div style="display:flex;gap:24px;flex-wrap:wrap;margin-top:12px;font-size:13px;">
                        <div>
                            <span style="color:var(--bone-dim);">Monto abonado: </span>
                            <strong>$<?= number_format($monto, 0, ',', '.') ?></strong>
                        </div>
                        <div>
                            <span style="color:var(--bone-dim);">Saldo pendiente: </span>
                            <strong style="color:var(--gold);">$<?= number_format($saldo, 0, ',', '.') ?></strong>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
