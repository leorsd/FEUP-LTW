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

    private function buildFilters(array $filters, array &$params): string
    {
        $where = [];
        $joins = '';

        // Favorites filter
        if (!empty($filters['favorites_owner'])) {
            $joins .= ' INNER JOIN favorites ON favorites.service_id = service.id ';
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
        $whereClause = $where ? ' WHERE ' . implode(' AND ', $where) : '';
        return $joins . $whereClause;
    }

    public function countFilteredServices(array $filters = []): int
    {
        $params = [];
        $query = 'SELECT COUNT(*) FROM service
            LEFT JOIN user ON service.creator_id = user.id';

        $query .= $this->buildFilters($filters, $params);

        $stmt = $this->db->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue(is_int($key) ? $key + 1 : ":$key", $value);
        }

        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function getFilteredAndOrderedServices(array $filters, string $orderby, ?int $page = null, ?int $per_page = null): array
    {
        $params = [];
        $query = 'SELECT service.*, service_category.name AS category_name, user.username AS provider_username, user.profile_picture AS provider_image
            FROM service
            LEFT JOIN service_category ON service.category = service_category.id
            LEFT JOIN user ON service.creator_id = user.id';

        $query .= $this->buildFilters($filters, $params);

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
            $stmt->bindValue(is_int($key) ? $key + 1 : ":$key", $value);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getServicesBoughtByUser(int $user_id, array $filters, string $orderby, ?int $page = null, ?int $per_page = null): array
    {
        $params = [];
        $query = 'SELECT service.*, service_category.name AS category_name, user.username AS provider_username, user.profile_picture AS provider_image, service_customer.status AS status_id, service_status.name AS status_name
              FROM service_customer
              JOIN service ON service_customer.service_id = service.id
              LEFT JOIN service_category ON service.category = service_category.id
              LEFT JOIN user ON service.creator_id = user.id
              LEFT JOIN service_status ON service_customer.status = service_status.id
              WHERE service_customer.customer_id = :user_id';
        $params['user_id'] = $user_id;
        // Apply status filter directly to service_customer.status
        if (!empty($filters['status']) && is_array($filters['status'])) {
            $placeholders = [];
            foreach ($filters['status'] as $i => $statusId) {
                $ph = ':status' . $i;
                $placeholders[] = $ph;
                $params['status' . $i] = $statusId;
            }
            $query .= ' AND service_customer.status IN (' . implode(', ', $placeholders) . ')';
        }
        // Remove status from filters before passing to buildFilters
        $filters = array_diff_key($filters, ['status' => 1]);
        $extraWhere = $this->buildFilters($filters, $params);
        if ($extraWhere) {
            $query .= ' AND ' . substr($extraWhere, 7); // remove leading ' WHERE '
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
            $stmt->bindValue(is_int($key) ? $key + 1 : ":$key", $value);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countServicesBoughtByUser(int $user_id, array $filters = []): int
    {
        $params = [];
        $query = 'SELECT COUNT(*) FROM service_customer
                  JOIN service ON service_customer.service_id = service.id
                  LEFT JOIN user ON service.creator_id = user.id
                  WHERE service_customer.customer_id = :user_id';
        $params['user_id'] = $user_id;
        // Apply status filter directly to service_customer.status
        if (!empty($filters['status']) && is_array($filters['status'])) {
            $placeholders = [];
            foreach ($filters['status'] as $i => $statusId) {
                $ph = ':status' . $i;
                $placeholders[] = $ph;
                $params['status' . $i] = $statusId;
            }
            $query .= ' AND service_customer.status IN (' . implode(', ', $placeholders) . ')';
        }
        // Remove status from filters before passing to buildFilters
        $filters = array_diff_key($filters, ['status' => 1]);
        $extraWhere = $this->buildFilters($filters, $params);
        if ($extraWhere) {
            $query .= ' AND ' . substr($extraWhere, 7);
        }
        $stmt = $this->db->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue(is_int($key) ? $key + 1 : ":$key", $value);
        }
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function hasUserOrderedService(int $serviceId, int $userId): bool
    {
        $stmt = $this->db->prepare('SELECT 1 FROM service_customer WHERE service_id = ? AND customer_id = ?');
        $stmt->execute([$serviceId, $userId]);
        return (bool) $stmt->fetchColumn();
    }

    public function getUserOrderStatus(int $serviceId, int $userId): ?string
    {
        $stmt = $this->db->prepare('SELECT service_status.name FROM service_customer JOIN service_status ON service_customer.status = service_status.id WHERE service_customer.service_id = ? AND service_customer.customer_id = ?');
        $stmt->execute([$serviceId, $userId]);
        return $stmt->fetchColumn() ?: null;
    }

    /**
     * Returns an array with 'hasOrdered' (bool) and 'status' (string|null) for a user and service.
     */
    public function getUserOrderInfo(int $serviceId, int $userId): array
    {
        $stmt = $this->db->prepare('SELECT service_status.name FROM service_customer JOIN service_status ON service_customer.status = service_status.id WHERE service_customer.service_id = ? AND service_customer.customer_id = ?');
        $stmt->execute([$serviceId, $userId]);
        $status = $stmt->fetchColumn();
        return [
            'hasOrdered' => $status !== false && $status !== null,
            'status' => $status ?: null
        ];
    }

    /**
     * Get all services created by a user (provider)
     */
    public function getServicesCreatedByUser(int $user_id, array $filters, string $orderby, ?int $page = null, ?int $per_page = null): array
    {
        $params = [];
        $query = 'SELECT service.*, service_category.name AS category_name, user.username AS provider_username, user.profile_picture AS provider_image
                  FROM service
                  LEFT JOIN service_category ON service.category = service_category.id
                  LEFT JOIN user ON service.creator_id = user.id
                  WHERE service.creator_id = :user_id';
        $params['user_id'] = $user_id;
        $extraWhere = $this->buildFilters($filters, $params);
        if ($extraWhere) {
            $query .= ' AND ' . substr($extraWhere, 7);
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
            $stmt->bindValue(is_int($key) ? $key + 1 : ":$key", $value);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Count all services created by a user (provider)
     */
    public function countServicesCreatedByUser(int $user_id, array $filters = []): int
    {
        $params = [];
        $query = 'SELECT COUNT(*) FROM service
                  LEFT JOIN user ON service.creator_id = user.id
                  WHERE service.creator_id = :user_id';
        $params['user_id'] = $user_id;
        $extraWhere = $this->buildFilters($filters, $params);
        if ($extraWhere) {
            $query .= ' AND ' . substr($extraWhere, 7);
        }
        $stmt = $this->db->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue(is_int($key) ? $key + 1 : ":$key", $value);
        }
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }
}
