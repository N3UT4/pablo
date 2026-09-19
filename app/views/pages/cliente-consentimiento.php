<?php
$cita = $cita ?? null;
?>
<div class="container container-narrow" style="padding-top:24px;">
  <div style="margin-bottom:16px;">
    <a href="javascript:history.back()" class="btn-outline btn-sm" style="text-decoration:none;"><i class="fa-solid fa-arrow-left"></i> Regresar</a>
  </div>

    <div class="glass-card">
        <div class="dash-section-title">
            <i class="fa-solid fa-file-signature"></i>
            <h2>Consentimiento Informado</h2>
        </div>

        <?php if (!$cita): ?>
            <div class="dash-empty">
                <i class="fa-solid fa-file-signature"></i>
                <h3>Sin consentimiento pendiente</h3>
                <p>No tienes una cita que requiera firma de consentimiento en este momento.</p>
                <a href="<?= BASE_URL ?>index.php?action=cliente-citas" class="btn-glow">Ver Mis Citas</a>
            </div>
        <?php else: ?>
            <div class="dash-alert info" style="margin-bottom:20px;">
                <strong>Cita seleccionada:</strong> <?= htmlspecialchars($cita['servicio'] ?? 'Servicio') ?>
                · <?= htmlspecialchars(date('d/m/Y', strtotime($cita['fecha_cita']))) ?> a las <?= htmlspecialchars(date('g:i a', strtotime($cita['hora_cita']))) ?>
            </div>

            <form method="POST" action="<?= BASE_URL ?>index.php?action=submit-consent" class="dash-form" onsubmit="return confirm('¿Confirmas que has leído y aceptas el consentimiento informado?');">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                <input type="hidden" name="appointment_id" value="<?= (int) $cita['id'] ?>">

                <div class="form-row">
                    <div class="form-group">
                        <label>Nombre completo</label>
                        <input type="text" name="nombre_cliente" required placeholder="Tu nombre completo">
                    </div>
                    <div class="form-group">
                        <label>Documento (cédula)</label>
                        <input type="text" name="documento" required placeholder="Número de documento" pattern="\d+">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Fecha de nacimiento</label>
                        <input type="date" name="fecha_nacimiento" required>
                    </div>
                    <div class="form-group">
                        <label>Procedimiento a realizar</label>
                        <input type="text" name="procedimiento" required placeholder="Describe el procedimiento">
                    </div>
                </div>

                <div class="form-group">
                    <label class="glass-check">
                        <input type="checkbox" name="acepta_riesgos" value="1" required>
                        <span>Declaro que he leído y acepto los riesgos asociados al procedimiento, liberando de responsabilidad a ITZA TATTOO y su personal.</span>
                    </label>
                </div>

                <div class="form-group">
                    <label>Firma digital</label>
                    <p style="color:var(--bone-dim);font-size:11px;margin-bottom:6px;">Firma en el espacio siguiente con tu dedo o mouse:</p>
                    <canvas id="signatureCanvas" class="signature-area" width="800" height="200"></canvas>
                    <input type="hidden" name="firma_cliente" id="firmaCliente" value="">
                    <button type="button" class="btn-outline btn-sm" id="clearSignature" style="margin-top:8px;">Limpiar firma</button>
                </div>

                <button type="submit" class="btn-glow" style="width:100%;margin-top:12px;">
                    <i class="fa-solid fa-signature"></i> Firmar Consentimiento
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>

<script>
(function() {
    var canvas = document.getElementById('signatureCanvas');
    if (!canvas) return;
    var ctx = canvas.getContext('2d');
    var drawing = false;
    ctx.strokeStyle = '#000';
    ctx.lineWidth = 2;
    ctx.lineCap = 'round';

    function getPos(e) {
        var r = canvas.getBoundingClientRect();
        var x = (e.touches ? e.touches[0].clientX : e.clientX) - r.left;
        var y = (e.touches ? e.touches[0].clientY : e.clientY) - r.top;
        return [x * (canvas.width / r.width), y * (canvas.height / r.height)];
    }

    canvas.addEventListener('mousedown', function(e) { drawing = true; ctx.beginPath(); ctx.moveTo(...getPos(e)); });
    canvas.addEventListener('mousemove', function(e) { if (drawing) ctx.lineTo(...getPos(e)); ctx.stroke(); });
    canvas.addEventListener('mouseup', function() { drawing = false; document.getElementById('firmaCliente').value = canvas.toDataURL(); });
    canvas.addEventListener('mouseleave', function() { drawing = false; document.getElementById('firmaCliente').value = canvas.toDataURL(); });
    canvas.addEventListener('touchstart', function(e) { e.preventDefault(); drawing = true; ctx.beginPath(); ctx.moveTo(...getPos(e)); });
    canvas.addEventListener('touchmove', function(e) { e.preventDefault(); if (drawing) ctx.lineTo(...getPos(e)); ctx.stroke(); });
    canvas.addEventListener('touchend', function() { drawing = false; document.getElementById('firmaCliente').value = canvas.toDataURL(); });

    document.getElementById('clearSignature').addEventListener('click', function() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        document.getElementById('firmaCliente').value = '';
    });
})();
</script>
