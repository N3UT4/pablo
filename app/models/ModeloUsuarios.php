<?php

require_once APP_ROOT . '/core/ModeloBase.php';

class ModeloUsuarios extends ModeloBase
{
    public function __construct()
    {
        parent::__construct();
    }

    public function findByEmail(string $email): ?array
    {
        $statement = $this->execute(
            'SELECT id, nombre, email, password, telefono, documento, fecha_nacimiento, rol, created_at
             FROM users WHERE email = :email LIMIT 1',
            ['email' => strtolower(trim($email))]
        );

        $user = $statement->fetch();
        return $user ?: null;
    }

    // Usado por ControladorConsentimiento para ubicar al cliente a partir de su documento.
    public function findByDocumento(string $documento): ?array
    {
        $statement = $this->execute(
            'SELECT id, nombre, email, telefono, documento FROM users WHERE documento = :documento LIMIT 1',
            ['documento' => trim($documento)]
        );

        $user = $statement->fetch();
        return $user ?: null;
    }

    public function register(array $data): array
    {
        $email = strtolower(trim((string) ($data['email'] ?? '')));
        $nombre = trim((string) ($data['nombre'] ?? ''));
        $password = (string) ($data['password'] ?? '');

        if ($email === '' || $nombre === '' || $password === '') {
            throw new InvalidArgumentException('Faltan datos obligatorios.');
        }

        if ($this->findByEmail($email)) {
            throw new InvalidArgumentException('Este correo ya está registrado.');
        }

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

    public function currentUser(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public function setSessionUser(array $user): void
    {
        $_SESSION['user'] = [
            'id' => $user['id'] ?? '',
            'nombre' => $user['nombre'] ?? '',
            'email' => $user['email'] ?? '',
            'rol' => $user['rol'] ?? 'cliente',
        ];
    }

    // --- Control de acceso por roles (rúbrica: Autenticación y Roles) ---

    public function isLoggedIn(): bool
    {
        return !empty($_SESSION['user']['id']);
    }

    public function hasRole(string ...$roles): bool
    {
        $actual = $_SESSION['user']['rol'] ?? null;
        return $actual !== null && in_array($actual, $roles, true);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isStaff(): bool
    {
        return $this->hasRole('admin', 'tatuador');
    }

    // Listado de todos los usuarios (solo administrador). CRUD - Read.
    public function all(): array
    {
        $statement = $this->execute(
            'SELECT id, nombre, email, telefono, documento, fecha_nacimiento, rol, created_at
             FROM users ORDER BY id ASC'
        );
        return $statement->fetchAll();
    }

    // Búsqueda por id. CRUD - Read.
    public function find(int $id): ?array
    {
        $statement = $this->execute(
            'SELECT id, nombre, email, telefono, documento, fecha_nacimiento, rol, created_at
             FROM users WHERE id = :id LIMIT 1',
            ['id' => $id]
        );
        $row = $statement->fetch();
        return $row ?: null;
    }

    // Actualización de datos y/o rol. CRUD - Update.
    public function update(int $id, array $data): bool
    {
        $campos = ['nombre', 'email', 'telefono', 'documento', 'fecha_nacimiento', 'rol'];
        $sets = [];
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

    public function logout(): void
    {
        unset($_SESSION['user']);
    }

    public function deleteAccount(int $userId): void
    {
        if ($userId <= 0) {
            throw new InvalidArgumentException('La cuenta no es válida.');
        }

        $this->execute('DELETE FROM users WHERE id = :id', ['id' => $userId]);
    }
}