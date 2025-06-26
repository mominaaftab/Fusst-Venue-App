<?php
// backend/models/Venue.php

require_once __DIR__ . '/../config/db.php';

class Venue
{
    public int    $id;
    public string $name;
    public string $location;
    public int    $capacity;
    public string $description;
    public string $created_at;

    public function __construct(array $data)
    {
        $this->id          = (int)$data['id'];
        $this->name        = $data['name'];
        $this->location    = $data['location'];
        $this->capacity    = (int)$data['capacity'];
        $this->description = $data['description'];
        $this->created_at  = $data['created_at'];
    }

    public static function getAll(): array
    {
        global $pdo;
        // Changed DESC to ASC to add new venues at the end
        $stmt = $pdo->query("SELECT * FROM venues ORDER BY created_at ASC");
        $venues = [];
        while ($row = $stmt->fetch()) {
            $venues[] = new Venue($row);
        }
        return $venues;
    }

    public static function findById(int $id): ?Venue
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM venues WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? new Venue($row) : null;
    }

    public static function create(string $name, string $location, int $capacity, string $description): ?Venue
    {
        global $pdo;
        $stmt = $pdo->prepare("
            INSERT INTO venues (name, location, capacity, description)
            VALUES (:name, :location, :capacity, :description)
        ");
        $saved = $stmt->execute([
            'name'        => $name,
            'location'    => $location,
            'capacity'    => $capacity,
            'description' => $description
        ]);
        if ($saved) {
            $id = (int)$pdo->lastInsertId();
            return self::findById($id);
        }
        return null;
    }

    public static function update(int $id, string $name, string $location, int $capacity, string $description): bool
    {
        global $pdo;
        $stmt = $pdo->prepare("
            UPDATE venues
            SET name = :name, location = :location, capacity = :capacity, description = :description
            WHERE id = :id
        ");
        return $stmt->execute([
            'id'          => $id,
            'name'        => $name,
            'location'    => $location,
            'capacity'    => $capacity,
            'description' => $description
        ]);
    }

    public static function delete(int $id): bool
    {
        global $pdo;
        $stmt = $pdo->prepare("DELETE FROM venues WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
