@echo off
echo Setting up Proforma Closing Cron Job...
echo.

REM Create the task to run every 5 minutes
schtasks /create /tn "ProformaClosingCheck" /tr "C:\xampp\htdocs\config\check-proforma-closing.bat" /sc minute /mo 5 /ru "SYSTEM" /f

if %errorlevel% equ 0 (
    echo ✓ Cron job created successfully!
    echo ✓ Task Name: ProformaClosingCheck
    echo ✓ Schedule: Every 5 minutes
    echo ✓ Command: check-proforma-closing.bat
    echo.
    echo To view the task:
    echo   schtasks /query /tn "ProformaClosingCheck"
    echo.
    echo To delete the task:
    echo   schtasks /delete /tn "ProformaClosingCheck" /f
    echo.
    echo To run the task manually:
    echo   schtasks /run /tn "ProformaClosingCheck"
) else (
    echo ✗ Failed to create cron job. Please run as Administrator.
    echo.
    echo Manual setup instructions:
    echo 1. Open Task Scheduler (taskschd.msc)
    echo 2. Create Basic Task
    echo 3. Name: ProformaClosingCheck
    echo 4. Trigger: Daily, repeat every 5 minutes
    echo 5. Action: Start a program
    echo 6. Program: C:\xampp\htdocs\config\check-proforma-closing.bat
)

echo.
echo Testing the command...
cd /d "C:\xampp\htdocs\config"
php artisan proforma:check-closing

echo.
echo Setup completed!
pause
