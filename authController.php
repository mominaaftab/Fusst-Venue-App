<?php
// backend/controllers/authController.php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../helpers/auth.php';

class AuthController
{
    public static function register(array $input): void
    {
        header('Content-Type: application/json');
        $name     = trim($input['name'] ?? '');
        $email    = trim($input['email'] ?? '');
        $password = $input['password'] ?? '';
        $role     = $input['role'] ?? 'user';

        if (!$name || !$email || !$password) {
            http_response_code(400);
            echo json_encode(['error' => 'Name, email & password are required.']);
            exit;
        }

        if (!in_array($role, ['user', 'admin'], true)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid role.']);
            exit;
        }

        $newUser = User::create($name, $email, $password, $role);
        if (!$newUser) {
            http_response_code(409);
            echo json_encode(['error' => 'Email already exists.']);
            exit;
        }

        loginUser($newUser->id, $newUser->email, $newUser->role);
        echo json_encode([
            'message' => 'Registration successful.',
            'user'    => [
                'id'    => $newUser->id,
                'name'  => $newUser->name,
                'email' => $newUser->email,
                'role'  => $newUser->role
            ]
        ]);
        exit;
    }

    public static function login(array $input): void
    {
        header('Content-Type: application/json');
        $email    = trim($input['email'] ?? '');
        $password = $input['password'] ?? '';

        if (!$email || !$password) {
            http_response_code(400);
            echo json_encode(['error' => 'Email & password are required.']);
            exit;
        }

        $user = User::findByEmail($email);
        if (!$user || !$user->verifyPassword($password)) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid credentials.']);
            exit;
        }

        loginUser($user->id, $user->email, $user->role);
        echo json_encode([
            'message' => 'Login successful.',
            'user'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role
            ]
        ]);
        exit;
    }

    public static function logout(): void
    {
        header('Content-Type: application/json');
        if (isLoggedIn()) {
            logoutUser();
            echo json_encode(['message' => 'Logout successful.']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'No user is currently logged in.']);
        }
        exit;
    }
}
