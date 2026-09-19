<?php
// Partial: Modal Lightbox (se usa en múltiples páginas).
// Se incluye al final de las vistas que lo necesiten.
?>
<div class="galeria-lightbox" id="galeriaLightbox" role="dialog" aria-modal="true" aria-label="Visor de imagen" aria-describedby="galeriaLightboxCaption" tabindex="-1" hidden>
  <div class="galeria-lightbox-backdrop" data-lightbox-close></div>
  <figure class="galeria-lightbox-figure">
    <button class="galeria-control galeria-lightbox-nav galeria-control-prev" type="button" data-lightbox-prev aria-label="Imagen anterior">
      <i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
    </button>
    <button class="galeria-control galeria-lightbox-nav galeria-control-next" type="button" data-lightbox-next aria-label="Imagen siguiente">
      <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
    </button>
    <button class="galeria-lightbox-close" type="button" data-lightbox-close aria-label="Cerrar visor">
      <i class="fa-solid fa-xmark" aria-hidden="true"></i>
    </button>
    <img class="galeria-lightbox-image" alt="">
    <figcaption id="galeriaLightboxCaption" class="galeria-lightbox-caption"></figcaption>
  </figure>
</div>
