<?php
// =====================================================================
// FILE: app/controllers/ControladorDashboard.php
// =====================================================================
// DESCRIPCIÓN: Controlador principal del dashboard. Gestiona las vistas del cliente (mis citas, abonos, consentimiento, agendar), las operaciones de administración (métricas, usuarios, servicios, transacciones) y las operaciones CRUD que requieren permisos de admin. Desacopla la lógica de negocio a DashboardService y los chequeos de acceso a AuthHelper.
// UBICACIÓN MVC: Controller
// ¿POR QUÉ EXISTE? Unifica el manejo de la sección privada de la aplicación. Los clientes ven sus citas y pagos; los administradores gestionan usuarios, servicios y transacciones; los tatuadores acceden a su panel específico (ver ControladorTatuador).
// CÓMO SE USA: El Enrutador despacha las acciones de dashboard, cliente-*, admin-* a los métodos de este controlador. Cada método llama a AuthHelper::guard() para verificar roles antes de ejecutar.
// DEPENDENCIAS:
//   - Models: ModeloUsuarios, ModeloCitas, ModeloPagos, ModeloConsentimiento, ModeloServicios
//   - Service: DashboardService (consulta métricas, transacciones, usuarios, servicios)
//   - Helper: AuthHelper (verificación de roles y acceso)
// MÉTODOS CLAVE:
//   - clienteCitas/abonos/consentimiento/agendar: vistas de usuario cliente, protegidas por AuthHelper.
//   - adminMetrics: métricas del dashboard (total citas, estados, abonos, últimas citas).
//   - adminUsuarios/adminServicios/adminTransacciones: listados para gestión.
//   - adminSaveUser/adminSaveService: crea/actualiza usuarios/servicios (valida CSRF + campos).
//   - adminDeleteService: elimina un servicio por ID.
// ESTRUCTURA DE DATOS: Cada método pasa un array $data a view() con 'title', 'pageTitle', 'currentPage', y los datos específicos (citas, usuarios, servicios, transacciones, metrics, user).
// SEGURIDAD: AuthHelper::guard('admin', 'login') impide acceso no autorizado. verify_csrf_token() en adminSaveUser y adminSaveService.
// =====================================================================

// Controlador del dashboard principal. Gestiona las vistas del cliente,
// administrador y las operaciones CRUD de usuarios, servicios y transacciones.
// Usa DashboardService y AuthHelper para desacoplar lógica de negocio y acceso.
require_once DIR_PATH . 'app/models/ModeloUsuarios.php';
require_once DIR_PATH . 'app/models/ModeloCitas.php';
require_once DIR_PATH . 'app/models/ModeloPagos.php';
require_once DIR_PATH . 'app/models/ModeloConsentimiento.php';
require_once DIR_PATH . 'app/models/ModeloServicios.php';
require_once DIR_PATH . 'app/services/DashboardService.php';
require_once DIR_PATH . 'app/helpers/AuthHelper.php';

class ControladorDashboard extends ControladorBase
{
    private ModeloUsuarios $userModel;
    private ModeloCitas $citaModel;
    private ModeloPagos $pagoModel;
    private ModeloConsentimiento $consentModel;
    private ModeloServicios $serviceModel;
    private DashboardService $dashService;
    private AuthHelper $authHelper;

    public function __construct()
    {
        $this->userModel = new ModeloUsuarios();
        $this->citaModel = new ModeloCitas();
        $this->pagoModel = new ModeloPagos();
        $this->consentModel = new ModeloConsentimiento();
        $this->serviceModel = new ModeloServicios();
        $this->dashService = new DashboardService($this->citaModel, $this->userModel, $this->serviceModel);
        $this->authHelper = new AuthHelper($this->userModel);
    }

    // Lista las citas del usuario cliente.
    public function clienteCitas(): void
    {
        $this->authHelper->guard(['cliente', 'tatuador', 'admin'], 'login');
        $userId = (int) ($_SESSION['user']['id'] ?? 0);
        $citas = [];

        try {
            $citas = $this->citaModel->listByUser($userId);
        } catch (Throwable $e) {
            error_log('[Dashboard] Error cargando citas del cliente: ' . $e->getMessage());
        }

        $this->view('cliente-citas', [
            'title' => 'Mis Citas — ITZA TATTOO',
            'pageTitle' => 'Mis Citas',
            'currentPage' => 'dashboard',
            'citas' => $citas,
            'user' => $_SESSION['user'],
        ]);
    }

