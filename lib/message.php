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

    public function getChatsForUser($user_id) {
    $stmt = $this->db->prepare(
        "SELECT
            other_user.id AS other_user_id,
            other_user.username AS other_username,
            m2.content AS last_message,
            m2.created_at AS last_message_time
        FROM (
            SELECT
                CASE
                    WHEN m.sender_id = :user_id THEN m.receiver_id
                    ELSE m.sender_id
                END AS chat_partner_id,
                MAX(m.id) AS last_message_id
            FROM message m
            WHERE m.sender_id = :user_id OR m.receiver_id = :user_id
            GROUP BY chat_partner_id
        ) chat
        JOIN user other_user ON other_user.id = chat.chat_partner_id
        JOIN message m2 ON m2.id = chat.last_message_id
        ORDER BY m2.created_at DESC"
    );
    $stmt->execute([':user_id' => $user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}