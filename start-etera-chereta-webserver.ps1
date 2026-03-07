# Etera-Chereta High-Performance Web Server Service
# Optimized for handling millions of records efficiently

param(
    [int]$Interval = 5,
    [int]$BatchSize = 1000,
    [string]$MemoryLimit = "512M",
    [int]$MaxExecutionTime = 300,
    [string]$LogFile = "etera-chereta-webserver.log"
)

# Set console title and performance
$Host.UI.RawUI.WindowTitle = "Etera-Chereta High-Performance Web Server Service"
$Host.UI.RawUI.BufferSize = New-Object System.Management.Automation.Host.Size(120, 3000)

# Performance counters
$PerformanceCounters = @{
    TotalProcessed = 0
    TotalChecks = 0
    StartTime = Get-Date
    PeakMemory = 0
    AverageProcessingTime = 0
}

# Function to write to both console and log file with performance tracking
function Write-PerformanceLog {
    param([string]$Message, [string]$Level = "INFO", [hashtable]$Metrics = @{})
    
    $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    $logMessage = "[$timestamp] [$Level] $Message"
    
    # Add performance metrics if provided
    if ($Metrics.Count -gt 0) {
        $metricsStr = ($Metrics.GetEnumerator() | ForEach-Object { "$($_.Key): $($_.Value)" }) -join " | "
        $logMessage += " | $metricsStr"
    }
    
    # Write to console with colors
    switch ($Level) {
        "ERROR" { Write-Host $logMessage -ForegroundColor Red }
        "WARN"  { Write-Host $logMessage -ForegroundColor Yellow }
        "SUCCESS" { Write-Host $logMessage -ForegroundColor Green }
        "PERFORMANCE" { Write-Host $logMessage -ForegroundColor Cyan }
        default { Write-Host $logMessage }
    }
    
    # Write to log file
    Add-Content -Path $LogFile -Value $logMessage
}

# Function to check system resources
function Test-SystemResources {
    try {
        $memory = Get-Counter "\Memory\Available MBytes" -SampleInterval 1 -MaxSamples 1
        $cpu = Get-Counter "\Processor(_Total)\% Processor Time" -SampleInterval 1 -MaxSamples 1
        $disk = Get-Counter "\PhysicalDisk(_Total)\% Disk Time" -SampleInterval 1 -MaxSamples 1
        
        $availableMemory = [math]::Round($memory.CounterSamples[0].CookedValue, 2)
        $cpuUsage = [math]::Round($cpu.CounterSamples[0].CookedValue, 2)
        $diskUsage = [math]::Round($disk.CounterSamples[0].CookedValue, 2)
        
        return @{
            AvailableMemory = $availableMemory
            CpuUsage = $cpuUsage
            DiskUsage = $diskUsage
        }
    }
    catch {
        return @{
            AvailableMemory = 0
            CpuUsage = 0
            DiskUsage = 0
        }
    }
}

# Function to run the Etera-Chereta command with performance monitoring
function Start-EteraCheretaService {
    param([int]$CheckInterval, [int]$BatchSize, [string]$MemoryLimit, [int]$MaxExecutionTime)
    
    $command = "php artisan etera-chereta:check-expiration --daemon --interval=$CheckInterval --batch-size=$BatchSize --memory-limit=$MemoryLimit --max-execution-time=$MaxExecutionTime"
    
    Write-PerformanceLog "Starting Etera-Chereta service with optimized parameters" "PERFORMANCE" @{
        BatchSize = $BatchSize
        MemoryLimit = $MemoryLimit
        MaxExecutionTime = $MaxExecutionTime
        Interval = $CheckInterval
    }
    
    try {
        # Change to the correct directory
        Set-Location "C:\xampp\htdocs\etera28082025"
        
        # Check if artisan exists
        if (-not (Test-Path "artisan")) {
            throw "artisan file not found. Please run this script from the Laravel project directory."
        }
        
        # Run the command with performance monitoring
        $process = Start-Process -FilePath "php" -ArgumentList "artisan", "etera-chereta:check-expiration", "--daemon", "--interval=$CheckInterval", "--batch-size=$BatchSize", "--memory-limit=$MemoryLimit", "--max-execution-time=$MaxExecutionTime" -PassThru -NoNewWindow
        
        Write-PerformanceLog "Service started with PID: $($process.Id)" "SUCCESS"
        
        # Monitor the process
        $startTime = Get-Date
        while (-not $process.HasExited) {
            Start-Sleep -Seconds 10
            
            # Check system resources every 10 seconds
            $resources = Test-SystemResources
            if ($resources.AvailableMemory -lt 100) {
                Write-PerformanceLog "Warning: Low memory available ($($resources.AvailableMemory) MB)" "WARN"
            }
            
            # Update peak memory
            $currentMemory = (Get-Process -Id $process.Id -ErrorAction SilentlyContinue).WorkingSet64 / 1MB
            if ($currentMemory -gt $PerformanceCounters.PeakMemory) {
                $PerformanceCounters.PeakMemory = $currentMemory
            }
        }
        
        $executionTime = (Get-Date) - $startTime
        Write-PerformanceLog "Service completed after $($executionTime.TotalSeconds) seconds" "INFO"
        
        return $process.ExitCode
    }
    catch {
        Write-PerformanceLog "Error starting service: $($_.Exception.Message)" "ERROR"
        return 1
    }
}

