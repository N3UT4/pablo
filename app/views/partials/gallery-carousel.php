<?php
// Partial: Función de renderizado de galería carrusel Swiper.
// Extraída de app/views/pages/inicio.php para reducir su tamaño.
function renderItzaGalleryCarousel(array $photos, string $carouselId, string $carouselLabel, bool $limitToSix = false): void
{
    if ($limitToSix && count($photos) > 6) {
        $photos = array_slice($photos, 0, 6);
    }
    $total = count($photos);
    if ($total === 0) {
        echo '<p class="gallery-empty">No hay fotos disponibles en esta galería.</p>';
        return;
    }

    $categories = ['neotradicional', 'blackwork', 'anime', 'realismo'];
    $catLabels = [
        'neotradicional' => 'Neotradicional',
        'blackwork' => 'Blackwork',
        'anime' => 'Anime / Manga',
        'realismo' => 'Realismo',
    ];
    $zones = ['Antebrazo', 'Espalda', 'Hombro', 'Costado', 'Manga', 'Pecho', 'Pierna', 'Cuello', 'Manos', 'Rascador'];

    $processedPhotos = [];
    foreach ($photos as $index => $photo) {
        $category = $photo['category'] ?? $categories[$index % count($categories)];
        $title = $photo['title'] ?? 'Pieza ' . ($index + 1) . ' - ' . $zones[$index % count($zones)];
        $styleLabel = $catLabels[$category] ?? ucfirst($category);
        $processedPhotos[] = array_merge($photo, [
            'category' => $category,
            'title' => $title,
            'styleLabel' => $styleLabel,
        ]);
    }
    ?>
    <div class="gallery-carousel reveal" id="<?= htmlspecialchars($carouselId, ENT_QUOTES, 'UTF-8') ?>" data-gallery-carousel data-booking-url="<?= htmlspecialchars(BASE_URL . 'index.php?action=booking', ENT_QUOTES, 'UTF-8') ?>" aria-roledescription="carrusel" aria-label="<?= htmlspecialchars($carouselLabel, ENT_QUOTES, 'UTF-8') ?>">
      <div class="gallery-filters" role="group" aria-label="Filtrar por estilo">
        <button class="gallery-filter active" type="button" data-filter="all" aria-pressed="true">Todos</button>
        <button class="gallery-filter" type="button" data-filter="neotradicional" aria-pressed="false">Neotradicional</button>
        <button class="gallery-filter" type="button" data-filter="blackwork" aria-pressed="false">Blackwork</button>
        <button class="gallery-filter" type="button" data-filter="anime" aria-pressed="false">Anime / Manga</button>
        <button class="gallery-filter" type="button" data-filter="realismo" aria-pressed="false">Realismo</button>
      </div>
      <div class="gallery-swiper-container">
        <div class="swiper gallery-swiper" aria-label="<?= htmlspecialchars($carouselLabel, ENT_QUOTES, 'UTF-8') ?>">
          <div class="swiper-wrapper">
            <?php foreach ($processedPhotos as $index => $photo):
              $photoSrc = htmlspecialchars((string) ($photo['src'] ?? ''), ENT_QUOTES, 'UTF-8');
              $photoAlt = htmlspecialchars((string) ($photo['alt'] ?? ''), ENT_QUOTES, 'UTF-8');
              $photoCategory = htmlspecialchars((string) ($photo['category'] ?? ''), ENT_QUOTES, 'UTF-8');
              $photoTitle = htmlspecialchars((string) ($photo['title'] ?? ''), ENT_QUOTES, 'UTF-8');
              $photoStyleLabel = htmlspecialchars((string) ($photo['styleLabel'] ?? ''), ENT_QUOTES, 'UTF-8');
            ?>
              <div class="swiper-slide" data-category="<?= $photoCategory ?>" data-index="<?= $index ?>">
                <div class="gallery-slide-card">
                  <button class="gallery-photo" type="button" data-lightbox-src="<?= $photoSrc ?>" data-lightbox-alt="<?= $photoAlt ?>" data-lightbox-caption="<?= $photoStyleLabel . ' · ' . $photoTitle ?>" aria-label="Ampliar <?= $photoAlt ?>">
                    <img src="<?= $photoSrc ?>" alt="<?= $photoAlt ?>" loading="lazy" decoding="async">
                  </button>
                  <span class="slide-tag"><?= $photoStyleLabel ?></span>
                  <div class="slide-info-bar">
                    <h3 class="slide-title"><?= $photoTitle ?></h3>
                    <button class="slide-quote-btn" type="button" data-style="<?= $photoCategory ?>" data-piece="<?= $photoTitle ?>">
                      <i class="fa-solid fa-pen-nib" aria-hidden="true"></i> Cotizar estilo similar
                    </button>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
      <div class="gallery-nav gallery-nav-prev swiper-button-prev" aria-label="Anterior"></div>
      <div class="gallery-nav gallery-nav-next swiper-button-next" aria-label="Siguiente"></div>
      <div class="gallery-pagination-custom" role="group" aria-label="Paginación de la galería">
        <span class="gallery-counter" aria-live="polite">Foto 1 de <?= $total ?></span>
        <div class="gallery-dots" role="list" aria-label="Diapositivas"></div>
      </div>
    </div>
    <?php
}
