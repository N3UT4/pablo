<?php
$servicios = $servicios ?? [];
$artistas = $artistas ?? [];
?>
<div class="container container-narrow" style="padding-top:24px;">
  <div style="margin-bottom:16px;">
    <a href="javascript:history.back()" class="btn-outline btn-sm" style="text-decoration:none;"><i class="fa-solid fa-arrow-left"></i> Regresar</a>
  </div>

  <div class="card card-enter">
    <div class="card-head">
      <span class="eyebrow">Nueva Cita</span>
      <h1>Agendar Cita</h1>
      <p>Selecciona el tamaño, fecha y tatuador. El abono mínimo se calcula automáticamente según el tamaño elegido.</p>
    </div>

    <form id="bookingForm" class="dash-form" novalidate>
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">

      <!-- Tatuador Selection -->
      <div class="field">
        <label>Tatuador <span class="optional">(requerido)</span></label>
        <div id="artistOptions" class="artist-grid" role="radiogroup" aria-label="Seleccionar tatuador">
          <?php if (!empty($artistas)): ?>
            <?php foreach ($artistas as $index => $artista): ?>
              <label class="artist-option">
                <input type="radio" name="id_tatuador" value="<?= (int)$artista['id'] ?>" <?= $index === 0 ? 'checked' : '' ?> required>
                <div class="artist-card">
                  <?php if (!empty($artista['foto'])): ?>
                    <img src="<?= BASE_URL . htmlspecialchars($artista['foto']) ?>" alt="<?= htmlspecialchars($artista['nombre']) ?>" class="artist-avatar">
                  <?php else: ?>
                    <div class="artist-avatar placeholder"><i class="fa-solid fa-user"></i></div>
                  <?php endif; ?>
                  <span class="artist-name"><?= htmlspecialchars($artista['nombre']) ?></span>
                </div>
              </label>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="empty-state" style="grid-column:1/-1; padding:24px;">
              <i class="fa-solid fa-user-slash"></i>
              <h3>No hay tatuadores disponibles</h3>
              <p>Contacta al estudio para más información.</p>
            </div>
          <?php endif; ?>
        </div>
        <span id="artistErr" class="err">Selecciona un tatuador</span>
      </div>

      <!-- Service Type -->
      <div class="field">
        <label>Tipo de servicio <span class="optional">(requerido)</span></label>
        <select name="tipo_servicio" id="tipoServicio" required>
          <option value="">Selecciona un estilo</option>
          <?php foreach ($servicios as $servicio): ?>
            <option value="<?= htmlspecialchars($servicio['slug']) ?>" data-precio="<?= (float)$servicio['precio_desde'] ?>">
              <?= htmlspecialchars($servicio['nombre']) ?> — $<?= number_format((float)$servicio['precio_desde'], 0, ',', '.') ?> COP
            </option>
          <?php endforeach; ?>
        </select>
        <span id="tipoServicioErr" class="err">Selecciona un tipo de servicio</span>
      </div>

      <!-- Tattoo Size Selector (Interactive) -->
      <fieldset class="field">
        <legend>Tamaño del tatuaje <span class="optional">(requerido)</span></legend>
        <div id="sizeSelector" class="size-grid" role="radiogroup" aria-label="Seleccionar tamaño de tatuaje">
          <label class="size-option" data-size="pequeño" data-deposit="20000">
            <input type="radio" name="tamano" value="pequeño">
            <div class="size-card">
              <div class="size-icon small"><i class="fa-solid fa-circle"></i></div>
              <div class="size-info">
                <strong>Pequeño</strong>
                <span class="size-desc">Hasta 5 cm / Diseño simple</span>
              </div>
              <div class="size-deposit">
                <span class="deposit-label">Abono mínimo</span>
                <span class="deposit-value">$20.000 COP</span>
              </div>
            </div>
          </label>
          <label class="size-option" data-size="mediano" data-deposit="50000">
            <input type="radio" name="tamano" value="mediano">
            <div class="size-card">
              <div class="size-icon medium"><i class="fa-solid fa-circle"></i></div>
              <div class="size-info">
                <strong>Mediano</strong>
                <span class="size-desc">5–15 cm / Detalle medio</span>
              </div>
              <div class="size-deposit">
                <span class="deposit-label">Abono mínimo</span>
                <span class="deposit-value">$50.000 COP</span>
              </div>
            </div>
          </label>
          <label class="size-option" data-size="grande" data-deposit="100000">
            <input type="radio" name="tamano" value="grande">
            <div class="size-card">
              <div class="size-icon large"><i class="fa-solid fa-circle"></i></div>
              <div class="size-info">
                <strong>Grande</strong>
                <span class="size-desc">15+ cm / Alta complejidad</span>
              </div>
              <div class="size-deposit">
                <span class="deposit-label">Abono mínimo</span>
                <span class="deposit-value">$100.000 COP</span>
              </div>
            </div>
          </label>
        </div>
        <span id="tamanoErr" class="err">Selecciona el tamaño del tatuaje</span>
      </fieldset>

      <!-- Date & Time -->
      <div class="row2">
        <div class="field">
          <label>Fecha de la cita <span class="optional">(requerido)</span></label>
          <input type="date" name="fecha_cita" id="fechaCita" required autocomplete="off">
          <span id="fechaErr" class="err">Selecciona una fecha válida (mínimo 72 horas de anticipación)</span>
        </div>
        <div class="field">
          <label>Hora <span class="optional">(requerido)</span></label>
          <input type="time" name="hora_cita" id="horaCita" required min="08:00" max="21:00" step="1800">
          <span id="horaErr" class="err">Selecciona una hora entre 08:00 y 21:00</span>
        </div>
      </div>

      <!-- Payment Breakdown Card -->
      <div id="paymentBreakdown" class="payment-summary" style="display:none;" aria-live="polite">
        <h4><i class="fa-solid fa-calculator"></i> Desglose del Pago</h4>
        <div class="payment-summary-item">
          <span class="label">Precio Estimado (<span id="breakdownServiceName">—</span>)</span>
          <span class="value amount-display" id="estimatedPrice">$0 COP</span>
        </div>
        <div class="payment-summary-item">
          <span class="label">Abono Mínimo Requerido (<span id="breakdownSizeName">—</span>)</span>
          <span class="value amount-display" id="minimumDeposit">$0 COP</span>
        </div>
        <div class="payment-summary-item">
          <span class="label">Abono a Pagar Hoy</span>
          <span class="value amount-display" id="depositToPay">$0 COP</span>
        </div>
        <div class="payment-summary-item">
          <span class="label">Saldo Pendiente</span>
          <span class="value amount-display" id="remainingBalance">$0 COP</span>
        </div>
        <div class="payment-summary-total">
          <span class="label">Total a Pagar Hoy</span>
          <span class="value amount-display" id="totalToday">$0 COP</span>
        </div>
        <div id="fullPaymentOption" class="full-payment-toggle" style="margin-top:16px; padding-top:16px; border-top:1px solid var(--line); display:none;">
          <label class="check-row" style="background:transparent; border:none; padding:0;">
            <input type="checkbox" id="payFullAmount" name="pay_full" value="1">
            <span>Pagar el 100% del valor ahora (<span id="fullAmountLabel">$0 COP</span>)</span>
          </label>
          <p style="font-size:12px; color:var(--bone-dim); margin-top:8px;">Al marcar esta opción, el monto a pagar hoy será el precio total del tatuaje y no quedará saldo pendiente.</p>
        </div>
      </div>

      <!-- Monto Input (Hidden, calculated) -->
      <input type="hidden" name="monto" id="montoInput" value="">

      <!-- Payment Method -->
      <div class="field">
        <label>Método de pago <span class="optional">(requerido)</span></label>
        <div class="pay-methods" role="radiogroup" aria-label="Método de pago">
          <label class="pay-pill">
            <input type="radio" name="metodo_pago" value="nequi" required>
            <span><i class="fa-brands fa-whatsapp" style="font-size:20px; color:#25D366;"></i><br>Nequi</span>
          </label>
          <label class="pay-pill">
            <input type="radio" name="metodo_pago" value="transferencia">
            <span><i class="fa-solid fa-university" style="font-size:20px; color:var(--gold);"></i><br>Transferencia</span>
          </label>
          <label class="pay-pill">
            <input type="radio" name="metodo_pago" value="efectivo">
            <span><i class="fa-solid fa-money-bill-wave" style="font-size:20px; color:var(--success);"></i><br>Efectivo</span>
          </label>
          <label class="pay-pill">
            <input type="radio" name="metodo_pago" value="tarjeta">
            <span><i class="fa-solid fa-credit-card" style="font-size:20px; color:var(--bone-dim);"></i><br>Tarjeta</span>
          </label>
        </div>
        <span id="metodoErr" class="err">Selecciona un método de pago</span>
      </div>

      <!-- Nequi Comprobante (conditional) -->
      <div id="nequiField" class="field" style="display:none;">
        <label>Comprobante Nequi <span class="optional">(requerido para Nequi)</span></label>
        <input type="text" name="comprobante" id="comprobanteInput" placeholder="Número de transacción / referencia" autocomplete="off">
        <span id="comprobanteErr" class="err">Ingresa el número de comprobante (mín. 4 caracteres)</span>
      </div>

      <!-- Custom Design Details -->
      <div id="customDesignField" class="field" style="display:none;">
        <label>Detalle del diseño personalizado <span class="optional">(requerido para diseño personalizado)</span></label>
        <textarea name="detalle_personalizado" id="detallePersonalizado" rows="3" placeholder="Describe el diseño que deseas, referencias, medidas aproximadas..."></textarea>
        <span id="personalizadoErr" class="err">Describe el diseño (mín. 10 caracteres)</span>
      </div>

      <!-- Observations -->
      <div class="field">
        <label>Observaciones <span class="optional">(opcional)</span></label>
        <textarea name="observaciones" rows="2" placeholder="Alergias, medicamentos, notas especiales..."></textarea>
      </div>

      <!-- Submit -->
      <button type="submit" id="submitBtn" class="submit-btn" style="width:100%;">
        <i class="fa-solid fa-calendar-plus"></i> Confirmar Cita y Abono
      </button>
    </form>
  </div>
</div>

<!-- Alert Container -->
<div id="formAlert" class="alert" role="alert" aria-live="assertive">
  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
    <circle cx="12" cy="12" r="10"></circle>
    <line x1="12" y1="8" x2="12" y2="12"></line>
    <line x1="12" y1="16" x2="12.01" y2="16"></line>
  </svg>
  <span id="alertMessage"></span>
</div>

<script src="<?= BASE_URL ?>js/api.php"></script>
<script src="<?= BASE_URL ?>js/agendar-cita.js"></script>