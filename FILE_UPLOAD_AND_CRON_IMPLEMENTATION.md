# File Upload and Cron Job Implementation

## Overview
This document outlines the implementation of enhanced file upload functionality from the Etera project and a comprehensive cron job system for proforma closing management.

## 🚀 Features Implemented

### 1. File Upload Services
- **ImageService**: Handles image uploads with temporary file management
- **VideoService**: Manages video file uploads and processing
- **AudioService**: Processes audio files including voice notes
- **TemporaryFileService**: Manages temporary file storage and cleanup

### 2. Enhanced Proforma Closing Logic
- Fixed proforma closing to require **3 garages + 3 shops** (not just one or the other)
- Added comprehensive logging for debugging
- Improved Etera-Chereta mode handling
- Better error handling and status tracking

### 3. Cron Job System
- **CheckProformaClosing** Artisan command
- Automated proforma closing every 5 minutes
- Temporary file cleanup
- Comprehensive logging and error handling

### 4. Media Rendering Components
- **File Upload Component**: FilePond-based upload with voice recording
- **Media Viewer Component**: Displays images, videos, and audio files
- Responsive design with modal previews
- Download functionality

## 📁 Files Created/Modified

### New Services
```
app/Services/ImageService.php
app/Services/VideoService.php
app/Services/AudioService.php
app/Services/TemporaryFileService.php
```

### New Commands
```
app/Console/Commands/CheckProformaClosing.php
```

### New Components
```
resources/views/components/file-upload.blade.php
resources/views/components/media-viewer.blade.php
```

### Scripts
```
check-proforma-closing.bat
check-proforma-closing.ps1
setup-cron-job.bat
```

### Modified Files
```
app/Services/ProformaApplicationRegistrationService.php (Fixed closing logic)
routes/web.php (Added temporary file upload routes)
```

## 🔧 Setup Instructions

### 1. Install Dependencies
```bash
# FilePond is loaded via CDN in the component
# No additional npm packages required
```

### 2. Set Up Cron Job

#### Option A: Automatic Setup (Windows)
```bash
# Run as Administrator
setup-cron-job.bat
```

#### Option B: Manual Setup
1. Open Task Scheduler (`taskschd.msc`)
2. Create Basic Task
3. Name: `ProformaClosingCheck`
4. Trigger: Daily, repeat every 5 minutes
5. Action: Start a program
6. Program: `C:\xampp\htdocs\config\check-proforma-closing.bat`

#### Option C: PowerShell
```powershell
# Run every 5 minutes
Register-ScheduledTask -TaskName "ProformaClosingCheck" -Trigger (New-ScheduledTaskTrigger -Once -At (Get-Date) -RepetitionInterval (New-TimeSpan -Minutes 5) -RepetitionDuration (New-TimeSpan -Days 365)) -Action (New-ScheduledTaskAction -Execute "C:\xampp\htdocs\config\check-proforma-closing.ps1") -RunLevel Highest
```

### 3. Test the Implementation
```bash
# Test the cron job command
php artisan proforma:check-closing

# Test file uploads
# Use the file-upload component in your forms
```

## 📋 Usage Examples

### File Upload Component
```blade
<!-- In your Blade template -->
<x-file-upload type="image" :multiple="true" :maxFiles="10" />
<x-file-upload type="video" :multiple="false" :maxFiles="5" />
<x-file-upload type="audio" :multiple="true" :maxFiles="3" />
```

### Media Viewer Component
```blade
<!-- Display proforma media -->
<x-media-viewer :proforma="$proforma" type="all" />

<!-- Display application media -->
<x-media-viewer :application="$application" type="images" />
```

