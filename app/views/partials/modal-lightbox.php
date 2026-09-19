<?php
// Partial: Modal Lightbox (se usa en múltiples páginas).
// Se incluye al final de las vistas que lo necesiten.
?>
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
