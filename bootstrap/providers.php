<?php

return [
    App\Providers\AppServiceProvider::class,
    // Disabled: this provider auto-spawns a new "etera-chereta:check-expiration --daemon"
    // process per-request whenever its cache flag lapses, with no real process check on
    // Linux. Multiple daemons piling up (each holding a persistent PDO connection) caused
    // MySQL "Too many connections" and took the site down. Etera-Chereta expiration is now
    // handled safely by the scheduled command `proformas:close-expired` (see
    // routes/console.php). Only re-enable if that scheduled command is ever removed AND
    // the underlying process-check bug (see AutoStartEteraCheretaMiddleware) is fixed.
    // App\Providers\EteraCheretaAutoStartServiceProvider::class,
];
