# PowerShell script for checking proforma closing
# Run this every 5 minutes via Windows Task Scheduler

Write-Host "Starting Proforma Closing Check..." -ForegroundColor Green
Write-Host "Timestamp: $(Get-Date)" -ForegroundColor Yellow

# Change to project directory
Set-Location "C:\xampp\htdocs\config"

try {
    # Run the artisan command
    php artisan proforma:check-closing
    
    Write-Host "Proforma Closing Check Completed Successfully" -ForegroundColor Green
} catch {
    Write-Host "Error running proforma closing check: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}

Write-Host "Timestamp: $(Get-Date)" -ForegroundColor Yellow
Write-Host ""
