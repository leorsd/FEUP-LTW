<?php
declare(strict_types=1);

class Category
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAllCategories(): array
    {
        $stmt = $this->db->query('SELECT id, name FROM service_category ORDER BY name ASC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategoryById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT id, name FROM service_category WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $category = $stmt->fetch(PDO::FETCH_ASSOC);
        return $category ?: null;
    }

    public function createCategory(string $name): bool
    {
        $stmt = $this->db->prepare('INSERT INTO service_category (name) VALUES (:name)');
        return $stmt->execute(['name' => $name]);
    }

    public function updateCategory(int $id, string $name): bool
    {
        $stmt = $this->db->prepare('UPDATE service_category SET name = :name WHERE id = :id');
        return $stmt->execute(['id' => $id, 'name' => $name]);
    }

    public function deleteCategory(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM service_category WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }
}