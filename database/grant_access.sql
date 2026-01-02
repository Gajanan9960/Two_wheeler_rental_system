-- Run this script as ROOT to fix the "Access Denied" error

CREATE DATABASE IF NOT EXISTS ride_ease;

-- Create the user if they don't exist
CREATE USER IF NOT EXISTS 'app_user'@'localhost' IDENTIFIED BY 'AppUserPassword@123';
CREATE USER IF NOT EXISTS 'app_user'@'127.0.0.1' IDENTIFIED BY 'AppUserPassword@123';

-- Grant full access to the ride_ease database
GRANT ALL PRIVILEGES ON ride_ease.* TO 'app_user'@'localhost';
GRANT ALL PRIVILEGES ON ride_ease.* TO 'app_user'@'127.0.0.1';

-- Apply changes
FLUSH PRIVILEGES;

-- Verify
SHOW GRANTS FOR 'app_user'@'localhost';
