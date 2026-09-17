<?php

require_once DIR_PATH . 'app/models/ModeloUsuarios.php';

class ControladorAutenticacion extends ControladorBase
{
    private ModeloUsuarios $userModel;

    public function __construct()
    {
        $this->userModel = new ModeloUsuarios();
    }

    public function register(): void
    {
        try {
            if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
                throw new InvalidArgumentException('La sesión del formulario expiró. Inténtalo de nuevo.');
            }

            $data = [
                'nombre' => $_POST['nombre_completo'] ?? '',
                'email' => $_POST['email'] ?? '',
                'telefono' => $_POST['telefono'] ?? '',
                'documento' => $_POST['documento'] ?? '',
                'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?? '',
                'password' => $_POST['password_hash'] ?? '',
            ];

            if (
                trim($data['nombre']) === ''
                || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)
                || strlen($data['password']) < 6
                || $data['password'] !== ($_POST['password2'] ?? '')
                || trim($data['telefono']) === ''
                || trim($data['documento']) === ''
                || !preg_match('/^\d+$/', $data['documento'])
                || trim($data['fecha_nacimiento']) === ''
            ) {
                throw new InvalidArgumentException('Datos inválidos para el registro.');
            }

            $user = $this->userModel->register($data);
            $this->userModel->setSessionUser($user);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Cuenta creada correctamente.'];
            $this->redirect(BASE_URL . 'index.php?action=dashboard');
        } catch (InvalidArgumentException $e) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => $e->getMessage()];
            $this->redirect(BASE_URL . 'index.php?action=register');
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'No fue posible crear la cuenta. Inténtalo más tarde.'];
            $this->redirect(BASE_URL . 'index.php?action=register');
        }
    }

    public function login(): void
    {
        try {
            if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
                throw new InvalidArgumentException('La sesión del formulario expiró. Inténtalo de nuevo.');
            }

            $email = trim((string) ($_POST['loginEmail'] ?? ''));
            $password = (string) ($_POST['loginPass'] ?? '');

            $user = $this->userModel->login($email, $password);
            if (!$user) {
                throw new InvalidArgumentException('Correo o contraseña incorrectos.');
            }

            $this->userModel->setSessionUser($user);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Inicio de sesión correcto.'];

            if (($user['rol'] ?? '') === 'tatuador') {
                $this->redirect(BASE_URL . 'index.php?action=artist-panel');
            }
            $this->redirect(BASE_URL . 'index.php?action=dashboard');
        } catch (InvalidArgumentException $e) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => $e->getMessage()];
            $this->redirect(BASE_URL . 'index.php?action=login');
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'No fue posible iniciar sesión. Inténtalo más tarde.'];
            $this->redirect(BASE_URL . 'index.php?action=login');
        }
    }

    public function logout(): void
    {
        $this->userModel->logout();
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Sesión cerrada.'];
        $this->redirect(BASE_URL . 'index.php?action=home');
    }

    public function showTempPasswordForm(): void
    {
        try {
            $userId = (int) ($_SESSION['user']['id'] ?? 0);
            if ($userId <= 0) {
                $_SESSION['flash'] = ['type' => 'error', 'message' => 'Debes iniciar sesión primero.'];
                $this->redirect(BASE_URL . 'index.php?action=login');
            }
            if (!$this->userModel->isTempPasswordActive($userId)) {
                $this->redirect(BASE_URL . 'index.php?action=dashboard');
            }
            $tempKey = bin2hex(random_bytes(6));
            $this->userModel->setTemporaryPassword($userId);
            $this->userModel->setSessionUser([
                'id' => $userId,
                'nombre' => $_SESSION['user']['nombre'] ?? '',
                'email' => $_SESSION['user']['email'] ?? '',
                'rol' => $_SESSION['user']['rol'] ?? 'cliente',
            ]);
            $_SESSION['temp_key_display'] = $tempKey;
            $this->redirect(BASE_URL . 'index.php?action=dashboard');
        } catch (Throwable $e) {
            error_log($e->getMessage());
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'No fue posible generar la clave temporal.'];
            $this->redirect(BASE_URL . 'index.php?action=dashboard');
        }
    }

    public function changeTempPassword(): void
    {
        try {
            if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
                throw new InvalidArgumentException('La sesión del formulario expiró. Inténtalo de nuevo.');
            }

            $userId = (int) ($_SESSION['user']['id'] ?? 0);
            if ($userId <= 0) {
                throw new InvalidArgumentException('Debes iniciar sesión primero.');
            }

            if (!$this->userModel->isTempPasswordActive($userId)) {
                $this->redirect(BASE_URL . 'index.php?action=dashboard');
            }

            $password = (string) ($_POST['nueva_clave'] ?? '');
            $password2 = (string) ($_POST['nueva_clave2'] ?? '');

            if (strlen($password) < 6 || $password !== $password2) {
                throw new InvalidArgumentException('Las contraseñas no coinciden o son muy cortas.');
            }

            $this->userModel->update($userId, ['password' => $password]);
            $this->userModel->deactivateTempPassword($userId);

            $this->userModel->setSessionUser([
                'id' => $userId,
                'nombre' => $_SESSION['user']['nombre'] ?? '',
                'email' => $_SESSION['user']['email'] ?? '',
                'rol' => $_SESSION['user']['rol'] ?? 'cliente',
            ]);
            unset($_SESSION['user']['change_temp']);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Contraseña cambiada correctamente.'];
            $this->redirect(BASE_URL . 'index.php?action=dashboard');
        } catch (InvalidArgumentException $e) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => $e->getMessage()];
            $this->redirect(BASE_URL . 'index.php?action=cambiar-clave');
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'No fue posible cambiar la contraseña. Inténtalo más tarde.'];
            $this->redirect(BASE_URL . 'index.php?action=cambiar-clave');
        }
    }

    public function deleteAccount(): void
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new InvalidArgumentException('Solicitud no válida.');
            }

            if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
                throw new InvalidArgumentException('La sesión del formulario expiró. Inténtalo de nuevo.');
            }

            $userId = (int) ($_SESSION['user']['id'] ?? 0);
            if ($userId <= 0) {
                throw new InvalidArgumentException('Debes iniciar sesión para eliminar tu cuenta.');
            }

            $this->userModel->deleteAccount($userId);
            $_SESSION = [];
            session_regenerate_id(true);
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Tu cuenta fue eliminada correctamente.'];
            $this->redirect(BASE_URL . 'index.php?action=home');
        } catch (InvalidArgumentException $e) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => $e->getMessage()];
            $this->redirect(BASE_URL . 'index.php?action=dashboard');
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'No fue posible eliminar la cuenta. Inténtalo más tarde.'];
            $this->redirect(BASE_URL . 'index.php?action=dashboard');
        }
    }
}