### Using Services in Controllers
```php
use App\Services\ImageService;
use App\Services\VideoService;
use App\Services\AudioService;

// In your controller
public function store(Request $request)
{
    $proforma = Proforma::create($request->validated());
    
    // Upload images
    if ($request->has('image')) {
        app(ImageService::class)->upload($request, $proforma->id);
    }
    
    // Upload videos
    if ($request->has('video')) {
        app(VideoService::class)->upload($request, $proforma->id);
    }
    
    // Upload audio
    if ($request->has('audio')) {
        app(AudioService::class)->upload($request, $proforma->id);
    }
}
```

## 🔍 Proforma Closing Logic

### Requirements
- **Garages**: Minimum 3 applications required
- **Shops**: Minimum 3 applications required
- **Both**: Must have BOTH garage and shop requirements met
- **Etera-Chereta**: Timer-based closing (overrides application count)

### Status Flow
```
pending/opened → completed (when requirements met)
pending/opened → closed (when timer expires or manually closed)
```

### Logging
All proforma closing activities are logged with:
- Proforma ID
- Application counts
- Requirements status
- Action taken
- Timestamps

## 🎯 File Upload Features

### Supported File Types
- **Images**: JPG, PNG, GIF, WebP
- **Videos**: MP4, WebM, AVI
- **Audio**: MP3, WAV, WebM (voice notes)

### Voice Recording
- Browser-based recording using MediaRecorder API
- WebM format with Opus codec
- Base64 encoding for form submission
- Real-time recording indicators

### File Management
- Temporary storage in `storage/app/temporary/tmp/`
- Permanent storage in `public/uploads/`
- Automatic cleanup of expired temporary files
- Unique file naming with timestamps

## 🚨 Error Handling

### Upload Errors
- File size validation (max 10MB)
- File type validation
- Storage space checks
- Network timeout handling

### Cron Job Errors
- Database connection issues
- File system errors
- Memory limit handling
- Comprehensive error logging

## 📊 Monitoring

### Log Files
- `storage/logs/laravel.log` - Application logs
- Task Scheduler logs - Windows event logs

### Key Metrics
- Proformas processed per run
- Files uploaded successfully
- Errors encountered
- Processing time

## 🔧 Troubleshooting

### Common Issues

#### 1. Cron Job Not Running
```bash
# Check if task exists
schtasks /query /tn "ProformaClosingCheck"

# Run manually
schtasks /run /tn "ProformaClosingCheck"
```

#### 2. File Upload Failures
- Check `storage/app/temporary/` permissions
- Verify `public/uploads/` directory exists
- Check file size limits in PHP configuration

#### 3. Voice Recording Issues
- Ensure HTTPS for microphone access
- Check browser permissions
- Verify MediaRecorder API support

### Debug Commands
```bash
# Test proforma closing
php artisan proforma:check-closing

# Check temporary files
php artisan tinker
>>> \App\Models\TemporaryFile::count()

# View recent logs
tail -f storage/logs/laravel.log
```

## 🔄 Maintenance

### Regular Tasks
1. **Daily**: Check cron job execution
2. **Weekly**: Review error logs
3. **Monthly**: Clean up old temporary files
4. **Quarterly**: Update file size limits if needed

### Performance Optimization
- Monitor database query performance
- Optimize file storage cleanup
- Review log file sizes
- Update FilePond configuration as needed

## 📈 Future Enhancements

### Planned Features
- [ ] File compression for large uploads
- [ ] Cloud storage integration (AWS S3, Google Drive)
- [ ] Advanced media processing (thumbnails, transcoding)
- [ ] Real-time upload progress notifications
- [ ] Bulk file operations
- [ ] File versioning system

### Integration Opportunities
- [ ] Email notifications for upload completion
- [ ] Webhook support for external systems
- [ ] API endpoints for mobile apps
- [ ] Advanced search and filtering

---

## 📞 Support

For issues or questions regarding this implementation:
1. Check the logs first: `storage/logs/laravel.log`
2. Verify cron job status: `schtasks /query /tn "ProformaClosingCheck"`
3. Test individual components: `php artisan proforma:check-closing`
4. Review file permissions and storage space

**Last Updated**: {{ date('Y-m-d H:i:s') }}
