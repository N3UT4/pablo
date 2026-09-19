<div class="container container-narrow">
  <div style="margin-bottom:16px;">
    <a href="javascript:history.back()" class="btn-outline btn-sm" style="text-decoration:none;"><i class="fa-solid fa-arrow-left"></i> Regresar</a>
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

      <button type="button" id="validarBtn" class="submit-btn" style="background: var(--gold); color: var(--ink); margin-bottom: 20px;">
        Validar cupón
      </button>

      <!-- Mostrado solo si el cupón es válido -->
      <div id="cuponValido" style="display:none;">
        <div class="alert success" style="display:flex;">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
          <span>¡Cupón válido! Este descuento se aplicará a tu próxima compra.</span>
        </div>

        <div style="background: var(--panel-2); border: 1px solid var(--line); border-radius: 4px; padding: 16px; margin-bottom: 20px;">
          <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
            <strong id="nombrePromo">Descuento 15%</strong>
            <span id="montoPromo" style="color: var(--blood-bright); font-weight: 600;">-$20.000</span>
          </div>
          <p id="descripcionPromo" style="font-size: 13px; color: var(--bone-dim); margin-bottom: 8px;">Válido para tatuajes desde $100.000</p>
          <p id="vigenciaPromo" style="font-size: 12px; color: var(--bone-dim);">Válido hasta: 31 dic 2026</p>
        </div>
      </div>

      <div id="formAplicacion" style="display:none;">
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
          <label for="acepta_terminos" style="text-transform:none; letter-spacing:0; font-weight:400; color:var(--bone-dim);">
            Acepto que el cupón solo es válido una vez y no es transferible.
          </label>
        </div>
        <span class="err" id="aceptaTerminosErr" style="margin-top:-8px;">Debes aceptar los términos para continuar.</span>

        <button type="submit" class="submit-btn">Registrar cupón en mi cuenta</button>
      </div>

      <div id="sinCupon" style="display:none; text-align: center; padding: 40px 0;">
        <p style="color: var(--bone-dim); font-size: 14px;">Código no encontrado o expirado</p>
        <p style="color: var(--bone-dim); font-size: 13px; margin-top: 8px;">Verifica el código e intenta de nuevo</p>
      </div>
    </form>

    <div class="card" style="margin-top: 24px; padding: 16px; border: 1px dashed rgba(247,243,236,0.15);">
      <div class="section-title">🧪 Demostración de errores</div>
      <p style="color: var(--bone-dim); font-size: 12px; margin-bottom: 12px;">Botones para presentar las páginas de error</p>
      <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <button type="button" class="btn-outline btn-sm" onclick="window.location.href='<?= BASE_URL ?>index.php?action=demo-404'">
          <i class="fa-solid fa-triangle-exclamation"></i> Mostrar Error 404
        </button>
        <button type="button" class="btn-outline btn-sm" onclick="window.location.href='<?= BASE_URL ?>index.php?action=demo-500'" style="color:#ff6b6b; border-color:rgba(255,107,107,0.3);">
          <i class="fa-solid fa-server"></i> Mostrar Error 500
        </button>
      </div>
    </div>
  </div>

  <div style="border-top: 1px solid var(--line); margin-top: 40px; padding-top: 24px;">
    <div class="section-title">Promociones activas</div>
    <div style="display: flex; flex-direction: column; gap: 12px;">
      <div style="background: var(--panel-2); border-left: 3px solid var(--gold); padding: 12px 14px; border-radius: 2px;">
        <strong style="color: var(--gold);">WELCOME20</strong> — Descuento 20% para clientes nuevos
      </div>
      <div style="background: var(--panel-2); border-left: 3px solid var(--success); padding: 12px 14px; border-radius: 2px;">
        <strong style="color: var(--success);">REFERIDOS</strong> — Descuento por cada amigo que refiera
      </div>
      <div style="background: var(--panel-2); border-left: 3px solid var(--blood-bright); padding: 12px 14px; border-radius: 2px;">
        <strong style="color: var(--blood-bright);">LOYALTY</strong> — Descuentos progresivos por sesiones
      </div>
    </div>
  </div>
</div>