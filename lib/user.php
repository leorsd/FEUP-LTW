<?php
// lib/user.php
declare(strict_types=1);

class User
{
  private PDO $db;
  private string $username;
  private string $email;
  private string $phone;

  public function __construct(PDO $db)
  {
    $this->db = $db;
  }

  public function setUserData(string $username, string $email, string $phone): void
  {
    $this->username = $username;
    $this->email = $email;
    $this->phone = $phone;
  }

  // Check if user exists based on username
  public function userExists(): bool
  {
    $stmt = $this->db->prepare('SELECT COUNT(*) FROM user WHERE username = :username');
    $stmt->execute(['username' => $this->username]);
    return $stmt->fetchColumn() > 0;
  }

  // Register a new user, password is passed as argument
  public function register(string $password): bool
  {
    if ($this->userExists()) {
      return false; // Username already exists
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $this->db->prepare(
      'INSERT INTO user (username, password, email, phone) VALUES (:username, :password, :email, :phone)'
    );

    return $stmt->execute([
      'username' => $this->username,
      'password' => $hashedPassword,
      'email' => $this->email,
      'phone' => $this->phone
    ]);
  }

  // Verify password, password is passed as argument
  public function verifyPassword(string $password): bool
  {
    $stmt = $this->db->prepare('SELECT password FROM user WHERE username = :username');
    $stmt->execute(['username' => $this->username]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result && password_verify($password, $result['password']);
  }

  // Get user info (e.g., profile data)
  // EXPERIMENTAL
  public function getUserInfo(): array
  {
    $stmt = $this->db->prepare('SELECT * FROM user WHERE username = :username');
    $stmt->execute(['username' => $this->username]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }
}

?>

