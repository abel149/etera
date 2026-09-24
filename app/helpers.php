<?php

/**
 * Global helper functions.
 *
 * IMPORTANT: This file is loaded via composer's "autoload.files" entry
 * (see composer.json), NOT via routes/web.php. Functions used to live
 * inline in routes/web.php, but once `php artisan route:cache` is active,
 * Laravel serves routes from bootstrap/cache/routes-v7.php and never
 * re-includes routes/web.php — so any plain top-level code (like these
 * function definitions) silently disappeared, causing
 * "Call to undefined function ..." errors in production.
 *
 * Keep any shared helper functions here from now on, not in route files.
 */

use App\Models\PaidUser;
use App\Models\TemporaryFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

if (!function_exists('processTemporaryFile')) {
    function processTemporaryFile($tempFile, $destinationFolder)
    {
        Log::info('Upload: processing temp file', [
            'temp_folder' => is_string($tempFile) ? $tempFile : 'NON_STRING',
            'destination' => $destinationFolder,
        ]);
        if (is_string($tempFile)) {
            // If it's a folder name from FilePond
            $tempFileModel = TemporaryFile::where('folder', $tempFile)->first();
            if ($tempFileModel) {
                $tempPath = 'temporary/tmp/' . $tempFile . '/' . $tempFileModel->file;
                $newPath = $destinationFolder . '/' . time() . '_' . $tempFileModel->file;

                if (Storage::disk('local')->exists($tempPath)) {
                    // Copy file to permanent location
                    Storage::disk('public')->put($newPath, Storage::disk('local')->get($tempPath));

                    // Clean up temporary file
                    Storage::disk('local')->deleteDirectory('temporary/tmp/' . $tempFile);
                    $tempFileModel->delete();

                    return $newPath;
                }
            }
        }
        return null;
    }
}

if (!function_exists('addCommissionRecord')) {
    function addCommissionRecord($user, $proformaId, $applicationId, $amount)
    {
        $role = $user->role; // 'shop', 'garage', or 'insurance'

        // 0. Idempotency guard: never create the same commission twice
        // (re-verification would otherwise inflate balances and analytics).
        $alreadyExists = PaidUser::where('user_id', $user->id)
            ->where('proforma_id', $proformaId)
            ->when(
                $applicationId,
                fn($q) => $q->where('application_id', $applicationId),
                fn($q) => $q->whereNull('application_id')
            )
            ->exists();

        if ($alreadyExists) {
            Log::info('Skipped duplicate commission record', [
                'user_id' => $user->id,
                'proforma_id' => $proformaId,
                'application_id' => $applicationId,
            ]);
            return null;
        }

        // 1. Create PaidUser record (Legacy/Work Log)
        $record = PaidUser::create([
            'user_id'        => $user->id,
            'proforma_id'    => $proformaId,
            'application_id' => $applicationId,
            'amount'         => $amount,
            'is_paid'        => false,
            'paid_at'        => null,
        ]);

        Log::info('PaidUser record created', [
            'user_id' => $user->id,
            'role' => $role,
            'amount' => $amount,
            'proforma_id' => $proformaId,
            'application_id' => $applicationId,
        ]);

        // 2. Create Transaction (Ledger)
        // Commission is "Money In" (Credit) for the user
        $walletService = new \App\Services\WalletService();
        $walletService->processTransaction(
            $user,
            -$amount,
            'commission',
            'Commission for Proforma #' . $proformaId,
            $record
        );

        return $record;
    }
}
