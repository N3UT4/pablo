document.addEventListener('DOMContentLoaded', function () {
  const session = localStorage.getItem('itza_session');
  const promos = document.getElementById('promos');
  const navCta = document.querySelector('.nav-cta');

  if (session) {
    // Cliente con sesión iniciada: mostrar contenido exclusivo
    if (promos) promos.style.display = 'block';

    if (navCta) {
      navCta.textContent = 'Cerrar sesión';
      navCta.setAttribute('href', '#');
      navCta.addEventListener('click', function (e) {
        e.preventDefault();
        localStorage.removeItem('itza_session');
        window.location.reload();
      });
    }
  }
});
