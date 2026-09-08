<?php

require_once APP_ROOT . '/app/models/UserModel.php';

class AuthController extends Controller
{
    private ?UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    private function requireUserModel(): UserModel
    {
        if ($this->userModel === null) {
            throw new RuntimeException('La base de datos no está disponible.');
        }

        return $this->userModel;
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
                || trim($data['fecha_nacimiento']) === ''
            ) {
                throw new InvalidArgumentException('Datos inválidos para el registro.');
            }

            $userModel = $this->requireUserModel();
            $user = $userModel->register($data);
            $userModel->setSessionUser($user);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Cuenta creada correctamente.'];
            $this->redirect('index.php?action=dashboard');
        } catch (InvalidArgumentException $e) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => $e->getMessage()];
            $this->redirect('index.php?action=register');
        } catch (RuntimeException $e) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'La base de datos no está disponible. Crea la base para usar registro e inicio de sesión.'];
            $this->redirect('index.php?action=register');
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'No fue posible crear la cuenta. Inténtalo más tarde.'];
            $this->redirect('index.php?action=register');
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

            $userModel = $this->requireUserModel();
            $user = $userModel->login($email, $password);
            if (!$user) {
                throw new InvalidArgumentException('Correo o contraseña incorrectos.');
            }

            $userModel->setSessionUser($user);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Inicio de sesión correcto.'];
            $this->redirect('index.php?action=dashboard');
        } catch (InvalidArgumentException $e) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => $e->getMessage()];
            $this->redirect('index.php?action=login');
        } catch (RuntimeException $e) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'La base de datos no está disponible. Crea la base para usar registro e inicio de sesión.'];
            $this->redirect('index.php?action=login');
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'No fue posible iniciar sesión. Inténtalo más tarde.'];
            $this->redirect('index.php?action=login');
        }
    }

    public function logout(): void
    {
        if ($this->userModel !== null) {
            $this->userModel->logout();
        }
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Sesión cerrada.'];
        $this->redirect('index.php?action=home');
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

            $userModel = $this->requireUserModel();
            $userModel->deleteAccount($userId);
            $_SESSION = [];
            session_regenerate_id(true);
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Tu cuenta fue eliminada correctamente.'];
            $this->redirect('index.php?action=home');
        } catch (InvalidArgumentException $e) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => $e->getMessage()];
            $this->redirect('index.php?action=dashboard');
        } catch (RuntimeException $e) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'La base de datos no está disponible. Crea la base para usar registro e inicio de sesión.'];
            $this->redirect('index.php?action=dashboard');
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'No fue posible eliminar la cuenta. Inténtalo más tarde.'];
            $this->redirect('index.php?action=dashboard');
        }
    }
}
