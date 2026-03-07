@echo off
echo ========================================
echo    QUICK TEST RUNNER
echo ========================================
echo.

echo Running Simple Endpoint Tests...
php artisan test --filter=SimpleEndpointTest
echo.

echo ========================================
echo    QUICK TEST COMPLETED!
echo ========================================
pause
