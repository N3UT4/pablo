<?php
require_once dirname(__DIR__) . '/config/config.php';
// Recurso JavaScript servido por PHP.
header('Content-Type: application/javascript; charset=utf-8');
?>
window.APP_BASE_URL = <?= json_encode(BASE_URL) ?>;

// Helper compartido: obtiene el csrf_token de la sesión PHP y envía los formularios
// de las páginas estáticas (galeria.html, abono.html, contacto.html, consentimiento.html,
// promociones.html) al backend en index.php.
//
// Requiere que el sitio corra bajo PHP (XAMPP, InfinityFree, etc.) sirviendo index.php
// en la misma carpeta que estos .html. Si abres el .html directamente con file:// o un
// servidor sin PHP, estas llamadas fallarán: eso es normal, monta el proyecto en un
// servidor con PHP + MySQL configurados (ver database.sql y config/config.php).

const ItzaAPI = (function () {
  let cachedToken = null;

  async function getCsrfToken() {
    if (cachedToken) return cachedToken;
    const res = await fetch(window.APP_BASE_URL + 'index.php?action=csrf-token', { credentials: 'same-origin' });
    const data = await res.json();
    cachedToken = data.csrf_token;
    return cachedToken;
  }

  // Envía un <form> (o un FormData ya armado) por POST a index.php?action=...
  // Devuelve siempre { ok, message, ...extra } tal como lo manda el backend.
  async function postForm(action, formOrFormData) {
    const token = await getCsrfToken();
    const formData = formOrFormData instanceof FormData
      ? formOrFormData
      : new FormData(formOrFormData);
    formData.set('csrf_token', token);

    try {
      const res = await fetch(window.APP_BASE_URL + 'index.php?action=' + encodeURIComponent(action), {
        method: 'POST',
        credentials: 'same-origin',
        body: formData,
      });
      const data = await res.json();
      return data;
    } catch (err) {
      return { ok: false, message: 'No se pudo conectar con el servidor. Verifica que el proyecto esté corriendo en PHP.' };
    }
  }

  return { getCsrfToken, postForm };
})();
