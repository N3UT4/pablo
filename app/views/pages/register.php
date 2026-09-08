<div class="container">
  <div class="card">
    <div class="card-head">
      <span class="eyebrow">Paso 1</span>
      <h1>Crear cuenta de cliente</h1>
      <p>Regístrate para poder agendar tu cita, hacer seguimiento de tus pagos y firmar tu consentimiento digital.</p>
    </div>

    <form method="POST" action="index.php?action=register" novalidate>
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
      <div class="section-title">Datos de usuario</div>

      <div class="field">
        <label for="nombre_completo">Nombre completo</label>
        <input type="text" id="nombre_completo" name="nombre_completo" placeholder="Nombre y apellido" required>
      </div>

      <div class="row2">
        <div class="field">
          <label for="email">Correo electrónico</label>
          <input type="email" id="email" name="email" placeholder="tu@correo.com" required>
        </div>
        <div class="field">
          <label for="telefono">Teléfono</label>
          <input type="tel" id="telefono" name="telefono" placeholder="300 000 0000" required>
        </div>
      </div>

      <div class="row2">
        <div class="field">
          <label for="documento">Documento de identidad</label>
          <input type="text" id="documento" name="documento" placeholder="Cédula / T.I." required>
        </div>
        <div class="field">
          <label for="fecha_nacimiento">Fecha de nacimiento</label>
          <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required>
        </div>
      </div>

      <div class="section-title">Seguridad</div>

      <div class="row2">
        <div class="field">
          <label for="password_hash">Contraseña</label>
          <input type="password" id="password_hash" name="password_hash" placeholder="Mínimo 6 caracteres" required>
        </div>
        <div class="field">
          <label for="password2">Confirmar contraseña</label>
          <input type="password" id="password2" name="password2" placeholder="Repite la contraseña" required>
        </div>
      </div>

      <button type="submit" class="submit-btn">Crear mi cuenta</button>
      <p class="hint">¿Ya tienes cuenta? <a href="index.php?action=login">Inicia sesión</a></p>
    </form>
  </div>
</div>
