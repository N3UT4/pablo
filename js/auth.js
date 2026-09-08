document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('loginForm');
  if (!form) return;

  const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  function setInvalid(input, errEl, isInvalid) {
    input.classList.toggle('invalid', isInvalid);
    if (errEl) errEl.classList.toggle('show', isInvalid);
  }

  form.addEventListener('submit', function (e) {
    const email = document.getElementById('loginEmail');
    const pass = document.getElementById('loginPass');
    let ok = true;

    const emailValid = emailRe.test(email.value.trim());
    setInvalid(email, document.getElementById('loginEmailErr'), !emailValid);
    if (!emailValid) ok = false;

    const passValid = pass.value.length >= 6;
    setInvalid(pass, document.getElementById('loginPassErr'), !passValid);
    if (!passValid) ok = false;

    if (!ok) {
      e.preventDefault();
      itzaError('Revisa los campos marcados en rojo antes de continuar.');
      return;
    }

    localStorage.setItem('itza_session', JSON.stringify({ email: email.value.trim(), loggedAt: Date.now() }));
  });

  document.querySelectorAll('input').forEach(inp => {
    inp.addEventListener('input', () => inp.classList.remove('invalid'));
  });
});
