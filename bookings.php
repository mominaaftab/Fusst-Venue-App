<?php
// backend/routes/bookings.php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

require_once __DIR__ . '/../controllers/bookingController.php';

$action = $_GET['action'] ?? null;
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (in_array($action, ['listAll', 'listUser'], true)) {
            BookingController::listBookings($action);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Missing or invalid action for GET.']);
            exit;
        }
        break;

    case 'POST':
        if ($action === 'create') {
            $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
            if (stripos($contentType, 'application/json') !== false) {
                $input = json_decode(file_get_contents('php://input'), true);
            } else {
                $input = $_POST;
            }
            BookingController::createBooking($input);
        } elseif ($action === 'updateStatus') {
            $id = intval($_GET['id'] ?? 0);
            if (!$id) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing booking ID.']);
                exit;
            }
            $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
            if (stripos($contentType, 'application/json') !== false) {
                $input = json_decode(file_get_contents('php://input'), true);
            } else {
                $input = $_POST;
            }
            BookingController::updateStatus($id, $input);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action for POST.']);
            exit;
        }
        break;

    case 'DELETE':
        if ($action === 'delete') {
            $id = intval($_GET['id'] ?? 0);
            if (!$id) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing booking ID.']);
                exit;
            }
            BookingController::deleteBooking($id);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action for DELETE.']);
            exit;
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
        exit;
}
