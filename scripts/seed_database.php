<?php
declare(strict_types=1);

// Include the database connection
require_once __DIR__ . '/../includes/db/connection.php';

// Function to seed the database
function seedDatabase(PDO $db): void
{
  $seedFile = __DIR__ . '/../database/seed.sql';
  if (!file_exists($seedFile)) {
    die("Error: seed.sql file is missing.");
  }

  $seed = file_get_contents($seedFile);

  if ($seed === false) {
    die("Error reading seed.sql file.");
  }

  // Begin transaction (optional)
  try {
    $db->beginTransaction(); // Start transaction

    // Execute the SQL commands to insert test data
    $db->exec($seed);

    // Commit transaction
    $db->commit();
    echo "Database seeding complete!";
  } catch (PDOException $e) {
    // If an error occurs, roll back the transaction
    $db->rollBack();
    die("Error seeding the database: " . $e->getMessage());
  }
}

// Get the database connection
$db = getDatabaseConnection();

// Seed the database
seedDatabase($db);
