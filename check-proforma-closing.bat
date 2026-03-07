@echo off
echo Starting Proforma Closing Check...
echo Timestamp: %date% %time%

cd /d "C:\xampp\htdocs\config"

php artisan proforma:check-closing

echo Proforma Closing Check Completed
echo Timestamp: %date% %time%
echo.
