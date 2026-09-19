<?php

require_once DIR_PATH . 'app/models/ModeloConsentimiento.php';
require_once DIR_PATH . 'app/models/ModeloCitas.php';
require_once DIR_PATH . 'app/models/ModeloUsuarios.php';

// Atiende el formulario de consentimiento (js/consentimiento.js).
// Lo diligencia el staff con el cliente presente, por eso se protege con STAFF_ACCESS_CODE
// en vez de una sesión de cliente.
class ControladorConsentimiento extends ControladorBase
{
    private ModeloConsentimiento $consentModel;
    private ModeloCitas $appointmentModel;
    private ModeloUsuarios $userModel;

    public function __construct()
    {
        $this->consentModel = new ModeloConsentimiento();
        $this->appointmentModel = new ModeloCitas();
        $this->userModel = new ModeloUsuarios();
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

            if (!hash_equals(STAFF_ACCESS_CODE, (string) ($_POST['staff_code'] ?? ''))) {
                $this->json(false, 'Código de staff inválido o ausente.', [], 403);
            }

            $nombre = trim((string) ($_POST['nombre_cliente'] ?? ''));
            $documento = trim((string) ($_POST['documento'] ?? ''));
            $fechaNacimiento = trim((string) ($_POST['fecha_nacimiento'] ?? ''));
            $procedimiento = trim((string) ($_POST['procedimiento'] ?? ''));
            $aceptaRiesgos = isset($_POST['acepta_riesgos']) && $_POST['acepta_riesgos'] !== '0';
            $firmaCliente = trim((string) ($_POST['firma_cliente'] ?? ''));
            $acudienteNombre = trim((string) ($_POST['acudiente_nombre'] ?? ''));
            $acudienteDocumento = trim((string) ($_POST['acudiente_documento'] ?? ''));
            $parentesco = trim((string) ($_POST['parentesco'] ?? ''));
            $firmaAcudiente = trim((string) ($_POST['firma_acudiente'] ?? ''));

            if ($nombre === '' || $documento === '' || $fechaNacimiento === '' || $procedimiento === ''
                || !$aceptaRiesgos || $firmaCliente === ''
                || !preg_match('/^\d+$/', $documento)) {
                $this->json(false, 'Completa todos los campos requeridos del consentimiento.', [], 422);
            }

            // El formulario no pide "cita_id" directamente: se ubica por el documento del cliente,
            // que debe tener una cuenta y una cita agendada previamente en abono.
            $user = $this->userModel->findByDocumento($documento);
            if (!$user) {
                $this->json(false, 'No existe un cliente registrado con ese documento. Debe crear su cuenta primero.', [], 422);
            }

            $fn = new DateTime($fechaNacimiento);
            $ahora = new DateTime();
            $edad = $ahora->diff($fn)->y;
            if ($edad < 15) {
                $this->json(false, 'El cliente es menor de 15 años. No se permite el procedimiento sin acompañante legal autorizado.', [], 422);
            }

            $appointment = $this->appointmentModel->latestPendingConsent((int) $user['id']);
            if (!$appointment) {
                $this->json(false, 'Este cliente no tiene una cita agendada pendiente de consentimiento. Agenda su cita en "Reservar cita" primero.', [], 422);
            }

            $consentId = $this->consentModel->create([
                'cita_id' => $appointment['id'],
                'nombre_cliente' => $nombre,
                'documento' => $documento,
                'fecha_nacimiento' => $fechaNacimiento,
                'procedimiento' => $procedimiento,
                'acepta_riesgos' => $aceptaRiesgos,
                'firma_cliente' => $firmaCliente,
                'acudiente_nombre' => $acudienteNombre,
                'acudiente_documento' => $acudienteDocumento,
                'parentesco' => $parentesco,
                'firma_acudiente' => $firmaAcudiente,
            ]);

            $this->json(true, 'Consentimiento informado registrado y firmado con éxito.', ['consent_id' => $consentId]);
        } catch (Throwable $e) {
            error_log($e->getMessage());
            $this->json(false, 'No fue posible registrar el consentimiento. Verifica la conexión a la base de datos.', [], 500);
        }
    }
}