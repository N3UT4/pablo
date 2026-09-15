<div class="container container-narrow">
  <div class="card">
    <div class="card-head">
      <span class="eyebrow">Bienvenido de nuevo</span>
      <h1>Iniciar sesión</h1>
      <p>Ingresa con tu correo y contraseña para gestionar tus citas y ver promociones exclusivas.</p>
    </div>

    <form id="loginForm" method="POST" action="index.php?action=login" novalidate>
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
      <div class="field">
        <label for="loginEmail">Correo electrónico</label>
        <input type="email" id="loginEmail" name="loginEmail" placeholder="tu@correo.com" required autocomplete="email">
      </div>
      <div class="field">
        <label for="loginPass">Contraseña</label>
        <input type="password" id="loginPass" name="loginPass" placeholder="••••••••" required>
      </div>
      <button type="submit" class="submit-btn">Entrar</button>
      <p class="hint">¿No tienes cuenta? <a href="index.php?action=register">Regístrate aquí</a></p>
    </form>
  </div>
</div>
