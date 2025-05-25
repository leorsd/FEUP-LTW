-- This is the seed file for populating the database with initial data
PRAGMA foreign_keys = ON;

-- Delete from child tables first, then parent tables to avoid FK errors
DELETE FROM service_order;
DELETE FROM service_review;
DELETE FROM favorites;
DELETE FROM message;
DELETE FROM service;
DELETE FROM admin;
DELETE FROM user;
DELETE FROM service_category;
DELETE FROM service_status;

-- Passwords (already hashed, generated with PHP's password_hash)
-- admin1: adminpass1
-- admin2: adminpass2
-- freelancer1: freelancerpass
-- user1: userpass

INSERT INTO user (email, username, password, age, phone, location, created_at, bio) VALUES
-- Admins (not using 'user' in username)
('admin1@example.com', 'admin1', '$2y$10$bSm6DSjNJEtCK8RGkTNW0ebM3jI67F39SbqBNBGvp1I3voKl3GcCq', 40, '3000000001', 'HQ', '2025-05-01 07:00:00', 'System administrator 1.'),
('admin2@example.com', 'admin2', '$2y$10$meNt48BvQKUctZj9owS.TORQ/xg8lZ08n13aOdI1.hHpbkON1Y.Ry', 41, '3000000002', 'HQ', '2025-05-01 07:30:00', 'System administrator 2.'),
-- Freelancers
('freelancer1@example.com', 'freelancer1', '$2y$10$4UKJNh.14nFgmvA/JUzGlOA5Z1WvyKK404ka.W0D23JXYxBdgXqtO', 30, '1000000001', 'Downtown', '2025-05-01 08:00:00', 'Freelancer 1 bio.'),
('freelancer2@example.com', 'freelancer2', '$2y$10$abcdefghijklmnopqrstuvOPQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQw', 31, '1000000002', 'Uptown', '2025-05-02 09:00:00', 'Freelancer 2 bio.'),
('freelancer3@example.com', 'freelancer3', '$2y$10$abcdefghijklmnopqrstuvOPQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQw', 32, '1000000003', 'Suburbs', '2025-05-03 10:00:00', 'Freelancer 3 bio.'),
('freelancer4@example.com', 'freelancer4', '$2y$10$abcdefghijklmnopqrstuvOPQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQw', 33, '1000000004', 'City Center', '2025-05-04 11:00:00', 'Freelancer 4 bio.'),
('freelancer5@example.com', 'freelancer5', '$2y$10$abcdefghijklmnopqrstuvOPQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQw', 34, '1000000005', 'Downtown', '2025-05-05 12:00:00', 'Freelancer 5 bio.'),
('freelancer6@example.com', 'freelancer6', '$2y$10$abcdefghijklmnopqrstuvOPQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQw', 35, '1000000006', 'Industrial Area', '2025-05-06 13:00:00', 'Freelancer 6 bio.'),
('freelancer7@example.com', 'freelancer7', '$2y$10$abcdefghijklmnopqrstuvOPQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQw', 36, '1000000007', 'Tech Park', '2025-05-07 14:00:00', 'Freelancer 7 bio.'),
('freelancer8@example.com', 'freelancer8', '$2y$10$abcdefghijklmnopqrstuvOPQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQw', 37, '1000000008', 'City Center', '2025-05-08 15:00:00', 'Freelancer 8 bio.'),
('freelancer9@example.com', 'freelancer9', '$2y$10$abcdefghijklmnopqrstuvOPQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQw', 38, '1000000009', 'Downtown', '2025-05-09 16:00:00', 'Freelancer 9 bio.'),
('freelancer10@example.com', 'freelancer10', '$2y$10$abcdefghijklmnopqrstuvOPQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQw', 39, '1000000010', 'Uptown', '2025-05-10 17:00:00', 'Freelancer 10 bio.'),
-- 15 regular users
('user1@example.com', 'user1', '$2y$10$6kLyGb.2yLW3R6lSAqyWXuU8Jdx2xkllLZXk4Ywo8Jvi5z0JyiDp.', 25, '2000000001', 'Downtown', '2025-05-11 08:00:00', 'User 1 bio.'),
('user2@example.com', 'user2', '$2y$10$abcdefghijklmnopqrstuvOPQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQw', 26, '2000000002', 'Uptown', '2025-05-12 09:00:00', 'User 2 bio.'),
('user3@example.com', 'user3', '$2y$10$abcdefghijklmnopqrstuvOPQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQw', 27, '2000000003', 'Suburbs', '2025-05-13 10:00:00', 'User 3 bio.'),
('user4@example.com', 'user4', '$2y$10$abcdefghijklmnopqrstuvOPQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQw', 28, '2000000004', 'City Center', '2025-05-14 11:00:00', 'User 4 bio.'),
('user5@example.com', 'user5', '$2y$10$abcdefghijklmnopqrstuvOPQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQw', 29, '2000000005', 'Downtown', '2025-05-15 12:00:00', 'User 5 bio.'),
('user6@example.com', 'user6', '$2y$10$abcdefghijklmnopqrstuvOPQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQw', 24, '2000000006', 'Uptown', '2025-05-16 08:00:00', 'User 6 bio.'),
('user7@example.com', 'user7', '$2y$10$abcdefghijklmnopqrstuvOPQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQw', 23, '2000000007', 'Suburbs', '2025-05-17 09:00:00', 'User 7 bio.'),
('user8@example.com', 'user8', '$2y$10$abcdefghijklmnopqrstuvOPQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQw', 22, '2000000008', 'City Center', '2025-05-18 10:00:00', 'User 8 bio.'),
('user9@example.com', 'user9', '$2y$10$abcdefghijklmnopqrstuvOPQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQw', 31, '2000000009', 'Downtown', '2025-05-19 11:00:00', 'User 9 bio.'),
('user10@example.com', 'user10', '$2y$10$abcdefghijklmnopqrstuvOPQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQw', 32, '2000000010', 'Uptown', '2025-05-20 12:00:00', 'User 10 bio.'),
('user11@example.com', 'user11', '$2y$10$abcdefghijklmnopqrstuvOPQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQw', 33, '2000000011', 'Suburbs', '2025-05-21 13:00:00', 'User 11 bio.'),
('user12@example.com', 'user12', '$2y$10$abcdefghijklmnopqrstuvOPQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQw', 34, '2000000012', 'City Center', '2025-05-22 14:00:00', 'User 12 bio.'),
('user13@example.com', 'user13', '$2y$10$abcdefghijklmnopqrstuvOPQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQw', 35, '2000000013', 'Downtown', '2025-05-23 15:00:00', 'User 13 bio.'),
('user14@example.com', 'user14', '$2y$10$abcdefghijklmnopqrstuvOPQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQw', 36, '2000000014', 'Uptown', '2025-05-24 16:00:00', 'User 14 bio.'),
('user15@example.com', 'user15', '$2y$10$abcdefghijklmnopqrstuvOPQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQw', 37, '2000000015', 'Suburbs', '2025-05-25 17:00:00', 'User 15 bio.');

-- Admins (first two users)
INSERT INTO admin (user_id) VALUES (1), (2);

-- Categories
INSERT INTO service_category (name) VALUES
('Car Wash'),
('Oil Change'),
('Engine Repair'),
('Brakes'),
('Wheels'),
('Paint'),
('Electronics'),
('Infotainment'),
('Interior');

-- Statuses
INSERT INTO service_status (name) VALUES
('Ordered'),
('Waiting for Payment'),
('In Progress'),
('Completed'),
('Rejected');

-- Services
-- 7 services for each freelancer (10 freelancers x 7 = 70 services)
INSERT INTO service (creator_id, title, description, price, location, created_at, category) VALUES
-- Freelancer 1 (id 3)
(3, 'Car Wash Deluxe', 'Deluxe car wash service.', 30.00, 'Downtown', '2025-05-01 10:00:00', 1),
(3, 'Express Oil Change', 'Quick oil change.', 35.00, 'Downtown', '2025-05-01 11:00:00', 2),
(3, 'Engine Check', 'Full engine diagnostics.', 120.00, 'Downtown', '2025-05-01 12:00:00', 3),
(3, 'Brake Fix', 'Brake replacement.', 90.00, 'Downtown', '2025-05-01 13:00:00', 4),
(3, 'Wheel Alignment', 'Professional alignment.', 50.00, 'Downtown', '2025-05-01 14:00:00', 5),
(3, 'Paint Touchup', 'Minor paint repairs.', 80.00, 'Downtown', '2025-05-01 15:00:00', 6),
(3, 'Stereo Install', 'Car stereo installation.', 110.00, 'Downtown', '2025-05-01 16:00:00', 7),
-- Freelancer 2 (id 4)
(4, 'Eco Car Wash', 'Eco-friendly wash.', 28.00, 'Uptown', '2025-05-02 10:00:00', 1),
(4, 'Premium Oil Change', 'Premium oil change.', 45.00, 'Uptown', '2025-05-02 11:00:00', 2),
(4, 'Engine Tune-Up', 'Engine tuning.', 130.00, 'Uptown', '2025-05-02 12:00:00', 3),
(4, 'Brake Inspection', 'Brake check.', 60.00, 'Uptown', '2025-05-02 13:00:00', 4),
(4, 'Tire Rotation', 'Tire rotation service.', 25.00, 'Uptown', '2025-05-02 14:00:00', 5),
(4, 'Full Paint Job', 'Complete paint job.', 400.00, 'Uptown', '2025-05-02 15:00:00', 6),
(4, 'GPS Install', 'GPS installation.', 150.00, 'Uptown', '2025-05-02 16:00:00', 8),
-- Freelancer 3 (id 5)
(5, 'Quick Wash', 'Fast car wash.', 20.00, 'Suburbs', '2025-05-03 10:00:00', 1),
(5, 'Synthetic Oil', 'Synthetic oil change.', 55.00, 'Suburbs', '2025-05-03 11:00:00', 2),
(5, 'Engine Overhaul', 'Complete overhaul.', 1000.00, 'Suburbs', '2025-05-03 12:00:00', 3),
(5, 'Brake Pads', 'Brake pad replacement.', 70.00, 'Suburbs', '2025-05-03 13:00:00', 4),
(5, 'Wheel Balancing', 'Wheel balancing.', 40.00, 'Suburbs', '2025-05-03 14:00:00', 5),
(5, 'Custom Paint', 'Custom paintwork.', 500.00, 'Suburbs', '2025-05-03 15:00:00', 6),
(5, 'Bluetooth Audio', 'Bluetooth audio install.', 120.00, 'Suburbs', '2025-05-03 16:00:00', 7),
-- Freelancer 4 (id 6)
(6, 'Hand Wash', 'Hand car wash.', 25.00, 'City Center', '2025-05-04 10:00:00', 1),
(6, 'Standard Oil', 'Standard oil change.', 38.00, 'City Center', '2025-05-04 11:00:00', 2),
(6, 'Engine Flush', 'Engine flushing.', 140.00, 'City Center', '2025-05-04 12:00:00', 3),
(6, 'Brake Bleed', 'Brake bleeding.', 65.00, 'City Center', '2025-05-04 13:00:00', 4),
(6, 'Tire Change', 'Tire change service.', 35.00, 'City Center', '2025-05-04 14:00:00', 5),
(6, 'Scratch Repair', 'Scratch repair.', 90.00, 'City Center', '2025-05-04 15:00:00', 6),
(6, 'Dash Cam Install', 'Dash cam installation.', 130.00, 'City Center', '2025-05-04 16:00:00', 8),
-- Freelancer 5 (id 7)
(7, 'Steam Wash', 'Steam car wash.', 32.00, 'Downtown', '2025-05-05 10:00:00', 1),
(7, 'Express Oil', 'Express oil change.', 36.00, 'Downtown', '2025-05-05 11:00:00', 2),
(7, 'Engine Diagnostics', 'Diagnostics service.', 160.00, 'Downtown', '2025-05-05 12:00:00', 3),
(7, 'Brake Service', 'Brake servicing.', 85.00, 'Downtown', '2025-05-05 13:00:00', 4),
(7, 'Wheel Repair', 'Wheel repair.', 55.00, 'Downtown', '2025-05-05 14:00:00', 5),
(7, 'Paint Correction', 'Paint correction.', 200.00, 'Downtown', '2025-05-05 15:00:00', 6),
(7, 'Alarm Install', 'Car alarm installation.', 140.00, 'Downtown', '2025-05-05 16:00:00', 7),
-- Freelancer 6 (id 8)
(8, 'Eco Wash', 'Eco-friendly wash.', 29.00, 'Industrial Area', '2025-05-06 10:00:00', 1),
(8, 'Premium Oil', 'Premium oil change.', 48.00, 'Industrial Area', '2025-05-06 11:00:00', 2),
(8, 'Engine Swap', 'Engine swap service.', 2000.00, 'Industrial Area', '2025-05-06 12:00:00', 3),
(8, 'Brake Upgrade', 'Brake upgrade.', 150.00, 'Industrial Area', '2025-05-06 13:00:00', 4),
(8, 'Wheel Custom', 'Custom wheels.', 300.00, 'Industrial Area', '2025-05-06 14:00:00', 5),
(8, 'Full Repaint', 'Full car repaint.', 900.00, 'Industrial Area', '2025-05-06 15:00:00', 6),
(8, 'Remote Start', 'Remote start install.', 180.00, 'Industrial Area', '2025-05-06 16:00:00', 7),
-- Freelancer 7 (id 9)
(9, 'Quick Clean', 'Quick car clean.', 18.00, 'Tech Park', '2025-05-07 10:00:00', 1),
(9, 'Oil Top-Up', 'Oil top-up service.', 22.00, 'Tech Park', '2025-05-07 11:00:00', 2),
(9, 'Engine Tune', 'Engine tuning.', 110.00, 'Tech Park', '2025-05-07 12:00:00', 3),
(9, 'Brake Adjust', 'Brake adjustment.', 55.00, 'Tech Park', '2025-05-07 13:00:00', 4),
(9, 'Wheel Paint', 'Wheel painting.', 70.00, 'Tech Park', '2025-05-07 14:00:00', 5),
(9, 'Spot Paint', 'Spot paint repair.', 60.00, 'Tech Park', '2025-05-07 15:00:00', 6),
(9, 'Speaker Install', 'Speaker installation.', 100.00, 'Tech Park', '2025-05-07 16:00:00', 7),
-- Freelancer 8 (id 10)
(10, 'Hand Polish', 'Hand car polish.', 45.00, 'City Center', '2025-05-08 10:00:00', 1),
(10, 'Oil Flush', 'Oil flush service.', 40.00, 'City Center', '2025-05-08 11:00:00', 2),
(10, 'Engine Clean', 'Engine cleaning.', 90.00, 'City Center', '2025-05-08 12:00:00', 3),
(10, 'Brake Clean', 'Brake cleaning.', 35.00, 'City Center', '2025-05-08 13:00:00', 4),
(10, 'Tire Shine', 'Tire shining.', 20.00, 'City Center', '2025-05-08 14:00:00', 5),
(10, 'Paint Sealant', 'Paint sealant application.', 75.00, 'City Center', '2025-05-08 15:00:00', 6),
(10, 'Navigation Install', 'Navigation system install.', 160.00, 'City Center', '2025-05-08 16:00:00', 8),
-- Freelancer 9 (id 11)
(11, 'Basic Wash', 'Basic car wash.', 15.00, 'Downtown', '2025-05-09 10:00:00', 1),
(11, 'Oil Filter', 'Oil filter change.', 27.00, 'Downtown', '2025-05-09 11:00:00', 2),
(11, 'Engine Mount', 'Engine mount replacement.', 210.00, 'Downtown', '2025-05-09 12:00:00', 3),
(11, 'Brake Fluid', 'Brake fluid replacement.', 45.00, 'Downtown', '2025-05-09 13:00:00', 4),
(11, 'Wheel Swap', 'Wheel swap.', 60.00, 'Downtown', '2025-05-09 14:00:00', 5),
(11, 'Paint Blending', 'Paint blending.', 85.00, 'Downtown', '2025-05-09 15:00:00', 6),
(11, 'USB Charger', 'USB charger install.', 35.00, 'Downtown', '2025-05-09 16:00:00', 7),
-- Freelancer 10 (id 12)
(12, 'Deluxe Wash', 'Deluxe car wash.', 32.00, 'Uptown', '2025-05-10 10:00:00', 1),
(12, 'Oil Inspection', 'Oil inspection.', 30.00, 'Uptown', '2025-05-10 11:00:00', 2),
(12, 'Engine Light Fix', 'Fix engine light.', 95.00, 'Uptown', '2025-05-10 12:00:00', 3),
(12, 'Brake Test', 'Brake testing.', 40.00, 'Uptown', '2025-05-10 13:00:00', 4),
(12, 'Wheel Polish', 'Wheel polishing.', 55.00, 'Uptown', '2025-05-10 14:00:00', 5),
(12, 'Paint Match', 'Paint matching.', 110.00, 'Uptown', '2025-05-10 15:00:00', 6),
(12, 'Android Auto', 'Android Auto install.', 170.00, 'Uptown', '2025-05-10 16:00:00', 8);


-- Orders: users 14, 15, 16, 17 order services from freelancer1 (id 3)
INSERT INTO service_order (service_id, customer_id, status, created_at) VALUES
(1, 14, 1, '2025-06-01 09:00:00'),
(2, 15, 2, '2025-06-01 10:00:00'),
(3, 16, 3, '2025-06-01 11:00:00'),
(4, 17, 4, '2025-06-01 12:00:00');

-- Orders: users 18, 19, 20, 21 order services from freelancer2 (id 4)
INSERT INTO service_order (service_id, customer_id, status, created_at) VALUES
(8, 18, 1, '2025-06-02 09:00:00'),
(9, 19, 2, '2025-06-02 10:00:00'),
(10, 20, 3, '2025-06-02 11:00:00'),
(11, 21, 4, '2025-06-02 12:00:00');

-- Reviews: users 14-17 review freelancer1's services
INSERT INTO service_review (service_id, reviewer_id, rating, text, created_at) VALUES
(1, 14, 5, 'Amazing wash!', '2025-06-01 13:00:00'),
(2, 15, 4, 'Oil change was fast.', '2025-06-01 14:00:00'),
(3, 16, 5, 'Engine check was thorough.', '2025-06-01 15:00:00'),
(4, 17, 3, 'Brake fix was ok.', '2025-06-01 16:00:00');

-- Reviews: users 18-21 review freelancer2's services
INSERT INTO service_review (service_id, reviewer_id, rating, text, created_at) VALUES
(8, 18, 5, 'Eco wash is the best!', '2025-06-02 13:00:00'),
(9, 19, 4, 'Premium oil change, recommended.', '2025-06-02 14:00:00'),
(10, 20, 5, 'Engine tune-up improved my car.', '2025-06-02 15:00:00'),
(11, 21, 4, 'Brake inspection was helpful.', '2025-06-02 16:00:00');

