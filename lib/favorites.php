<?php
declare(strict_types=1);


class Favorites {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function toggleFavorite(int $user_id, int $service_id): ?bool {
        $stmt = $this->db->prepare('SELECT 1 FROM favorites WHERE user_id = ? AND service_id = ?');
        $stmt->execute([$user_id, $service_id]);
        $exists = $stmt->fetchColumn();

        if ($exists) {
            $stmt = $this->db->prepare('DELETE FROM favorites WHERE user_id = ? AND service_id = ?');
            $stmt->execute([$user_id, $service_id]);
            return false; // Now unfavorited
        } else {
            $stmt = $this->db->prepare('INSERT INTO favorites (user_id, service_id) VALUES (?, ?)');
            $stmt->execute([$user_id, $service_id]);
            return true; // Now favorited
        }
    }
}