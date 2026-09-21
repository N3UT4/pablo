document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('bookingForm');
  const alertBox = document.getElementById('formAlert');
  const alertMsg = document.getElementById('alertMessage');

  // DOM Elements
  const artistOptions = document.getElementById('artistOptions');
  const tipoServicio = document.getElementById('tipoServicio');
  const sizeSelector = document.getElementById('sizeSelector');
  const fechaCita = document.getElementById('fechaCita');
  const horaCita = document.getElementById('horaCita');
  const montoInput = document.getElementById('montoInput');
  const payFullAmount = document.getElementById('payFullAmount');
  const nequiField = document.getElementById('nequiField');
  const comprobanteInput = document.getElementById('comprobanteInput');
  const customDesignField = document.getElementById('customDesignField');
  const detallePersonalizado = document.getElementById('detallePersonalizado');
  const submitBtn = document.getElementById('submitBtn');

  // Breakdown Elements
  const paymentBreakdown = document.getElementById('paymentBreakdown');
  const breakdownServiceName = document.getElementById('breakdownServiceName');
  const breakdownSizeName = document.getElementById('breakdownSizeName');
  const estimatedPrice = document.getElementById('estimatedPrice');
  const minimumDeposit = document.getElementById('minimumDeposit');
  const depositToPay = document.getElementById('depositToPay');
  const remainingBalance = document.getElementById('remainingBalance');
  const totalToday = document.getElementById('totalToday');
  const fullPaymentOption = document.getElementById('fullPaymentOption');
  const fullAmountLabel = document.getElementById('fullAmountLabel');

  // State
  let selectedService = null;
  let selectedSize = null;
  let selectedArtist = null;

  // Deposit amounts by size
  const DEPOSITS = {
    'pequeño': 20000,
    'mediano': 50000,
    'grande': 100000
  };

  const SIZE_LABELS = {
    'pequeño': 'Pequeño',
    'mediano': 'Mediano',
    'grande': 'Grande'
  };

  // Format COP currency
  function formatCOP(value) {
    return '$' + value.toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) + ' COP';
  }

  // Show alert
  function showAlert(message, isSuccess = false) {
    alertMsg.textContent = message;
    alertBox.classList.toggle('success', isSuccess);
    alertBox.classList.add('show');
    alertBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  }

  function hideAlert() {
    alertBox.classList.remove('show', 'success');
  }

  // Set invalid state
  function setInvalid(input, errId, isInvalid) {
    const errEl = document.getElementById(errId);
    if (input) input.classList.toggle('invalid', isInvalid);
    if (errEl) errEl.classList.toggle('show', isInvalid);
  }

  // Validate time range (08:00 - 21:00)
  function isTimeInRange(timeStr) {
    if (!timeStr) return false;
    const [h, m] = timeStr.split(':').map(Number);
    const minutes = h * 60 + m;
    return minutes >= 8 * 60 && minutes <= 21 * 60;
  }

  // Calculate minimum date (72 hours / 3 days from now)
  function getMinDate() {
    const now = new Date();
    const minDate = new Date(now.getTime() + 72 * 60 * 60 * 1000);
    const yyyy = minDate.getFullYear();
    const mm = String(minDate.getMonth() + 1).padStart(2, '0');
    const dd = String(minDate.getDate()).padStart(2, '0');
    return `${yyyy}-${mm}-${dd}`;
  }

  // Initialize date picker with 72h restriction
  function initDatePicker() {
    const minDateStr = getMinDate();
    fechaCita.setAttribute('min', minDateStr);

    // Also disable past dates in the calendar UI
    fechaCita.addEventListener('change', function () {
      const selected = this.value;
      if (selected < minDateStr) {
        setInvalid(this, 'fechaErr', true);
        this.value = '';
      } else {
        setInvalid(this, 'fechaErr', false);
      }
      updateBreakdown();
    });
  }

  // Artist selection handler
  function initArtistSelection() {
    if (!artistOptions) return;
    const radios = artistOptions.querySelectorAll('input[name="id_tatuador"]');
    radios.forEach(radio => {
      radio.addEventListener('change', function () {
        selectedArtist = this.value;
        document.querySelectorAll('.artist-option').forEach(opt => {
          opt.classList.toggle('selected', opt.querySelector('input').checked);
        });
        setInvalid(document.querySelector('input[name="id_tatuador"]'), 'artistErr', !selectedArtist);
      });
    });
    // Set initial
    const checked = artistOptions.querySelector('input[name="id_tatuador"]:checked');
    if (checked) selectedArtist = checked.value;
  }

  // Service selection handler
  function initServiceSelection() {
    if (!tipoServicio) return;
    tipoServicio.addEventListener('change', function () {
      const option = this.options[this.selectedIndex];
      if (option.value) {
        selectedService = {
          slug: option.value,
          nombre: option.text.split(' — ')[0],
          precio: parseFloat(option.dataset.precio) || 0
        };
      } else {
        selectedService = null;
      }
      updateBreakdown();
      setInvalid(this, 'tipoServicioErr', !selectedService);

      // Show/hide custom design field
      const isCustom = option.value === 'personalizado';
      customDesignField.style.display = isCustom ? 'block' : 'none';
      detallePersonalizado.required = isCustom;
    });
  }

  // Size selector handler (interactive cards)
  function initSizeSelector() {
    if (!sizeSelector) return;
    const options = sizeSelector.querySelectorAll('.size-option');
    options.forEach(option => {
      const radio = option.querySelector('input[type="radio"]');
      option.addEventListener('click', function (e) {
        if (e.target !== radio) radio.checked = true;
        selectedSize = radio.value;
        options.forEach(o => o.classList.toggle('selected', o.querySelector('input').checked));
        setInvalid(radio, 'tamanoErr', !selectedSize);
        updateBreakdown();
      });
      radio.addEventListener('change', function () {
        selectedSize = this.value;
        options.forEach(o => o.classList.toggle('selected', o.querySelector('input').checked));
        setInvalid(this, 'tamanoErr', !selectedSize);
        updateBreakdown();
      });
    });
  }

  // Time input handler
  function initTimeValidation() {
    if (!horaCita) return;
    horaCita.addEventListener('change', function () {
      const valid = isTimeInRange(this.value);
      // If same day as min date, check not in past
      if (valid && fechaCita.value === getMinDate()) {
        const now = new Date();
        const [h, m] = this.value.split(':').map(Number);
        const selectedTime = new Date();
        selectedTime.setHours(h, m, 0, 0);
        if (selectedTime < now) {
          setInvalid(this, 'horaErr', true);
          return;
        }
      }
      setInvalid(this, 'horaErr', !valid);
      updateBreakdown();
    });
  }

  // Payment method handler (show Nequi field)
  function initPaymentMethod() {
    const metodoRadios = document.querySelectorAll('input[name="metodo_pago"]');
    metodoRadios.forEach(radio => {
      radio.addEventListener('change', function () {
        nequiField.style.display = this.value === 'nequi' ? 'block' : 'none';
        comprobanteInput.required = this.value === 'nequi';
        setInvalid(comprobanteInput, 'comprobanteErr', this.value === 'nequi' && comprobanteInput.value.trim().length < 4);
        setInvalid(this, 'metodoErr', false);
      });
    });
  }

  // Full payment checkbox handler
  function initFullPaymentToggle() {
    if (!payFullAmount) return;
    payFullAmount.addEventListener('change', function () {
      updateBreakdown();
    });
  }

  // Nequi comprobante validation
  function initComprobanteValidation() {
    if (!comprobanteInput) return;
    comprobanteInput.addEventListener('input', function () {
      const metodo = document.querySelector('input[name="metodo_pago"]:checked');
      if (metodo && metodo.value === 'nequi') {
        setInvalid(this, 'comprobanteErr', this.value.trim().length < 4);
      }
    });
  }

  // Custom design validation
  function initCustomDesignValidation() {
    if (!detallePersonalizado) return;
    detallePersonalizado.addEventListener('input', function () {
      const tipo = tipoServicio.value;
      if (tipo === 'personalizado') {
        setInvalid(this, 'personalizadoErr', this.value.trim().length < 10);
      }
    });
  }

  // Update payment breakdown display
  function updateBreakdown() {
    if (!selectedService || !selectedSize) {
      paymentBreakdown.style.display = 'none';
      montoInput.value = '';
      return;
    }

    const precioTotal = selectedService.precio;
    const abonoMinimo = DEPOSITS[selectedSize];
    const isFullPayment = payFullAmount && payFullAmount.checked;

    let montoAPagar = isFullPayment ? precioTotal : abonoMinimo;
    let saldoPendiente = isFullPayment ? 0 : precioTotal - abonoMinimo;

    // Ensure monto doesn't exceed total
    if (montoAPagar > precioTotal) montoAPagar = precioTotal;
    if (saldoPendiente < 0) saldoPendiente = 0;

    // Update UI
    breakdownServiceName.textContent = selectedService.nombre;
    breakdownSizeName.textContent = SIZE_LABELS[selectedSize];
    estimatedPrice.textContent = formatCOP(precioTotal);
    minimumDeposit.textContent = formatCOP(abonoMinimo);
    depositToPay.textContent = formatCOP(montoAPagar);
    remainingBalance.textContent = formatCOP(saldoPendiente);
    totalToday.textContent = formatCOP(montoAPagar);
    fullAmountLabel.textContent = formatCOP(precioTotal);

    // Show/hide full payment option
    fullPaymentOption.style.display = abonoMinimo < precioTotal ? 'block' : 'none';

    // Set hidden monto input
    montoInput.value = montoAPagar;

    paymentBreakdown.style.display = 'block';
  }

  // Form validation before submit
  function validateForm() {
    let ok = true;

    // Artist
    const artistRadio = document.querySelector('input[name="id_tatuador"]:checked');
    setInvalid(artistRadio, 'artistErr', !artistRadio);
    if (!artistRadio) ok = false;

    // Service
    setInvalid(tipoServicio, 'tipoServicioErr', !selectedService);
    if (!selectedService) ok = false;

    // Custom design detail
    if (selectedService && selectedService.slug === 'personalizado') {
      const detalleValid = detallePersonalizado.value.trim().length >= 10;
      setInvalid(detallePersonalizado, 'personalizadoErr', !detalleValid);
      if (!detalleValid) ok = false;
    }

    // Size
    setInvalid(document.querySelector('input[name="tamano"]:checked'), 'tamanoErr', !selectedSize);
    if (!selectedSize) ok = false;

    // Date
    const minDate = getMinDate();
    const fechaValid = fechaCita.value && fechaCita.value >= minDate;
    setInvalid(fechaCita, 'fechaErr', !fechaValid);
    if (!fechaValid) ok = false;

    // Time
    const horaValid = isTimeInRange(horaCita.value);
    if (horaValid && fechaCita.value === minDate) {
      const now = new Date();
      const [h, m] = horaCita.value.split(':').map(Number);
      const selectedTime = new Date();
      selectedTime.setHours(h, m, 0, 0);
      if (selectedTime < now) {
        setInvalid(horaCita, 'horaErr', true);
        ok = false;
      }
    } else {
      setInvalid(horaCita, 'horaErr', !horaValid);
    }
    if (!horaValid) ok = false;

    // Monto
    const montoValid = parseFloat(montoInput.value) > 0;
    setInvalid(montoInput, 'montoErr', !montoValid);
    if (!montoValid) ok = false;

    // Payment method
    const metodoSeleccionado = document.querySelector('input[name="metodo_pago"]:checked');
    setInvalid(metodoSeleccionado, 'metodoErr', !metodoSeleccionado);
    if (!metodoSeleccionado) ok = false;

    // Nequi comprobante
    if (metodoSeleccionado && metodoSeleccionado.value === 'nequi') {
      const compValid = comprobanteInput.value.trim().length >= 4;
      setInvalid(comprobanteInput, 'comprobanteErr', !compValid);
      if (!compValid) ok = false;
    }

    return ok;
  }

  // Submit handler
  form.addEventListener('submit', async function (e) {
    e.preventDefault();
    hideAlert();

    if (!validateForm()) {
      showAlert('Revisa los campos marcados en rojo antes de continuar.');
      return;
    }

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Procesando...';

    try {
      const formData = new FormData(form);
      const result = await ItzaAPI.postForm('book-appointment', formData);

      if (!result.ok) {
        if (result.redirect) {
          const redirectUrl = /^https?:\/\//i.test(result.redirect) || result.redirect.startsWith('/')
            ? result.redirect
            : window.APP_BASE_URL + result.redirect.replace(/^\.?\//, '');
          showAlert(result.message || 'Error al agendar la cita.');
          setTimeout(() => { window.location.href = redirectUrl; }, 2500);
        } else {
          showAlert(result.message || 'No fue posible agendar la cita.');
        }
        return;
      }

      showAlert(result.message || '¡Tu cita quedó agendada y tu abono registrado! Te esperamos.', true);

      setTimeout(() => {
        form.reset();
        selectedService = null;
        selectedSize = null;
        selectedArtist = null;
        paymentBreakdown.style.display = 'none';
        nequiField.style.display = 'none';
        customDesignField.style.display = 'none';
        comprobanteInput.required = false;
        detallePersonalizado.required = false;

        // Reset visual states
        document.querySelectorAll('.artist-option, .size-option').forEach(el => el.classList.remove('selected'));
        const firstArtist = artistOptions?.querySelector('input[name="id_tatuador"]');
        const firstSize = sizeSelector?.querySelector('input[name="tamano"]');
        if (firstArtist) firstArtist.checked = true;
        if (firstSize) firstSize.checked = true;
        if (firstArtist) selectedArtist = firstArtist.value;
        if (firstSize) selectedSize = firstSize.value;

        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fa-solid fa-calendar-plus"></i> Confirmar Cita y Abono';
      }, 2000);

    } catch (err) {
      console.error('Booking error:', err);
      showAlert('Error de conexión. Verifica que el servidor esté activo.');
    } finally {
      if (submitBtn.disabled) {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fa-solid fa-calendar-plus"></i> Confirmar Cita y Abono';
      }
    }
  });

  // Clear invalid on input
  document.querySelectorAll('input, select, textarea').forEach(inp => {
    inp.addEventListener('input', () => inp.classList.remove('invalid'));
    inp.addEventListener('change', () => inp.classList.remove('invalid'));
  });

  // Initialize all
  initDatePicker();
  initArtistSelection();
  initServiceSelection();
  initSizeSelector();
  initTimeValidation();
  initPaymentMethod();
  initFullPaymentToggle();
  initComprobanteValidation();
  initCustomDesignValidation();

  // Initial breakdown update if values pre-selected
  if (tipoServicio.value) {
    const option = tipoServicio.options[tipoServicio.selectedIndex];
    selectedService = {
      slug: option.value,
      nombre: option.text.split(' — ')[0],
      precio: parseFloat(option.dataset.precio) || 0
    };
  }
  const checkedSize = document.querySelector('input[name="tamano"]:checked');
  if (checkedSize) selectedSize = checkedSize.value;
  updateBreakdown();
});

// SweetAlert-like helpers (if not already defined)
window.itzaError = window.itzaError || function (msg) {
  return Promise.resolve(alert(msg));
};
window.itzaSuccess = window.itzaSuccess || function (msg) {
  return Promise.resolve(alert(msg));
};