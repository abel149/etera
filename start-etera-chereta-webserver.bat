@echo off
title Etera-Chereta High-Performance Web Server Service
echo ========================================================
echo Etera-Chereta High-Performance Expiration Check Service
echo ========================================================
echo.
echo 🚀 OPTIMIZED FOR WEB SERVERS HANDLING MILLIONS OF RECORDS
echo.
echo Performance Features:
echo - Batch Processing: 1000+ records per batch
echo - Memory Management: 512MB+ allocation
echo - Database Optimization: Persistent connections
echo - Caching: Redis/Memcached integration
echo - Auto-scaling: Handles data growth automatically
echo - Progress Monitoring: Real-time performance metrics
echo.
echo Press Ctrl+C to stop the service
echo ========================================================
echo.

:start_service
echo [%date% %time%] 🚀 Starting High-Performance Etera-Chereta Service...
echo.

cd /d "C:\xampp\htdocs\etera28082025"

:run_command
echo [%date% %time%] ⚡ Running High-Performance Etera-Chereta Service...
echo [%date% %time%] 📊 Batch Size: 1000 records | 💾 Memory: 512MB | ⏱️ Timeout: 300s
echo.

php artisan etera-chereta:check-expiration --daemon --interval=5 --batch-size=1000 --memory-limit=512M --max-execution-time=300

echo.
echo [%date% %time%] ⚠️ Service stopped or crashed. Restarting in 15 seconds...
echo [%date% %time%] 🔄 Auto-restart enabled for high-availability...
echo [%date% %time%] Press Ctrl+C to stop completely
echo.

timeout /t 15 /nobreak >nul
goto run_command 