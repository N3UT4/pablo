<?php
// Controlador del formulario de contacto.
// Procesa los mensajes de contacto enviados por los visitantes.
require_once DIR_PATH . 'app/models/ModeloContacto.php';

class ControladorContacto extends ControladorBase
{
    private ModeloContacto $contactModel;

    public function __construct()
    {
        $this->contactModel = new ModeloContacto();
    }

    // Recibe y valida el formulario de contacto, guarda el mensaje en la BD.
    public function store(): void
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $_SESSION['flash'] = ['type' => 'error', 'message' => 'Solicitud no válida.'];
                $this->redirect('index.php?action=contact');
            }

            if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
                $_SESSION['flash'] = ['type' => 'error', 'message' => 'La sesión del formulario expiró. Recarga la página e inténtalo de nuevo.'];
                $this->redirect('index.php?action=contact');
            }

            $nombre = trim((string) ($_POST['nombre_contacto'] ?? ''));
            $email = trim((string) ($_POST['email_contacto'] ?? ''));
            $asunto = trim((string) ($_POST['asunto'] ?? ''));
            $mensaje = trim((string) ($_POST['mensaje'] ?? ''));
            $acepta = isset($_POST['acepta_contacto']) && $_POST['acepta_contacto'] !== '0';

            if ($nombre === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $asunto === ''
                || strlen($mensaje) < 10 || !$acepta) {
                $_SESSION['flash'] = ['type' => 'error', 'message' => 'Revisa los campos marcados en rojo antes de enviar.'];
                $this->redirect('index.php?action=contact');
            }

            $messageId = $this->contactModel->create([
                'nombre' => $nombre,
                'email' => $email,
                'asunto' => $asunto,
                'mensaje' => $mensaje,
            ]);

            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Tu mensaje fue enviado. Te responderemos pronto.'];
            $this->redirect('index.php?action=contact');
        } catch (Throwable $e) {
            error_log($e->getMessage());
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'No fue posible enviar tu mensaje. Verifica la conexión a la base de datos.'];
            $this->redirect('index.php?action=contact');
        }
    }
}
