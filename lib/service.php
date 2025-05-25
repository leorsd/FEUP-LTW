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
        ?int $category = null,
        ?string $image = null
    ): bool {
        $stmt = $this->db->prepare(
            'INSERT INTO service (title, description, price, location, creator_id, category, image)
             VALUES (:title, :description, :price, :location, :creator_id, :category, :image)'
        );
        return $stmt->execute([
            'title' => $title,
            'description' => $description,
            'price' => $price,
            'location' => $location,
            'creator_id' => $creatorId,
            'category' => $category,
            'image' => $image,
        ]);
    }

    public function updateService(int $serviceId, int $userId, array $fields): bool
    {
        // Only allow certain fields to be updated
        $allowed = ['title', 'description', 'price', 'location', 'category', 'image'];
        $set = [];
        $params = [];

        foreach ($fields as $key => $value) {
            if (in_array($key, $allowed, true)) {
                $set[] = "$key = :$key";
                $params[$key] = $value;
            }
        }

        if (empty($set)) return false;

        // Ensure only the creator can update
        $params['service_id'] = $serviceId;
        $params['user_id'] = $userId;

        $sql = "UPDATE service SET " . implode(', ', $set) . " WHERE id = :service_id AND creator_id = :user_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function deleteService(int $serviceId): bool
    {
        $stmt = $this->db->prepare('DELETE FROM service WHERE id = :id');
        return $stmt->execute(['id' => $serviceId]);
    }

    public function getServiceById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT service.*, service_category.name AS category_name, user.username AS provider_username, user.profile_picture AS provider_image
             FROM service
             LEFT JOIN service_category ON service.category = service_category.id
             LEFT JOIN user ON service.creator_id = user.id
             WHERE service.id = :id'
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function isFavorite(int $userId, int $serviceId): bool
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM favorites WHERE user_id = :user_id AND service_id = :service_id');
        $stmt->execute(['user_id' => $userId, 'service_id' => $serviceId]);
        return (bool) $stmt->fetchColumn();
    }

    /**
     * Unified service/order query.
     * $context: 'all', 'bought', 'sold', 'created'
     * $user_id: user id for context (customer or provider)
     * $filters: associative array of filters (search, category, location, price, rating, status, etc)
     * $orderby: string for ordering
     * $page, $per_page: pagination
     * Returns: ['total' => int, 'services' => array]
     */
    public function getServices(
        string $context = 'all',
        ?int $user_id = null,
        array $filters = [],
        string $orderby = 'created_at-desc',
        ?int $page = null,
        ?int $per_page = null
    ): array {
        $params = [];
        $select = 'service.*, service_category.name AS category_name, user.username AS provider_username, user.profile_picture AS provider_image, COUNT(*) OVER() AS total_count';
        $from = 'service
            LEFT JOIN service_category ON service.category = service_category.id
            LEFT JOIN user ON service.creator_id = user.id';
        $where = [];

        // Context-specific joins and where
        if ($context === 'bought' && $user_id !== null) {
            $from = 'service_order
                JOIN service ON service_order.service_id = service.id
                LEFT JOIN service_category ON service.category = service_category.id
                LEFT JOIN user ON service.creator_id = user.id
                LEFT JOIN service_status ON service_order.status = service_status.id';
            $select .= ', service_order.status AS status_id, service_status.name AS status_name';
            $where[] = 'service_order.customer_id = :user_id';
            $params['user_id'] = $user_id;
        } elseif ($context === 'sold' && $user_id !== null) {
            $from = 'service_order
                JOIN service ON service_order.service_id = service.id
                LEFT JOIN service_category ON service.category = service_category.id
                LEFT JOIN user AS provider ON service.creator_id = provider.id
                LEFT JOIN user AS customer ON service_order.customer_id = customer.id
                LEFT JOIN service_status ON service_order.status = service_status.id';
            $select .= ', customer.id AS customer_id, customer.username AS customer_username, customer.profile_picture AS customer_image, service_order.status AS status_id, service_status.name AS status_name';
            $where[] = 'service.creator_id = :user_id';
            $params['user_id'] = $user_id;
        } elseif ($context === 'created' && $user_id !== null) {
            $where[] = 'service.creator_id = :user_id';
            $params['user_id'] = $user_id;
        }

        // Filters
        if (!empty($filters['favorites_owner'])) {
            $from .= ' INNER JOIN favorites ON favorites.service_id = service.id ';
            $where[] = 'favorites.user_id = :favorites_owner';
            $params['favorites_owner'] = $filters['favorites_owner'];
        }
        if (!empty($filters['search'])) {
            $where[] = '(service.title LIKE :search OR service.description LIKE :search OR service.location LIKE :search OR user.username LIKE :search)';
            $params['search'] = '%' . $filters['search'] . '%';
        }
        if (!empty($filters['provider'])) {
            $where[] = 'user.username LIKE :provider';
            $params['provider'] = '%' . $filters['provider'] . '%';
        }
        if (!empty($filters['provider_id'])) {
            $where[] = 'user.id = :provider_id';
            $params['provider_id'] = $filters['provider_id'];
        }
        if (!empty($filters['exclude_provider_id'])) {
            $where[] = 'user.id != :exclude_provider_id';
            $params['exclude_provider_id'] = $filters['exclude_provider_id'];
        }
        if (!empty($filters['category']) && is_array($filters['category'])) {
            $placeholders = [];
            foreach ($filters['category'] as $i => $catId) {
                $key = "category_$i";
                $placeholders[] = ":$key";
                $params[$key] = $catId;
            }
            $where[] = 'service.category IN (' . implode(', ', $placeholders) . ')';
        }
        if (!empty($filters['location'])) {
            $where[] = 'service.location LIKE :location';
            $params['location'] = '%' . $filters['location'] . '%';
        }
        if (isset($filters['min_price'])) {
            $where[] = 'service.price >= :min_price';
            $params['min_price'] = $filters['min_price'];
        }
        if (isset($filters['max_price'])) {
            $where[] = 'service.price <= :max_price';
            $params['max_price'] = $filters['max_price'];
        }
        if (isset($filters['min_rating'])) {
            $where[] = 'service.rating >= :min_rating';
            $params['min_rating'] = $filters['min_rating'];
        }
        if (isset($filters['max_rating'])) {
            $where[] = 'service.rating <= :max_rating';
            $params['max_rating'] = $filters['max_rating'];
        }
        if (!empty($filters['status']) && is_array($filters['status']) && ($context === 'bought' || $context === 'sold')) {
            $placeholders = [];
            foreach ($filters['status'] as $i => $statusId) {
                $ph = ':status' . $i;
                $placeholders[] = $ph;
                $params['status' . $i] = $statusId;
            }
            $where[] = 'service_order.status IN (' . implode(', ', $placeholders) . ')';
        }

        $query = "SELECT $select FROM $from";
        if ($where) {
            $query .= ' WHERE ' . implode(' AND ', $where);
        }

        // Order by
        $query .= match ($orderby) {
            'price-asc' => ' ORDER BY service.price ASC',
            'price-desc' => ' ORDER BY service.price DESC',
            'rating-asc' => ' ORDER BY service.rating ASC',
            'rating-desc' => ' ORDER BY service.rating DESC',
            'created_at-asc' => ' ORDER BY service.created_at ASC',
            default => ' ORDER BY service.created_at DESC',
        };

        // Pagination
        if ($page !== null && $per_page !== null) {
            $offset = ($page - 1) * $per_page;
            $query .= ' LIMIT :limit OFFSET :offset';
            $params['limit'] = $per_page;
            $params['offset'] = $offset;
        }

        $stmt = $this->db->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue(is_int($key) ? $key + 1 : ":$key", $value);
        }
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get total count from the first row (if exists)
        $total = isset($results[0]['total_count']) ? (int)$results[0]['total_count'] : 0;

        return [
            'total' => $total,
            'services' => $results
        ];
    }
}