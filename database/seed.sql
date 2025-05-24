-- This is the seed file for populating the database with initial data
PRAGMA foreign_keys = ON;

DELETE FROM service;
DELETE FROM service_category;
DELETE FROM user;
DELETE FROM service_status;
DELETE FROM service_review;


INSERT INTO user (email, username, password, age, phone, location, created_at, bio) VALUES
('john.doe@example.com', 'john', 'password123', 30, '1234567890', 'Downtown', '2025-05-01 08:00:00', 'Car enthusiast and professional detailer.'),
('jane.smith@example.com', 'jane', 'password123', 28, '1234567891', 'Uptown', '2025-05-02 09:00:00', 'Oil change specialist with years of experience.'),
('mike.johnson@example.com','mike',  'password123', 35, '1234567892', 'Suburbs', '2025-05-03 10:00:00', 'Expert in engine diagnostics and repair.'),
('susan.brown@example.com', 'susan',  'password123', 32, '1234567893', 'City Center', '2025-05-04 11:00:00', 'Brake replacement and maintenance professional.'),
('david.wilson@example.com', 'david' , 'password123', 40, '1234567894', 'Downtown', '2025-05-05 12:00:00', 'Wheel alignment and balancing expert.'),
('lisa.taylor@example.com', 'lisa',  'password123', 29, '1234567895', 'Industrial Area', '2025-05-06 13:00:00', 'Specialist in custom car painting.'),
('paul.martin@example.com', 'paul', 'password123', 33, '1234567896', 'Tech Park', '2025-05-07 14:00:00', 'Car electronics repair and installation expert.'),
('emma.thomas@example.com', 'emma', 'password123', 27, '1234567897', 'City Center', '2025-05-08 15:00:00', 'Infotainment system upgrade specialist.'),
('olivia.james@example.com', 'olivia' ,'password123', 31, '1234567898', 'Downtown', '2025-05-09 16:00:00', 'Interior detailing professional.');


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
(1, 'Ordered'),
(2, 'In Progress'),
(3, 'Completed');


INSERT INTO service (id, creator_id, title, description, price, location, created_at, category) VALUES
(1, 1, 'Premium Car Wash', 'A thorough car wash service for your vehicle.', 25.00, 'Downtown', '2025-05-01 10:00:00', 1),
(2, 2, 'Quick Oil Change', 'Fast and affordable oil change service.', 40.00, 'Uptown', '2025-05-02 12:00:00', 2),
(3, 3, 'Engine Diagnostics', 'Comprehensive engine diagnostics and repair.', 150.00, 'Suburbs', '2025-05-03 14:00:00', 3),
(4, 4, 'Brake Replacement', 'High-quality brake replacement service.', 120.00, 'City Center', '2025-05-04 16:00:00', 4),
(5, 5, 'Wheel Alignment', 'Professional wheel alignment for your car.', 60.00, 'Downtown', '2025-05-05 18:00:00', 5),
(6, 6, 'Car Painting', 'Custom car painting service.', 300.00, 'Industrial Area', '2025-05-06 20:00:00', 6),
(7, 7, 'Car Electronics Repair', 'Repair and installation of car electronics.', 200.00, 'Tech Park', '2025-05-07 22:00:00', 7),
(8, 8, 'Infotainment System Upgrade', 'Upgrade your car infotainment system.', 500.00, 'City Center', '2025-05-08 09:00:00', 8),
(9, 9, 'Interior Detailing', 'Complete interior detailing for your car.', 100.00, 'Downtown', '2025-05-09 11:00:00', 9),
(10, 1, 'Basic Car Wash', 'A quick wash for your vehicle.', 15.00, 'Downtown', '2025-05-10 08:00:00', 1),
(11, 2, 'Synthetic Oil Change', 'High-quality synthetic oil change service.', 60.00, 'Uptown', '2025-05-11 09:00:00', 2),
(12, 3, 'Engine Overhaul', 'Complete engine overhaul service.', 1200.00, 'Suburbs', '2025-05-12 10:00:00', 3),
(13, 4, 'Brake Inspection', 'Comprehensive brake inspection service.', 50.00, 'City Center', '2025-05-13 11:00:00', 4),
(14, 5, 'Tire Rotation', 'Professional tire rotation service.', 30.00, 'Downtown', '2025-05-14 12:00:00', 5),
(15, 6, 'Custom Vinyl Wrap', 'Custom vinyl wrap for your car.', 800.00, 'Industrial Area', '2025-05-15 13:00:00', 6),
(16, 7, 'Car Audio Installation', 'Installation of car audio systems.', 250.00, 'Tech Park', '2025-05-16 14:00:00', 7),
(17, 8, 'Navigation System Upgrade', 'Upgrade your car navigation system.', 600.00, 'City Center', '2025-05-17 15:00:00', 8),
(18, 9, 'Leather Seat Cleaning', 'Professional leather seat cleaning service.', 120.00, 'Downtown', '2025-05-18 16:00:00', 9),
(19, 10, 'Mobile Detailing', 'On-site car detailing at your convenience.', 80.00, 'Suburbs', '2025-05-19 10:00:00', 1),
(20, 10, 'Eco-Friendly Wash', 'Eco-friendly car wash using minimal water.', 35.00, 'Uptown', '2025-05-20 11:00:00', 1),
(21, 10, 'Express Interior Clean', 'Quick interior cleaning for busy schedules.', 45.00, 'Downtown', '2025-05-21 12:00:00', 9);


INSERT INTO service_customer (service_id, customer_id, status, created_at) VALUES
(1, 10, 3, '2025-05-12 10:00:00'), -- Premium Car Wash, completed
(2, 10, 2, '2025-05-13 11:00:00'), -- Quick Oil Change, in progress
(3, 10, 1, '2025-05-14 12:00:00'), -- Engine Diagnostics, ordered
(19, 1, 1, '2025-05-19 13:00:00'), -- Mobile Detailing, ordered
(19, 2, 1, '2025-05-19 13:00:00'), -- Mobile Detailing, ordered
(20, 2, 2, '2025-05-20 14:00:00'), -- Eco-Friendly Wash, in progress
(21, 3, 3, '2025-05-21 15:00:00'); -- Express Interior Clean, completed
