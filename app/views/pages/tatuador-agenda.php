<?php
$citas = $citas ?? [];
$estadoLabels = [
    'pendiente' => 'Pendiente por revisar',
    'confirmada' => 'Confirmada',
    'completada' => 'Completada',
    'cancelada' => 'Rechazada',
];
$estadoColors = [
    'pendiente' => 'var(--gold)',
    'confirmada' => '#3b82f6',
    'completada' => '#10b981',
    'cancelada' => '#ef4444',
];
?>

<div class="container container-narrow">
  <div class="welcome-banner">
    <h2>Mi Agenda</h2>
    <p>Citas asignadas a ti</p>
  </div>

  <div class="card">
    <div class="card-head">
      <span class="eyebrow">Citas</span>
      <h1>Citas Asignadas</h1>
    </div>

    <?php if (empty($citas)): ?>
      <p class="hint">No tienes citas asignadas en este momento.</p>
    <?php else: ?>
      <div class="table-wrapper">
        <table class="appointment-table">
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
                  <?= htmlspecialchars($cita['cliente']) ?>
                  <span class="hint small"><?= htmlspecialchars($cita['email_cliente']) ?></span>
                </td>
                <td class="servicio-cell"><?= htmlspecialchars($cita['servicio']) ?></td>
                <td class="estado-cell">
                  <select class="estado-select" data-cita-id="<?= $cita['id'] ?>">
                    <?php foreach ($estadoLabels as $val => $label): ?>
                      <option value="<?= $val ?>" <?= ($cita['estado'] ?? '') === $val ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                  </select>
                  <span class="status-badge" style="background:<?= $estadoColors[$cita['estado'] ?? 'pendiente'] ?>1A; color:<?= $estadoColors[$cita['estado'] ?? 'pendiente'] ?>;">
                    <?= htmlspecialchars($estadoLabels[$cita['estado'] ?? 'pendiente']) ?>
                  </span>
                </td>
                <td class="acciones-cell">
                  <a href="<?= BASE_URL ?>index.php?action=artist-agenda&view=cliente&id=<?= $cita['id'] ?>" class="btn-ghost small">Expediente</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <p class="hint small" style="margin-top:16px;">Cambia el estado de una cita usando el menú desplegable en cada fila.</p>
    <?php endif; ?>
  </div>
</div>
