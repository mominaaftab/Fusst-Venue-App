<?php
// backend/routes/venues.php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

require_once __DIR__ . '/../controllers/venueController.php';

$action = $_GET['action'] ?? null;
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        VenueController::listVenues();
        break;

    case 'POST':
        if ($action === 'create') {
            $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
            if (stripos($contentType, 'application/json') !== false) {
                $input = json_decode(file_get_contents('php://input'), true);
            } else {
                $input = $_POST;
            }
            VenueController::addVenue($input);
        } elseif ($action === 'update') {
            $id = intval($_GET['id'] ?? 0);
            if (!$id) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing venue ID.']);
                exit;
            }
            $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
            if (stripos($contentType, 'application/json') !== false) {
                $input = json_decode(file_get_contents('php://input'), true);
            } else {
                $input = $_POST;
            }
            VenueController::updateVenue($id, $input);
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
                echo json_encode(['error' => 'Missing venue ID.']);
                exit;
            }
            VenueController::deleteVenue($id);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action for DELETE.']);
            exit;
        }
        break;

    case 'PUT':
        if ($action === 'update') {
            $id = intval($_GET['id'] ?? 0);
            if (!$id) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing venue ID.']);
                exit;
            }
            $input = json_decode(file_get_contents('php://input'), true);
            VenueController::updateVenue($id, $input);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action for PUT.']);
            exit;
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
        exit;
}
