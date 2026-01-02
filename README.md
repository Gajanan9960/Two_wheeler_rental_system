# Ride-Ease: Two-Wheeler Rental System

Welcome to the Ride-Ease Two-Wheeler Rental System! This application is designed to provide a seamless rental experience for users and a robust management interface for administrators.

## 🚀 Getting Started on Windows (Seamless Setup)

This project handles paths and configurations to work smoothly on Windows environments like XAMPP, WAMP, or Laragon.

### Prerequisites
1.  **XAMPP** (Recommended) or WAMP Server installed.
    -   Download XAMPP: [apachefriends.org](https://www.apachefriends.org/index.html)
2.  **Git** (Optional, to clone the repo).

### Installation Steps

1.  **Clone/Copy the Project**:
    -   Place the project folder inside your `htdocs` directory.
    -   Example: `C:\xampp\htdocs\ride-ease`

2.  **Start Services**:
    -   Open **XAMPP Control Panel**.
    -   Start **Apache** and **MySQL**.

3.  **Database Setup**:
    -   Open your browser and search for `http://localhost/phpmyadmin`.
    -   Click the **Import** tab.
    -   Select the file `database/setup.sql` from the project folder.
    -   Click **Go** at the bottom.
    -   *Note: This script will create the database and tables.*
    
    **Fixing Permissions (Important):**
    -   The default database configuration assumes a specific user. If you encounter "Access Denied", you can fix it by running the `database/fix_db_user.sql` script in the SQL tab of phpMyAdmin.
    -   Alternatively, edit `config/db.php` to match your local Setup (Default XAMPP user is `root` with NO password).

4.  **Verify Environment**:
    -   Visit `http://localhost/ride-ease/check_env.php` to ensure everything is configured correctly.

5.  **Run the App**:
    -   Go to `http://localhost/ride-ease/`
    -   **Admin Login**: `admin@ride-ease.com` / `password123`
    -   **User Login**: Sign up for a new account.

---

## 🍎 Getting Started on Mac/Linux

1.  **Start MySQL Service**:
    ```bash
    brew services start mysql  # MacOS
    sudo service mysql start   # Linux
    ```

2.  **Setup Database**:
    ```bash
    mysql -u root -p < database/setup.sql
    mysql -u root -p < database/grant_access.sql
    ```

3.  **Run with Built-in Server**:
    ```bash
    php -S localhost:8000
    ```
    -   Visit `http://localhost:8000`

## ✅ Key Features
-   **User Dashboard**: Browse vehicles, book rides, view history.
-   **Admin Dashboard**: Manage fleet (add/edit vehicles), view statistics, manage bookings.
-   **Responsive Design**: Works on Desktop and Mobile.
-   **Security**: Password hashing, role-based access control.

## 📁 Project Structure
-   `/assets`: CSS, JS, Images.
-   `/config`: Database connection.
-   `/database`: SQL schema and seed data.
-   `/includes`: Reusable Header/Footer.
-   `/modules`: Feature-specific logic (Auth, User, Admin, Bookings).
