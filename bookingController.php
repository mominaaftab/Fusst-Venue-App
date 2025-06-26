<?php
// backend/controllers/bookingController.php

require_once __DIR__ . '/../models/Booking.php';
require_once __DIR__ . '/../helpers/auth.php';

class BookingController
{
    public static function listBookings(string $action): void
    {
        header('Content-Type: application/json');
        requireLogin();
        if ($action === 'listAll') {
            requireAdmin();
            $all = Booking::getAll();
            echo json_encode(['bookings' => $all]);
            exit;
        }
        if ($action === 'listUser') {
            $userId = (int)$_SESSION['user_id'];
            $userBookings = Booking::getByUser($userId);
            echo json_encode(['bookings' => $userBookings]);
            exit;
        }
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action.']);
        exit;
    }

    public static function createBooking(array $input): void
    {
        header('Content-Type: application/json');
        requireLogin();
        $userId      = (int)$_SESSION['user_id'];
        $venueId     = intval($input['venue_id'] ?? 0);
        $bookingDate = trim($input['booking_date'] ?? '');
        $startTime   = trim($input['start_time'] ?? '');
        $endTime     = trim($input['end_time'] ?? '');

        if (!$venueId || !$bookingDate || !$startTime || !$endTime) {
            http_response_code(400);
            echo json_encode(['error' => 'All booking fields are required.']);
            exit;
        }

        $newBooking = Booking::create($userId, $venueId, $bookingDate, $startTime, $endTime);
        if (!$newBooking) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create booking.']);
            exit;
        }

        echo json_encode([
            'message' => 'Booking created (pending approval).',
            'booking' => [
                'id'           => $newBooking->id,
                'user_id'      => $newBooking->user_id,
                'venue_id'     => $newBooking->venue_id,
                'booking_date' => $newBooking->booking_date,
                'start_time'   => $newBooking->start_time,
                'end_time'     => $newBooking->end_time,
                'status'       => $newBooking->status,
                'created_at'   => $newBooking->created_at
            ]
        ]);
        exit;
    }

    public static function updateStatus(int $id, array $input): void
    {
        header('Content-Type: application/json');
        requireAdmin();
        $newStatus = trim($input['status'] ?? '');
        if (!in_array($newStatus, ['pending', 'approved', 'cancelled'], true)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid status.']);
            exit;
        }
        $success = Booking::updateStatus($id, $newStatus);
        if (!$success) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update status.']);
            exit;
        }
        echo json_encode(['message' => 'Booking status updated.']);
        exit;
    }

    public static function deleteBooking(int $id): void
    {
        header('Content-Type: application/json');
        requireLogin();
        $bookingOwnerId = null;
        $all = Booking::getAll();
        foreach ($all as $b) {
            if ((int)$b['id'] === $id) {
                $bookingOwnerId = (int)$b['user_id'];
                break;
            }
        }
        if ($bookingOwnerId === null) {
            http_response_code(404);
            echo json_encode(['error' => 'Booking not found.']);
            exit;
        }
        if (!isAdmin() && $bookingOwnerId !== (int)$_SESSION['user_id']) {
            http_response_code(403);
            echo json_encode(['error' => 'Forbidden: Not allowed to delete this booking.']);
            exit;
        }
        $deleted = Booking::delete($id);
        if (!$deleted) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete booking.']);
            exit;
        }
        echo json_encode(['message' => 'Booking deleted successfully.']);
        exit;
    }
}
