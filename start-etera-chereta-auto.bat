@echo off
title Etera-Chereta Auto-Start Service
echo ================================================
echo Etera-Chereta Auto-Start Service
echo ================================================
echo.
echo This script automatically starts the Etera-Chereta monitoring service
echo when the Laravel project starts running.
echo.
echo Features:
echo - Auto-starts monitoring service on project startup
echo - Runs in background automatically
echo - No manual intervention required
echo - Monitors millions of records efficiently
echo.
echo ================================================
echo.

:start_service
echo [%date% %time%] 🚀 Starting Etera-Chereta Auto-Start Service...
echo.

cd /d "C:\xampp\htdocs\etera28082025"

:check_and_start
echo [%date% %time%] 🔍 Checking if service is already running...

REM Check if the service is already running
php artisan etera-chereta:status >nul 2>&1
if %errorlevel% equ 0 (
    echo [%date% %time%] ✅ Service is already running
    goto :monitor
) else (
    echo [%date% %time%] ⚠️ Service not running, starting now...
    goto :start_monitoring
)

:start_monitoring
echo [%date% %time%] 🚀 Starting Etera-Chereta monitoring service...
php artisan etera-chereta:check-expiration --daemon --interval=5 --batch-size=1000 --memory-limit=512M

echo.
echo [%date% %time%] ⚠️ Service stopped. Restarting in 10 seconds...
echo [%date% %time%] 🔄 Auto-restart enabled...
echo.

timeout /t 10 /nobreak >nul
goto :check_and_start

:monitor
echo [%date% %time%] 📊 Monitoring service status...
echo [%date% %time%] 💡 Service is running automatically
echo [%date% %time%] 🌐 Check status at: http://localhost/etera28082025/etera-chereta-status.html
echo.
echo [%date% %time%] ⏰ This script will continue monitoring...
echo [%date% %time%] Press Ctrl+C to stop monitoring
echo.

:monitor_loop
timeout /t 60 /nobreak >nul
echo [%date% %time%] 🔍 Checking service status...
php artisan etera-chereta:status >nul 2>&1
if %errorlevel% neq 0 (
    echo [%date% %time%] ⚠️ Service stopped, restarting...
    goto :start_monitoring
)
echo [%date% %time%] ✅ Service running normally
goto :monitor_loop 