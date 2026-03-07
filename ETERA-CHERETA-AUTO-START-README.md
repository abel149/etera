# 🚀 Etera-Chereta Auto-Start System

## Overview
The Etera-Chereta monitoring service now **automatically starts when your Laravel project starts running**, ensuring continuous monitoring without manual intervention.

## ✨ How It Works

### 1. **Automatic Startup on Web Requests**
- **Middleware Approach**: The service automatically starts when the first web request hits your Laravel application
- **Background Process**: Runs the monitoring service in the background using optimized parameters
- **Smart Detection**: Prevents multiple instances from running simultaneously

### 2. **Multiple Auto-Start Methods**
- **Web Request Trigger**: Automatically starts on first HTTP request
- **Service Provider**: Laravel service provider ensures startup
- **Manual Commands**: Console commands for manual control
- **Windows Scripts**: Batch scripts for Windows environments

## 🚀 Auto-Start Methods

### **Method 1: Web Request Auto-Start (Recommended)**
The service automatically starts when someone visits your website:

```bash
# Just visit any page on your website
http://localhost/etera28082025/
# OR
http://localhost/etera28082025/etera-chereta-status.html
```

**How it works:**
1. First web request triggers the middleware
2. Middleware checks if service is running
3. If not running, automatically starts the service
4. Service runs in background monitoring millions of records

### **Method 2: Service Provider Auto-Start**
The service provider automatically starts the service when Laravel boots:

```php
// Automatically registered in bootstrap/providers.php
App\Providers\EteraCheretaAutoStartServiceProvider::class
```

### **Method 3: Manual Console Commands**
Check status and manually start the service:

```bash
# Check service status
php artisan etera-chereta:status

# Start the service manually
php artisan etera-chereta:status --start

# Run the monitoring service directly
php artisan etera-chereta:check-expiration --daemon
```

### **Method 4: Windows Batch Scripts**
Use the provided batch scripts for Windows environments:

```bash
# Auto-start script (recommended for Windows)
start-etera-chereta-auto.bat

# High-performance script
start-etera-chereta-webserver.bat
```

## 🔧 Configuration

### **Auto-Start Settings**
The service automatically starts with these optimized parameters:
- **Interval**: 5 seconds between checks
- **Batch Size**: 1000 records per batch
- **Memory Limit**: 512MB
- **Execution Time**: 300 seconds

### **Custom Configuration**
You can override the auto-start parameters:

```bash
# Custom parameters
php artisan etera-chereta:check-expiration --daemon --interval=10 --batch-size=2000 --memory-limit=1G
```

## 📊 Monitoring & Status

### **Web Status Page**
View service status in your browser:
```
http://localhost/etera28082025/etera-chereta-status.html
```

**Features:**
- Real-time status updates
- Auto-refresh every 30 seconds
- Visual status indicators
- Detailed service information

### **API Status Endpoint**
Get service status as JSON:
```
GET /etera-chereta/status
```

**Response:**
```json
{
    "status": "running",
    "platform": "WINNT",
    "auto_start_enabled": true,
    "process_running": true,
    "cache_status": "active",
    "last_check": "2025-01-15T10:30:00.000000Z",
    "timestamp": "2025-01-15T10:30:00.000000Z"
}
```

### **Console Status Command**
```bash
php artisan etera-chereta:status
```

**Output:**
```
🔍 Checking Etera-Chereta monitoring service status...
📊 Service Status Information:
================================
🔄 Status: Running
💻 Platform: WINNT
🚀 Auto-start: Enabled
⚙️  Process: Running
💾 Cache: Active
⏰ Last Check: 2025-01-15T10:30:00.000000Z
================================
✅ Service is running normally
```

## 🚀 Setup Instructions

### **1. Automatic Setup (Already Done)**
The auto-start system is already configured in your project:
- ✅ Service provider registered
- ✅ Middleware configured
- ✅ Routes added
- ✅ Commands created

### **2. Windows Auto-Start (Optional)**
To make the service start automatically when Windows boots:

