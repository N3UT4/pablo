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

<div class="container container-narrow">
  <div class="welcome-banner">
    <h2>Mis Horarios</h2>
    <p>Disponibilidad semanal</p>
  </div>

  <div class="card">
    <div class="card-head">
      <span class="eyebrow">Horarios</span>
      <h1>Mi Disponibilidad</h1>
      <p class="hint">Marca los días que trabajas y ajusta los horarios. Estos horarios no afectan los horarios globales del sitio web.</p>
    </div>

    <div class="schedule-editor">
      <?php foreach ($dias as $diaNum => $diaNombre): ?>
        <?php $h = $horarioMap[$diaNum] ?? null; ?>
        <div class="schedule-row" data-dia="<?= $diaNum ?>">
          <div class="schedule-day">
            <label class="checkbox-label">
              <input type="checkbox" class="dia-disponible" data-dia="<?= $diaNum ?>"
                     <?= ($h['disponible'] ?? 0) ? 'checked' : '' ?>>
              <span class="checkmark"></span>
              <span><?= htmlspecialchars($diaNombre) ?></span>
            </label>
          </div>
          <div class="schedule-times">
            <input type="time" class="hora-inicio" data-dia="<?= $diaNum ?>"
                   value="<?= htmlspecialchars(substr($h['hora_inicio'] ?? '09:00:00', 0, 5)) ?>"
                   <?= ($h['disponible'] ?? 0) ? '' : 'disabled' ?>>
            <span class="time-separator">—</span>
            <input type="time" class="hora-fin" data-dia="<?= $diaNum ?>"
                   value="<?= htmlspecialchars(substr($h['hora_fin'] ?? '19:00:00', 0, 5)) ?>"
                   <?= ($h['disponible'] ?? 0) ? '' : 'disabled' ?>>
          </div>
          <button type="button" class="save-dia btn-ghost small" data-dia="<?= $diaNum ?>">Guardar</button>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="hint small" style="margin-top: 16px;">
      Los horarios que configures aquí se usan para la gestión de tu agenda. No modifican los horarios globales del estudio.
    </div>
  </div>
</div>
