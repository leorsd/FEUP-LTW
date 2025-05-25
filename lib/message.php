<?php
declare(strict_types=1);

class Message {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function getMessagesBetween($user1_id, $user2_id, $after_message_id = 0) {
        $stmt = $this->db->prepare(
            "SELECT m.*, u.username AS sender_username
            FROM message m
            JOIN user u ON m.sender_id = u.id
            WHERE (
                (m.sender_id = :user1 AND m.receiver_id = :user2)
                OR (m.sender_id = :user2 AND m.receiver_id = :user1)
            )
            AND m.id > :after_message_id
            ORDER BY m.created_at ASC"
        );
        $stmt->execute([
            ':user1' => $user1_id,
            ':user2' => $user2_id,
            ':after_message_id' => $after_message_id
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function sendMessage($sender_id, $receiver_id, $content) {
        $stmt = $this->db->prepare(
            "INSERT INTO message (sender_id, receiver_id, content, created_at)
             VALUES (:sender_id, :receiver_id, :content, CURRENT_TIMESTAMP)"
        );
        $stmt->execute([
            ':sender_id' => $sender_id,
            ':receiver_id' => $receiver_id,
            ':content' => $content
        ]);
        return $this->db->lastInsertId();
    }
}