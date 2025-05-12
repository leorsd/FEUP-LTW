<?php
declare(strict_types=1);

class Service
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function createService(
        string $title,
        string $description,
        float $price,
        string $location,
        int $creatorId,
        ?int $status = null,
        ?int $category = null,
        ?string $image = null
    ): bool {
        $stmt = $this->db->prepare(
            'INSERT INTO service (title, description, price, location, creator_id, status, category, image)
       VALUES (:title, :description, :price, :location, :creator_id, :status, :category, :image)'
        );
        return $stmt->execute([
            'title' => $title,
            'description' => $description,
            'price' => $price,
            'location' => $location,
            'creator_id' => $creatorId,
            'status' => $status,
            'category' => $category,
            'image' => $image,
        ]);
    }

    public function countFilteredServices(?int $category): int
    {
        $query = 'SELECT COUNT(*) FROM service';
        $params = [];

        if ($category) {
            $query .= ' WHERE category = :category';
            $params['category'] = $category;
        }

        $stmt = $this->db->prepare($query);
        $stmt = $this->db->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }


    public function getPaginatedFilteredAndOrderedServices(int $limit, int $offset, ?int $category, string $orderby): array
    {
        $query = 'SELECT service.*, service_category.name AS category_name, service_status.name AS status_name, 
                user.username AS provider_username
              FROM service
              LEFT JOIN service_category ON service.category = service_category.id
              LEFT JOIN service_status ON service.status = service_status.id
              LEFT JOIN user ON service.creator_id = user.id';
        $params = [];

        // Add category filter
        if ($category) {
            $query .= ' WHERE service.category = :category';
            $params['category'] = $category;
        }

        // Add ordering logic
        $query .= match ($orderby) {
            'price-asc' => ' ORDER BY service.price ASC',
            'price-desc' => ' ORDER BY service.price DESC',
            'rating-asc' => ' ORDER BY service.rating ASC',
            'rating-desc' => ' ORDER BY service.rating DESC',
            'created_at-asc' => ' ORDER BY service.created_at ASC',
            default => ' ORDER BY service.created_at DESC',
        };

        // Add pagination
        $query .= ' LIMIT :limit OFFSET :offset';
        $params['limit'] = $limit;
        $params['offset'] = $offset;

        $stmt = $this->db->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllFilteredAndOrderedServices(?int $category, string $orderby): array
    {
        $query = 'SELECT service.*, service_category.name AS category_name, service_status.name AS status_name, 
                user.username AS provider_username
              FROM service
              LEFT JOIN service_category ON service.category = service_category.id
              LEFT JOIN service_status ON service.status = service_status.id
              LEFT JOIN user ON service.creator_id = user.id';
        $params = [];

        // Add category filter
        if ($category) {
            $query .= ' WHERE service.category = :category';
            $params['category'] = $category;
        }

        // Add ordering logic
        $query .= match ($orderby) {
            'price-asc' => ' ORDER BY service.price ASC',
            'price-desc' => ' ORDER BY service.price DESC',
            'rating-asc' => ' ORDER BY service.rating ASC',
            'rating-desc' => ' ORDER BY service.rating DESC',
            'created_at-asc' => ' ORDER BY service.created_at ASC',
            default => ' ORDER BY service.created_at DESC',
        };

        $stmt = $this->db->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


}
