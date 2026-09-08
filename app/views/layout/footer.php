<footer class="site-footer">
    <div class="footer-inner">
        <div>
            <a href="index.php?action=home" class="footer-logo">ITZA <span>TATTOO</span></a>
            <p><?= htmlspecialchars(STUDIO_CITY) ?></p>
        </div>
        <div class="social-links" aria-label="Redes sociales del estudio">
            <a href="<?= htmlspecialchars(STUDIO_INSTAGRAM) ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                <i class="fa-brands fa-instagram" aria-hidden="true"></i><span>Instagram</span>
            </a>
            <a href="<?= htmlspecialchars(STUDIO_FACEBOOK) ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                <i class="fa-brands fa-facebook-f" aria-hidden="true"></i><span>Facebook</span>
            </a>
            <a href="<?= htmlspecialchars(STUDIO_TIKTOK) ?>" target="_blank" rel="noopener noreferrer" aria-label="TikTok">
                <i class="fa-brands fa-tiktok" aria-hidden="true"></i><span>TikTok</span>
            </a>
            <a href="https://wa.me/<?= STUDIO_WHATSAPP ?>" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp">
                <i class="fa-brands fa-whatsapp" aria-hidden="true"></i><span>WhatsApp</span>
            </a>
            <a href="<?= htmlspecialchars(STUDIO_MAPS) ?>" target="_blank" rel="noopener noreferrer" aria-label="Google Maps">
                <i class="fa-solid fa-location-dot" aria-hidden="true"></i><span>Ubicación</span>
            </a>
        </div>
    </div>
</footer>
    <script src="js/swal-theme.js"></script>
    <script src="js/i18n.js"></script>
    <script src="js/form-fx.js"></script>
    <script src="js/animations.js"></script>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('gallery-modal');
        const modalImage = document.getElementById('gallery-modal-image');
        const closeBtn = document.querySelector('.gallery-modal-close');
        const triggerImages = document.querySelectorAll('[data-gallery-image]');

        function openGalleryModal(src, alt) {
          if (!modal || !modalImage) return;
          modalImage.src = src;
          modalImage.alt = alt || 'Foto ampliada';
          modal.classList.remove('hidden');
          modal.setAttribute('aria-hidden', 'false');
          document.body.style.overflow = 'hidden';
        }

        function closeGalleryModal() {
          if (!modal) return;
          modal.classList.add('hidden');
          modal.setAttribute('aria-hidden', 'true');
          document.body.style.overflow = '';
          modalImage.src = '';
        }

        triggerImages.forEach(function (image) {
          image.addEventListener('click', function () {
            openGalleryModal(image.dataset.galleryImage, image.alt);
          });
        });

        if (closeBtn) {
          closeBtn.addEventListener('click', closeGalleryModal);
        }

        if (modal) {
          modal.addEventListener('click', function (event) {
            if (event.target && event.target.dataset.closeGallery === 'true') {
              closeGalleryModal();
            }
          });
        }

        document.addEventListener('keydown', function (event) {
          if (event.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
            closeGalleryModal();
          }
        });
      });
    </script>
    <?php if (($currentPage ?? '') === 'login'): ?>
        <script src="js/auth.js"></script>
    <?php elseif (($currentPage ?? '') === 'register'): ?>
        <script src="js/registro.js"></script>
    <?php endif; ?>
</body>
</html>
