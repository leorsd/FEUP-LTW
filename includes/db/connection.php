<?php
declare(strict_types=1);

function getDatabaseConnection(): PDO
{
  $db_file = getenv('DB_PATH') ?: __DIR__ . '/../../database/database.db';

  try {
    $db = new PDO("sqlite:" . $db_file);

    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $db->exec('PRAGMA foreign_keys = ON;');

    return $db;
  } catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    exit;
  }
}
