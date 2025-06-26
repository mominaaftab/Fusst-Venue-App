<?php
// backend/helpers/auth.php

session_start();

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

function isAdmin(): bool
{
    return (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized: Please log in.']);
        exit;
    }
}

function requireAdmin(): void
{
    if (!isLoggedIn() || !isAdmin()) {
        http_response_code(403);
        echo json_encode(['error' => 'Forbidden: Admin access required.']);
        exit;
    }
}

function loginUser(int $userId, string $email, string $role): void
{
    $_SESSION['user_id'] = $userId;
    $_SESSION['email']   = $email;
    $_SESSION['role']    = $role;
}

function logoutUser(): void
{
    session_unset();
    session_destroy();
}
