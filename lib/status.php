<?php
declare(strict_types=1);

class Status{
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function getAllStatuses(): array {
        $stmt = $this->db->prepare("SELECT id, name FROM service_status");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStatusById(int $id) {
        $stmt = $this->db->prepare("SELECT id, name FROM service_status WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}