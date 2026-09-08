// Ejemplos de uso del sistema i18n en diferentes contextos

// ============================================
// 1. TRADUCIR MENSAJES DE ERROR EN JAVASCRIPT
// ============================================

// Ejemplo: En un script de validación
function mostrarError(campo) {
  // Esperar a que i18n esté listo
  await i18n.loadTranslations();
  
  // Obtener el mensaje de error traducido
  const mensaje = i18n.t(`login.${campo}_error`);
  console.log(mensaje);
  
  // Con SweetAlert2
  itzaError(i18n.t('login.email_error'));
}

// ============================================
// 2. ACTUALIZAR CONTENIDO AL CAMBIAR IDIOMA
// ============================================

window.addEventListener('languageChanged', (e) => {
  const nuevoIdioma = e.detail.lang;
  
  // Actualizar un elemento específico
  const titulo = document.querySelector('h1');
  if (titulo) {
    titulo.textContent = i18n.t('login.titulo');
  }
  
  // Recargar datos o actualizar componentes
  console.log('Idioma cambiado a:', nuevoIdioma);
});

// ============================================
// 3. AGREGAR NUEVAS TRADUCCIONES
// ============================================

// En lang/translations.json:
/*
{
  "es": {
    "miSeccion": {
      "miCampo": "Mi valor en español"
    }
  },
  "en": {
    "miSeccion": {
      "miCampo": "My value in English"
    }
  }
}
*/

// Luego en HTML:
// <label data-i18n="miSeccion.miCampo">Fallback text</label>

// ============================================
// 4. TRADUCIR MENSAJES DE VALIDACIÓN
// ============================================

function validarFormulario() {
  const email = document.getElementById('email');
  const emailValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value);
  
  if (!emailValid) {
    // Obtener el error en el idioma actual
    const errorMsg = i18n.t('login.email_error');
    mostrarErrorEnPantalla(email, errorMsg);
  }
}

// ============================================
// 5. CAMBIAR IDIOMA PROGRAMÁTICAMENTE
// ============================================

// Botón para cambiar a inglés
document.getElementById('btnEnglish').addEventListener('click', async () => {
  i18n.setLanguage('en');
  await translatePage(); // Función en i18n.js
});

// Botón para cambiar a español
document.getElementById('btnSpanish').addEventListener('click', async () => {
  i18n.setLanguage('es');
  await translatePage();
});

// ============================================
// 6. CREAR ATRIBUTOS DATA-I18N DINÁMICAMENTE
// ============================================

function crearElementoTraducido() {
  const label = document.createElement('label');
  label.setAttribute('data-i18n', 'contacto.nombre');
  label.textContent = 'Nombre'; // Fallback
  
  document.body.appendChild(label);
  
  // Traducir el nuevo elemento
  await translatePage();
}

// ============================================
// 7. VALIDAR FORM CON MENSAJES TRADUCIDOS
// ============================================

function validarYMostrarErrores(formulario) {
  const errores = [];
  
  // Validar email
  const email = formulario.email;
  const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRe.test(email.value)) {
    const msg = i18n.t('login.email_error');
    errores.push(msg);
  }
  
  // Validar contraseña
  const password = formulario.password;
  if (password.value.length < 6) {
    const msg = i18n.t('login.password_error');
    errores.push(msg);
  }
  
  // Mostrar errores si los hay
  if (errores.length > 0) {
    itzaError(errores.join('\n'));
    return false;
  }
  
  return true;
}

// ============================================
// 8. OBTENER TODOS LOS VALORES DE UNA SECCIÓN
// ============================================

function obtenerTraducciones(seccion) {
  const traducciones = i18n.translations[i18n.currentLang][seccion];
  return traducciones;
}

// Ejemplo: Obtener todos los placeholders
const placeholders = obtenerTraducciones('login');
console.log(placeholders);
// Output: { titulo: "...", email_placeholder: "...", ... }

// ============================================
// 9. INTEGRACIÓN CON VALIDACIÓN DE FORMULARIOS
// ============================================

class FormValidator {
  constructor(formId) {
    this.form = document.getElementById(formId);
    this.errors = new Map();
  }
  
  validar() {
    const campos = this.form.querySelectorAll('input, select, textarea');
    campos.forEach(campo => {
      const resultado = this.validarCampo(campo);
      if (!resultado.valido) {
        this.errors.set(campo.id, resultado.error);
      }
    });
    return this.errors.size === 0;
  }
  
  validarCampo(campo) {
    const tipo = campo.type;
    const valor = campo.value.trim();
    
    if (!valor) {
      return {
        valido: false,
        error: i18n.t(`${this.formId}.${campo.id}_error`)
      };
    }
    
    if (tipo === 'email') {
      const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRe.test(valor)) {
        return {
          valido: false,
          error: i18n.t(`${this.formId}.email_error`)
        };
      }
    }
    
    return { valido: true };
  }
  
  mostrarErrores() {
    this.errors.forEach((error, fieldId) => {
      const campo = document.getElementById(fieldId);
      const errSpan = documento.getElementById(`${fieldId}Err`);
      errSpan.textContent = error;
      errSpan.classList.add('show');
    });
  }
}

// Uso:
// const validator = new FormValidator('loginForm');
// if (validator.validar()) { ... }

// ============================================
// 10. VERIFICAR IDIOMA ACTUAL
// ============================================

// Obtener idioma actual
const idioma = i18n.getCurrentLanguage();

// Hacer algo solo en inglés
if (idioma === 'en') {
  // Configurar algo específico para inglés
}

// Hacer algo solo en español
if (idioma === 'es') {
  // Configurar algo específico para español
}

// Obtener idiomas disponibles
const idiomas = i18n.getAvailableLanguages();
console.log(idiomas); // ['es', 'en']
