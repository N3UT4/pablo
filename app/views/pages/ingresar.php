<div class="container container-narrow">
  <div style="margin-bottom:16px;">
    <a href="javascript:history.back()" class="btn-outline btn-sm" style="text-decoration:none;"><i class="fa-solid fa-arrow-left"></i> Regresar</a>
  </div>
  <div class="card">
    <div class="card-head">
      <span class="eyebrow">Bienvenido de nuevo</span>
      <h1>Iniciar sesión</h1>
      <p>Ingresa con tu correo y contraseña para gestionar tus citas y ver promociones exclusivas.</p>
    </div>

    <form id="loginForm" method="POST" action="<?= BASE_URL ?>index.php?action=login" novalidate>
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
      <div class="field">
        <label for="loginEmail">Correo electrónico</label>
        <input type="email" id="loginEmail" name="loginEmail" placeholder="tu@correo.com" required autocomplete="email">
        <span class="err" id="loginEmailErr">Ingresa un correo válido.</span>
      </div>
      <div class="field">
        <label for="loginPass">Contraseña</label>
        <div class="password-wrap">
          <input type="password" id="loginPass" name="loginPass" placeholder="••••••••" required>
          <button type="button" class="password-toggle" aria-label="Mostrar contraseña"><i class="fa-solid fa-eye"></i></button>
        </div>
        <span class="err" id="loginPassErr">Mínimo 6 caracteres.</span>
      </div>
      <button type="submit" class="submit-btn">Entrar</button>
      <p class="hint">¿No tienes cuenta? <a href="<?= BASE_URL ?>index.php?action=register">Regístrate aquí</a></p>
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
