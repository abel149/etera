# Etera-Chereta Expiration Checker PowerShell Script
# This script runs every 5 seconds to check for expired proformas

Write-Host "Starting Etera-Chereta Expiration Checker..." -ForegroundColor Green
Write-Host "This script will run every 5 seconds to check for expired proformas" -ForegroundColor Yellow
Write-Host "Press Ctrl+C to stop" -ForegroundColor Red
Write-Host ""

$projectPath = "C:\xampp\htdocs\etera28082025"
$phpPath = "C:\xampp\php\php.exe"

try {
    while ($true) {
        $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
        Write-Host "[$timestamp] Checking for expired Etera-Chereta proformas..." -ForegroundColor Cyan
        
        # Run the artisan command
        & $phpPath "$projectPath\artisan" etera-chereta:check-expiration
        
        # Wait for 5 seconds
        Start-Sleep -Seconds 5
        Write-Host ""
    }
} catch {
    Write-Host "Script stopped by user or error occurred" -ForegroundColor Red
    Write-Host $_.Exception.Message -ForegroundColor Red
} 