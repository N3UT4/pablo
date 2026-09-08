// Sistema de internacionalización (i18n) para ITZA TATTOO STUDIO
// Soporta: Español (es) e Inglés (en)

class I18n {
  constructor() {
    this.translations = {};
    this.currentLang = localStorage.getItem('itza_lang') || 'es';
    this.defaultLang = 'es';
    this.loadedLangs = new Set();
  }

  // Cargar traducciones desde JSON
  async loadTranslations() {
    if (this.loadedLangs.has(this.currentLang)) return;
    
    try {
      const response = await fetch('lang/translations.json');
      const data = await response.json();
      this.translations = data;
      this.loadedLangs.add(this.currentLang);
    } catch (error) {
      console.error('Error cargando traducciones:', error);
      // El contenido estático puede traducirse con el catálogo local incluso
      // cuando el navegador bloquea fetch (por ejemplo, al abrir index.html).
      this.translations = {};
    }
  }

  // Obtener un valor traducido usando notación de punto
  t(key, defaultValue = '') {
    const keys = key.split('.');
    let value = this.translations[this.currentLang];

    for (let k of keys) {
      if (value && typeof value === 'object') {
        value = value[k];
      } else {
        return defaultValue || key;
      }
    }

    return value || defaultValue || key;
  }

  // Cambiar idioma
  setLanguage(lang) {
    if (lang === 'es' || lang === 'en') {
      this.currentLang = lang;
      localStorage.setItem('itza_lang', lang);
      this.loadedLangs.add(lang);
      return true;
    }
    return false;
  }

  // Obtener idioma actual
  getCurrentLanguage() {
    return this.currentLang;
  }

  // Obtener todos los idiomas disponibles
  getAvailableLanguages() {
    return ['es', 'en'];
  }
}

// Instancia global
const i18n = new I18n();

// Función para traducir elementos del DOM
async function translatePage() {
  await i18n.loadTranslations();

  // Traducir todos los elementos con data-i18n
  document.querySelectorAll('[data-i18n]').forEach(element => {
    const key = element.getAttribute('data-i18n');
    const translation = i18n.t(key);
    
    if (translation === key) return;
    if (element.tagName === 'INPUT' && element.type === 'placeholder') {
      element.placeholder = translation;
    } else if (element.placeholder !== undefined && element.getAttribute('data-i18n-placeholder')) {
      element.placeholder = translation;
    } else {
      element.textContent = translation;
    }
  });

  // Traducir placeholders
  document.querySelectorAll('[data-i18n-placeholder]').forEach(element => {
    const key = element.getAttribute('data-i18n-placeholder');
    const translation = i18n.t(key);
    if (translation !== key) element.placeholder = translation;
  });

  // Traducir atributos title
  document.querySelectorAll('[data-i18n-title]').forEach(element => {
    const key = element.getAttribute('data-i18n-title');
    element.title = i18n.t(key);
  });

  // Traducir atributos aria-label
  document.querySelectorAll('[data-i18n-aria]').forEach(element => {
    const key = element.getAttribute('data-i18n-aria');
    element.setAttribute('aria-label', i18n.t(key));
  });

  translateSourceContent();
  document.documentElement.lang = i18n.getCurrentLanguage();
  document.__i18nTitle ||= document.title;
  document.title = translateText(document.__i18nTitle);
}

