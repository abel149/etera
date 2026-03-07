@echo off
echo Starting Etera-Chereta Expiration Checker...
echo This script will run every 5 seconds to check for expired proformas
echo Press Ctrl+C to stop

:loop
echo.
echo [%date% %time%] Checking for expired Etera-Chereta proformas...
"C:\xampp\php\php.exe" "C:\xampp\htdocs\etera28082025\artisan" etera-chereta:check-expiration
timeout /t 5 /nobreak >nul
goto loop 