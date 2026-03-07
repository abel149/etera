# Proforma Closing System Documentation

## Overview

The proforma closing system has been refactored to remove the `timer_enabled` and `timer_type` fields and instead identify timer behavior through the `required_number_of_shops` field. When `required_number_of_shops = 0`, the proforma is in "Etera-Chereta" mode, which enables timer-based automatic closing.

## Key Changes Made

### 1. Database Schema Updates

- **Removed fields**: `timer_enabled`, `timer_type`
- **Kept fields**: `timer_duration`, `timer_expires_at`
- **New logic**: Etera-Chereta mode is identified by `required_number_of_shops = 0`

### 2. Model Updates (`app/Models/Proforma.php`)

#### New Methods:
- `isEteraCheretaMode()`: Returns `true` when `required_number_of_shops = 0`
- Updated `isTimerExpired()`: Now checks Etera-Chereta mode instead of `timer_enabled`
- Updated `getRemainingTime()`: Works with new timer system
- Updated `scheduleAutoSelection()`: Triggers for Etera-Chereta mode only

#### Updated Methods:
- `getRemainingShopsAttribute()`: Shows infinity (∞) for Etera-Chereta mode
- `isApplicableBy()`: Checks timer expiration for Etera-Chereta mode
- `getFormattedRemainingTime()`: Returns "Not applicable" for non-Etera-Chereta proformas

### 3. Auto-Selection Job Updates (`app/Jobs/AutoSelectProformaOffers.php`)

- **Trigger condition**: Now checks `isEteraCheretaMode()` instead of `timer_enabled`
- **Status update**: Sets proforma status to `'closed'` instead of `'completed'`
- **Selection criteria**: Always uses lowest price and selects top 5 applications
- **Logging**: Enhanced logging for better debugging

### 4. New Service Class (`app/Services/ProformaClosingService.php`)

#### Methods:
- `closeProforma()`: Manually close a proforma (admin action)
- `completeProforma()`: Mark proforma as completed
- `handleExpiredProforma()`: Handle timer expiration
- `handleEteraCheretaExpiration()`: Special handling for Etera-Chereta mode
- `shouldAutoClose()`: Check if proforma should be automatically closed
- `getStatusSummary()`: Get comprehensive proforma status information

### 5. Controller Updates (`app/Http/Controllers/ProformaController.php`)

- **Dependency injection**: Now uses `ProformaClosingService`
- **New endpoints**: 
  - `GET /proforma/{id}/status`: Get proforma status summary
  - `POST /proforma/{id}/check-auto-close`: Trigger auto-close check
- **Enhanced error handling**: Better error messages and logging

### 6. New Artisan Command (`app/Console/Commands/CloseExpiredProformas.php`)

- **Command**: `php artisan proformas:close-expired`
- **Purpose**: Process expired proformas and trigger appropriate actions
- **Usage**: Can be scheduled via cron or run manually
- **Logic**: 
  - Etera-Chereta mode: Triggers auto-selection
  - Regular proformas: Closes them directly

### 7. Frontend JavaScript (`public/assets/js/proforma-status.js`)

- **Automatic status checking**: Every 30 seconds
- **Real-time updates**: Status badges, remaining time, application counts
- **Auto-close triggering**: Automatically triggers when timer expires
- **User experience**: Shows closed messages and updates UI accordingly

## How It Works

### Etera-Chereta Mode (Timer-Based)

1. **Creation**: When `number_of_proformas = -1` is selected during proforma creation
2. **Identification**: `required_number_of_shops` is set to `0`
3. **Timer**: `etera_chereta_hours` sets the duration
4. **Auto-selection**: Job is dispatched to run after timer expires
5. **Closing**: Proforma status is set to `'closed'` after auto-selection

### Regular Mode (Fixed Limit)

1. **Creation**: When specific number of proformas is selected
2. **Identification**: `required_number_of_shops` and `required_number_of_garages` have positive values
3. **Closing**: Proforma closes when application limit is reached
4. **Status**: Proforma status is set to `'completed'`

### Automatic Closing Process

1. **Timer Expiration**: System detects when `timer_expires_at` is in the past
2. **Mode Check**: Determines if proforma is in Etera-Chereta mode
3. **Action Dispatch**: 
   - Etera-Chereta: Triggers auto-selection job
   - Regular: Closes proforma directly
4. **Status Update**: Updates proforma status accordingly
5. **Cleanup**: Removes inbox records and updates related data

## Usage Examples

### Creating a Timer-Based Proforma

