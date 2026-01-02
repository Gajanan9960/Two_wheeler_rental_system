-- Force fix for Access Denied error
-- Run this as ROOT

-- 1. Drop the user to ensure a clean slate (ignore error if doesn't exist)
DROP USER IF EXISTS 'app_user'@'localhost';

-- 2. Re-create the user with the EXACT password from config/db.php
CREATE USER 'app_user'@'localhost' IDENTIFIED BY 'AppUserPassword@123';

-- 3. Grant privileges
GRANT ALL PRIVILEGES ON ride_ease.* TO 'app_user'@'localhost';

-- 4. Flush
FLUSH PRIVILEGES;

-- 5. Verify
SELECT user, host FROM mysql.user WHERE user = 'app_user';
