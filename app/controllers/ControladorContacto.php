<?php

require_once APP_ROOT . '/app/models/ModeloContacto.php';

// Atiende el formulario de contacto (js/contacto.js).
class ControladorContacto extends ControladorBase
{
    private ModeloContacto $contactModel;

    public function __construct()
    {
        $this->contactModel = new ModeloContacto();
    }

    public function store(): void
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->json(false, 'Solicitud no válida.', [], 405);
            }

            if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
                $this->json(false, 'La sesión del formulario expiró. Recarga la página e inténtalo de nuevo.', [], 419);
            }

            $nombre = trim((string) ($_POST['nombre_contacto'] ?? ''));
            $email = trim((string) ($_POST['email_contacto'] ?? ''));
            $asunto = trim((string) ($_POST['asunto'] ?? ''));
            $mensaje = trim((string) ($_POST['mensaje'] ?? ''));
            $acepta = isset($_POST['acepta_contacto']) && $_POST['acepta_contacto'] !== '0';

            if ($nombre === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $asunto === ''
                || strlen($mensaje) < 10 || !$acepta) {
                $this->json(false, 'Revisa los campos marcados en rojo antes de enviar.', [], 422);
            }

            $messageId = $this->contactModel->create([
                'nombre' => $nombre,
                'email' => $email,
                'asunto' => $asunto,
                'mensaje' => $mensaje,
            ]);

            $this->json(true, 'Tu mensaje fue enviado. Te responderemos pronto.', ['message_id' => $messageId]);
        } catch (Throwable $e) {
            error_log($e->getMessage());
            $this->json(false, 'No fue posible enviar tu mensaje. Verifica la conexión a la base de datos.', [], 500);
        }
    }
}