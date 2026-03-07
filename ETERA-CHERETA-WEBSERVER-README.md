# 🚀 Etera-Chereta High-Performance Web Server Service

## Overview
This service is specifically optimized for web servers handling **millions of data records** efficiently. It automatically monitors and processes Etera-Chereta proforma expirations with high-performance optimizations.

## 🎯 Key Features

### High-Performance Processing
- **Batch Processing**: Processes 1000+ records per batch
- **Memory Management**: Configurable memory limits (default: 512MB)
- **Database Optimization**: Persistent connections and query optimization
- **Caching Integration**: Redis/Memcached support for performance
- **Auto-scaling**: Automatically handles data growth

### Data Handling Capabilities
- **Millions of Records**: Efficiently processes massive datasets
- **Real-time Monitoring**: Continuous monitoring with configurable intervals
- **Progress Tracking**: Real-time progress indicators and performance metrics
- **Error Recovery**: Automatic restart and error handling

### System Monitoring
- **Resource Monitoring**: CPU, Memory, and Disk usage tracking
- **Performance Metrics**: Detailed performance logging and analysis
- **Health Checks**: Automatic database and system health monitoring

## 🚀 Quick Start

### 1. Basic Usage
```bash
# Run as a single check
php artisan etera-chereta:check-expiration

# Run as daemon with default settings
php artisan etera-chereta:check-expiration --daemon

# Run with custom batch size and memory limit
php artisan etera-chereta:check-expiration --daemon --batch-size=2000 --memory-limit=1G
```

### 2. Windows Batch Script (Recommended for XAMPP)
```bash
# Run the high-performance batch script
start-etera-chereta-webserver.bat
```

### 3. PowerShell Script (Advanced Features)
```powershell
# Run with default settings
.\start-etera-chereta-webserver.ps1

# Run with custom parameters
.\start-etera-chereta-webserver.ps1 -Interval 10 -BatchSize 2000 -MemoryLimit "1G"
```

## ⚙️ Configuration Options

### Command Line Parameters
| Parameter | Description | Default | Example |
|-----------|-------------|---------|---------|
| `--daemon` | Run as continuous daemon process | false | `--daemon` |
| `--interval` | Check interval in seconds | 5 | `--interval=10` |
| `--batch-size` | Records per batch | 1000 | `--batch-size=2000` |
| `--memory-limit` | PHP memory limit | 512M | `--memory-limit=1G` |
| `--max-execution-time` | Max execution time | 300 | `--max-execution-time=600` |

### Performance Tuning
```bash
# For very large datasets (10M+ records)
php artisan etera-chereta:check-expiration --daemon --batch-size=5000 --memory-limit=2G --max-execution-time=600

# For moderate datasets (1M-10M records)
php artisan etera-chereta:check-expiration --daemon --batch-size=2000 --memory-limit=1G --max-execution-time=300

# For smaller datasets (<1M records)
php artisan etera-chereta:check-expiration --daemon --batch-size=1000 --memory-limit=512M --max-execution-time=300
```

## 📊 Performance Monitoring

### Real-time Metrics
The service provides real-time performance monitoring:
- **Processing Rate**: Records processed per second
- **Memory Usage**: Current and peak memory consumption
- **Execution Time**: Time per batch and total execution time
- **System Resources**: CPU, Memory, and Disk usage

### Performance Logs
All performance data is logged to:
- Console output with color-coded messages
- Log files with detailed metrics
- Performance summary reports

## 🔧 Database Requirements

### Required Tables
- `proformas` - Main proforma data
- `proforma_applications` - Application records
- `proforma_part_prices` - Part pricing data
- `users` - User information

### Database Optimization
- **Indexes**: Ensure proper indexing on key fields
- **Connection Pooling**: Persistent database connections
- **Query Optimization**: Efficient queries with eager loading
- **Batch Operations**: Bulk updates and deletes

## 🚨 Error Handling & Recovery

### Automatic Recovery
- **Database Connection**: Automatic reconnection attempts
- **Service Restart**: Automatic restart on failures
- **Memory Cleanup**: Automatic memory management
- **Error Logging**: Comprehensive error logging and reporting

### Error Scenarios
- Database connection failures
- Memory limit exceeded
- Execution timeouts
- System resource constraints

## 📈 Scaling Recommendations

### For Different Data Volumes

#### Small Scale (<100K records)
```bash
php artisan etera-chereta:check-expiration --daemon --batch-size=500 --memory-limit=256M
```

#### Medium Scale (100K - 1M records)
```bash
php artisan etera-chereta:check-expiration --daemon --batch-size=1000 --memory-limit=512M
```

#### Large Scale (1M - 10M records)
```bash
php artisan etera-chereta:check-expiration --daemon --batch-size=2000 --memory-limit=1G
```

#### Enterprise Scale (10M+ records)
```bash
php artisan etera-chereta:check-expiration --daemon --batch-size=5000 --memory-limit=2G --max-execution-time=600
```

### Server Requirements
- **CPU**: Multi-core processor (4+ cores recommended)
- **Memory**: Minimum 2GB RAM, 8GB+ for large datasets
- **Storage**: SSD storage for database operations
- **Network**: Stable database connection

## 🔄 Auto-Start Configuration

### Windows Startup
1. Copy `start-etera-chereta-webserver.bat` to startup folder
2. Or use Task Scheduler for automatic startup

### Linux/Unix Startup
```bash
# Create systemd service
sudo nano /etc/systemd/system/etera-chereta.service

# Enable and start service
sudo systemctl enable etera-chereta
sudo systemctl start etera-chereta
```

## 📝 Logging & Monitoring

### Log Files
- **Service Logs**: `etera-chereta-webserver.log`
- **Laravel Logs**: `storage/logs/laravel.log`
- **Performance Logs**: Console output with timestamps

### Monitoring Commands
```bash
# Check service status
php artisan etera-chereta:check-expiration --help

# View performance metrics
tail -f etera-chereta-webserver.log

# Monitor system resources
# Use the PowerShell script for detailed monitoring
```

## 🚀 Production Deployment

### Best Practices
1. **Use PowerShell Script**: Better performance monitoring and error handling
2. **Configure Log Rotation**: Prevent log files from growing too large
3. **Monitor System Resources**: Set up alerts for resource constraints
4. **Database Optimization**: Ensure proper indexing and query optimization
5. **Backup Strategy**: Regular database backups before large operations

### Security Considerations
- Run service with appropriate user permissions
- Secure database connections
- Monitor access logs
- Regular security updates

## 🆘 Troubleshooting

### Common Issues

#### Memory Issues
```bash
# Increase memory limit
php artisan etera-chereta:check-expiration --daemon --memory-limit=1G

# Reduce batch size
php artisan etera-chereta:check-expiration --daemon --batch-size=500
```

#### Performance Issues
```bash
# Increase batch size for better performance
php artisan etera-chereta:check-expiration --daemon --batch-size=2000

# Increase execution time
php artisan etera-chereta:check-expiration --daemon --max-execution-time=600
```

#### Database Issues
- Check database connection settings
- Verify table structure and indexes
- Monitor database performance

## 📞 Support

For issues or questions:
1. Check the log files for error details
2. Verify system requirements and configuration
3. Test with smaller batch sizes first
4. Monitor system resources during operation

---

**Note**: This service is designed to handle millions of records efficiently. Start with default settings and adjust based on your specific data volume and server capabilities. 