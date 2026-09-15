<?php

require_once APP_ROOT . '/app/models/ModeloPromociones.php';

// Atiende el formulario de promociones (js/promociones.js).
class ControladorPromociones extends ControladorBase
{
    private ModeloPromociones $promotionModel;

    public function __construct()
    {
        $this->promotionModel = new ModeloPromociones();
    }

    // Botón "Validar cupón": busca el código en la tabla promotions.
    public function validateCode(): void
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->json(false, 'Solicitud no válida.', [], 405);
            }

            $codigo = trim((string) ($_POST['codigo_cupon'] ?? ''));
            if ($codigo === '') {
                $this->json(false, 'Ingresa un código de cupón.', [], 422);
            }

            $promo = $this->promotionModel->findValidByCode($codigo);
            if (!$promo) {
                $this->json(false, 'Este cupón no existe, ya venció o fue desactivado.', [], 404);
            }

            $vigencia = $promo['fecha_fin']
                ? 'Válido hasta: ' . date('d M Y', strtotime($promo['fecha_fin']))
                : 'Sin fecha límite';

            $this->json(true, 'Cupón válido.', [
                'promo' => [
                    'nombre' => $promo['nombre'],
                    'descuento' => $promo['descuento'] !== null ? $promo['descuento'] . '%' : '',
                    'descripcion' => $promo['descripcion'],
                    'vigencia' => $vigencia,
                ],
            ]);
        } catch (Throwable $e) {
            error_log($e->getMessage());
            $this->json(false, 'No fue posible validar el cupón. Verifica la conexión a la base de datos.', [], 500);
        }
    }

    // Envío del formulario: guarda la redención del cupón para el usuario autenticado.
    public function redeem(): void
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->json(false, 'Solicitud no válida.', [], 405);
            }

            if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
                $this->json(false, 'La sesión del formulario expiró. Recarga la página e inténtalo de nuevo.', [], 419);
            }

            $userId = (int) ($_SESSION['user']['id'] ?? 0);
            if ($userId <= 0) {
                $this->json(false, 'Debes iniciar sesión para guardar un cupón en tu cuenta.', ['redirect' => 'index.php?action=login'], 401);
            }

            $codigo = trim((string) ($_POST['codigo_cupon'] ?? ''));
            $email = trim((string) ($_POST['email_cupon'] ?? ''));
            $fechaUso = trim((string) ($_POST['fecha_uso'] ?? ''));
            $acepta = isset($_POST['acepta_terminos']) && $_POST['acepta_terminos'] !== '0';

            if ($codigo === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $fechaUso === '' || !$acepta) {
                $this->json(false, 'Revisa los campos marcados en rojo antes de continuar.', [], 422);
            }

            $promo = $this->promotionModel->findValidByCode($codigo);
            if (!$promo) {
                $this->json(false, 'Valida un cupón antes de continuar.', [], 422);
            }

            if ($this->promotionModel->alreadyRedeemed((int) $promo['id'], $userId)) {
                $this->json(false, 'Ya guardaste este cupón en tu cuenta anteriormente.', [], 409);
            }

            $this->promotionModel->redeem((int) $promo['id'], $userId);

            $this->json(true, 'Cupón "' . $promo['nombre'] . '" registrado en tu cuenta. Úsalo en tu próxima cita.');
        } catch (Throwable $e) {
            error_log($e->getMessage());
            $this->json(false, 'No fue posible guardar el cupón. Verifica la conexión a la base de datos.', [], 500);
        }
    }
}