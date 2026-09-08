// Configuración compartida de SweetAlert2 para ITZA TATTOO STUDIO
function itzaAlert(opts) {
  const translate = typeof translateText === 'function' ? translateText : (value => value);
  return Swal.fire(Object.assign({
    background: '#161616',
    color: '#F7F3EC',
    confirmButtonColor: '#D4123A',
    confirmButtonText: translate('Entendido'),
    customClass: { popup: 'itza-swal' }
  }, opts));
}
function itzaError(msg) {
  return itzaAlert({ icon: 'error', title: translateText('Revisa el formulario'), text: translateText(msg) });
}
function itzaSuccess(msg, title) {
  return itzaAlert({ icon: 'success', title: translateText(title || '¡Listo!'), text: translateText(msg), confirmButtonColor: '#2E7D46' });
}
