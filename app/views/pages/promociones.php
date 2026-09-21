<div class="container container-narrow">
  <div class="card-head-cta">
    <a href="javascript:history.back()" class="btn-outline btn-sm">
      <i class="fa-solid fa-arrow-left"></i> Regresar
    </a>
  </div>

  <div class="card">
    <div class="card-head">
      <span class="eyebrow">Descuentos exclusivos</span>
      <h1>Canjear cupón</h1>
      <p>Ingresa un código de promoción para obtener descuentos en tu próxima cita.</p>
    </div>

    <form id="promocionesForm" novalidate>
      <div class="section-title">Validar cupón</div>

      <div class="field">
        <label for="codigo_cupon">Código de cupón</label>
        <input type="text" id="codigo_cupon" name="codigo_cupon" placeholder="Ej: ITZA2026" autocomplete="off">
        <span class="err" id="codigoCuponErr">Ingresa un código válido.</span>
      </div>

      <button type="button" id="validarBtn" class="submit-btn gold">
        Validar cupón
      </button>

      <div id="cuponValido" class="hidden">
        <div class="alert success show">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
          <span>¡Cupón válido! Este descuento se aplicará a tu próxima compra.</span>
        </div>

        <div class="promo-display">
          <div class="promo-name">
            <strong id="nombrePromo">Descuento 15%</strong>
            <span class="promo-amount" id="montoPromo">-$20.000</span>
          </div>
          <p class="promo-desc" id="descripcionPromo">Válido para tatuajes desde $100.000</p>
          <p class="promo-vigencia" id="vigenciaPromo">Válido hasta: 31 dic 2026</p>
        </div>
      </div>

      <div id="formAplicacion" class="hidden">
        <div class="section-title">Aplicar a tu cita</div>

        <div class="field">
          <label for="email_cupon">Tu correo electrónico</label>
          <input type="email" id="email_cupon" name="email_cupon" placeholder="tu@correo.com">
          <span class="err" id="emailCuponErr">Ingresa un correo válido.</span>
        </div>

        <div class="field">
          <label for="fecha_uso">Fecha planeada para usar el cupón</label>
          <input type="date" id="fecha_uso" name="fecha_uso">
          <span class="err" id="fechaUsoErr">Ingresa una fecha válida.</span>
        </div>

        <div class="field">
          <label for="notas_cupon">Notas <span class="optional">(opcional)</span></label>
          <textarea id="notas_cupon" name="notas_cupon" placeholder="Ej: Aplicar a mi próxima sesión de blackwork" rows="3"></textarea>
        </div>

        <div class="check-row">
          <input type="checkbox" id="acepta_terminos" name="acepta_terminos">
          <label for="acepta_terminos">
            Acepto que el cupón solo es válido una vez y no es transferible.
          </label>
        </div>
        <span class="err" id="aceptaTerminosErr">Debes aceptar los términos para continuar.</span>

        <button type="submit" class="submit-btn">Registrar cupón en mi cuenta</button>
      </div>

      <div id="sinCupon" class="hidden">
        <p class="hint">Código no encontrado o expirado</p>
        <p class="hint">Verifica el código e intenta de nuevo</p>
      </div>
    </form>

    <div class="info-card">
      <div class="info-card-body">
        <div class="section-title">🧪 Demostración de errores</div>
        <p class="hint">Botones para presentar las páginas de error</p>
        <div class="demo-actions">
          <button type="button" class="btn-outline btn-sm" onclick="window.location.href='<?= BASE_URL ?>index.php?action=demo-404'">
            <i class="fa-solid fa-triangle-exclamation"></i> Mostrar Error 404
          </button>
          <button type="button" class="btn-outline btn-sm btn-error" onclick="window.location.href='<?= BASE_URL ?>index.php?action=demo-500'">
            <i class="fa-solid fa-server"></i> Mostrar Error 500
          </button>
        </div>
      </div>
    </div>
  </div>

  <div class="section-divider-thin">
    <div class="section-title">Promociones activas</div>
    <div class="promo-list">
      <div class="promo-list-item promo-gold">
        <strong>WELCOME20</strong> — Descuento 20% para clientes nuevos
      </div>
      <div class="promo-list-item promo-success">
        <strong>REFERIDOS</strong> — Descuento por cada amigo que refiera
      </div>
      <div class="promo-list-item promo-blood">
        <strong>LOYALTY</strong> — Descuentos progresivos por sesiones
      </div>
    </div>
  </div>
</div>
