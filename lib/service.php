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

    public function countFilteredServices(array $filters = []): int
    {
        $query = 'SELECT COUNT(*) FROM service
                LEFT JOIN service_category ON service.category = service_category.id
                LEFT JOIN service_status ON service.status = service_status.id
                LEFT JOIN user ON service.creator_id = user.id';
        $params = [];
        $where = [];

        if (!empty($filters['search'])) {
            $where[] = '(service.title LIKE :search OR service.description LIKE :search OR service.location LIKE :search OR user.username LIKE :search)';
            $params['search'] = '%' . $filters['search'] . '%';
        }
        if (!empty($filters['provider'])) {
            $where[] = 'user.username LIKE :provider';
            $params['provider'] = '%' . $filters['provider'] . '%';
        }
        if (!empty($filters['category'])) {
            $where[] = 'service.category = :category';
            $params['category'] = $filters['category'];
        }
        if (!empty($filters['location'])) {
            $where[] = 'service.location LIKE :location';
            $params['location'] = '%' . $filters['location'] . '%';
        }
        if (!empty($filters['status'])) {
            $where[] = 'service_status.name = :status';
            $params['status'] = $filters['status'];
        }
        if (!empty($filters['min_price'])) {
            $where[] = 'service.price >= :min_price';
            $params['min_price'] = $filters['min_price'];
        }
        if (!empty($filters['max_price'])) {
            $where[] = 'service.price <= :max_price';
            $params['max_price'] = $filters['max_price'];
        }
        if (!empty($filters['min_rating'])) {
            $where[] = 'service.rating >= :min_rating';
            $params['min_rating'] = $filters['min_rating'];
        }
        if (!empty($filters['max_rating'])) {
            $where[] = 'service.rating <= :max_rating';
            $params['max_rating'] = $filters['max_rating'];
        }

        if (!empty($where)) {
            $query .= ' WHERE ' . implode(' AND ', $where);
        }

        $stmt = $this->db->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }


    public function getFilteredAndOrderedServices(array $filters, string $orderby, ?int $page = null, ?int $per_page = null): array
    {
        $query = 'SELECT service.*, service_category.name AS category_name, service_status.name AS status_name, 
                user.username AS provider_username
              FROM service
              LEFT JOIN service_category ON service.category = service_category.id
              LEFT JOIN service_status ON service.status = service_status.id
              LEFT JOIN user ON service.creator_id = user.id';
        $params = [];
        $where = [];

        if (!empty($filters['search'])) {
            $where[] = '(service.title LIKE :search OR service.description LIKE :search OR service.location LIKE :search OR user.username LIKE :search)';
            $params['search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['provider'])) {
            $where[] = 'user.username LIKE :provider';
            $params['provider'] = '%' . $filters['provider'] . '%';
        }

        if (!empty($filters['category'])) {
            $where[] = 'service.category = :category';
            $params['category'] = $filters['category'];
        }

        if (!empty($filters['location'])) {
            $where[] = 'service.location LIKE :location';
            $params['location'] = '%' . $filters['location'] . '%';
        }

        if (!empty($filters['status'])) {
            $where[] = 'status_name = :status';
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['min_price'])) {
            $where[] = 'service.price >= :min_price';
            $params['min_price'] = $filters['min_price'];
        }

        if (!empty($filters['max_price'])) {
            $where[] = 'service.price <= :max_price';
            $params['max_price'] = $filters['max_price'];
        }

        if (!empty($filters['min_rating'])) {
            $where[] = 'service.rating >= :min_rating';
            $params['min_rating'] = $filters['min_rating'];
        }

        if (!empty($filters['max_rating'])) {
            $where[] = 'service.rating <= :max_rating';
            $params['max_rating'] = $filters['max_rating'];
        }

        if (!empty($where)) {
            $query .= ' WHERE ' . implode(' AND ', $where);
        }

        $query .= match ($orderby) {
            'price-asc' => ' ORDER BY service.price ASC',
            'price-desc' => ' ORDER BY service.price DESC',
            'rating-asc' => ' ORDER BY service.rating ASC',
            'rating-desc' => ' ORDER BY service.rating DESC',
            'created_at-asc' => ' ORDER BY service.created_at ASC',
            default => ' ORDER BY service.created_at DESC',
        };

        if ($page !== null && $per_page !== null) {
            $offset = ($page - 1) * $per_page;
            $query .= ' LIMIT :limit OFFSET :offset';
            $params['limit'] = $per_page;
            $params['offset'] = $offset;
        }

        $stmt = $this->db->prepare($query);
        foreach ($params as $key => $value) {
            if ($key === 'limit' || $key === 'offset') {
                $stmt->bindValue(":$key", (int)$value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue(":$key", $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }
        }
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
