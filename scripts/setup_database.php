<?php
declare(strict_types=1);

// Include the database connection
require_once __DIR__ . '/../includes/db/connection.php';

// Function to create the database schema
function setupDatabase(PDO $db): void
{
  $schemaFile = __DIR__ . '/../database/schema.sql';
  if (!file_exists($schemaFile)) {
    die("Error: schema.sql file is missing.");
  }

  $schema = file_get_contents($schemaFile);

  if ($schema === false) {
    die("Error reading schema.sql file.");
  }

  // Begin transaction
  try {
    $db->beginTransaction(); // Start transaction

    // Execute the SQL commands to create tables and set up the database
    $db->exec($schema);

    // Commit transaction
    $db->commit();
    echo "Database setup complete!";
  } catch (PDOException $e) {
    // If an error occurs, roll back the transaction
    $db->rollBack();
    die("Error setting up the database: " . $e->getMessage());
  }
}

// Get the database connection
$db = getDatabaseConnection();

// Set up the database
setupDatabase($db);
