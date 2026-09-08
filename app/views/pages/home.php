<main>
  <section class="hero">
    <div class="wrap hero-grid">
      <div>
        <span class="eyebrow">Estudio de tatuajes · <?= htmlspecialchars(STUDIO_CITY) ?></span>
        <h1>Tinta con<br><span>propósito</span>,<br>citas sin fricción</h1>
        <p>Agenda tu cita, paga por QR con Nequi y firma tu consentimiento informado desde el celular. Todo en un solo lugar, sin filas ni papeleo.</p>
        <div class="hero-actions">
          <a href="index.php?action=register" class="btn-primary">Reservar mi cita</a>
          <a href="#servicios" class="btn-ghost">Ver servicios</a>
        </div>
      </div>
      <div class="hero-panel">
        <img src="img/machine.svg" alt="Máquina de tatuaje" class="hero-icon">
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
      </div>
      <div class="service-grid">
        <div class="service-card reveal">
          <img class="service-icon" src="img/mandala.svg" alt="Mandala blackwork">
          <div class="num">01</div>
          <h3>Blackwork</h3>
          <p>Trazos sólidos, alto contraste, diseño geométrico y tribal.</p>
        </div>
        <div class="service-card reveal">
          <img class="service-icon" src="img/skull.svg" alt="Calavera realismo">
          <div class="num">02</div>
          <h3>Realismo</h3>
          <p>Retratos y escenas con sombreado fino y detalle fotográfico.</p>
        </div>
        <div class="service-card reveal">
          <img class="service-icon" src="img/snake.svg" alt="Serpiente fine line">
          <div class="num">03</div>
          <h3>Fine Line</h3>
          <p>Líneas delicadas para diseños minimalistas y delicados.</p>
        </div>
        <div class="service-card reveal">
          <img class="service-icon" src="img/rose.svg" alt="Rosa a color">
          <div class="num">04</div>
          <h3>Color</h3>
          <p>Piezas vibrantes con paletas personalizadas por artista.</p>
        </div>
        <div class="service-card reveal">
          <img class="service-icon" src="img/dagger.svg" alt="Daga cover-up">
          <div class="num">05</div>
          <h3>Cover-up</h3>
          <p>Rediseño y cobertura de tatuajes antiguos.</p>
        </div>
        <div class="service-card reveal">
          <img class="service-icon" src="img/machine.svg" alt="Máquina de piercing">
          <div class="num">06</div>
          <h3>Piercing</h3>
          <p>Perforaciones con material estéril certificado.</p>
        </div>
        <div class="service-card featured reveal">
          <span class="badge">✦ Nuevo</span>
          <img class="service-icon" src="img/custom.svg" alt="Diseño personalizado">
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
      </div>
      <div class="flash-grid">
        <div class="flash-item reveal"><img src="img/rose.svg" alt="Diseño rosa"><span>Rosa clásica</span></div>
        <div class="flash-item reveal"><img src="img/dagger.svg" alt="Diseño daga"><span>Daga tradicional</span></div>
        <div class="flash-item reveal"><img src="img/skull.svg" alt="Diseño calavera"><span>Calavera</span></div>
        <div class="flash-item reveal"><img src="img/mandala.svg" alt="Diseño mandala"><span>Mandala</span></div>
        <div class="flash-item reveal"><img src="img/snake.svg" alt="Diseño serpiente"><span>Serpiente</span></div>
        <div class="flash-item reveal"><img src="img/machine.svg" alt="Diseño máquina"><span>Máquina old school</span></div>
      </div>
    </div>
  </section>

  <?php
  $studioPhotos = glob(APP_ROOT . '/img/WhatsApp Image*.jpeg') ?: [];
  sort($studioPhotos, SORT_NATURAL);
  ?>
  <section class="studio-gallery" id="galeria">
    <div class="wrap">
      <div class="sec-head reveal">
        <span class="eyebrow">Trabajo real</span>
        <h2>Galería del estudio</h2>
        <p>Conoce algunos de nuestros trabajos y el ambiente de ITZA TATTOO.</p>
      </div>
      <div class="studio-photo-grid">
        <?php foreach ($studioPhotos as $index => $photo): ?>
          <?php $photoName = basename($photo); ?>
          <a class="studio-photo reveal" href="img/<?= rawurlencode($photoName) ?>" target="_blank" rel="noopener noreferrer">
            <img src="img/<?= rawurlencode($photoName) ?>"
                 alt="Trabajo de tatuaje del estudio, imagen <?= $index + 1 ?>"
                 loading="lazy">
            <span class="photo-caption">ITZA TATTOO · <?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="artists" id="artistas">
    <div class="wrap">
      <div class="sec-head reveal">
        <span class="eyebrow">La artista</span>
        <h2>Conoce a Itza</h2>
        <p>La artista detrás de cada pieza de ITZA TATTOO.</p>
      </div>
      <div class="artist-feature reveal">
        <div class="artist-feature-photo">
          <img src="img/itza%20tatto%20perfil.jpeg" alt="Itza, tatuadora principal de ITZA TATTOO" loading="lazy">
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
          <a href="index.php?action=register" class="btn-primary">Crear cuenta</a>
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
