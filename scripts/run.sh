#!/bin/bash
echo "=============================================="
echo "     Starting Ride-Ease Application"
echo "=============================================="
echo ""

if ! command -v php &> /dev/null
then
    echo "[ERROR] PHP is not installed or not in PATH."
    exit 1
fi

echo "[OK] PHP is available."
echo ""
echo "Starting Server at http://localhost:8000"
echo "(Press Ctrl+C to stop)"

# Open browser in background
if [[ "$OSTYPE" == "darwin"* ]]; then
    (sleep 2 && open http://localhost:8000) &
else
    (sleep 2 && xdg-open http://localhost:8000) &
fi

cd ..
php -S localhost:8000
