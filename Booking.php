<?php
// backend/models/Booking.php

require_once __DIR__ . '/../config/db.php';

class Booking
{
    public int    $id;
    public int    $user_id;
    public int    $venue_id;
    public string $booking_date;
    public string $start_time;
    public string $end_time;
    public string $status; // 'pending', 'approved', 'cancelled'
    public string $created_at;

    public function __construct(array $data)
    {
        $this->id           = (int)$data['id'];
        $this->user_id      = (int)$data['user_id'];
        $this->venue_id     = (int)$data['venue_id'];
        $this->booking_date = $data['booking_date'];
        $this->start_time   = $data['start_time'];
        $this->end_time     = $data['end_time'];
        $this->status       = $data['status'];
        $this->created_at   = $data['created_at'];
    }

    public static function getAll(): array
    {
        global $pdo;
        $stmt = $pdo->query("
            SELECT b.*, u.name AS student_name, v.name AS venue_name
            FROM bookings b
            JOIN users u ON b.user_id = u.id
            JOIN venues v ON b.venue_id = v.id
            ORDER BY b.booking_date DESC, b.start_time
        ");
        $bookings = [];
        while ($row = $stmt->fetch()) {
            $bookings[] = $row;
        }
        return $bookings;
    }

    public static function getByUser(int $userId): array
    {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT b.*, v.name AS venue_name
            FROM bookings b
            JOIN venues v ON b.venue_id = v.id
            WHERE b.user_id = :user_id
            ORDER BY b.booking_date DESC, b.start_time
        ");
        $stmt->execute(['user_id' => $userId]);
        $bookings = [];
        while ($row = $stmt->fetch()) {
            $bookings[] = $row;
        }
        return $bookings;
    }

    public static function create(int $userId, int $venueId, string $date, string $startTime, string $endTime): ?Booking
    {
        global $pdo;
        $stmt = $pdo->prepare("
            INSERT INTO bookings (user_id, venue_id, booking_date, start_time, end_time)
            VALUES (:user_id, :venue_id, :booking_date, :start_time, :end_time)
        ");
        $saved = $stmt->execute([
            'user_id'      => $userId,
            'venue_id'     => $venueId,
            'booking_date' => $date,
            'start_time'   => $startTime,
            'end_time'     => $endTime
        ]);
        if ($saved) {
            $id = (int)$pdo->lastInsertId();
            $stmt2 = $pdo->prepare("SELECT * FROM bookings WHERE id = :id");
            $stmt2->execute(['id' => $id]);
            $row = $stmt2->fetch();
            return new Booking($row);
        }
        return null;
    }

    public static function updateStatus(int $id, string $newStatus): bool
    {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE bookings SET status = :status WHERE id = :id");
        return $stmt->execute([
            'status' => $newStatus,
            'id'     => $id
        ]);
    }

    public static function delete(int $id): bool
    {
        global $pdo;
        $stmt = $pdo->prepare("DELETE FROM bookings WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