    // Lista los abonos/pagos del usuario cliente.
    public function clienteAbonos(): void
    {
        $this->authHelper->guard(['cliente', 'tatuador', 'admin'], 'login');
        $userId = (int) ($_SESSION['user']['id'] ?? 0);
        $citas = [];

        try {
            $citas = $this->citaModel->listByUser($userId);
        } catch (Throwable $e) {
            error_log('[Dashboard] Error cargando abonos del cliente: ' . $e->getMessage());
        }

        $this->view('cliente-abonos', [
            'title' => 'Abonos — ITZA TATTOO',
            'pageTitle' => 'Mis Abonos',
            'currentPage' => 'dashboard',
            'citas' => $citas,
            'user' => $_SESSION['user'],
        ]);
    }

    // Muestra el formulario de consentimiento informado pendiente.
    public function clienteConsentimiento(): void
    {
        $this->authHelper->guard(['cliente', 'tatuador', 'admin'], 'login');
        $userId = (int) ($_SESSION['user']['id'] ?? 0);
        $cita = null;

        try {
            $cita = $this->citaModel->latestPendingConsent($userId);
        } catch (Throwable $e) {
            error_log('[Dashboard] Error cargando consentimiento: ' . $e->getMessage());
        }

        $this->view('cliente-consentimiento', [
            'title' => 'Consentimiento Informado — ITZA TATTOO',
            'pageTitle' => 'Consentimiento Informado',
            'currentPage' => 'dashboard',
            'cita' => $cita,
            'user' => $_SESSION['user'],
        ]);
    }

    // Muestra la página para agendar una nueva cita.
    public function clienteAgendar(): void
    {
        $this->authHelper->guard(['cliente', 'tatuador', 'admin'], 'login');
        $servicios = [];

        try {
            $servicios = $this->serviceModel->listActive();
        } catch (Throwable $e) {
            error_log('[Dashboard] Error cargando servicios: ' . $e->getMessage());
        }

        $this->view('cliente-agendar', [
            'title' => 'Agendar Cita — ITZA TATTOO',
            'pageTitle' => 'Agendar Cita',
            'currentPage' => 'dashboard',
            'servicios' => $servicios,
            'user' => $_SESSION['user'],
        ]);
    }

    // Panel de métricas para administradores (total de citas, estados, abonos, etc.).
    public function adminMetrics(): void
    {
        $this->authHelper->guard('admin', 'login');
        $metrics = $this->dashService->getMetrics();

        $this->view('admin-metrics', [
            'title' => 'Métricas — ITZA TATTOO',
            'pageTitle' => 'Métricas Generales',
            'currentPage' => 'dashboard',
            'metrics' => $metrics,
            'user' => $_SESSION['user'],
        ]);
    }

    // Lista todos los usuarios para administración.
    public function adminUsuarios(): void
    {
        $this->authHelper->guard('admin', 'login');
        $usuarios = $this->dashService->getUsuarios();

        $this->view('admin-usuarios', [
            'title' => 'Gestión de Usuarios — ITZA TATTOO',
            'pageTitle' => 'Usuarios y Tatuadores',
            'currentPage' => 'dashboard',
            'usuarios' => $usuarios,
            'user' => $_SESSION['user'],
        ]);
    }

    // Lista los servicios y precios para administración.
    public function adminServicios(): void
    {
        $this->authHelper->guard('admin', 'login');
        $servicios = $this->dashService->getServicios();

        $this->view('admin-servicios', [
            'title' => 'Servicios y Precios — ITZA TATTOO',
            'pageTitle' => 'Servicios y Precios',
            'currentPage' => 'dashboard',
            'servicios' => $servicios,
            'user' => $_SESSION['user'],
        ]);
    }

