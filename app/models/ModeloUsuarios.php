<?php
// =====================================================================
// FILE: app/models/ModeloUsuarios.php
// =====================================================================
// DESCRIPCIÓN: Modelo de datos para la tabla 'users'. Gestiona registro de usuarios, autenticación (login), manejo de sesión, verificación de roles, contraseñas temporales y operaciones CRUD completas (create, read, update, delete) de usuarios.
// UBICACIÓN MVC: Model
// ¿POR QUÉ EXISTE? Encapsula todas las operaciones de la base de datos relacionadas con usuarios. Evita escribir SQL directamente en los controladores y centraliza la lógica de autenticación y roles.
// CÓMO SE USA: Instanciado por ControladorAutenticacion y ControladorDashboard (inyectado como dependencia). Los métodos retornan arrays asociativos o PDOStatement para operaciones de conteo.
// CAMPOS DE LA TABLA users:
//   id, nombre, email, password, telefono, documento, fecha_nacimiento, rol,
//   artist_id, temp_password, temp_password_active, created_at
// MÉTODOS CLAVE POR CATEGORÍA:
//   Autenticación: findByEmail(), login(), register(), setSessionUser(), currentUser(), logout()
//   Roles: isLoggedIn(), hasRole(), isAdmin(), isTatuador(), isStaff()
//   CRUD: all(), find(), save(), update(), deleteAccount()
//   Contraseña temporal: setTemporaryPassword(), isTempPasswordActive(), deactivateTempPassword()
//   Relación artista: findArtistByUser() — JOIN users ↔ artists
// SEGURIDAD: Las contraseñas se hashean con password_hash(PASSWORD_DEFAULT). El login usa password_verify(). Los emails se normalizan a minúsculas. El borrado de cuenta elimina primero datos relacionados para mantener integridad referencial.
// RECURSOS: Hereda de ModeloBase (conexión PDO, método execute()).
// =====================================================================

// Modelo de datos para usuarios.
// Gestiona registro, login, sesión, roles, contraseñas temporales
// y operaciones CRUD de usuarios.
require_once DIR_PATH . 'core/ModeloBase.php';

class ModeloUsuarios extends ModeloBase
{
    public function __construct()
    {
        parent::__construct();
    }

    // Busca un usuario por email (para login).
    // Normaliza el email a minúsculas y elimina espacios.
    // Retorna el hash de password para poder verificarlo en login().
    public function findByEmail(string $email): ?array
    {
        // SELECT con parámetro nombrado :email — evita inyección SQL
        $statement = $this->execute(
            'SELECT id, nombre, email, password, telefono, documento, fecha_nacimiento, rol, artist_id, created_at
             FROM users WHERE email = :email LIMIT 1',
            ['email' => strtolower(trim($email))]
        );

        $user = $statement->fetch();
        return $user ?: null;
    }

    // Busca un usuario por documento de identidad.
    // Usado por ControladorConsentimiento para ubicar al cliente.
    public function findByDocumento(string $documento): ?array
    {
        // SELECT con parámetro nombrado :documento
        $statement = $this->execute(
            'SELECT id, nombre, email, telefono, documento FROM users WHERE documento = :documento LIMIT 1',
            ['documento' => trim($documento)]
        );

        $user = $statement->fetch();
        return $user ?: null;
    }

