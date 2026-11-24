<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Mail;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use App\Mail\ServerErrorMail;
use App\Models\AppError;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )

    // =========================
    // MIDDLEWARE
    // =========================
    ->withMiddleware(function (Middleware $middleware) {

        // Disabled: this middleware auto-spawns "etera-chereta:check-expiration --daemon"
        // per-request whenever its cache flag lapses, with no real process check on Linux.
        // Multiple daemons piling up (each holding a persistent PDO connection) caused
        // MySQL "Too many connections" and took the site down. Etera-Chereta expiration
        // is now handled safely by the scheduled command `proformas:close-expired` (see
        // routes/console.php), which requires only the standard Laravel cron entry:
        // `* * * * * php artisan schedule:run`. Only re-enable this middleware if that
        // scheduled command is ever removed AND the underlying process-check bug is fixed.
        // $middleware->append(
        //     \App\Http\Middleware\AutoStartEteraCheretaMiddleware::class
        // );

        // Refresh CSRF token
        $middleware->append(
            \App\Http\Middleware\RefreshCsrfToken::class
        );

        // Prevent caching for authenticated users (fix back-button showing private pages after logout)
        $middleware->append(
            \App\Http\Middleware\NoCacheAuthenticated::class
        );

        // Force Terms & Conditions agreement, then Telegram connect, for all users
        // on first login — regardless of role middleware (insurance, garage, shop, etc.).
        // Must be in the web group so StartSession has run and Auth::user() resolves.
        // Order matters: T&C must be agreed before the Telegram connect prompt appears.
        $middleware->web(append: [
            \App\Http\Middleware\TermsMiddleware::class,
            \App\Http\Middleware\TelegramConnectMiddleware::class,
        ]);

        // Route middleware aliases
        $middleware->alias([
            'auth.user'  => \App\Http\Middleware\AuthenticateUser::class,
            'guest'      => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'superadmin' => \App\Http\Middleware\SuperAdminMiddleware::class,
        ]);
    })

    // =========================
    // EXCEPTIONS (Laravel 11)
    // =========================
    ->withExceptions(function (Exceptions $exceptions) {

        /*
        |--------------------------------------------------------------------------
        | INLINE DEVELOPER LIST
        |--------------------------------------------------------------------------
        */
        $developers = [
            
            
            ['name' => 'Husni Owner', 'email' => 'hsherif77@gmail.com'],
        
            ['name' => 'Abel Ashenafi dev', 'email' => 'ashenafiabel49@gmail.com'],
        ];

        /*
        |--------------------------------------------------------------------------
        | RENDER EXCEPTIONS (ERROR PAGES)
        |--------------------------------------------------------------------------
        */
        $exceptions->renderable(function (\Throwable $e, $request) use ($developers) {

            // Validation errors must be handled by Laravel's default redirect-back-with-errors.
            // Returning null here lets the default renderer take over.
            if ($e instanceof \Illuminate\Validation\ValidationException
                || $e instanceof \Illuminate\Auth\AuthenticationException) {
                return null;
            }

            // 419 - Page Expired
            if ($e instanceof TokenMismatchException) {
                try {
                    return response()->view('errors.419', [], 419);
                } catch (\Throwable $viewEx) {
                    return response('Session expired. Please go back and try again.', 419);
                }
            }

            $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;

            // =========================
            // REPORT 500+ ERRORS (DB + EMAIL + LOG)
            // =========================
            if ($status >= 500) {

                // Log to laravel.log — wrapped so a storage-permission failure does NOT
                // cause the exception handler itself to crash (which silences everything).
                try {
                    \Log::error('🔥 500 ERROR DETECTED', [
                        'exception' => $e->getMessage(),
                        'file'      => $e->getFile(),
                        'line'      => $e->getLine(),
                        'user'      => auth()->user()?->email ?? 'Guest',
                        'url'       => request()->fullUrl(),
                    ]);
                } catch (\Throwable $logEx) {
                    // Log write failed — storage permissions likely broken on server.
                    // Run: sudo chown -R www-data:www-data /var/www/html/storage
                    //      sudo chmod -R 775 /var/www/html/storage
                }

                // Hash to prevent duplicate DB records
                $hash = md5($e->getMessage() . $e->getFile() . $e->getLine());

                try {
                    if (!AppError::where('hash', $hash)->where('fixed', false)->exists()) {

                        // Save to database
                        AppError::create([
                            'user_id'     => auth()->id(),
                            'url'         => request()->fullUrl(),
                            'method'      => request()->method(),
                            'status_code' => $status,
                            'message'     => $e->getMessage(),
                            'trace'       => $e->getTraceAsString(),
                            'hash'        => $hash,
                            'fixed'       => false,
                        ]);

                        // Prepare user array
                        $userData = ['email' => auth()->user()?->email ?? 'Guest'];

                        if (config('app.send_server_error_emails', false)) {
                            // Email developers
                            foreach ($developers as $dev) {
                                try {
                                    Mail::to($dev['email'])->send(new ServerErrorMail([
                                        'errorMessage' => (string)$e->getMessage(),
                                        'status'  => $status,
                                        'url'     => request()->fullUrl(),
                                        'method'  => request()->method(),
                                        'trace'   => (string)$e->getTraceAsString(),
                                        'user'    => $userData,
                                    ]));
                                } catch (\Throwable $mailError) {
                                    try {
                                        \Log::error('Failed to send server error email', [
                                            'original_exception' => $e->getMessage(),
                                            'mail_exception' => $mailError->getMessage(),
                                            'developer_email' => $dev['email'],
                                        ]);
                                    } catch (\Throwable $logEx2) {}
                                }
                            }
                        }
                    }
                } catch (\Throwable $dbEx) {
                    // DB save failed — already logged above (if log worked)
                }
            }

            // =========================
            // RENDER ERROR PAGES
            // =========================

            // HTTP exceptions (4xx / 5xx)
            if ($e instanceof HttpExceptionInterface) {
                if ($status >= 400 && $status < 500) {
                    try {
                        return response()->view('errors.4xx', ['exception' => $e], $status);
                    } catch (\Throwable $viewEx) {
                        return response('An error occurred (' . $status . ').', $status);
                    }
                }
                if ($status >= 500) {
                    try {
                        return response()->view('errors.5xx', ['exception' => $e], $status);
                    } catch (\Throwable $viewEx) {
                        return response('A server error occurred. Please try again.', 500);
                    }
                }
            }

            // Non-HTTP exceptions → 500
            try {
                return response()->view('errors.5xx', ['exception' => $e], 500);
            } catch (\Throwable $viewEx) {
                return response('A server error occurred. Please try again.', 500);
            }
        });

        /*
        |--------------------------------------------------------------------------
        | DO NOT REPORT
        |--------------------------------------------------------------------------
        */
        $exceptions->dontReport([
            TokenMismatchException::class,
            \Illuminate\Validation\ValidationException::class,
        ]);
    })

    ->create();
