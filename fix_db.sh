#!/bin/bash
echo "=============================================="
echo "      Ride-Ease Database Setup Fix"
echo "=============================================="
echo ""
echo "It seems your MySQL 'root' user has a password."
echo "Please enter your MySQL root password when prompted below."
echo "Note: Your password will NOT be visible while typing."
echo ""

# Run setup using the user's input password
mysql -h 127.0.0.1 -u root -p < database/setup.sql

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ Database set up successfully!"
    echo "You can now refresh the application in your browser."
else
    echo ""
    echo "❌ Setup failed. Please check your password and try again."
fi
