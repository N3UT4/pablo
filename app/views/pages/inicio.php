<?php
function renderItzaGalleryCarousel(array $photos, string $carouselId, string $carouselLabel): void
{
    $total = count($photos);
    if ($total === 0) {
        echo '<p class="gallery-empty">No hay fotos disponibles en esta galería.</p>';
        return;
    }
    ?>
    <div class="gallery-carousel reveal" id="<?= htmlspecialchars($carouselId, ENT_QUOTES, 'UTF-8') ?>" data-gallery-carousel aria-roledescription="carrusel" aria-label="<?= htmlspecialchars($carouselLabel, ENT_QUOTES, 'UTF-8') ?>">
      <div class="gallery-viewport" tabindex="0" aria-label="Fotos de <?= htmlspecialchars($carouselLabel, ENT_QUOTES, 'UTF-8') ?>">
        <button class="gallery-control gallery-control-prev" type="button" data-gallery-prev aria-label="Anterior">
          <i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
        </button>
        <button class="gallery-control gallery-control-next" type="button" data-gallery-next aria-label="Siguiente">
          <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
        </button>
        <div class="gallery-track">
          <?php foreach ($photos as $index => $photo): ?>
            <?php
            $photoSrc = htmlspecialchars((string) ($photo['src'] ?? ''), ENT_QUOTES, 'UTF-8');
            $photoAlt = htmlspecialchars((string) ($photo['alt'] ?? ''), ENT_QUOTES, 'UTF-8');
            $photoCaption = htmlspecialchars((string) ($photo['caption'] ?? ''), ENT_QUOTES, 'UTF-8');
            ?>
            <article class="gallery-slide" role="group" aria-roledescription="diapositiva" aria-label="Foto <?= $index + 1 ?> de <?= $total ?>">
              <button class="gallery-photo" type="button" data-lightbox-src="<?= $photoSrc ?>" data-lightbox-alt="<?= $photoAlt ?>" data-lightbox-caption="<?= $photoCaption ?>" aria-label="Ampliar <?= $photoAlt ?>">
                <img src="<?= $photoSrc ?>" alt="<?= $photoAlt ?>" loading="lazy" decoding="async">
              </button>
              <span class="photo-caption"><?= $photoCaption ?></span>
            </article>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="gallery-indicators" role="group" aria-label="Seleccionar foto"></div>
      <p class="gallery-status" aria-live="polite">Foto 1 de <?= $total ?></p>
    </div>
    <?php
}

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
  <section class="hero">
    <div class="wrap hero-grid">
      <div>
        <span class="eyebrow">Estudio de tatuajes · <?= htmlspecialchars(STUDIO_CITY) ?></span>
        <h1>Tinta con<br><span>propósito</span>,<br>citas sin fricción</h1>
        <p>Agenda tu cita, paga por QR con Nequi y firma tu consentimiento informado desde el celular. Todo en un solo lugar, sin filas ni papeleo.</p>
        <div class="hero-actions">
          <a href="<?= BASE_URL ?>index.php?action=register" class="btn-primary">Reservar mi cita</a>
          <a href="#servicios" class="btn-ghost">Ver servicios</a>
        </div>
      </div>
      <div class="hero-panel">
        <img src="<?= BASE_URL ?>img/machine.svg" alt="Máquina de tatuaje" class="hero-icon">
        <div class="row"><span class="label">Pago</span><strong>QR Nequi</strong></div>
        <div class="row"><span class="label">Consentimiento</span><strong>Firma digital</strong></div>
        <div class="row"><span class="label">Agenda</span><strong>24/7 online</strong></div>
        <div class="row"><span class="label">Confirmación</span><strong>Al instante</strong></div>
      </div>
    </div>
  </section>

  <section class="services" id="servicios">
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

  <section class="studio-gallery" id="galeria">
    <div class="wrap">
      <div class="sec-head reveal">
        <span class="eyebrow">Trabajo real</span>
        <h2>Galería del estudio</h2>
        <p>Conoce algunos de nuestros trabajos y el ambiente de ITZA TATTOO.</p>
        <img src="<?= BASE_URL ?>img/ornament.svg" class="sec-ornament" alt="">
      </div>
      <?php renderItzaGalleryCarousel($studioGalleryPhotos, 'galeriaCarousel', 'Galería del estudio'); ?>
    </div>
  </section>

  <?php
  // Fotos subidas por el staff desde galeria.html (tabla "gallery"). Si la base de datos
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
  <section class="studio-gallery" id="trabajos-recientes">
    <div class="wrap">
      <div class="sec-head reveal">
        <span class="eyebrow">Recién publicado</span>
        <h2>Últimos trabajos</h2>
        <p>Fotos subidas por el equipo directo desde el estudio.</p>
        <img src="<?= BASE_URL ?>img/ornament.svg" class="sec-ornament" alt="">
      </div>
      <?php renderItzaGalleryCarousel($uploadedGalleryPhotos, 'trabajosRecientesCarousel', 'Últimos trabajos'); ?>
    </div>
  </section>
  <?php endif; ?>
  <section class="artists" id="artistas">
    <div class="wrap">
      <div class="sec-head reveal">
        <span class="eyebrow">La artista</span>
        <h2>Conoce a Itza</h2>
        <p>La artista detrás de cada pieza de ITZA TATTOO.</p>
        <img src="<?= BASE_URL ?>img/ornament.svg" class="sec-ornament" alt="">
      </div>
      <div class="artist-feature reveal">
        <div class="artist-feature-photo">
          <img src="<?= BASE_URL ?>img/itza%20tatto%20perfil.jpeg" alt="Itza, tatuadora principal de ITZA TATTOO" loading="lazy">
        </div>
        <div class="artist-feature-content">
          <span class="eyebrow">Tatuadora principal · Fundadora</span>
          <h3>Itza</h3>
          <p>
            En ITZA TATTOO, Itza convierte ideas, símbolos y recuerdos en piezas
            diseñadas especialmente para cada persona. Su trabajo combina detalle,
            técnica y creatividad para que cada tatuaje tenga una identidad propia.
          </p>
          <p>
            Su forma de trabajar parte de escuchar la idea del cliente y transformarla
            en un diseño personalizado, cuidando cada línea, sombra y color durante el proceso.
          </p>
          <div class="artist-specialties">
            <span>Puntillismo</span>
            <span>Blackwork</span>
            <span>Color</span>
            <span>Geometría</span>
            <span>Blackout</span>
            <span>Diseños personalizados</span>
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
          <a href="<?= BASE_URL ?>index.php?action=register" class="btn-primary">Crear cuenta</a>
        </div>
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
          <a href="https://wa.me/<?= STUDIO_WHATSAPP ?>" target="_blank" rel="noopener noreferrer">
            <?= htmlspecialchars(STUDIO_PHONE) ?>
          </a>
          <span>Escríbenos para agendar o resolver tus dudas.</span>
        </div>
      </div>
    </div>
  </section>
</main>

<!-- Modal Lightbox con efecto de fondo desenfocado (backdrop-filter) -->
<div class="gallery-lightbox" id="galleryLightbox" role="dialog" aria-modal="true" aria-label="Visor de imagen" aria-describedby="galleryLightboxCaption" tabindex="-1" hidden>
  <div class="gallery-lightbox-backdrop" data-lightbox-close></div>
  <figure class="gallery-lightbox-figure">
    <button class="gallery-control gallery-lightbox-nav gallery-control-prev" type="button" data-lightbox-prev aria-label="Imagen anterior">
      <i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
    </button>
    <button class="gallery-control gallery-lightbox-nav gallery-control-next" type="button" data-lightbox-next aria-label="Imagen siguiente">
      <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
    </button>
    <button class="gallery-lightbox-close" type="button" data-lightbox-close aria-label="Cerrar visor">
      <i class="fa-solid fa-xmark" aria-hidden="true"></i>
    </button>
    <img class="gallery-lightbox-image" alt="">
    <figcaption id="galleryLightboxCaption" class="gallery-lightbox-caption"></figcaption>
  </figure>
</div>
