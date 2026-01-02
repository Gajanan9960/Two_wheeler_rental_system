<?php
$db_file = __DIR__ . '/../database/ride_ease.db';

if (!file_exists($db_file)) {
    // Create new SQLite database
    try {
        $pdo = new PDO("sqlite:$db_file");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Define Schema (Adapted from setup.sql)
        $queries = [
            "CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT NOT NULL,
                email TEXT NOT NULL UNIQUE,
                password TEXT NOT NULL,
                phone TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )",
            "CREATE TABLE IF NOT EXISTS admins (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT NOT NULL UNIQUE,
                password TEXT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )",
            "CREATE TABLE IF NOT EXISTS vehicles (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                category TEXT NOT NULL CHECK(category IN ('bike', 'scooter', 'ebike')),
                price_per_day REAL NOT NULL,
                image TEXT NOT NULL,
                description TEXT,
                status TEXT DEFAULT 'available' CHECK(status IN ('available', 'maintenance', 'rented')),
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )",
            "CREATE TABLE IF NOT EXISTS bookings (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                vehicle_id INTEGER NOT NULL,
                start_date DATE NOT NULL,
                end_date DATE NOT NULL,
                total_price REAL NOT NULL,
                status TEXT DEFAULT 'pending' CHECK(status IN ('pending', 'confirmed', 'completed', 'cancelled')),
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id),
                FOREIGN KEY (vehicle_id) REFERENCES vehicles(id)
            )",
            // Seed Admin
            "INSERT INTO admins (username, password) VALUES 
            ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi')",
            // Seed Vehicles
            "INSERT INTO vehicles (name, category, price_per_day, image) VALUES
            ('Honda SP 125', 'bike', 599.00, 'assets/img/sp125sprightfrontthreequarter.jpeg'),
            ('Bajaj Pulsar 125', 'bike', 699.00, 'assets/img/pulsar125bajajpulsarfrontthreequarter.jpeg'),
            ('TVS Raider 125', 'bike', 749.00, 'assets/img/raider125raiderrightfrontthreequarter.jpeg'),
            ('TVS Apache RTR 160', 'bike', 899.00, 'assets/img/apachertr1604vapachertrvrightfrontthreequarter.png'),
            ('Hunter 350', 'bike', 1299.00, 'assets/img/hunt-services.avif'),
            ('Dominar 400', 'bike', 1699.00, 'assets/img/dominar400bajajdominarrightsideview6.jpeg'),
            ('Husqvarna Svartpilen', 'bike', 1799.00, 'assets/img/svartpilen401svartpilenrightfrontthreequarter.jpeg'),
            ('Duke 390', 'bike', 2199.00, 'assets/img/390dukektmdukerightfrontthreequarter16.jpeg'),
            ('Ola S1 Pro', 'ebike', 699.00, 'assets/img/s1pros1progenrightsideview.png'),
            ('TVS iQube', 'ebike', 799.00, 'assets/img/Ele1.jpg'),
            ('Bajaj Chetak', 'ebike', 899.00, 'assets/img/ele2.jpeg'),
            ('Ather 450X', 'ebike', 999.00, 'assets/img/450ximage.png')"
        ];

        foreach ($queries as $query) {
            $pdo->exec($query);
        }
        
    } catch (PDOException $e) {
        die("Database Initialization Failed: " . $e->getMessage());
    }
}
?>
