<?php
declare(strict_types=1);

function getDatabaseConnection(): PDO
{
  // Use environment variable or fallback to the default path
  $db_file = getenv('DB_PATH') ?: __DIR__ . '/../../database/database.db';

  try {
    // Create the PDO connection
    $db = new PDO("sqlite:" . $db_file);

    // Set attributes
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    return $db;
  } catch (PDOException $e) {
    // Log the error
    error_log("Database connection failed: " . $e->getMessage());

    // Display a generic error message
    echo "Sorry, we are experiencing technical difficulties. Please try again later.";
    exit;
  }
}
