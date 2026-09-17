<div class="container container-narrow">
  <div class="card">
    <div class="card-head">
      <span class="eyebrow">Seguridad</span>
      <h1>Cambiar clave temporal</h1>
      <p>Has iniciado sesión con una clave temporal. Debes cambiarla antes de continuar.</p>
    </div>

    <form id="cambiarClaveForm" method="POST" action="<?= BASE_URL ?>index.php?action=cambiar-clave" novalidate>
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">

      <div class="field">
        <label for="nueva_clave">Nueva contraseña</label>
        <div class="password-wrap">
          <input type="password" id="nueva_clave" name="nueva_clave" placeholder="Mínimo 6 caracteres" required>
          <button type="button" class="password-toggle" aria-label="Mostrar contraseña"><i class="fa-solid fa-eye"></i></button>
        </div>
        <span class="err" id="nuevaClaveErr">Mínimo 6 caracteres.</span>
      </div>

      <div class="field">
        <label for="nueva_clave2">Confirmar contraseña</label>
        <div class="password-wrap">
          <input type="password" id="nueva_clave2" name="nueva_clave2" placeholder="Repite la contraseña" required>
          <button type="button" class="password-toggle" aria-label="Mostrar contraseña"><i class="fa-solid fa-eye"></i></button>
        </div>
        <span class="err" id="nuevaClave2Err">Las contraseñas no coinciden.</span>
      </div>

      <button type="submit" class="submit-btn">Cambiar contraseña</button>
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