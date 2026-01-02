# Ride-Ease: Two-Wheeler Rental System

Welcome to the Ride-Ease Two-Wheeler Rental System! This application is designed to provide a seamless rental experience for users and a robust management interface for administrators.

## 🚀 Getting Started (Automatic Setup)
The project now supports **single-command execution** for both Windows and Mac/Linux. This will automatically start the server and attempt to configure the database if needed.

### Windows (Single Command)
1.  **Double-click** the file `run_windows.bat` in the project folder.
2.  The application will open in your browser automatically.
    *   *Note: If it's your first time, the system will attempt to auto-configure the database using default XAMPP settings (root/empty).*

### Mac/Linux (Single Command)
1.  Open Terminal in the project folder.
2.  Run:
    ```bash
    ./run.sh
    ```

---

## 🛠 Manual Setup (Legacy)

### Windows (Manual XAMPP)
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
    -   Click **Go**.
    
    *Troubleshooting permissions:*
    -   If connection fails, check `config/db.php`.
    -   Or run `database/fix_db_user.sql` manually.

4.  **Run the App**:
    -   Go to `http://localhost/ride-ease/`

### Mac/Linux (Manual)
1.  **Start MySQL Service**:
    ```bash
    brew services start mysql  # MacOS
    sudo service mysql start   # Linux
    ```

2.  **Setup Database**:
    ```bash
    mysql -u root -p < database/setup.sql
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
