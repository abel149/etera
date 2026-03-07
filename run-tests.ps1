Write-Host "========================================" -ForegroundColor Cyan
Write-Host "    LARAVEL PROJECT TESTING SUITE" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

Write-Host "[1/4] Running Simple Endpoint Tests..." -ForegroundColor Yellow
php artisan test --filter=SimpleEndpointTest
Write-Host ""

Write-Host "[2/4] Running Full Endpoint Tests..." -ForegroundColor Yellow
php artisan test --filter=EndpointTesting
Write-Host ""

Write-Host "[3/4] Running All Feature Tests..." -ForegroundColor Yellow
php artisan test tests/Feature/
Write-Host ""

Write-Host "[4/4] Running All Tests..." -ForegroundColor Yellow
php artisan test
Write-Host ""

Write-Host "========================================" -ForegroundColor Green
Write-Host "    TESTING COMPLETED!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green

Read-Host "Press Enter to continue"
