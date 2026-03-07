@echo off
echo ========================================
echo    LARAVEL PROJECT TESTING SUITE
echo ========================================
echo.

echo [1/4] Running Simple Endpoint Tests...
php artisan test --filter=SimpleEndpointTest
echo.

echo [2/4] Running Full Endpoint Tests...
php artisan test --filter=EndpointTesting
echo.

echo [3/4] Running All Feature Tests...
php artisan test tests/Feature/
echo.

echo [4/4] Running All Tests...
php artisan test
echo.

echo ========================================
echo    TESTING COMPLETED!
echo ========================================
pause