    // Registra un nuevo usuario con contraseña hasheada.
    // Normaliza email a minúsculas. Lanza excepción si el email ya existe.
    public function register(array $data): array
    {
        $email = strtolower(trim((string) ($data['email'] ?? '')));
        $nombre = trim((string) ($data['nombre'] ?? ''));
        $password = (string) ($data['password'] ?? '');

        // Validación de datos obligatorios antes de tocar la BD
        if ($email === '' || $nombre === '' || $password === '') {
            throw new InvalidArgumentException('Faltan datos obligatorios.');
        }

        // Previene duplicados de email
        if ($this->findByEmail($email)) {
            throw new InvalidArgumentException('Este correo ya está registrado.');
        }

        // INSERT con parámetros nombrados: password se hashea con PASSWORD_DEFAULT (bcrypt/argon2)
        $this->execute(
            'INSERT INTO users (nombre, email, password, telefono, documento, fecha_nacimiento)
             VALUES (:nombre, :email, :password, :telefono, :documento, :fecha_nacimiento)',
            [
                'nombre' => $nombre,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'telefono' => trim((string) ($data['telefono'] ?? '')),
                'documento' => trim((string) ($data['documento'] ?? '')),
                'fecha_nacimiento' => trim((string) ($data['fecha_nacimiento'] ?? '')),
            ]
        );

        return $this->findByEmail($email);
    }

    // Guarda (crea o actualiza) un usuario según si tiene ID.
    public function save(int $id, array $data): void
    {
        if ($id > 0) {
            $this->update($id, $data);
        } else {
            if (empty($data['password'])) {
                throw new InvalidArgumentException('La contraseña es obligatoria al crear un usuario.');
            }
            $this->register($data);
        }
    }

    // Verifica credenciales de login. Retorna el usuario o null.
    public function login(string $email, string $password): ?array
    {
        $user = $this->findByEmail(strtolower(trim($email)));
        if (!$user) {
            return null;
        }

        if (!isset($user['password']) || !password_verify($password, $user['password'])) {
            return null;
        }

        return $user;
    }

