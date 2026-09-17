<?php

require_once DIR_PATH . 'app/models/ModeloGaleria.php';
require_once DIR_PATH . 'app/models/ModeloArtistas.php';

// Atiende el formulario de staff en galeria (js/galeria.js).
class ControladorGaleria extends ControladorBase
{
    private ModeloGaleria $galleryModel;
    private ModeloArtistas $artistModel;

    private const MAX_SIZE_BYTES = 5 * 1024 * 1024; // 5MB, igual que el texto del formulario.
    private const ALLOWED_MIME = ['image/jpeg' => 'jpg', 'image/png' => 'png'];

    public function __construct()
    {
        $this->galleryModel = new ModeloGaleria();
        $this->artistModel = new ModeloArtistas();
    }

    // Lista las fotos activas en JSON. Útil para pintar la galería dinámica desde JS si se necesita.
    public function list(): void
    {
        try {
            $photos = $this->galleryModel->listActive();
            $this->json(true, 'ok', ['photos' => $photos]);
        } catch (Throwable $e) {
            error_log($e->getMessage());
            $this->json(false, 'No fue posible cargar la galería. Verifica la conexión a la base de datos.', [], 500);
        }
    }

    // Recibe el formulario multipart de galeria: título, tatuador, estilo, descripción y una o más fotos.
    public function upload(): void
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->json(false, 'Solicitud no válida.', [], 405);
            }

            if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
                $this->json(false, 'La sesión del formulario expiró. Recarga la página e inténtalo de nuevo.', [], 419);
            }

            // Doble candado: además de la puerta visual en el navegador, el servidor valida el código.
            if (!hash_equals(STAFF_ACCESS_CODE, (string) ($_POST['staff_code'] ?? ''))) {
                $this->json(false, 'Código de staff inválido o ausente.', [], 403);
            }

            $titulo = trim((string) ($_POST['titulo_trabajo'] ?? ''));
            $artistId = (int) ($_POST['tatuador_trabajo'] ?? 0);
            $estilo = trim((string) ($_POST['estilo_trabajo'] ?? ''));
            $descripcion = trim((string) ($_POST['descripcion_trabajo'] ?? ''));
            $autoriza = isset($_POST['autoriza_publicacion']) && $_POST['autoriza_publicacion'] !== '0';

            if (strlen($titulo) < 3 || $artistId <= 0 || $estilo === '' || !$autoriza) {
                $this->json(false, 'Revisa el título, el tatuador, el estilo y la autorización.', [], 422);
            }

            if (!$this->artistModel->exists($artistId)) {
                $this->json(false, 'El tatuador seleccionado no existe o está inactivo.', [], 422);
            }

            if (empty($_FILES['fotos_trabajo']) || empty($_FILES['fotos_trabajo']['name'][0])) {
                $this->json(false, 'Selecciona al menos una foto.', [], 422);
            }

            if (!is_dir(GALLERY_UPLOAD_DIR)) {
                mkdir(GALLERY_UPLOAD_DIR, 0755, true);
            }

            $descripcionFull = $descripcion !== '' ? $descripcion . ' (' . $estilo . ')' : 'Estilo: ' . $estilo;
            $saved = [];
            $files = $_FILES['fotos_trabajo'];
            $total = count($files['name']);

            for ($i = 0; $i < $total; $i++) {
                if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                    continue;
                }
                if ($files['size'][$i] > self::MAX_SIZE_BYTES) {
                    continue;
                }

                $mime = mime_content_type($files['tmp_name'][$i]);
                if (!isset(self::ALLOWED_MIME[$mime])) {
                    continue;
                }

                $extension = self::ALLOWED_MIME[$mime];
                $filename = 'g' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
                $destination = GALLERY_UPLOAD_DIR . '/' . $filename;

                if (!move_uploaded_file($files['tmp_name'][$i], $destination)) {
                    continue;
                }

                $relativePath = GALLERY_UPLOAD_PATH . '/' . $filename;
                $this->galleryModel->create([
                    'artist_id' => $artistId,
                    'titulo' => $titulo,
                    'descripcion' => $descripcionFull,
                    'imagen' => $relativePath,
                ]);
                $saved[] = $relativePath;
            }

            if (empty($saved)) {
                $this->json(false, 'Ninguna foto pudo guardarse. Revisa el formato (JPG/PNG) y el peso (máx 5MB).', [], 422);
            }

            $this->json(true, count($saved) . ' foto(s) subida(s) y publicada(s) en la galería.', ['files' => $saved]);
        } catch (Throwable $e) {
            error_log($e->getMessage());
            $this->json(false, 'No fue posible subir las fotos. Verifica la conexión a la base de datos.', [], 500);
        }
    }
}