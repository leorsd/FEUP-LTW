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
    ?int $rating = null,
    ?int $status = null,
    ?int $category = null
  ): bool {
    $stmt = $this->db->prepare(
      'INSERT INTO service (title, description, price, location, creator_id, status, category, rating) 
       VALUES (:title, :description, :price, :location, :creator_id, :status, :category)'
    );
    return $stmt->execute([
      'title' => $title,
      'description' => $description,
      'price' => $price,
      'location' => $location,
      'creator_id' => $creatorId,
      'status' => $status,
      'category' => $category,
      'rating' => $rating
    ]);
  }

  public function getFilteredAndOrderedServices(int $limit, int $offset, ?int $category, string $orderby): array
  {
    $query = 'SELECT * FROM service';
    $params = [];

    // Add category filter
    if ($category) {
        $query .= ' WHERE category = :category';
        $params['category'] = $category;
    }

    // Add ordering logic
    switch ($orderby) {
        case 'price-asc':
            $query .= ' ORDER BY price ASC';
            break;
        case 'price-desc':
            $query .= ' ORDER BY price DESC';
            break;
        case 'rating-asc':
            $query .= ' ORDER BY rating ASC';
            break;
        case 'rating-desc':
            $query .= ' ORDER BY rating DESC';
            break;
        case 'created_at-asc':
            $query .= ' ORDER BY created_at ASC';
            break;
        case 'created_at-desc':
        default:
            $query .= ' ORDER BY created_at DESC';
            break;
    }

    // Add pagination
    $query .= ' LIMIT :limit OFFSET :offset';
    $params['limit'] = $limit;
    $params['offset'] = $offset;

    $stmt = $this->db->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue(":$key", $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
    }
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getAllFilteredAndOrderedServices(?int $category, string $orderby): array
  {
    $query = 'SELECT * FROM service';
    $params = [];

    // Add category filter
    if ($category) {
        $query .= ' WHERE category = :category';
        $params['category'] = $category;
    }

    // Add ordering logic
    switch ($orderby) {
        case 'price-asc':
            $query .= ' ORDER BY price ASC';
            break;
        case 'price-desc':
            $query .= ' ORDER BY price DESC';
            break;
        case 'rating-asc':
            $query .= ' ORDER BY rating ASC';
            break;
        case 'rating-desc':
            $query .= ' ORDER BY rating DESC';
            break;
        case 'created_at-asc':
            $query .= ' ORDER BY created_at ASC';
            break;
        case 'created_at-desc':
        default:
            $query .= ' ORDER BY created_at DESC';
            break;
    }

    $stmt = $this->db->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue(":$key", $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
    }
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  
}