    // Retorna el usuario de la sesión actual.
    public function currentUser(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    // Almacena los datos del usuario en la sesión.
    // También busca si tiene un artista asociado y guarda el artist_id en sesión.
    // Estructura de $_SESSION['user']: {id, nombre, email, rol, artist_id?}
    public function setSessionUser(array $user): void
    {
        $_SESSION['user'] = [
            'id' => $user['id'] ?? '',
            'nombre' => $user['nombre'] ?? '',
            'email' => $user['email'] ?? '',
            'rol' => $user['rol'] ?? 'cliente',
        ];
        $_SESSION['role'] = $_SESSION['user']['rol'];

        $artistUser = $this->findArtistByUser((int) $user['id']);
        if ($artistUser !== null) {
            $_SESSION['user']['artist_id'] = $artistUser['id'];
            $_SESSION['artist_id'] = $artistUser['id'];
        }
    }

    // Busca un artista asociado a un usuario por la relación user.artist_id.
    public function findArtistByUser(int $userId): ?array
    {
        $statement = $this->execute(
            'SELECT a.id, a.nombre, a.bio, a.foto, a.activo
             FROM artists a
             JOIN users u ON u.artist_id = a.id
             WHERE u.id = :user_id LIMIT 1',
            ['user_id' => $userId]
        );
        $row = $statement->fetch();
        return $row ?: null;
    }

    // --- Control de acceso por roles (rúbrica: Autenticación y Roles) ---

    // Verifica si hay un usuario logueado.
    public function isLoggedIn(): bool
    {
        return !empty($_SESSION['user']['id']);
    }

    // Verifica si el usuario tiene alguno de los roles indicados.
    // Lee el rol desde $_SESSION['user']['rol'].
    public function hasRole(string ...$roles): bool
    {
        // in_array con true para comparación estricta (evita coerción de tipos)
        $actual = $_SESSION['user']['rol'] ?? null;
        return $actual !== null && in_array($actual, $roles, true);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isTatuador(): bool
    {
        return $this->hasRole('tatuador');
    }

    public function isStaff(): bool
    {
        return $this->hasRole('admin', 'tatuador');
    }

    // Listado de todos los usuarios (solo administrador). CRUD - Read.
    public function all(): array
    {
        $statement = $this->execute(
            'SELECT id, nombre, email, telefono, documento, fecha_nacimiento, rol, artist_id, created_at
             FROM users ORDER BY id ASC'
        );
        return $statement->fetchAll();
    }

    // Búsqueda por id. CRUD - Read.
    public function find(int $id): ?array
    {
        $statement = $this->execute(
            'SELECT id, nombre, email, telefono, documento, fecha_nacimiento, rol, artist_id, created_at
             FROM users WHERE id = :id LIMIT 1',
            ['id' => $id]
        );
        $row = $statement->fetch();
        return $row ?: null;
    }

    // Actualización de datos y/o rol. CRUD - Update.
    // Construye dinámicamente la cláusula SET solo con los campos presentes en $data.
    // Si $data['password'] está presente, la hashea antes de guardar.
    public function update(int $id, array $data): bool
    {
        // Lista blanca de campos actualizables (protege contra inyección por campo)
        $campos = ['nombre', 'email', 'telefono', 'documento', 'fecha_nacimiento', 'rol'];
        $sets = [];
        // Parámetros con 'id' siempre presente para el WHERE
        $params = ['id' => $id];

        foreach ($campos as $campo) {
            if (array_key_exists($campo, $data)) {
                $sets[] = "$campo = :$campo";
                $params[$campo] = $data[$campo];
            }
        }

        if (!empty($data['password'])) {
            $sets[] = 'password = :password';
            $params['password'] = password_hash((string) $data['password'], PASSWORD_DEFAULT);
        }

        if (empty($sets)) {
            return false;
        }

        $this->execute('UPDATE users SET ' . implode(', ', $sets) . ' WHERE id = :id', $params);
        return true;
    }

    // Elimina la sesión del usuario (logout).
    public function logout(): void
    {
        unset($_SESSION['user']);
    }

    // Genera y almacena una contraseña temporal para el usuario.
    public function setTemporaryPassword(int $userId): void
    {
        $temp = bin2hex(random_bytes(6));
        $this->execute(
            'UPDATE users SET temp_password = :temp, temp_password_active = 1 WHERE id = :id',
            ['temp' => password_hash($temp, PASSWORD_DEFAULT), 'id' => $userId]
        );
    }

    // Verifica si el usuario tiene una contraseña temporal activa.
    public function isTempPasswordActive(int $userId): bool
    {
        $stmt = $this->execute(
            'SELECT temp_password, temp_password_active FROM users WHERE id = :id',
            ['id' => $userId]
        );
        $row = $stmt->fetch();
        return $row && (bool) $row['temp_password_active'] && $row['temp_password'] !== null;
    }

    // Desactiva la contraseña temporal tras el cambio exitoso.
    public function deactivateTempPassword(int $userId): void
    {
        $this->execute(
            'UPDATE users SET temp_password = NULL, temp_password_active = 0 WHERE id = :id',
            ['id' => $userId]
        );
    }

    // Elimina la cuenta del usuario de la base de datos.
    // Primero elimina datos relacionados para mantener la integridad referencial.
    // Orden: payments → consents → appointments → promotion_redemptions → users
    public function deleteAccount(int $userId): void
    {
        if ($userId <= 0) {
            throw new InvalidArgumentException('La cuenta no es válida.');
        }

        // DELETE en cascada manual: elimina pagos de citas del usuario
        $this->execute('DELETE FROM payments WHERE appointment_id IN (SELECT id FROM appointments WHERE user_id = :id)', ['id' => $userId]);
        // Elimina consentimientos de las citas del usuario
        $this->execute('DELETE FROM consents WHERE appointment_id IN (SELECT id FROM appointments WHERE user_id = :id)', ['id' => $userId]);
        // Elimina las citas del usuario
        $this->execute('DELETE FROM appointments WHERE user_id = :id', ['id' => $userId]);
        // Elimina canjes de promociones del usuario
        $this->execute('DELETE FROM promotion_redemptions WHERE user_id = :id', ['id' => $userId]);
        // Finalmente elimina el usuario
        $this->execute('DELETE FROM users WHERE id = :id', ['id' => $userId]);
    }
}
