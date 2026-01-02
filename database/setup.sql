-- Database Setup for Ride-ease
-- Run this script in your MySQL interface (phpMyAdmin, Workbench, CLI)

CREATE DATABASE IF NOT EXISTS ride_ease;
USE ride_ease;

-- Create App User for localhost
CREATE USER IF NOT EXISTS 'app_user'@'localhost' IDENTIFIED BY 'AppUserPassword@123';
GRANT ALL PRIVILEGES ON ride_ease.* TO 'app_user'@'localhost';

-- Create App User for 127.0.0.1 (TCP/IP connection)
CREATE USER IF NOT EXISTS 'app_user'@'127.0.0.1' IDENTIFIED BY 'AppUserPassword@123';
GRANT ALL PRIVILEGES ON ride_ease.* TO 'app_user'@'127.0.0.1';

FLUSH PRIVILEGES;

-- 1. Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(15),
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Vehicles Table
CREATE TABLE IF NOT EXISTS vehicles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category ENUM('bike', 'scooter', 'ebike') NOT NULL,
    price_per_day DECIMAL(10, 2) NOT NULL,
    image VARCHAR(255) NOT NULL,
    description TEXT,
    status ENUM('available', 'maintenance', 'rented') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Bookings Table
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    vehicle_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id)
);

-- 4. Seed Data: Default Admin
-- Username: admin, Password: password123 (hashed)
INSERT INTO users (username, email, password, role) VALUES 
('Admin', 'admin@ride-ease.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- 5. Seed Data: Vehicles (From existing HTML)
INSERT INTO vehicles (name, category, price_per_day, image) VALUES
('Hunter 350', 'bike', 1399.00, 'assets/img/hunt-services.avif'),
('Honda SP 125', 'bike', 799.00, 'assets/img/sp125sprightfrontthreequarter.jpeg'),
('TVS Rider 125', 'bike', 1400.00, 'assets/img/raider125raiderrightfrontthreequarter.jpeg'),
('Bajaj Pulsar 125', 'bike', 1400.00, 'assets/img/pulsar125bajajpulsarfrontthreequarter.jpeg'),
('Duke 390', 'bike', 1400.00, 'assets/img/390dukektmdukerightfrontthreequarter16.jpeg'),
('Dominar 400', 'bike', 1400.00, 'assets/img/dominar400bajajdominarrightsideview6.jpeg'),
('Husqvarna Svartpilen', 'bike', 1400.00, 'assets/img/svartpilen401svartpilenrightfrontthreequarter.jpeg'),
('TVS Apache RTR 160', 'bike', 1400.00, 'assets/img/apachertr1604vapachertrvrightfrontthreequarter.png'),
('Ather 450X', 'ebike', 999.00, 'assets/img/450ximage.png'),
('TVS iQube', 'ebike', 1199.00, 'assets/img/Ele1.jpg'),
('Bajaj Chetak', 'ebike', 1299.00, 'assets/img/ele2.jpeg'),
('Ola S1 pro', 'ebike', 1099.00, 'assets/img/s1pros1progenrightsideview.png');
