PRAGMA foreign_keys = ON;

DROP TABLE IF EXISTS service_order;
DROP TABLE IF EXISTS service_review;
DROP TABLE IF EXISTS favorites;
DROP TABLE IF EXISTS message;
DROP TABLE IF EXISTS service;
DROP TABLE IF EXISTS admin;
DROP TABLE IF EXISTS user;
DROP TABLE IF EXISTS service_category;
DROP TABLE IF EXISTS service_status;

CREATE TABLE user (
  id INTEGER PRIMARY KEY,
  username TEXT NOT NULL UNIQUE,
  email TEXT NOT NULL UNIQUE,
  password TEXT NOT NULL,
  age INTEGER,
  phone TEXT NOT NULL UNIQUE,
  profile_picture TEXT,
  location TEXT DEFAULT 'Unknown',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  bio TEXT DEFAULT 'Start writing your bio here...'
);

CREATE TABLE admin (
  user_id INTEGER PRIMARY KEY REFERENCES user (id) ON DELETE CASCADE
);

CREATE TABLE service_category (
  id INTEGER PRIMARY KEY,
  name TEXT NOT NULL UNIQUE
);

CREATE TABLE service (
  id INTEGER PRIMARY KEY,
  creator_id INTEGER REFERENCES user (id) ON DELETE CASCADE,
  title TEXT NOT NULL,
  description TEXT NOT NULL,
  price REAL NOT NULL,
  rating REAL CHECK (rating >= 0 AND rating <= 5) DEFAULT NULL,
  image TEXT,
  location TEXT NOT NULL DEFAULT 'Unknown',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  category INTEGER REFERENCES service_category (id) ON DELETE SET NULL
);

CREATE TABLE message (
  id INTEGER PRIMARY KEY,
  sender_id INTEGER REFERENCES user (id) ON DELETE CASCADE,
  receiver_id INTEGER REFERENCES user (id) ON DELETE CASCADE,
  content TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE favorites (
  user_id INTEGER REFERENCES user (id) ON DELETE CASCADE,
  service_id INTEGER REFERENCES service (id) ON DELETE CASCADE,
  PRIMARY KEY (user_id, service_id)
);

CREATE TABLE service_status (
  id INTEGER PRIMARY KEY,
  name TEXT NOT NULL UNIQUE
);

CREATE TABLE service_review (
  id INTEGER PRIMARY KEY,
  service_id INTEGER REFERENCES service (id) ON DELETE CASCADE,
  reviewer_id INTEGER REFERENCES user (id) ON DELETE CASCADE,
  rating INTEGER CHECK (rating >= 1 AND rating <= 5),
  text TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE service_order (
  id INTEGER PRIMARY KEY,
  service_id INTEGER REFERENCES service (id) ON DELETE CASCADE,
  customer_id INTEGER REFERENCES user (id) ON DELETE CASCADE,
  status INTEGER REFERENCES service_status (id) ON DELETE SET NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);