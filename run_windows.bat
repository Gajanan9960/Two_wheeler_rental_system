@echo off
title Ride-Ease Server
echo ==============================================
echo      Starting Ride-Ease Application
echo ==============================================
echo.
echo 1. Checking PHP installation...
php -v >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERROR] PHP is not installed or not in PATH.
    echo Please install XAMPP or add PHP to your PATH.
    pause
    exit
)
echo [OK] PHP is available.
echo.
echo 2. Opening default browser...
start http://localhost:8000
echo.
echo 3. Starting PHP built-in server...
echo    (Press Ctrl+C to stop)
echo.
php -S localhost:8000
pause
