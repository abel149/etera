<?php

namespace App\Services;

use App\Models\Proforma;
use App\Models\User;
use App\Models\Inbox;
use App\Notifications\InboxNotification;
use Illuminate\Support\Facades\Log;

class InboxNotificationService
{
    /**
     * Send proforma to spare-part users and notify them
     */
    public function sendToSparePartUsers(Proforma $proforma, array $userIds = [])
    {
        try {
            // If no specific users provided, get all spare-part users
            if (empty($userIds)) {
                $sparePartUsers = User::where('role', 'shop')->get();
            } else {
                $sparePartUsers = User::whereIn('id', $userIds)
                    ->where('role', 'shop')
                    ->get();
            }

            foreach ($sparePartUsers as $user) {
                // Create inbox record (idempotent)
                $inbox = Inbox::firstOrCreate([
                    'proforma_id' => $proforma->id,
                    'user_id' => $user->id,
                ]);

                // Send notification
                $user->notify(new InboxNotification($proforma));
            }

            Log::info("Inbox notifications sent to " . $sparePartUsers->count() . " spare-part users for proforma {$proforma->id}");

            return [
                'success' => true,
                'message' => 'Inbox notifications sent successfully',
                'count' => $sparePartUsers->count()
            ];

        } catch (\Exception $e) {
            Log::error("Error sending inbox notifications: " . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Error sending inbox notifications: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send proforma to garage users and notify them
     */
    public function sendToGarageUsers(Proforma $proforma, array $userIds = [])
    {
        try {
            // If no specific users provided, get all garage users
            if (empty($userIds)) {
                $garageUsers = User::where('role', 'garage')->get();
            } else {
                $garageUsers = User::whereIn('id', $userIds)
                    ->where('role', 'garage')
                    ->get();
            }

            foreach ($garageUsers as $user) {
                // Create inbox record (idempotent)
                $inbox = Inbox::firstOrCreate([
                    'proforma_id' => $proforma->id,
                    'user_id' => $user->id,
                ]);

                // Send notification
                $user->notify(new InboxNotification($proforma));
            }

            Log::info("Inbox notifications sent to " . $garageUsers->count() . " garage users for proforma {$proforma->id}");

            return [
                'success' => true,
                'message' => 'Inbox notifications sent successfully',
                'count' => $garageUsers->count()
            ];

        } catch (\Exception $e) {
            Log::error("Error sending inbox notifications: " . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Error sending inbox notifications: ' . $e->getMessage()
            ];
        }
    }
}
