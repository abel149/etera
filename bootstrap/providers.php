<?php

return [
    App\Providers\AppServiceProvider::class,
    // Disabled: this provider auto-spawns a new "etera-chereta:check-expiration --daemon"
    // process per-request whenever its cache flag lapses, with no real process check on
    // Linux. Multiple daemons piling up (each holding a persistent PDO connection) caused
    // MySQL "Too many connections" and took the site down. Re-enable only after this is
    // replaced with a single supervised/cron-managed process. See AutoStartEteraCheretaMiddleware too.
    // App\Providers\EteraCheretaAutoStartServiceProvider::class,
];
