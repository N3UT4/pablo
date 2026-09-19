<?php
$horarios = $horarios ?? [];
$dias = [
    1 => 'Lunes',
    2 => 'Martes',
    3 => 'Miércoles',
    4 => 'Jueves',
    5 => 'Viernes',
    6 => 'Sábado',
    0 => 'Domingo',
];
$horarioMap = [];
foreach ($horarios as $h) {
    $horarioMap[$h['dia_semana']] = $h;
}
?>
<div class="container container-narrow" style="padding-top:24px;">

    <div class="glass-card">
        <div class="dash-section-title">
            <i class="fa-solid fa-clock"></i>
            <h2>Mi Disponibilidad</h2>
        </div>

        <p class="hint small" style="margin-bottom:20px;">Marca los días que trabajas y ajusta los horarios. Estos horarios no afectan los horarios globales del sitio web.</p>

        <div class="schedule-editor">
            <?php foreach ($dias as $diaNum => $diaNombre): ?>
                <?php $h = $horarioMap[$diaNum] ?? null; ?>
                <div class="schedule-row" data-dia="<?= $diaNum ?>" style="background:rgba(28,28,28,0.5);border:1px solid rgba(247,243,236,0.06);border-radius:8px;padding:14px 18px;margin-bottom:10px;display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
                    <div class="schedule-day" style="flex:0 0 auto;min-width:120px;">
                        <label class="glass-check" style="padding:0;">
                            <input type="checkbox" class="dia-disponible" data-dia="<?= $diaNum ?>" <?= ($h['disponible'] ?? 0) ? 'checked' : '' ?>>
                            <span><?= htmlspecialchars($diaNombre) ?></span>
                        </label>
                    </div>
                    <div class="schedule-times" style="flex:1;min-width:200px;display:flex;align-items:center;gap:8px;">
                        <input type="time" class="hora-inicio" data-dia="<?= $diaNum ?>"
                               value="<?= htmlspecialchars(substr($h['hora_inicio'] ?? '09:00:00', 0, 5)) ?>"
                               <?= ($h['disponible'] ?? 0) ? '' : 'disabled' ?> style="flex:1;">
                        <span style="color:var(--bone-dim);">—</span>
                        <input type="time" class="hora-fin" data-dia="<?= $diaNum ?>"
                               value="<?= htmlspecialchars(substr($h['hora_fin'] ?? '19:00:00', 0, 5)) ?>"
                               <?= ($h['disponible'] ?? 0) ? '' : 'disabled' ?> style="flex:1;">
                    </div>
                    <button type="button" class="btn-glow btn-sm save-dia" data-dia="<?= $diaNum ?>">Guardar</button>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="hint small" style="margin-top:16px;color:var(--bone-dim);">
            Los horarios que configures aquí se usan para la gestión de tu agenda. No modifican los horarios globales del estudio.
        </div>
    </div>
</div>
