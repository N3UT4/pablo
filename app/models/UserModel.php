<?php

require_once APP_ROOT . '/core/Model.php';

class UserModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function findByEmail(string $email): ?array
    {
        $statement = $this->execute(
            'SELECT id, nombre, email, password, telefono, documento, fecha_nacimiento, created_at
             FROM users WHERE email = :email LIMIT 1',
            ['email' => strtolower(trim($email))]
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
        ];
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