```php
// In routes/web.php or controller
$isEteraChereta = $request->input('number_of_proformas') === '-1';

if ($isEteraChereta) {
    $eteraHours = (int) $request->input('etera_chereta_hours', 24);
    $timerMinutes = $eteraHours * 60;
    $timerExpiresAt = now()->addMinutes($timerMinutes);
    $requiredShops = 0; // Etera-Chereta mode
    $requiredGarages = 0; // Etera-Chereta mode
}

$proforma = Proforma::create([
    // ... other fields
    'required_number_of_shops' => $requiredShops,
    'required_number_of_garages' => $requiredGarages,
    'timer_duration' => $timerMinutes,
    'timer_expires_at' => $timerExpiresAt,
]);

// Dispatch auto-selection job
if ($isEteraChereta) {
    AutoSelectProformaOffers::dispatch($proforma->id)
        ->delay(now()->addMinutes($timerMinutes));
}
```

### Checking Proforma Status

```php
// Using the service
$closingService = new ProformaClosingService();
$summary = $closingService->getStatusSummary($proforma);

// Summary contains:
// - status, total_applications, required_applications
// - is_etera_chereta, timer_expired, remaining_time
// - can_apply, is_closed
```

### Manual Proforma Closing

```php
// Admin action
$result = $closingService->closeProforma($proforma, auth()->id());

if ($result['success']) {
    // Proforma closed successfully
    return back()->with('success', $result['message']);
} else {
    // Error occurred
    return back()->with('error', $result['message']);
}
```

## Scheduling and Automation

### Cron Job Setup

Add to your server's crontab to run every hour:

```bash
0 * * * * cd /path/to/your/project && php artisan proformas:close-expired
```

### Laravel Task Scheduling

In `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('proformas:close-expired')->hourly();
}
```

## Frontend Integration

### Include JavaScript

```html
<!-- In your Blade template -->
<script src="{{ asset('assets/js/proforma-status.js') }}"></script>

<!-- Add meta tag for proforma ID -->
<meta name="proforma-id" content="{{ $proforma->id }}">
```

### Status Display Elements

```html
<!-- Status badge -->
<span class="badge rounded-pill bg-warning proforma-status-badge">
    {{ ucfirst($proforma->status) }}
</span>

<!-- Remaining time -->
<span class="remaining-time">{{ $proforma->getFormattedRemainingTime() }}</span>

<!-- Application count -->
<span class="application-count">{{ $proforma->applications->count() }}/{{ $proforma->number_of_proformas }}</span>

<!-- Apply button -->
<button class="apply-button btn btn-primary" style="display: {{ $proforma->status === 'pending' ? 'block' : 'none' }}">
    Apply Now
</button>
```

## Error Handling and Logging

### Logging

All proforma closing actions are logged with:
- Proforma ID
- Action performed
- User ID (if applicable)
- Timestamp
- Success/failure status

### Error Handling

- Database transactions ensure data consistency
- Rollback on errors
- User-friendly error messages
- Detailed logging for debugging

## Testing

### Manual Testing

1. Create a proforma with Etera-Chereta mode
2. Wait for timer to expire
3. Check if auto-selection job is dispatched
4. Verify proforma status is updated to 'closed'

### Command Testing

```bash
# Test the command manually
php artisan proformas:close-expired

# Check logs for results
tail -f storage/logs/laravel.log
```

## Migration Notes

### Before Running Migration

1. Backup your database
2. Ensure no active proformas are using the old timer system
3. Test in development environment first

### After Migration

1. Verify existing proformas still work correctly
2. Check that new proformas use the new system
3. Monitor logs for any errors
4. Update any custom code that references old timer fields

## Troubleshooting

### Common Issues

1. **Timer not working**: Check if `required_number_of_shops = 0`
2. **Auto-selection not triggered**: Verify job queue is running
3. **Status not updating**: Check if JavaScript is loaded correctly
4. **Permission errors**: Ensure user has appropriate role

### Debug Commands

```bash
# Check proforma status
php artisan tinker
>>> $proforma = App\Models\Proforma::find(1);
>>> $proforma->isEteraCheretaMode();
>>> $proforma->isTimerExpired();

# Check queue status
php artisan queue:work --once
php artisan queue:failed
```

## Future Enhancements

### Potential Improvements

1. **Real-time notifications**: WebSocket integration for instant updates
2. **Advanced selection criteria**: More sophisticated auto-selection algorithms
3. **Batch processing**: Handle multiple expired proformas simultaneously
4. **Analytics**: Track proforma lifecycle and performance metrics
5. **Custom timers**: Allow users to set custom expiration times

### Configuration Options

Consider adding to `config/etera.php`:

```php
return [
    'auto_selection_enabled' => env('ETERA_AUTO_SELECTION_ENABLED', true),
    'auto_selection_count' => env('ETERA_AUTO_SELECTION_COUNT', 5),
    'status_check_interval' => env('ETERA_STATUS_CHECK_INTERVAL', 30),
    'default_etera_hours' => env('ETERA_DEFAULT_HOURS', 24),
];
```

This system provides a robust, automated way to handle proforma closing while maintaining flexibility for different use cases and ensuring data integrity throughout the process. 