1. **Copy the auto-start script:**
   ```bash
   copy start-etera-chereta-auto.bat "C:\Users\%USERNAME%\AppData\Roaming\Microsoft\Windows\Start Menu\Programs\Startup\"
   ```

2. **Or use Task Scheduler:**
   - Open Task Scheduler
   - Create Basic Task
   - Set trigger to "At startup"
   - Set action to start `start-etera-chereta-auto.bat`

### **3. Linux/Unix Auto-Start (Optional)**
Create a systemd service:

```bash
sudo nano /etc/systemd/system/etera-chereta.service
```

**Service file content:**
```ini
[Unit]
Description=Etera-Chereta Monitoring Service
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/path/to/your/project
ExecStart=/usr/bin/php artisan etera-chereta:check-expiration --daemon
Restart=always
RestartSec=10

[Install]
WantedBy=multi-user.target
```

**Enable and start:**
```bash
sudo systemctl enable etera-chereta
sudo systemctl start etera-chereta
```

## 🔍 Troubleshooting

### **Service Not Starting Automatically**

1. **Check Laravel logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Check service status:**
   ```bash
   php artisan etera-chereta:status
   ```

3. **Manual start:**
   ```bash
   php artisan etera-chereta:status --start
   ```

### **Common Issues**

#### **Permission Issues**
```bash
# Ensure PHP can execute artisan commands
chmod +x artisan
chmod -R 755 storage/
```

#### **Memory Issues**
```bash
# Increase PHP memory limit
php -d memory_limit=1G artisan etera-chereta:check-expiration --daemon
```

#### **Database Connection Issues**
- Check database configuration in `.env`
- Ensure database is running
- Verify database permissions

## 📈 Performance Monitoring

### **Real-Time Metrics**
The service provides real-time performance monitoring:
- Processing rate (records/second)
- Memory usage
- Execution time
- System resources

### **Log Files**
- **Laravel Logs**: `storage/logs/laravel.log`
- **Service Logs**: Console output with timestamps
- **Performance Logs**: Detailed metrics and analysis

## 🎯 Benefits of Auto-Start

### **1. Zero Manual Intervention**
- ✅ Service starts automatically when project runs
- ✅ No need to remember to start monitoring
- ✅ Continuous operation without breaks

### **2. High Availability**
- ✅ Automatic restart on failures
- ✅ Background process management
- ✅ Service health monitoring

### **3. Production Ready**
- ✅ Optimized for millions of records
- ✅ Memory-efficient processing
- ✅ Scalable architecture

### **4. Easy Monitoring**
- ✅ Web-based status page
- ✅ API endpoints for integration
- ✅ Console commands for management

## 🚀 Quick Start

### **Immediate Auto-Start**
1. **Start your Laravel project** (XAMPP, etc.)
2. **Visit any page** on your website
3. **Service starts automatically** in background
4. **Check status** at `/etera-chereta-status.html`

### **Verify Auto-Start**
```bash
# Check if service is running
php artisan etera-chereta:status

# Should show: Status: Running
```

### **Monitor Performance**
- Visit the web status page for real-time updates
- Check Laravel logs for detailed information
- Use console commands for management

## 🔄 Auto-Start Flow

```
1. Laravel Project Starts
   ↓
2. First Web Request
   ↓
3. Middleware Triggers
   ↓
4. Check Service Status
   ↓
5. If Not Running → Start Service
   ↓
6. Service Runs in Background
   ↓
7. Continuous Monitoring
   ↓
8. Auto-Restart on Failure
```

## 📝 Summary

The Etera-Chereta monitoring service now **automatically starts when your project runs** with these features:

- 🚀 **Zero Configuration Required** - Works out of the box
- 🔄 **Automatic Startup** - Starts on first web request
- 📊 **Real-Time Monitoring** - Web-based status page
- ⚡ **High Performance** - Handles millions of records
- 🛡️ **Auto-Recovery** - Restarts automatically on failures
- 💻 **Cross-Platform** - Works on Windows, Linux, macOS

**Just start your Laravel project and the monitoring begins automatically!** 🎉 