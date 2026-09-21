<?php
require_once DIR_PATH . 'app/views/partials/gallery-carousel.php';

$studioPhotos = glob(DIR_PATH . 'img/[0-9]*.jpeg') ?: [];
sort($studioPhotos, SORT_NATURAL);
$studioGalleryPhotos = array_map(static function (string $photo, int $index): array {
    $photoName = basename($photo);
    $number = str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT);

    return [
        'src' => BASE_URL . 'img/' . rawurlencode($photoName),
        'alt' => 'Trabajo de tatuaje del estudio, imagen ' . ($index + 1),
        'caption' => 'ITZA TATTOO · ' . $number,
    ];
}, $studioPhotos, array_keys($studioPhotos));
?>
<main>
  <section class="hero" aria-labelledby="hero-title">
    <div class="wrap hero-grid">
      <div class="hero-copy">
        <span class="eyebrow hero-eyebrow" data-i18n="home.eyebrow">Estudio de tatuajes • Bogotá, La Victoria • 20 de Julio</span>
        <h1 id="hero-title"><span data-i18n="home.title">El primer paso para tu próximo tatuaje empieza aquí.</span></h1>
        <p class="hero-description" data-i18n="home.description">Agenda tu cita en minutos, asegura tu cupo con un pago QR por Nequi y firma tu consentimiento informado desde el celular. Menos filas, menos papeleo y más tiempo para crear.</p>
        <div class="hero-actions">
          <?php if ($isAuthenticated): ?>
          <a href="<?= BASE_URL ?>index.php?action=cliente-agendar" class="btn-primary btn-primary-hero">
            <span data-i18n="home.reserve">Reservar mi cita</span>
            <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
          </a>
          <?php else: ?>
          <a href="<?= BASE_URL ?>index.php?action=register" class="btn-primary btn-primary-hero">
            <span data-i18n="home.reserve">Reservar mi cita</span>
            <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
          </a>
          <?php endif; ?>
          <a href="#servicios" class="btn-ghost btn-ghost-hero">
            <span data-i18n="home.servicios">Ver servicios</span>
            <i class="fa-solid fa-arrow-down" aria-hidden="true"></i>
          </a>
        </div>
      </div>
      <aside class="hero-panel" aria-label="Beneficios de agendar en línea" data-i18n-aria="home.benefits">
        <div class="hero-panel-glow" aria-hidden="true"></div>
        <div class="feature-row">
          <span class="feature-icon" aria-hidden="true"><i class="fa-solid fa-qrcode"></i></span>
          <div class="feature-copy"><span class="feature-label">Pago Rápido y Seguro</span><span class="feature-detail">QR Nequi</span></div>
        </div>
        <div class="feature-row">
          <span class="feature-icon" aria-hidden="true"><i class="fa-solid fa-file-signature"></i></span>
          <div class="feature-copy"><span class="feature-label">Consentimiento Sin Papeleo</span><span class="feature-detail">Firma Digital</span></div>
        </div>
        <div class="feature-row">
          <span class="feature-icon" aria-hidden="true"><i class="fa-regular fa-calendar-check"></i></span>
          <div class="feature-copy"><span class="feature-label">Agenda Cuando Tú Quieras</span><span class="feature-detail">24/7 Online</span></div>
        </div>
        <div class="feature-row">
          <span class="feature-icon" aria-hidden="true"><i class="fa-solid fa-bolt"></i></span>
          <div class="feature-copy"><span class="feature-label">Confirmación Sin Esperas</span><span class="feature-detail">Al Instante</span></div>
        </div>
      </aside>
    </div>
  </section>

  <section class="servicios" id="servicios">
    <div class="wrap">
      <div class="sec-head reveal">
        <span class="eyebrow">Servicios</span>
        <h2>Lo que hacemos</h2>
        <p>Cada estilo tiene su técnica. Elige el que se ajuste a tu idea.</p>
        <img src="<?= BASE_URL ?>img/ornament.svg" class="sec-ornament" alt="">
      </div>
      <div class="service-grid">
        <div class="service-card reveal">
          <img class="service-icon" src="<?= BASE_URL ?>img/mandala.svg" alt="Mandala blackwork">
          <div class="num">01</div>
          <h3>Blackwork</h3>
          <p>Trazos sólidos, alto contraste, diseño geométrico y tribal.</p>
        </div>
        <div class="service-card reveal">
          <img class="service-icon" src="<?= BASE_URL ?>img/skull.svg" alt="Calavera realismo">
          <div class="num">02</div>
          <h3>Realismo</h3>
          <p>Retratos y escenas con sombreado fino y detalle fotográfico.</p>
        </div>
        <div class="service-card reveal">
          <img class="service-icon" src="<?= BASE_URL ?>img/snake.svg" alt="Serpiente fine line">
          <div class="num">03</div>
          <h3>Fine Line</h3>
          <p>Líneas delicadas para diseños minimalistas y delicados.</p>
        </div>
        <div class="service-card reveal">
          <img class="service-icon" src="<?= BASE_URL ?>img/rose.svg" alt="Rosa a color">
          <div class="num">04</div>
          <h3>Color</h3>
          <p>Piezas vibrantes con paletas personalizadas por artista.</p>
        </div>
        <div class="service-card reveal">
          <img class="service-icon" src="<?= BASE_URL ?>img/dagger.svg" alt="Daga cover-up">
          <div class="num">05</div>
          <h3>Cover-up</h3>
          <p>Rediseño y cobertura de tatuajes antiguos.</p>
        </div>
        <div class="service-card reveal">
          <img class="service-icon" src="<?= BASE_URL ?>img/machine.svg" alt="Máquina de piercing">
          <div class="num">06</div>
          <h3>Piercing</h3>
          <p>Perforaciones con material estéril certificado.</p>
        </div>
        <div class="service-card featured reveal">
          <span class="badge">✦ Nuevo</span>
          <img class="service-icon" src="<?= BASE_URL ?>img/custom.svg" alt="Diseño personalizado">
          <div class="num">07</div>
          <h3>Diseño personalizado</h3>
          <p>Trae tu idea y la convertimos en un diseño único contigo, junto al artista.</p>
        </div>
      </div>
    </div>
  </section>

  <section id="proceso">
    <div class="wrap">
      <div class="sec-head reveal">
        <span class="eyebrow">Proceso</span>
        <h2>Cómo funciona</h2>
        <p>Del boceto a la aguja, en cuatro pasos.</p>
        <img src="<?= BASE_URL ?>img/ornament.svg" class="sec-ornament" alt="">
      </div>
      <div class="process-grid">
        <div class="step reveal">
          <div class="dot"></div>
          <h3>1. Agenda</h3>
          <p>Elige artista, estilo y horario disponible desde la plataforma.</p>
        </div>
        <div class="step reveal">
          <div class="dot"></div>
          <h3>2. Paga el abono</h3>
          <p>Confirma tu cita con un pago QR vía Nequi, seguro y rápido.</p>
        </div>
        <div class="step reveal">
          <div class="dot"></div>
          <h3>3. Firma digital</h3>
          <p>Completa el consentimiento informado desde tu celular.</p>
        </div>
        <div class="step reveal">
          <div class="dot"></div>
          <h3>4. Llega y tatúate</h3>
          <p>Preséntate a tu hora reservada, todo ya está listo.</p>
        </div>
      </div>
    </div>
  </section>

  <section class="flash">
    <div class="wrap">
      <div class="sec-head reveal">
        <span class="eyebrow">Galería</span>
        <h2>Flash sheet</h2>
        <p>Diseños disponibles para agendar directamente, sin espera de boceto personalizado.</p>
        <img src="<?= BASE_URL ?>img/ornament.svg" class="sec-ornament" alt="">
      </div>
      <div class="flash-grid">
        <div class="flash-item reveal"><img src="<?= BASE_URL ?>img/rose.svg" alt="Diseño rosa"><span>Rosa clásica</span></div>
        <div class="flash-item reveal"><img src="<?= BASE_URL ?>img/dagger.svg" alt="Diseño daga"><span>Daga tradicional</span></div>
        <div class="flash-item reveal"><img src="<?= BASE_URL ?>img/skull.svg" alt="Diseño calavera"><span>Calavera</span></div>
        <div class="flash-item reveal"><img src="<?= BASE_URL ?>img/mandala.svg" alt="Diseño mandala"><span>Mandala</span></div>
        <div class="flash-item reveal"><img src="<?= BASE_URL ?>img/snake.svg" alt="Diseño serpiente"><span>Serpiente</span></div>
        <div class="flash-item reveal"><img src="<?= BASE_URL ?>img/machine.svg" alt="Diseño máquina"><span>Máquina old school</span></div>
        <div class="flash-item reveal"><img src="<?= BASE_URL ?>img/heart.svg" alt="Diseño corazón"><span>Corazón tradicional</span></div>
        <div class="flash-item reveal"><img src="<?= BASE_URL ?>img/anchor.svg" alt="Diseño ancla"><span>Ancla marinera</span></div>
        <div class="flash-item reveal"><img src="<?= BASE_URL ?>img/eye.svg" alt="Diseño ojo protector"><span>Ojo protector</span></div>
        <div class="flash-item reveal"><img src="<?= BASE_URL ?>img/wave.svg" alt="Diseño ola"><span>Ola japonesa</span></div>
        <div class="flash-item reveal"><img src="<?= BASE_URL ?>img/thunder.svg" alt="Diseño rayo"><span>Rayo</span></div>
        <div class="flash-item reveal"><img src="<?= BASE_URL ?>img/swallow.svg" alt="Diseño golondrina"><span>Golondrina</span></div>
      </div>
    </div>
  </section>

  <section class="studio-galeria" id="galeria">
    <div class="wrap">
      <div class="sec-head reveal">
        <span class="eyebrow">Trabajo real</span>
        <h2>Galería del estudio</h2>
        <p>Conoce algunos de nuestros trabajos y el ambiente de ITZA TATTOO.</p>
        <img src="<?= BASE_URL ?>img/ornament.svg" class="sec-ornament" alt="">
      </div>
      <?php renderItzaGalleryCarousel($studioGalleryPhotos, 'galeriaCarousel', 'Galería del estudio', !$isAuthenticated); ?>
      <?php if (!$isAuthenticated): ?>
      <div class="galeria-cta reveal" style="text-align:center; margin-top:1.5rem;">
        <a href="<?= BASE_URL ?>index.php?action=login" class="btn-primary">
          <i class="fa-solid fa-images" aria-hidden="true"></i> Ver galería completa
        </a>
        <p style="margin-top:.75rem; color:var(--bone-dim); font-size:.9rem;">Inicia sesión o regístrate para ver todos los trabajos</p>
      </div>
      <?php endif; ?>
    </div>
  </section>

  <?php
  // Fotos subidas por el staff desde galeria.html (tabla "galeria"). Si la base de datos
  // aún no está configurada o no hay fotos, esta sección simplemente no se muestra.
  $uploadedPhotos = [];
  try {
      require_once DIR_PATH . 'app/models/ModeloGaleria.php';
      $uploadedPhotos = (new ModeloGaleria())->listActive();
  } catch (Throwable $e) {
      error_log($e->getMessage());
  }
    $uploadedGalleryPhotos = [];
    foreach ($uploadedPhotos as $photo) {
        $titulo = trim((string) ($photo['titulo'] ?? ''));
        $imagen = trim((string) ($photo['imagen'] ?? ''));
        $url = filter_var($imagen, FILTER_VALIDATE_URL);
        $scheme = $url ? strtolower((string) parse_url($url, PHP_URL_SCHEME)) : '';
        $isAbsolute = $url && in_array($scheme, ['http', 'https'], true);
        $isRootGalleryPath = strpos($imagen, '/img/') === 0;
        $isRelativeGalleryPath = strpos($imagen, 'img/') === 0
            && strpos($imagen, '..') === false
            && strpos($imagen, '\\') === false;

        if (!$isAbsolute && !$isRootGalleryPath && !$isRelativeGalleryPath) {
            continue;
        }

        $src = $isAbsolute
            ? $imagen
            : ($isRootGalleryPath ? $imagen : BASE_URL . $imagen);

        $uploadedGalleryPhotos[] = [
            'src' => $src,
            'alt' => $titulo ?: 'Trabajo del estudio',
            'caption' => $titulo ?: 'ITZA TATTOO',
        ];
    }
  ?>
  <?php if (!empty($uploadedGalleryPhotos)): ?>
  <section class="studio-galeria" id="trabajos-recientes">
    <div class="wrap">
      <div class="sec-head reveal">
        <span class="eyebrow">Recién publicado</span>
        <h2>Últimos trabajos</h2>
        <p>Fotos subidas por el equipo directo desde el estudio.</p>
        <img src="<?= BASE_URL ?>img/ornament.svg" class="sec-ornament" alt="">
      </div>
      <?php renderItzaGalleryCarousel($uploadedGalleryPhotos, 'trabajosRecientesCarousel', 'Últimos trabajos', !$isAuthenticated); ?>
      <?php if (!$isAuthenticated): ?>
      <div class="galeria-cta reveal" style="text-align:center; margin-top:1.5rem;">
        <a href="<?= BASE_URL ?>index.php?action=login" class="btn-primary">
          <i class="fa-solid fa-images" aria-hidden="true"></i> Ver galería completa
        </a>
        <p style="margin-top:.75rem; color:var(--bone-dim); font-size:.9rem;">Inicia sesión o regístrate para ver todos los trabajos</p>
      </div>
      <?php endif; ?>
    </div>
  </section>
  <?php endif; ?>
  <section class="artistas artist-spotlight-section" id="artistas">
    <div class="wrap">
      <div class="artist-spotlight-card reveal">
        <div class="artist-spotlight-glow" aria-hidden="true"></div>
        <div class="artist-spotlight-grid">
          <div class="artist-spotlight-media">
            <div class="artist-photo-frame">
              <img src="<?= BASE_URL ?>img/itza%20tatto%20perfil.jpeg" alt="Itza, tatuadora principal y fundadora de ITZA TATTOO" loading="lazy">
              <span class="artist-role-badge">
                <i class="fa-solid fa-crown" aria-hidden="true"></i>
                <span>ARTISTA PRINCIPAL &amp; FUNDADORA</span>
              </span>
            </div>
          </div>

          <div class="artist-spotlight-body">
            <div class="artist-spotlight-heading">
              <span class="artist-spotlight-eyebrow"><i class="fa-solid fa-star-of-life" aria-hidden="true"></i> Conoce a Itza · Artist spotlight</span>
              <h2 class="artist-spotlight-name">Itza<span>.</span></h2>
              <div class="artist-official-role">
                <span class="artist-role-line"><i class="fa-solid fa-feather-pointed" aria-hidden="true"></i> Tatuadora principal · Fundadora</span>
                <span class="artist-signature">Itza</span>
              </div>
            </div>

            <div class="artist-stats-grid" aria-label="Trayectoria de Itza">
              <div class="artist-stat-card">
                <strong>+6</strong>
                <span>Años</span>
                <small>Experiencia</small>
              </div>
              <div class="artist-stat-card">
                <strong>+400</strong>
                <span>Piezas</span>
                <small>Tatuajes realizados</small>
              </div>
              <div class="artist-stat-card">
                <strong>100%</strong>
                <span>Únicas</span>
                <small>Diseños personalizados</small>
              </div>
            </div>

            <div class="artist-bio">
              <p>Itza es la artista principal y fundadora de ITZA TATTOO. Con <strong>6 años de experiencia</strong> y más de <strong>400 tatuajes</strong> realizados, transforma ideas, símbolos y recuerdos en piezas con identidad propia. Su trayectoria combina técnica, <strong>diseño personalizado</strong> y una mirada artística que hace que cada cliente se sienta parte del proceso.</p>
              <p>Cada proyecto nace de escuchar con <strong>atención al detalle</strong> y de llevar la <strong>creatividad</strong> a cada línea, sombra y color. El resultado es una pieza exclusiva, pensada para durar y para contar una historia.</p>
            </div>

            <div class="artist-specialties" aria-label="Especialidades de Itza">
              <span>Neotradicional</span>
              <span>Puntillismo</span>
              <span>Blackwork</span>
              <span>Color</span>
              <span>Diseños personalizados</span>
            </div>

            <div class="artist-cta-row">
              <?php if ($isAuthenticated): ?>
              <a href="<?= BASE_URL ?>index.php?action=cliente-agendar" class="artist-cta-primary">
                <span>Agendar con Itza</span>
                <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
              </a>
              <?php else: ?>
              <a href="<?= BASE_URL ?>index.php?action=login" class="artist-cta-primary">
                <span>Agendar con Itza</span>
                <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
              </a>
              <?php endif; ?>
              <a href="#galeria" class="artist-cta-secondary">
                <span>Ver portafolio de Itza</span>
                <i class="fa-solid fa-images" aria-hidden="true"></i>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="wrap">
    <div class="cta-banner reveal">
      <div>
        <span class="eyebrow">Reserva ya</span>
        <h2>Tu próximo tatuaje empieza aquí.</h2>
      </div>
      <div>
        <p>Haz tu cita, firma el consentimiento y deja tu estilo en buenas manos.</p>
        <div class="hero-actions" style="margin-top:18px;">
          <?php if ($isAuthenticated): ?>
          <a href="<?= BASE_URL ?>index.php?action=cliente-agendar" class="btn-primary">Agendar cita</a>
          <?php else: ?>
          <a href="<?= BASE_URL ?>index.php?action=register" class="btn-primary">Crear cuenta</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>

  <section class="legal-sections" id="legal">
    <div class="wrap">
      <div class="legal-heading reveal">
        <span class="eyebrow" data-i18n="home.legal">Legal</span>
        <h2 data-i18n="home.legal_title">Información legal</h2>
        <p data-i18n="home.legal_intro">Conoce cómo gestionamos tus datos, citas y preferencias del sitio.</p>
      </div>
      <div class="legal-grid">
        <article class="legal-card reveal" id="privacidad">
          <h3 data-i18n="home.privacy_title">Privacidad</h3>
          <p data-i18n="home.privacy_text">En ITZA TATTOO tratamos tus datos únicamente para gestionar citas, pagos, consentimientos y la comunicación solicitada. No compartimos tu información con terceros salvo cuando sea necesario para prestar el servicio o por obligación legal.</p>
        </article>
        <article class="legal-card reveal" id="terminos">
          <h3 data-i18n="home.terms_title">Términos</h3>
          <p data-i18n="home.terms_text">Al reservar una cita aceptas proporcionar información veraz, respetar los horarios acordados y cumplir las condiciones de pago, reprogramación y cancelación comunicadas durante el proceso.</p>
        </article>
        <article class="legal-card reveal" id="cookies">
          <h3 data-i18n="home.cookies_title">Cookies</h3>
          <p data-i18n="home.cookies_text">Este sitio utiliza almacenamiento local para recordar tu idioma y preferencias básicas. Puedes limpiar los datos del navegador cuando quieras; esta elección no afecta la prestación del servicio.</p>
        </article>
      </div>
    </div>
  </section>

  <section class="studio-contact">
    <div class="wrap">
      <div class="sec-head reveal">
        <span class="eyebrow">Visítanos</span>
        <h2>Encuéntranos en La Victoria</h2>
        <p><?= htmlspecialchars(STUDIO_ADDRESS) ?> · <?= htmlspecialchars(STUDIO_CITY) ?></p>
        <img src="<?= BASE_URL ?>img/ornament.svg" class="sec-ornament" alt="">
      </div>
      <div class="contact-details">
        <div class="contact-detail">
          <span class="contact-icon" aria-hidden="true">⌖</span>
          <strong>Ubicación</strong>
          <span><?= htmlspecialchars(STUDIO_ADDRESS) ?></span>
          <span><?= htmlspecialchars(STUDIO_CITY) ?></span>
        </div>
        <div class="contact-detail">
          <span class="contact-icon" aria-hidden="true">◷</span>
          <strong>Horarios de atención</strong>
          <?php foreach (STUDIO_HOURS as $hour): ?>
            <span class="hours-line"><?= htmlspecialchars($hour) ?></span>
          <?php endforeach; ?>
        </div>
        <div class="contact-detail">
          <span class="contact-icon" aria-hidden="true">✆</span>
          <strong>Contacto y WhatsApp</strong>
          <a href="<?= htmlspecialchars(whatsapp_url('¡Hola! Me gustaría agendar una cita para un tatuaje.')) ?>" target="_blank" rel="noopener noreferrer">
            <?= htmlspecialchars(STUDIO_PHONE) ?>
          </a>
          <span>Escríbenos para agendar o resolver tus dudas.</span>
        </div>
      </div>
    </div>
  </section>
</main>

<!-- Modal Lightbox con efecto de fondo desenfocado (backdrop-filter) -->
<?php require_once DIR_PATH . 'app/views/partials/modal-lightbox.php'; ?>
