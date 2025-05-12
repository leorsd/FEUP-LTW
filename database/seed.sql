-- This is the seed file for populating the database with initial data
PRAGMA foreign_keys = ON;

DELETE FROM service;
DELETE FROM service_category;
DELETE FROM user;

INSERT INTO user (email, username, password, age, phone, profile_picture, location, created_at, bio) VALUES
('john.doe@example.com', 'john', 'password123', 30, '1234567890', 'user.jpg', 'Downtown', '2025-05-01 08:00:00', 'Car enthusiast and professional detailer.'),
('jane.smith@example.com', 'jane', 'password123', 28, '1234567891', 'user.jpg', 'Uptown', '2025-05-02 09:00:00', 'Oil change specialist with years of experience.'),
('mike.johnson@example.com','mike',  'password123', 35, '1234567892', 'user.jpg', 'Suburbs', '2025-05-03 10:00:00', 'Expert in engine diagnostics and repair.'),
('susan.brown@example.com', 'susan',  'password123', 32, '1234567893', 'user.jpg', 'City Center', '2025-05-04 11:00:00', 'Brake replacement and maintenance professional.'),
('david.wilson@example.com', 'david' , 'password123', 40, '1234567894', 'user.jpg', 'Downtown', '2025-05-05 12:00:00', 'Wheel alignment and balancing expert.'),
('lisa.taylor@example.com', 'lisa',  'password123', 29, '1234567895', 'user.jpg', 'Industrial Area', '2025-05-06 13:00:00', 'Specialist in custom car painting.'),
('paul.martin@example.com', 'paul', 'password123', 33, '1234567896', 'user.jpg', 'Tech Park', '2025-05-07 14:00:00', 'Car electronics repair and installation expert.'),
('emma.thomas@example.com', 'emma', 'password123', 27, '1234567897', 'user.jpg', 'City Center', '2025-05-08 15:00:00', 'Infotainment system upgrade specialist.'),
('olivia.james@example.com', 'olivia' ,'password123', 31, '1234567898', 'user.jpg', 'Downtown', '2025-05-09 16:00:00', 'Interior detailing professional.');

INSERT INTO service_category (id, name) VALUES
(1, 'Car Wash'),
(2, 'Oil Change'),
(3, 'Engine Repair'),
(4, 'Brakes'),
(5, 'Wheels'),
(6, 'Paint'),
(7, 'Electronics'),
(8, 'Infotainment'),
(9, 'Interior');

INSERT INTO service_status (id, name) VALUES
(1, 'Open'),
(2, 'Completed'),
(3, 'Cancelled');

INSERT INTO service (id, creator_id, title, description, price, rating, location, created_at, status, category) VALUES
(1, 1, 'Premium Car Wash', 'A thorough car wash service for your vehicle.', 25.00, 4.5, 'Downtown', '2025-05-01 10:00:00', 1, 1),
(2, 2, 'Quick Oil Change', 'Fast and affordable oil change service.', 40.00, 4.8, 'Uptown', '2025-05-02 12:00:00', 1, 2),
(3, 3, 'Engine Diagnostics', 'Comprehensive engine diagnostics and repair.', 150.00, 4.9, 'Suburbs', '2025-05-03 14:00:00', 1, 3),
(4, 4, 'Brake Replacement', 'High-quality brake replacement service.', 120.00, 4.7, 'City Center', '2025-05-04 16:00:00', 1, 4),
(5, 5, 'Wheel Alignment', 'Professional wheel alignment for your car.', 60.00, 4.6, 'Downtown', '2025-05-05 18:00:00', 2, 5),
(6, 6, 'Car Painting', 'Custom car painting service.', 300.00, 4.4, 'car_painting.jpg', '2025-05-06 20:00:00', 1, 6),
(7, 7, 'Car Electronics Repair', 'Repair and installation of car electronics.', 200.00, 4.3,'Tech Park', '2025-05-07 22:00:00', 1, 7),
(8, 8, 'Infotainment System Upgrade', 'Upgrade your car infotainment system.', 500.00, 4.9, 'City Center', '2025-05-08 09:00:00', 2, 8),
(9, 9, 'Interior Detailing', 'Complete interior detailing for your car.', 100.00, 4.8, 'Downtown', '2025-05-09 11:00:00', 3, 9);
