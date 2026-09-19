<div class="container container-narrow">
  <div style="margin-bottom:16px;">
    <a href="javascript:history.back()" class="btn-outline btn-sm" style="text-decoration:none;"><i class="fa-solid fa-arrow-left"></i> Regresar</a>
  </div>
  <div class="card">
    <div class="card-head">
      <span class="eyebrow">Área personal</span>
      <h1>Editar perfil</h1>
      <p>Actualiza tu información de contacto y datos personales.</p>
    </div>

    <form id="perfilForm" novalidate>
      <div class="section-title">Datos personales</div>

      <div class="field">
        <label for="nombre_perfil">Nombre completo</label>
        <input type="text" id="nombre_perfil" name="nombre_perfil" placeholder="Nombre y apellido">
        <span class="err" id="nombrePerfilErr">Escribe tu nombre completo.</span>
      </div>

      <div class="row2">
        <div class="field">
          <label for="email_perfil">Correo electrónico</label>
          <input type="email" id="email_perfil" name="email_perfil" placeholder="tu@correo.com">
          <span class="err" id="emailPerfilErr">Ingresa un correo válido.</span>
        </div>
        <div class="field">
          <label for="telefono_perfil">Teléfono</label>
          <input type="tel" id="telefono_perfil" name="telefono_perfil" placeholder="300 000 0000">
          <span class="err" id="telefonoPerfilErr">Ingresa 10 dígitos.</span>
        </div>
      </div>

      <div class="row2">
        <div class="field">
          <label for="documento_perfil">Documento de identidad</label>
          <input type="text" id="documento_perfil" name="documento_perfil" placeholder="Cédula / T.I.">
          <span class="err" id="documentoPerfilErr">Ingresa un documento válido.</span>
        </div>
        <div class="field">
          <label for="fecha_nacimiento_perfil">Fecha de nacimiento</label>
          <input type="date" id="fecha_nacimiento_perfil" name="fecha_nacimiento_perfil">
          <span class="err" id="fechaNacimientoPerfilErr">Ingresa tu fecha de nacimiento.</span>
        </div>
      </div>

      <div class="section-title">Dirección</div>

      <div class="field">
        <label for="ciudad">Ciudad</label>
        <input type="text" id="ciudad" name="ciudad" placeholder="Ej: Medellín">
        <span class="err" id="ciudadErr">Ingresa tu ciudad.</span>
      </div>

      <div class="field">
        <label for="direccion">Dirección</label>
        <input type="text" id="direccion" name="direccion" placeholder="Calle, carrera, número">
        <span class="err" id="direccionErr">Ingresa tu dirección.</span>
      </div>

      <div class="row2">
        <div class="field">
          <label for="barrio">Barrio <span class="optional">(opcional)</span></label>
          <input type="text" id="barrio" name="barrio" placeholder="Ej: Laureles">
        </div>
        <div class="field">
          <label for="codigo_postal">Código postal <span class="optional">(opcional)</span></label>
          <input type="text" id="codigo_postal" name="codigo_postal" placeholder="Ej: 050021">
        </div>
      </div>

      <div class="section-title">Preferencias</div>

      <div class="check-row">
        <input type="checkbox" id="recibir_promo" name="recibir_promo" checked>
        <label for="recibir_promo" style="text-transform:none; letter-spacing:0; font-weight:400; color:var(--bone-dim);">
          Deseo recibir promociones, ofertas y noticias de ITZA TATTOO por correo y WhatsApp.
        </label>
      </div>

      <div class="section-title">Cambiar contraseña</div>

      <div class="field">
        <label for="password_actual">Contraseña actual <span class="optional">(solo si deseas cambiarla)</span></label>
        <div class="password-wrap">
          <input type="password" id="password_actual" name="password_actual" placeholder="••••••••">
          <button type="button" class="password-toggle" aria-label="Mostrar contraseña"><i class="fa-solid fa-eye"></i></button>
        </div>
        <span class="err" id="passwordActualErr">Contraseña incorrecta.</span>
      </div>

      <div class="row2">
        <div class="field">
          <label for="password_nueva">Nueva contraseña</label>
          <div class="password-wrap">
            <input type="password" id="password_nueva" name="password_nueva" placeholder="Mínimo 6 caracteres">
            <button type="button" class="password-toggle" aria-label="Mostrar contraseña"><i class="fa-solid fa-eye"></i></button>
          </div>
          <span class="err" id="passwordNuevaErr">Mínimo 6 caracteres.</span>
        </div>
        <div class="field">
          <label for="password_confirm">Confirmar contraseña</label>
          <div class="password-wrap">
            <input type="password" id="password_confirm" name="password_confirm" placeholder="Repite la contraseña">
            <button type="button" class="password-toggle" aria-label="Mostrar contraseña"><i class="fa-solid fa-eye"></i></button>
          </div>
          <span class="err" id="passwordConfirmErr">Las contraseñas no coinciden.</span>
        </div>
      </div>

      <button type="submit" class="submit-btn">Guardar cambios</button>
      <p class="hint">Tus datos están protegidos con encriptación de seguridad.</p>
    </form>
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.password-toggle').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var input = this.previousElementSibling;
      var icon = this.querySelector('i');
      if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
        this.setAttribute('aria-label', 'Ocultar contraseña');
      } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
        this.setAttribute('aria-label', 'Mostrar contraseña');
      }
    });
  });
});
</script>
