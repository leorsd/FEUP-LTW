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

  // Check if email exists
  public function emailExists(): bool
  {
    $stmt = $this->db->prepare('SELECT COUNT(*) FROM user WHERE email = :email');
    $stmt->execute(['email' => $this->email]);
    return $stmt->fetchColumn() > 0;
  }
  // Check if phone exists
  public function phoneExists(): bool
  {
    $stmt = $this->db->prepare('SELECT COUNT(*) FROM user WHERE phone = :phone');
    $stmt->execute(['phone' => $this->phone]);
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

  // Update user profile fields (except password and profile_picture)
  public function updateProfile(array $fields): bool
  {
    // $allowed = ['email', 'phone', 'age', 'location', 'bio'];
    $allowed = ['phone', 'age', 'location', 'bio'];
    $set = [];
    $params = ['username' => $this->username];
    foreach ($allowed as $field) {
      if (isset($fields[$field])) {
        // Validate age as integer or null
        if ($field === 'age') {
          $age = $fields[$field];
          if ($age === '' || $age === null) {
            $set[] = "age = NULL";
          } else if (is_numeric($age) && intval($age) >= 0) {
            $set[] = "age = :age";
            $params['age'] = intval($age);
          } else {
            continue; // skip invalid age
          }
        } else {
          $set[] = "$field = :$field";
          $params[$field] = $fields[$field];
        }
      }
    }
    if (empty($set))
      return false;
    $sql = 'UPDATE user SET ' . implode(', ', $set) . ' WHERE username = :username';
    $stmt = $this->db->prepare($sql);
    return $stmt->execute($params);
  }

  // Update profile picture filename
  public function updateProfilePicture(string $filename): bool
  {
    $stmt = $this->db->prepare('UPDATE user SET profile_picture = :profile_picture WHERE username = :username');
    return $stmt->execute([
      'profile_picture' => $filename,
      'username' => $this->username
    ]);
  }

  // Update password (after verifying current password)
  public function updatePassword(string $currentPassword, string $newPassword): bool
  {
    if (!$this->verifyPassword($currentPassword))
      return false;
    $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $this->db->prepare('UPDATE user SET password = :password WHERE username = :username');
    return $stmt->execute([
      'password' => $hashed,
      'username' => $this->username
    ]);
  }
}

?>

