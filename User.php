<?php
// backend/models/User.php

require_once __DIR__ . '/../config/db.php';

class User
{
    public int    $id;
    public string $name;
    public string $email;
    public string $password_hash;
    public string $role;
    public string $created_at;

    public function __construct(array $data)
    {
        $this->id            = (int)$data['id'];
        $this->name          = $data['name'];
        $this->email         = $data['email'];
        $this->password_hash = $data['password'];
        $this->role          = $data['role'];
        $this->created_at    = $data['created_at'];
    }

    public static function findByEmail(string $email): ?User
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();
        return $row ? new User($row) : null;
    }

    public static function findById(int $id): ?User
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? new User($row) : null;
    }

    public static function create(string $name, string $email, string $password, string $role = 'user'): ?User
    {
        global $pdo;
        if (self::findByEmail($email) !== null) {
            return null;
        }
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("
            INSERT INTO users (name, email, password, role)
            VALUES (:name, :email, :password, :role)
        ");
        $saved = $stmt->execute([
            'name'     => $name,
            'email'    => $email,
            'password' => $password_hash,
            'role'     => $role
        ]);
        if ($saved) {
            $id = (int)$pdo->lastInsertId();
            return self::findById($id);
        }
        return null;
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password_hash);
    }
}
