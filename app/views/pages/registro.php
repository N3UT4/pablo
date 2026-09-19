<div class="agenda-wrap">
  <div class="welcome-banner">
    <h2>Crear tu cuenta</h2>
    <p>Regístrate para poder agendar tu cita, hacer seguimiento de tus pagos y firmar tu consentimiento digital.</p>
  </div>

  <div class="container wide" style="margin-top:24px;">
  <div style="margin-bottom:16px;">
    <a href="javascript:history.back()" class="btn-outline btn-sm" style="text-decoration:none;"><i class="fa-solid fa-arrow-left"></i> Regresar</a>
  </div>
    <div class="card">
      <div class="card-head">
        <span class="eyebrow">Paso 1</span>
        <h1>Cuenta de cliente</h1>
      </div>

      <div class="form-progress">
        <div class="form-step active"><span class="step-num">1</span> Datos Personales</div>
        <div class="form-step-divider"></div>
        <div class="form-step"><span class="step-num">2</span> Seguridad</div>
        <div class="form-step-divider"></div>
        <div class="form-step"><span class="step-num">3</span> Confirmar</div>
      </div>

      <form id="registroForm" method="POST" action="<?= BASE_URL ?>index.php?action=register" novalidate>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">

        <div class="section-title">Datos de usuario</div>

        <div class="field">
          <label for="nombre_completo">Nombre completo</label>
          <input type="text" id="nombre_completo" name="nombre_completo" placeholder="Nombre y apellido" required>
          <span class="err" id="nombreErr">Escribe tu nombre completo.</span>
        </div>

        <div class="row2">
          <div class="field">
            <label for="email">Correo electrónico</label>
            <input type="email" id="email" name="email" placeholder="tu@correo.com" required>
            <span class="err" id="emailErr">Ingresa un correo válido.</span>
          </div>
          <div class="field">
            <label for="telefono">Teléfono</label>
            <input type="tel" id="telefono" name="telefono" placeholder="300 000 0000" required>
            <span class="err" id="telefonoErr">Ingresa 10 dígitos.</span>
          </div>
        </div>

        <div class="row2">
          <div class="field">
            <label for="documento">Documento de identidad</label>
            <input type="text" id="documento" name="documento" placeholder="Cédula / T.I." required>
            <span class="err" id="documentoErr">Ingresa un documento válido.</span>
          </div>
          <div class="field">
            <label for="fecha_nacimiento">Fecha de nacimiento</label>
            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required>
            <span class="err" id="fechaNacimientoErr">Ingresa tu fecha de nacimiento.</span>
          </div>
        </div>

        <div class="section-title">Seguridad</div>

        <div class="row2">
          <div class="field">
            <label for="password_hash">Contraseña</label>
            <div class="password-wrap">
              <input type="password" id="password_hash" name="password_hash" placeholder="Mínimo 6 caracteres" required>
              <button type="button" class="password-toggle" aria-label="Mostrar contraseña"><i class="fa-solid fa-eye"></i></button>
            </div>
            <span class="err" id="passwordErr">Mínimo 6 caracteres.</span>
          </div>
          <div class="field">
            <label for="password2">Confirmar contraseña</label>
            <div class="password-wrap">
              <input type="password" id="password2" name="password2" placeholder="Repite la contraseña" required>
              <button type="button" class="password-toggle" aria-label="Mostrar contraseña"><i class="fa-solid fa-eye"></i></button>
            </div>
            <span class="err" id="password2Err">Las contraseñas no coinciden.</span>
          </div>
        </div>

        <button type="submit" class="submit-btn btn-submit-register">Crear mi cuenta</button>
        <p class="hint">¿Ya tienes cuenta? <a href="<?= BASE_URL ?>index.php?action=login">Inicia sesión</a></p>
      </form>
    </div>
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
