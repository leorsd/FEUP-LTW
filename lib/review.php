<?php
declare(strict_types=1);

class Review
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function addReview(int $service_id, int $reviewer_id, int $rating, string $text): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO service_review (service_id, reviewer_id, rating, text) VALUES (?, ?, ?, ?)'
        );
        $result = $stmt->execute([$service_id, $reviewer_id, $rating, $text]);

        if ($result) {
            $avgStmt = $this->db->prepare(
                'SELECT AVG(rating) as avg_rating FROM service_review WHERE service_id = ?'
            );
            $avgStmt->execute([$service_id]);
            $avg = $avgStmt->fetchColumn();
            
            $updateStmt = $this->db->prepare(
                'UPDATE service SET rating = ? WHERE id = ?'
            );
            $updateStmt->execute([$avg, $service_id]);
        }

        return $result;
    }

    public function getReviewsService(int $service_id): array
    {
        $stmt = $this->db->prepare(
            'SELECT service_review.*, user.username AS reviewer_username, user.profile_picture AS reviewer_image
             FROM service_review
             LEFT JOIN user ON service_review.reviewer_id = user.id
             WHERE service_review.service_id = :service_id
             ORDER BY service_review.created_at DESC'
        );
        $stmt->execute(['service_id' => $service_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}