// Traducciones para el contenido estático que aún no usa atributos data-i18n.
// Cada nodo conserva su texto original, por lo que se puede alternar de idioma sin recargar.
const englishText = {
  'ITZA TATTOO STUDIO — Bogotá':'ITZA TATTOO STUDIO — Bogotá','Ingresar — ITZA TATTOO STUDIO':'Sign in — ITZA TATTOO STUDIO','Crear cuenta — ITZA TATTOO STUDIO':'Create account — ITZA TATTOO STUDIO','Contacto — ITZA TATTOO STUDIO':'Contact — ITZA TATTOO STUDIO','Mi perfil — ITZA TATTOO STUDIO':'My profile — ITZA TATTOO STUDIO','Promociones — ITZA TATTOO STUDIO':'Promotions — ITZA TATTOO STUDIO','Galería — ITZA TATTOO STUDIO':'Gallery — ITZA TATTOO STUDIO','Agendar y abonar — ITZA TATTOO STUDIO':'Book & pay — ITZA TATTOO STUDIO','Consentimiento informado — ITZA TATTOO STUDIO':'Informed consent — ITZA TATTOO STUDIO','Mi Dashboard — ITZA TATTOO STUDIO':'My dashboard — ITZA TATTOO STUDIO','404 — Página no encontrada | ITZA TATTOO STUDIO':'404 — Page not found | ITZA TATTOO STUDIO','500 — Error del servidor | ITZA TATTOO STUDIO':'500 — Server error | ITZA TATTOO STUDIO',
  'Servicios':'Services','Cómo funciona':'How it works','Artistas':'Artists','Estudio de tatuajes · Bogotá':'Tattoo studio · Bogotá','Tinta con':'Ink with','propósito':'purpose','citas sin fricción':'effortless appointments','Agenda tu cita, paga por QR con Nequi y firma tu consentimiento informado desde el celular. Todo en un solo lugar, sin filas ni papeleo.':'Book your appointment, pay by Nequi QR and sign your informed consent from your phone. Everything in one place, with no lines or paperwork.','Reservar mi cita':'Book my appointment','Ver servicios':'View services','Pago':'Payment','Consentimiento':'Consent','Firma digital':'Digital signature','Agenda':'Scheduling','24/7 online':'Online 24/7','Confirmación':'Confirmation','Al instante':'Instantly','Lo que hacemos':'What we do','Cada estilo tiene su técnica. Elige el que se ajuste a tu idea.':'Every style has its technique. Choose the one that fits your idea.','Trazos sólidos, alto contraste, diseño geométrico y tribal.':'Solid strokes, high contrast, geometric and tribal design.','Retratos y escenas con sombreado fino y detalle fotográfico.':'Portraits and scenes with fine shading and photographic detail.','Líneas delicadas para diseños minimalistas y delicados.':'Delicate lines for minimalist, refined designs.','Piezas vibrantes con paletas personalizadas por artista.':'Vibrant pieces with palettes customized by each artist.','Rediseño y cobertura de tatuajes antiguos.':'Redesign and cover-up of old tattoos.','Perforaciones con material estéril certificado.':'Piercings with certified sterile materials.','Nuevo':'New','Trae tu idea y la convertimos en un diseño único contigo, junto al artista.':'Bring your idea and we will turn it into a unique design with you and the artist.','Proceso':'Process','Del boceto a la aguja, en cuatro pasos.':'From sketch to needle in four steps.','1. Agenda':'1. Book','Elige artista, estilo y horario disponible desde la plataforma.':'Choose an artist, style and available time on the platform.','2. Paga el abono':'2. Pay the deposit','Confirma tu cita con un pago QR vía Nequi, seguro y rápido.':'Confirm your appointment with a quick, secure Nequi QR payment.','3. Firma digital':'3. Digital signature','Completa el consentimiento informado desde tu celular.':'Complete the informed consent from your phone.','4. Llega y tatúate':'4. Arrive and get tattooed','Preséntate a tu hora reservada, todo ya está listo.':'Arrive at your reserved time; everything will be ready.','Flash sheet':'Flash sheet','Diseños disponibles para agendar directamente, sin espera de boceto personalizado.':'Designs available to book directly, with no wait for a custom sketch.','Rosa clásica':'Classic rose','Daga tradicional':'Traditional dagger','Calavera':'Skull','Mandala':'Mandala','Serpiente':'Snake','Máquina old school':'Old-school machine','Exclusivo para ti':'Exclusive for you','Promociones activas':'Active promotions','Disponibles solo para clientes con sesión iniciada.':'Available only to signed-in customers.','Martes de Fine Line':'Fine Line Tuesdays','Todos los martes, diseños fine line con 20% de descuento en el valor total.':'Every Tuesday, fine-line designs are 20% off the total price.','Piercing doble':'Double piercing','Llévate un segundo piercing gratis al agendar tu primera perforación del mes.':'Get a second piercing free when you book your first piercing of the month.','Retoque de color':'Color touch-up','Retoque gratuito dentro de los primeros 30 días para tatuajes a color.':'Free touch-up within the first 30 days for color tattoos.','El equipo':'The team','Nuestros artistas':'Our artists','Cada uno con su propio estilo y especialidad.':'Each with their own style and specialty.','¿Listo para tu próxima pieza?':'Ready for your next piece?','Crea tu cuenta y agenda en menos de 3 minutos.':'Create your account and book in under 3 minutes.','Crear cuenta gratis':'Create a free account','Acceso staff':'Staff access',
  'Inicio':'Home','Registro':'Register','Ingresar':'Sign In','Salir':'Sign Out','Agendar y abonar':'Book & Pay','Agendar cita y abonar':'Book appointment & pay','Agendar cita':'Book appointment','Contacto':'Contact','Mi perfil':'My profile','Promociones':'Promotions','Galería':'Gallery',
  'Paso 1':'Step 1','Paso 4':'Step 4','Paso final':'Final step','Área personal':'Personal area','Gestión de contenido':'Content management','Acceso restringido':'Restricted access','Solo personal autorizado':'Authorized staff only','Área de staff · Admin / Tatuador':'Staff area · Admin / Tattoo artist',
  'Crear cuenta de cliente':'Create customer account','Iniciar sesión':'Sign in','Editar perfil':'Edit profile','Contáctanos':'Contact us','Canjear cupón':'Redeem coupon','Subir a galería':'Upload to gallery','Consentimiento informado':'Informed consent','Bienvenido de nuevo':'Welcome back','¿Preguntas?':'Questions?',
  'Regístrate para poder agendar tu cita, hacer seguimiento de tus pagos y firmar tu consentimiento digital.':'Register to book your appointment, track your payments and sign your digital consent.','Ingresa con tu correo y contraseña para gestionar tus citas y ver promociones exclusivas.':'Sign in with your email and password to manage appointments and view exclusive promotions.','Actualiza tu información de contacto y datos personales.':'Update your contact information and personal details.','Cuéntanos en qué podemos ayudarte. Nos pondremos en contacto a la brevedad.':'Tell us how we can help. We will get in touch shortly.','Ingresa un código de promoción para obtener descuentos en tu próxima cita.':'Enter a promo code to get discounts on your next appointment.','Elige tu tatuador, estilo, fecha y horario, y confirma tu cita con un abono.':'Choose your artist, style, date and time, then confirm your appointment with a deposit.','Agrega fotos de trabajos realizados a la galería del estudio.':'Add photos of completed work to the studio gallery.',
  'Esta sección es exclusiva para administradores y tatuadores. Ingresa el código de acceso para continuar.':'This section is only for administrators and tattoo artists. Enter the access code to continue.','Esta sección es exclusiva para administradores y tatuadores del estudio. Ingresa el código de acceso para continuar.':'This section is only for studio administrators and tattoo artists. Enter the access code to continue.','Antes de tatuarte debes leer, entender y firmar este consentimiento. Si eres menor de edad, tu acudiente debe completar y firmar también.':'Before getting tattooed, you must read, understand and sign this consent. If you are underage, your guardian must also complete and sign it.',
  'Datos de usuario':'User details','Datos de cliente':'Customer details','Datos personales':'Personal details','Tu información':'Your information','Tu consulta':'Your inquiry','Detalles de la cita':'Appointment details','Fecha y hora':'Date and time','Abono':'Deposit','Dirección':'Address','Preferencias':'Preferences','Cambiar contraseña':'Change password','Detalles del trabajo':'Work details','Subir fotos':'Upload photos','Vista previa':'Preview','Procedimiento':'Procedure','Autorización de acudiente':'Guardian authorization',
  'Nombre completo':'Full name','Correo electrónico':'Email address','Teléfono':'Phone number','Documento de identidad':'ID document','Fecha de nacimiento':'Date of birth','Contraseña':'Password','Confirmar contraseña':'Confirm password','Ciudad':'City','Barrio':'Neighborhood','Código postal':'Postal code','Contraseña actual':'Current password','Nueva contraseña':'New password','Asunto':'Subject','Mensaje':'Message','Código de cupón':'Coupon code','Fecha planeada para usar el cupón':'Planned coupon date','Notas':'Notes','Tatuador':'Tattoo artist','Estilo':'Style','Estilo de tatuaje':'Tattoo style','Fecha':'Date','Hora':'Time','Monto del abono (COP)':'Deposit amount (COP)','Observaciones':'Notes','Comprobante / referencia':'Receipt / reference','Descripción':'Description','Título del trabajo':'Work title','Descripción del procedimiento':'Procedure description','Nombre del acudiente':'Guardian name','Documento del acudiente':'Guardian ID','Parentesco con el menor':'Relationship to the minor','Firma digital del cliente':'Customer digital signature','Firma digital del acudiente':'Guardian digital signature',
  'Selecciona un tatuador':'Select an artist','Selecciona un estilo':'Select a style','Selecciona un asunto':'Select a subject','Selecciona una opción':'Select an option','Diseño personalizado':'Custom design','Cuéntanos tu idea de diseño personalizado':'Tell us about your custom design idea','Escanea con la app Nequi':'Scan with the Nequi app','Transferencia':'Bank transfer','Efectivo':'Cash','Tarjeta':'Card','Ingresar código de staff':'Enter staff code','¿Eres cliente?':'Are you a customer?','Vuelve al inicio':'Back to home','Crear mi cuenta':'Create my account','Enviar consulta':'Send inquiry','Guardar cambios':'Save changes','Validar cupón':'Validate coupon','Aplicar a tu cita':'Apply to your appointment','Registrar cupón en mi cuenta':'Save coupon to my account','Firmar y enviar consentimiento':'Sign and send consent','Selecciona al menos una foto.':'Select at least one photo.',
  'Arrastra fotos aquí o haz clic para seleccionar':'Drag photos here or click to select','Formatos: JPG, PNG · Máx 5MB por foto':'Formats: JPG, PNG · Max. 5MB per photo','Autorizo la publicación de estas fotos en la galería y redes sociales de ITZA TATTOO.':'I authorize publication of these photos in the ITZA TATTOO gallery and social media.','Deseo recibir promociones, ofertas y noticias de ITZA TATTOO por correo y WhatsApp.':'I want to receive ITZA TATTOO promotions, offers and news by email and WhatsApp.','Acepto que ITZA TATTOO se comunique conmigo por correo, WhatsApp o teléfono para responder mi consulta.':'I agree that ITZA TATTOO may contact me by email, WhatsApp or phone to answer my inquiry.','Acepto que el cupón solo es válido una vez y no es transferible.':'I accept that the coupon is valid once only and is non-transferable.','El cliente es menor de edad. Un acudiente debe completar y firmar esta sección para autorizar el procedimiento.':'The customer is underage. A guardian must complete and sign this section to authorize the procedure.','Tus datos están protegidos con encriptación de seguridad.':'Your data is protected with security encryption.','Las fotos serán revisadas antes de publicarse en el sitio.':'Photos will be reviewed before being published on the site.','Responderemos en máximo 24 horas hábiles.':'We will respond within 24 business hours.','Este documento queda asociado a tu historial de cliente en el estudio.':'This document will be added to your customer history at the studio.',
  '¿Ya tienes cuenta?':'Already have an account?','Inicia sesión':'Sign in','Regístrate aquí':'Register here','(opcional)':'(optional)','(solo si deseas cambiarla)':'(only if you want to change it)','(escribe tu nombre completo)':'(type your full name)',
  'Esta página se perdió en el proceso':'This page got lost in the process','Puede que el enlace esté roto, la página haya cambiado de lugar o nunca haya existido. Pasa que hasta el mejor tatuador se sale de la línea a veces.':'The link may be broken, the page may have moved, or it may never have existed. Even the best tattoo artist goes outside the line sometimes.','Volver al inicio':'Back to home','Agendar una cita':'Book an appointment','Algo falló de nuestro lado':'Something went wrong on our end','No fue nada que hiciste. Nuestro equipo técnico ya fue notificado y está revisando la máquina. Intenta de nuevo en unos minutos.':'It was not anything you did. Our technical team has been notified and is checking the machine. Please try again in a few minutes.','Reintentar':'Try again','¿Sigue fallando?':'Still not working?','Escríbenos a soporte':'Contact support',
  'Revisa los campos marcados en rojo antes de continuar.':'Review the fields marked in red before continuing.','Hay campos incompletos o inválidos. Corrígelos para continuar.':'Some fields are incomplete or invalid. Please correct them to continue.','Tu cita quedó agendada y tu abono registrado. ¡Te esperamos!':'Your appointment and deposit have been registered. We look forward to seeing you!','Inicio de sesión exitoso. Redirigiendo a tu área personal...':'Sign-in successful. Redirecting to your personal area...','¡Tu consulta fue enviada! Nos pondremos en contacto pronto.':'Your inquiry was sent! We will be in touch soon.','Consulta recibida':'Inquiry received','Cuenta creada con éxito. Ya puedes agendar tu cita.':'Account created successfully. You can now book your appointment.','Tu perfil ha sido actualizado correctamente.':'Your profile has been updated successfully.','Cambios guardados':'Changes saved','¡Fotos subidas! Serán revisadas y publicadas en breve.':'Photos uploaded! They will be reviewed and published shortly.','Galería actualizada':'Gallery updated','Completa todos los campos requeridos del consentimiento antes de firmar.':'Complete all required consent fields before signing.','Consentimiento informado registrado y firmado con éxito.':'Informed consent registered and signed successfully.','Acceso concedido. Bienvenido(a) al panel de staff.':'Access granted. Welcome to the staff panel.','Código incorrecto. Este contenido es solo para staff autorizado.':'Incorrect code. This content is for authorized staff only.','Código de acceso':'Access code','Código de staff':'Staff code','Cancelar':'Cancel','Validar':'Validate','¡Listo!':'Done!','Revisa el formulario':'Check the form','Entendido':'Got it'
};