# Function to display performance summary
function Show-PerformanceSummary {
    $totalTime = (Get-Date) - $PerformanceCounters.StartTime
    
    Write-PerformanceLog "================================================" "PERFORMANCE"
    Write-PerformanceLog "PERFORMANCE SUMMARY" "PERFORMANCE"
    Write-PerformanceLog "================================================" "PERFORMANCE"
    Write-PerformanceLog "Total Runtime: $($totalTime.TotalHours.ToString('F2')) hours" "PERFORMANCE"
    Write-PerformanceLog "Total Checks: $($PerformanceCounters.TotalChecks)" "PERFORMANCE"
    Write-PerformanceLog "Total Records Processed: $($PerformanceCounters.TotalProcessed)" "PERFORMANCE"
    Write-PerformanceLog "Peak Memory Usage: $([math]::Round($PerformanceCounters.PeakMemory, 2)) MB" "PERFORMANCE"
    Write-PerformanceLog "Average Processing Rate: $([math]::Round($PerformanceCounters.TotalProcessed / [math]::Max(1, $totalTime.TotalHours), 2)) records/hour" "PERFORMANCE"
    Write-PerformanceLog "================================================" "PERFORMANCE"
}

# Main execution
Write-PerformanceLog "================================================" "PERFORMANCE"
Write-PerformanceLog "Etera-Chereta High-Performance Web Server Service" "PERFORMANCE"
Write-PerformanceLog "================================================" "PERFORMANCE"
Write-PerformanceLog ""
Write-PerformanceLog "🚀 OPTIMIZED FOR WEB SERVERS HANDLING MILLIONS OF RECORDS" "PERFORMANCE"
Write-PerformanceLog ""
Write-PerformanceLog "Performance Features:" "PERFORMANCE"
Write-PerformanceLog "- Batch Processing: $BatchSize+ records per batch" "PERFORMANCE"
Write-PerformanceLog "- Memory Management: $MemoryLimit allocation" "PERFORMANCE"
Write-PerformanceLog "- Database Optimization: Persistent connections" "PERFORMANCE"
Write-PerformanceLog "- Caching: Redis/Memcached integration" "PERFORMANCE"
Write-PerformanceLog "- Auto-scaling: Handles data growth automatically" "PERFORMANCE"
Write-PerformanceLog "- Progress Monitoring: Real-time performance metrics" "PERFORMANCE"
Write-PerformanceLog "- System Resource Monitoring: CPU, Memory, Disk" "PERFORMANCE"
Write-PerformanceLog ""
Write-PerformanceLog "Press Ctrl+C to stop the service" "PERFORMANCE"
Write-PerformanceLog "================================================" "PERFORMANCE"
Write-PerformanceLog ""

# Check if we're in the right directory
if (-not (Test-Path "artisan")) {
    Write-PerformanceLog "Error: artisan file not found. Please run this script from the Laravel project directory." "ERROR"
    exit 1
}

# Check PHP availability
try {
    $phpVersion = & php --version 2>$null | Select-Object -First 1
    Write-PerformanceLog "PHP detected: $phpVersion" "SUCCESS"
} catch {
    Write-PerformanceLog "Error: PHP not found or not accessible. Please ensure PHP is in your PATH." "ERROR"
    exit 1
}

# Check system resources before starting
$initialResources = Test-SystemResources
Write-PerformanceLog "System Resources Check:" "PERFORMANCE" @{
    AvailableMemory = "$($initialResources.AvailableMemory) MB"
    CpuUsage = "$($initialResources.CpuUsage)%"
    DiskUsage = "$($initialResources.DiskUsage)%"
}

# Main service loop with performance monitoring
$restartCount = 0
$maxRestarts = 1000

while ($restartCount -lt $maxRestarts) {
    $restartCount++
    $PerformanceCounters.TotalChecks++
    
    Write-PerformanceLog "Service attempt #$restartCount" "INFO"
    
    # Start the service
    $exitCode = Start-EteraCheretaService -CheckInterval $Interval -BatchSize $BatchSize -MemoryLimit $MemoryLimit -MaxExecutionTime $MaxExecutionTime
    
    if ($exitCode -eq 0) {
        Write-PerformanceLog "Service completed successfully" "SUCCESS"
        break
    } else {
        Write-PerformanceLog "Service exited with code: $exitCode" "WARN"
        
        if ($restartCount -lt $maxRestarts) {
            Write-PerformanceLog "Restarting service in 15 seconds... (Press Ctrl+C to stop)" "INFO"
            Start-Sleep -Seconds 15
        } else {
            Write-PerformanceLog "Maximum restart attempts reached. Stopping service." "ERROR"
            break
        }
    }
}

# Show final performance summary
Show-PerformanceSummary

Write-PerformanceLog "Service stopped after $restartCount attempts" "INFO"
Write-PerformanceLog "Check the log file for details: $LogFile" "INFO"
Write-PerformanceLog "Performance data saved to: $LogFile" "INFO" 