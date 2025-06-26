<?php
// backend/controllers/venueController.php

require_once __DIR__ . '/../models/Venue.php';
require_once __DIR__ . '/../helpers/auth.php';

class VenueController
{
    public static function listVenues(): void
    {
        requireLogin();
        $venues = Venue::getAll();
        echo json_encode(['venues' => $venues]);
        exit;
    }

    public static function addVenue(array $input): void
    {
        requireAdmin();

        $name        = trim($input['name'] ?? '');
        $location    = trim($input['location'] ?? '');
        $capacity    = intval($input['capacity'] ?? 0);
        $description = trim($input['description'] ?? '');

        if (!$name || !$location || $capacity < 1 || !$description) {
            http_response_code(400);
            echo json_encode(['error' => 'All fields are required, capacity must be ≥ 1.']);
            exit;
        }

        $venue = Venue::create($name, $location, $capacity, $description);
        if (!$venue) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create venue.']);
            exit;
        }

        echo json_encode([
            'message' => 'Venue created successfully.',
            'venue'   => $venue
        ]);
        exit;
    }

    public static function updateVenue(int $id, array $input): void
    {
        requireAdmin();

        $name        = trim($input['name'] ?? '');
        $location    = trim($input['location'] ?? '');
        $capacity    = intval($input['capacity'] ?? 0);
        $description = trim($input['description'] ?? '');

        if (!$name || !$location || $capacity < 1 || !$description) {
            http_response_code(400);
            echo json_encode(['error' => 'All fields are required, capacity must be ≥ 1.']);
            exit;
        }

        $existing = Venue::findById($id);
        if (!$existing) {
            http_response_code(404);
            echo json_encode(['error' => 'Venue not found.']);
            exit;
        }

        $success = Venue::update($id, $name, $location, $capacity, $description);
        if (!$success) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update venue.']);
            exit;
        }

        echo json_encode(['message' => 'Venue updated successfully.']);
        exit;
    }

    public static function deleteVenue(int $id): void
    {
        requireAdmin();

        $existing = Venue::findById($id);
        if (!$existing) {
            http_response_code(404);
            echo json_encode(['error' => 'Venue not found.']);
            exit;
        }

        $deleted = Venue::delete($id);
        if (!$deleted) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete venue.']);
            exit;
        }

        echo json_encode(['message' => 'Venue deleted successfully.']);
        exit;
    }
}