function translateText(value) {
  return i18n.getCurrentLanguage() === 'en' ? (englishText[value] || value) : value;
}

function translateSourceContent() {
  const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT, { acceptNode(node) {
    return node.parentElement && !['SCRIPT', 'STYLE'].includes(node.parentElement.tagName) && node.nodeValue.trim() ? NodeFilter.FILTER_ACCEPT : NodeFilter.FILTER_REJECT;
  }});
  const nodes = [];
  while (walker.nextNode()) nodes.push(walker.currentNode);
  nodes.forEach(node => {
    const source = node.__i18nSource || node.nodeValue;
    node.__i18nSource = source;
    const text = source.trim();
    node.nodeValue = source.match(/^\s*/)[0] + translateText(text) + source.match(/\s*$/)[0];
  });
  document.querySelectorAll('[placeholder], [title], [alt], [aria-label]').forEach(element => ['placeholder', 'title', 'alt', 'aria-label'].forEach(attribute => {
    if (!element.hasAttribute(attribute)) return;
    element.__i18nAttributes ||= {};
    const source = element.__i18nAttributes[attribute] || element.getAttribute(attribute);
    element.__i18nAttributes[attribute] = source;
    element.setAttribute(attribute, translateText(source));
  }));
}

// Crear selector de idioma
function createLanguageSwitcher() {
  const switcher = document.querySelector('.lang-switcher');
  
  // Si no existe el elemento, crear uno automáticamente en el topbar
  if (!switcher) {
    const topbar = document.querySelector('.topbar-inner') || document.querySelector('.error-wrap');
    if (topbar) {
      const newSwitcher = document.createElement('div');
      newSwitcher.className = 'lang-switcher';
      topbar.appendChild(newSwitcher);
    } else {
      console.warn('No se encontró .topbar-inner para crear selector de idioma');
      return;
    }
  }

  const currentLang = i18n.getCurrentLanguage();
  const langs = i18n.getAvailableLanguages();
  const switcherElement = document.querySelector('.lang-switcher');

  switcherElement.innerHTML = langs.map(lang => `
    <button 
      class="lang-btn ${lang === currentLang ? 'active' : ''}" 
      data-lang="${lang}"
      title="${lang === 'es' ? 'Español' : 'English'}"
      type="button"
    >
      ${lang.toUpperCase()}
    </button>
  `).join('');

  // Event listeners para cambiar idioma
  switcherElement.querySelectorAll('.lang-btn').forEach(btn => {
    btn.addEventListener('click', async (e) => {
      e.preventDefault();
      e.stopPropagation();
      const lang = e.currentTarget.getAttribute('data-lang');
      console.log('🌍 Cambiar idioma a:', lang);
      i18n.setLanguage(lang);
      await translatePage();
      
      // Actualizar botones activos
      switcherElement.querySelectorAll('.lang-btn').forEach(b => {
        if (b.getAttribute('data-lang') === lang) {
          b.classList.add('active');
        } else {
          b.classList.remove('active');
        }
      });

      // Event para que otros scripts sepan que el idioma cambió
      window.dispatchEvent(new CustomEvent('languageChanged', { detail: { lang } }));
      console.log('✅ Idioma cambiado correctamente a:', lang);
    });
  });
}

// Inicializar i18n cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', async () => {
  await translatePage();
  createLanguageSwitcher();
});
