-- This is the schema definition file for the database
PRAGMA foreign_keys = ON;

DROP TABLE IF EXISTS user;

DROP TABLE IF EXISTS admin;

DROP TABLE IF EXISTS service;

DROP TABLE IF EXISTS message;

DROP TABLE IF EXISTS favorites;

DROP TABLE IF EXISTS service_category;

DROP TABLE IF EXISTS service_status;

DROP TABLE IF EXISTS service_review;

CREATE TABLE
  user (
    id INT PRIMARY KEY,
    username VARCHAR NOT NULL UNIQUE,
    email VARCHAR NOT NULL UNIQUE,
    password VARCHAR NOT NULL,
    age INT NOT NULL,
    phone VARCHAR NOT NULL UNIQUE,
    profile_picture VARCHAR DEFAULT 'user.png',
    location VARCHAR NOT NULL DEFAULT 'Unknown',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    bio TEXT DEFAULT 'Start writing your bio here...'
  );

CREATE TABLE
  admin (
    user_id INT PRIMARY KEY REFERENCES user (id) ON DELETE CASCADE
  );

CREATE TABLE
  service (
    id INT PRIMARY KEY,
    creator_id INT REFERENCES user (id) ON DELETE CASCADE,
    title VARCHAR NOT NULL,
    description VARCHAR NOT NULL,
    price REAL NOT NULL,
    location VARCHAR NOT NULL DEFAULT 'Unknown',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status INT REFERENCES service_status (id) ON DELETE SET NULL,
    category INT REFERENCES service_category (id) ON DELETE SET NULL
  );

CREATE TABLE
  message (
    id INT PRIMARY KEY,
    sender_id INT REFERENCES user (id) ON DELETE CASCADE,
    receiver_id INT REFERENCES user (id) ON DELETE CASCADE,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  );

CREATE TABLE
  favorites (
    user_id INT REFERENCES user (id) ON DELETE CASCADE,
    service_id INT REFERENCES service (id) ON DELETE CASCADE,
    PRIMARY KEY (user_id, service_id)
  );

CREATE TABLE
  service_category (id INT PRIMARY KEY, name VARCHAR NOT NULL UNIQUE);

CREATE TABLE
  service_status (id INT PRIMARY KEY, name VARCHAR NOT NULL UNIQUE);

CREATE TABLE
  service_review (
    id INT PRIMARY KEY,
    service_id INT REFERENCES service (id) ON DELETE CASCADE,
    user_id INT REFERENCES user (id) ON DELETE CASCADE,
    rating INT CHECK (
      rating >= 1
      AND rating <= 5
    ),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  );