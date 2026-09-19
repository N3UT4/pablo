<?php
$servicios = $servicios ?? [];
?>
<div class="container container-narrow" style="padding-top:24px;">
  <div style="margin-bottom:16px;">
    <a href="javascript:history.back()" class="btn-outline btn-sm" style="text-decoration:none;"><i class="fa-solid fa-arrow-left"></i> Regresar</a>
  </div>

    <div class="glass-card">
        <div class="dash-section-title">
            <i class="fa-solid fa-plus"></i>
            <h2>Agendar Nueva Cita</h2>
        </div>

        <form method="POST" action="<?= BASE_URL ?>index.php?action=book-appointment" class="dash-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">

            <div class="form-group">
                <label>Selecciona un artista</label>
                <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:6px;">
                    <?php if (!empty($servicios)): ?>
                        <?php foreach ($servicios as $servicio): ?>
                            <label class="glass-check" style="padding:12px;border:1px solid rgba(247,243,236,0.08);border-radius:8px;cursor:pointer;flex:1;min-width:200px;">
                                <input type="radio" name="id_tatuador" value="1" checked>
                                <span>
                                    <strong style="color:var(--bone);display:block;"><?= htmlspecialchars($servicio['nombre']) ?></strong>
                                    <span style="color:var(--bone-dim);font-size:12px;">Desde $<?= htmlspecialchars($servicio['precio_desde'] ?? '0') ?></span>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="dash-empty" style="padding:20px;">
                            <p>No hay servicios disponibles actualmente.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Tipo de servicio</label>
                    <select name="tipo_servicio" required>
                        <?php foreach ($servicios as $servicio): ?>
                            <option value="<?= htmlspecialchars($servicio['slug']) ?>">
                                <?= htmlspecialchars($servicio['nombre']) ?> — $<?= htmlspecialchars($servicio['precio_desde'] ?? '0') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Fecha de la cita</label>
                    <input type="date" name="fecha_cita" required min="<?= date('Y-m-d') ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Hora</label>
                    <input type="time" name="hora_cita" required>
                </div>
                <div class="form-group">
                    <label>Monto del abono</label>
                    <input type="number" name="monto" required min="50000" step="50000" placeholder="50000" value="50000">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Método de pago</label>
                    <select name="metodo_pago" required>
                        <option value="nequi">Nequi</option>
                        <option value="transferencia">Transferencia</option>
                        <option value="efectivo">Efectivo</option>
                        <option value="tarjeta">Tarjeta</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Comprobante (Nequi/Transferencia)</label>
                    <input type="text" name="comprobante" placeholder="Número de referencia">
                </div>
            </div>

            <div class="form-group">
                <label>Detalle personalizado (opcional)</label>
                <textarea name="detalle_personalizado" rows="3" placeholder="Describe el diseño que deseas..."></textarea>
            </div>

            <div class="form-group">
                <label>Observaciones (opcional)</label>
                <textarea name="observaciones" rows="2" placeholder="Alergias, notas especiales..."></textarea>
            </div>

            <button type="submit" class="btn-glow" style="width:100%;">
                <i class="fa-solid fa-calendar-plus"></i> Confirmar Cita y Abono
            </button>
        </form>
    </div>
</div>