    // Lista las transacciones/pagos para administración.
    public function adminTransacciones(): void
    {
        $this->authHelper->guard('admin', 'login');
        $transacciones = $this->dashService->getTransacciones(200);

        $this->view('admin-transacciones', [
            'title' => 'Transacciones — ITZA TATTOO',
            'pageTitle' => 'Registro de Transacciones',
            'currentPage' => 'dashboard',
            'transacciones' => $transacciones,
            'user' => $_SESSION['user'],
        ]);
    }

    // Crea o actualiza un usuario (administrador).
    public function adminSaveUser(): void
    {
        $this->authHelper->guard('admin', 'login');

        // Verificación de token CSRF: protege contra ataques Cross-Site Request Forgery
        if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'La sesión del formulario expiró.'];
            $this->redirect(BASE_URL . 'index.php?action=admin-usuarios');
            return;
        }

        $userId = (int) ($_POST['user_id'] ?? 0);
        $nombre = trim((string) ($_POST['nombre'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $telefono = trim((string) ($_POST['telefono'] ?? ''));
        $documento = trim((string) ($_POST['documento'] ?? ''));
        $rol = trim((string) ($_POST['rol'] ?? 'cliente'));
        $password = trim((string) ($_POST['password'] ?? ''));

        if ($nombre === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || !in_array($rol, ['cliente', 'tatuador', 'admin'], true)) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Datos inválidos para el usuario.'];
            $this->redirect(BASE_URL . 'index.php?action=admin-usuarios');
            return;
        }

        if ($userId === 0 && $password === '') {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'La contraseña es obligatoria al crear un usuario.'];
            $this->redirect(BASE_URL . 'index.php?action=admin-usuarios');
            return;
        }

        $data = [
            'nombre' => $nombre,
            'email' => $email,
            'telefono' => $telefono,
            'documento' => $documento,
            'rol' => $rol,
        ];
        if ($password !== '') {
            $data['password'] = $password;
        }

        try {
            $this->userModel->save($userId, $data);
            $_SESSION['flash'] = ['type' => 'success', 'message' => $userId > 0 ? 'Usuario actualizado correctamente.' : 'Usuario creado correctamente.'];
        } catch (Throwable $e) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => $e->getMessage()];
        }

        $this->redirect(BASE_URL . 'index.php?action=admin-usuarios');
    }

    // Crea o actualiza un servicio (administrador).
    public function adminSaveService(): void
    {
        $this->authHelper->guard('admin', 'login');

        // Verificación de token CSRF: protege contra ataques Cross-Site Request Forgery
        if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'La sesión del formulario expiró.'];
            $this->redirect(BASE_URL . 'index.php?action=admin-servicios');
            return;
        }

        $serviceId = (int) ($_POST['service_id'] ?? 0);
        $nombre = trim((string) ($_POST['nombre'] ?? ''));
        $slug = trim((string) ($_POST['slug'] ?? ''));
        $precio = (float) ($_POST['precio_desde'] ?? 0);
        $descripcion = trim((string) ($_POST['descripcion'] ?? ''));

        if ($nombre === '' || $slug === '') {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Nombre y slug son obligatorios.'];
            $this->redirect(BASE_URL . 'index.php?action=admin-servicios');
            return;
        }

        try {
            if ($serviceId > 0) {
                $this->serviceModel->save($serviceId, $nombre, $slug, $precio, $descripcion);
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Servicio actualizado.'];
            } else {
                $this->serviceModel->save(0, $nombre, $slug, $precio, $descripcion);
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Servicio creado.'];
            }
        } catch (Throwable $e) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Error al guardar el servicio: ' . $e->getMessage()];
        }

        $this->redirect(BASE_URL . 'index.php?action=admin-servicios');
    }

    // Elimina un servicio (administrador).
    public function adminDeleteService(): void
    {
        $this->authHelper->guard('admin', 'login');

        $serviceId = (int) ($_GET['id'] ?? 0);
        if ($serviceId > 0) {
            try {
                $this->serviceModel->delete($serviceId);
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Servicio eliminado.'];
            } catch (Throwable $e) {
                $_SESSION['flash'] = ['type' => 'error', 'message' => 'Error al eliminar el servicio: ' . $e->getMessage()];
            }
        }

        $this->redirect(BASE_URL . 'index.php?action=admin-servicios');
    }
}
