<?php
declare(strict_types=1);

class User
  {
    private PDO $db;

    public function __construct(PDO $db)
    {
      $this->db = $db;
    }

    // Username must be at least 3 characters, only letters, numbers, underscores
    public static function validateUsername(string $username): bool
    {
      return preg_match('/^[A-Za-z0-9_]{3,}$/', $username) === 1;
    }

    // Email must be valid
    public static function validateEmail(string $email): bool
    {
      return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    // Phone must be digits, 9-15 digits
    public static function validatePhone(string $phone): bool
    {
      return preg_match('/^\+?[0-9]{9,15}$/', $phone) === 1;
    }

    // Password: min 8 chars, at least 1 letter, 1 number
    public static function validatePassword(string $password): bool
    {
      return preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d!@#$%^&*()_+\-=]{8,}$/', $password) === 1;
    }

    public function usernameExists(string $username): bool
    {
      $stmt = $this->db->prepare('SELECT COUNT(*) FROM user WHERE username = :username');
      $stmt->execute(['username' => $username]);
      return $stmt->fetchColumn() > 0;
    }

    public function userExists(int $id): bool
    {
      $stmt = $this->db->prepare('SELECT COUNT(*) FROM user WHERE id = :id');
      $stmt->execute(['id' => $id]);
      return $stmt->fetchColumn() > 0;
    }

    public function emailExists(string $email): bool
    {
      $stmt = $this->db->prepare('SELECT COUNT(*) FROM user WHERE email = :email');
      $stmt->execute(['email' => $email]);
      return $stmt->fetchColumn() > 0;
    }

    public function phoneExists(string $phone): bool
    {
      $stmt = $this->db->prepare('SELECT COUNT(*) FROM user WHERE phone = :phone');
      $stmt->execute(['phone' => $phone]);
      return $stmt->fetchColumn() > 0;
    }

    public function getAllUsers(?string $search = null): array
    {
      if ($search !== null && $search !== '') {
        $stmt = $this->db->prepare('SELECT * FROM user WHERE username LIKE :search');
        $stmt->execute(['search' => '%' . $search . '%']);
      } else {
        $stmt = $this->db->query('SELECT * FROM user');
      }
      $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

      foreach ($users as &$user) {
        $adminStmt = $this->db->prepare('SELECT 1 FROM admin WHERE user_id = :id');
        $adminStmt->execute(['id' => $user['id']]);
        $user['is_admin'] = (bool)$adminStmt->fetchColumn();
      }
      return $users;
    }

    public function register(string $username, string $email, string $phone, string $password): ?int
    {
      if (!self::validateUsername($username)) return null;
      if (!self::validateEmail($email)) return null;
      if (!self::validatePhone($phone)) return null;
      if (!self::validatePassword($password)) return null;

      // Check if username already exists
      $stmt = $this->db->prepare('SELECT COUNT(*) FROM user WHERE username = :username');
      $stmt->execute(['username' => $username]);
      if ($stmt->fetchColumn() > 0) return null;

      $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

      $stmt = $this->db->prepare(
        'INSERT INTO user (username, password, email, phone) VALUES (:username, :password, :email, :phone)'
      );

      if ($stmt->execute([
        'username' => $username,
        'password' => $hashedPassword,
        'email' => $email,
        'phone' => $phone
      ])) {
          return (int)$this->db->lastInsertId();
      } else {
          return null;
      }
    }

    public function verifyPassword(string $username, string $password): ?int
    {
      $stmt = $this->db->prepare('SELECT id, password FROM user WHERE username = :username');
      $stmt->execute(['username' => $username]);
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($result && password_verify($password, $result['password'])) {
        return (int)$result['id'];
      }
      return null;
    }

    public function verifyPasswordId(int $id, string $password): bool
    {
      $stmt = $this->db->prepare('SELECT password FROM user WHERE id = :id');
      $stmt->execute(['id' => $id]);
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      return $result && password_verify($password, $result['password']);
    }

    public function getUserInfo(int $id): array
    {
      $stmt = $this->db->prepare('SELECT * FROM user WHERE id = :id');
      $stmt->execute(['id' => $id]);
      $user = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

      if ($user) {
        $adminStmt = $this->db->prepare('SELECT 1 FROM admin WHERE user_id = :id');
        $adminStmt->execute(['id' => $id]);
        $user['is_admin'] = (bool)$adminStmt->fetchColumn();
      } else {
        $user['is_admin'] = false;
      }

      return $user;
    }

    public function updateProfile(int $id, array $fields): bool
    {
      // Now allow updating username and email as well
      $allowed = ['phone', 'age', 'location', 'bio', 'email', 'username'];
      $set = [];
      $params = ['id' => $id];
      foreach ($allowed as $field) {
        if (isset($fields[$field])) {
          if ($field === 'age') {
            $age = $fields[$field];
            if ($age === '' || $age === null) {
              $set[] = "age = NULL";
            } else if (is_numeric($age) && intval($age) >= 13) {
              $set[] = "age = :age";
              $params['age'] = intval($age);
            } else {
              continue;
            }
          } else if ($field === 'location') {
            if (strlen($fields[$field]) > 100)
              continue;
            $set[] = "location = :location";
            $params['location'] = $fields[$field];
          } else if ($field === 'bio') {
            if (strlen($fields[$field]) > 1000)
              continue; 
            $set[] = "bio = :bio";
            $params['bio'] = $fields[$field];
          } else if ($field === 'email') {
            if (!self::validateEmail($fields[$field]))
              continue; 
            $set[] = "email = :email";
            $params['email'] = $fields[$field];
          } else if ($field === 'username') {
            if (!self::validateUsername($fields[$field]))
              continue; 
            $set[] = "username = :username";
            $params['username'] = $fields[$field];
          } else {
            $set[] = "$field = :$field";
            $params[$field] = $fields[$field];
          }
        }
      }
      if (empty($set))
        return false;
      $sql = 'UPDATE user SET ' . implode(', ', $set) . ' WHERE id = :id';
      $stmt = $this->db->prepare($sql);
      return $stmt->execute($params);
    }

    public function updateProfilePicture(int $id, string $filename): bool
    {
      $stmt = $this->db->prepare('UPDATE user SET profile_picture = :profile_picture WHERE id = :id');
      return $stmt->execute([
        'profile_picture' => $filename,
        'id' => $id
      ]);
    }

    public function updatePassword(int $id, string $currentPassword, string $newPassword): bool
    {
      if (!$this->verifyPasswordId($id, $currentPassword))
        return false;
      $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
      $stmt = $this->db->prepare('UPDATE user SET password = :password WHERE id = :id');
      return $stmt->execute([
        'password' => $hashed,
        'id' => $id
      ]);
    }

    public function deleteUser(int $id): bool
    {
      $stmt = $this->db->prepare('DELETE FROM user WHERE id = :id');
      return $stmt->execute(['id' => $id]);
    }

    public function promoteToAdmin(int $id): bool
    {
      $stmt = $this->db->prepare('SELECT 1 FROM admin WHERE user_id = :id');
      $stmt->execute(['id' => $id]);
      if ($stmt->fetchColumn()) return true; 

      $stmt = $this->db->prepare('INSERT INTO admin (user_id) VALUES (:id)');
      return $stmt->execute(['id' => $id]);
    }

    public function isAdmin(int $id): bool
    {
      $stmt = $this->db->prepare('SELECT 1 FROM admin WHERE user_id = :id');
      $stmt->execute(['id' => $id]);
      return (bool)$stmt->fetchColumn();
    }
  }
?>