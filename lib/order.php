<?php
declare(strict_types=1);

class Order {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    // Create a new order (status defaults to 1, e.g., "Ordered")
    public function createOrder(int $service_id, int $customer_id, int $status_id = 1): ?int {
        $stmt = $this->db->prepare(
            "INSERT INTO service_order (service_id, customer_id, status, created_at)
             VALUES (:service_id, :customer_id, :status, CURRENT_TIMESTAMP)"
        );
        if ($stmt->execute([
            ':service_id' => $service_id,
            ':customer_id' => $customer_id,
            ':status' => $status_id
        ])) {
            return (int)$this->db->lastInsertId();
        }
        return null;
    }

    // General fetch helper for orders
    private function fetchOrders(string $where, array $params = [], string $select = "o.*, u.username AS customer_username, s.name AS status_name"): array {
        $sql = "SELECT $select
                FROM service_order o
                JOIN user u ON o.customer_id = u.id
                LEFT JOIN service_status s ON o.status = s.id
                $where
                ORDER BY o.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get all orders for a service (for providers)
    public function getOrdersByService(int $service_id): array {
        return $this->fetchOrders("WHERE o.service_id = :service_id", [':service_id' => $service_id]);
    }

    // Get all orders for a customer
    public function getOrdersByCustomer(int $customer_id): array {
        $sql = "SELECT o.*, s.title AS service_title, st.name AS status_name
                FROM service_order o
                JOIN service s ON o.service_id = s.id
                LEFT JOIN service_status st ON o.status = st.id
                WHERE o.customer_id = :customer_id
                ORDER BY o.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':customer_id' => $customer_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get all orders for a provider (all their services)
    public function getOrdersByProvider(int $provider_id): array {
        $sql = "SELECT o.*, u.username AS customer_username, s.name AS status_name
                FROM service_order o
                JOIN service sv ON o.service_id = sv.id
                JOIN user u ON o.customer_id = u.id
                LEFT JOIN service_status s ON o.status = s.id
                WHERE sv.creator_id = :provider_id
                ORDER BY o.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':provider_id' => $provider_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get all orders for a provider and a specific customer
    public function getOrdersByProviderAndCustomer(int $provider_id, int $customer_id): array {
        $sql = "SELECT o.*, u.username AS customer_username, s.name AS status_name
                FROM service_order o
                JOIN service sv ON o.service_id = sv.id
                JOIN user u ON o.customer_id = u.id
                LEFT JOIN service_status s ON o.status = s.id
                WHERE sv.creator_id = :provider_id AND o.customer_id = :customer_id
                ORDER BY o.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':provider_id' => $provider_id, ':customer_id' => $customer_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get a specific order (with status name)
    public function getOrder(int $service_id, int $customer_id): ?array {
        $orders = $this->fetchOrders(
            "WHERE o.service_id = :service_id AND o.customer_id = :customer_id",
            [':service_id' => $service_id, ':customer_id' => $customer_id]
        );
        return $orders[0] ?? null;
    }

    // Update order status (status is the status ID)
    public function updateOrderStatus(int $service_id, int $customer_id, int $status_id): bool {
        $stmt = $this->db->prepare(
            "UPDATE service_order
             SET status = :status
             WHERE service_id = :service_id AND customer_id = :customer_id"
        );
        return $stmt->execute([
            ':status' => $status_id,
            ':service_id' => $service_id,
            ':customer_id' => $customer_id
        ]);
    }

    // Cancel an order (delete)
    public function cancelOrder(int $service_id, int $customer_id): bool {
        $stmt = $this->db->prepare(
            "DELETE FROM service_order
             WHERE service_id = :service_id AND customer_id = :customer_id"
        );
        return $stmt->execute([
            ':service_id' => $service_id,
            ':customer_id' => $customer_id
        ]);
    }

    // Check if a user has ordered a service
    public function hasUserOrderedService(int $serviceId, int $userId): bool {
        $stmt = $this->db->prepare('SELECT 1 FROM service_order WHERE service_id = ? AND customer_id = ?');
        $stmt->execute([$serviceId, $userId]);
        return (bool) $stmt->fetchColumn();
    }

    // Get the status name for a user's order
    public function getUserOrderStatus(int $serviceId, int $userId): ?string {
        $stmt = $this->db->prepare('SELECT service_status.name FROM service_order JOIN service_status ON service_order.status = service_status.id WHERE service_order.service_id = ? AND service_order.customer_id = ?');
        $stmt->execute([$serviceId, $userId]);
        return $stmt->fetchColumn() ?: null;
    }

    // Get order info: hasOrdered (bool) and status (string|null)
    public function getUserOrderInfo(int $serviceId, int $userId): array {
        $status = $this->getUserOrderStatus($serviceId, $userId);
        return [
            'hasOrdered' => $status !== false && $status !== null,
            'status' => $status ?: null
        ];
    }
}
?>