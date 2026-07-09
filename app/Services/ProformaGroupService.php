<?php

namespace App\Services;

use App\Models\Inbox;
use App\Models\Partial;
use App\Models\Proforma;
use App\Models\ProformaApplication;
use App\Models\ProformaPartPrice;
use App\Models\User;
use App\Notifications\PartialNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ProformaGroupService
{
    /**
     * Find the first empty group slot available for a fresh (non-partial) application.
     *
     * "Empty" means priced_count = 0 AND no insurance-inboxed shop is still waiting for it.
     * Protected groups (insurance-inboxed pending) are skipped so public/null-group shops
     * don't collide with dedicated partner slots.
     *
     * Returns null if no fresh empty slot is available.
     */
    public function autoAssignGroup(Proforma $proforma): ?int
    {
        $required = (int) ($proforma->required_number_of_shops ?? 0);

        if ($required <= 0) {
            return null;
        }

        for ($g = 1; $g <= $required; $g++) {
            // Skip if an insurance-inboxed shop is still waiting for this group
            $insurancePending = Inbox::where('proforma_id', $proforma->id)
                ->where('source', 'insurance')
                ->where('inbox_group', $g)
                ->exists();

            if ($insurancePending) {
                continue;
            }

            // Skip if the group already has priced parts (not empty)
            $pricedCount = ProformaPartPrice::where('proforma_id', $proforma->id)
                ->where('inbox_group', $g)
                ->count();

            if ($pricedCount > 0) {
                continue;
            }

            return $g;
        }

        return null;
    }

    /**
     * Return all part prices already saved for a given group (for locking UI).
     * Returns a Collection of ProformaPartPrice keyed by car_part_id.
     */
    public function getLockedParts(int $proformaId, int $group): Collection
    {
        return ProformaPartPrice::with('part')
            ->where('proforma_id', $proformaId)
            ->where('inbox_group', $group)
            ->get()
            ->keyBy('car_part_id');
    }

    /**
     * Post-submission check: if a group was partially filled and has no more inboxed
     * shops waiting, create Partial records for all eligible shops immediately.
     *
     * Called after every shop application submission.
     */
    public function checkAndTriggerPartials(Proforma $proforma, int $group): void
    {
        $totalParts = $proforma->parts()->count();

        if ($totalParts === 0) {
            return;
        }

        $pricedCount = ProformaPartPrice::where('proforma_id', $proforma->id)
            ->where('inbox_group', $group)
            ->count();

        // Condition 1: group was started (at least 1 part priced)
        if ($pricedCount === 0) {
            return;
        }

        // Condition 2: group is not yet complete
        if ($pricedCount >= $totalParts) {
            return;
        }

        // Condition 3: no insurance-inboxed shop still pending for this group
        $insurancePending = Inbox::where('proforma_id', $proforma->id)
            ->where('source', 'insurance')
            ->where('inbox_group', $group)
            ->exists();

        if ($insurancePending) {
            return;
        }

        // Condition 4: no Partial already exists for this group
        $alreadyBroadcast = Partial::where('proforma_id', $proforma->id)
            ->where('inbox_group', $group)
            ->exists();

        if ($alreadyBroadcast) {
            return;
        }

        $partsNeeded = $totalParts - $pricedCount;

        Log::info("ProformaGroupService: triggering partial broadcast", [
            'proforma_id'  => $proforma->id,
            'group'        => $group,
            'priced'       => $pricedCount,
            'total'        => $totalParts,
            'parts_needed' => $partsNeeded,
        ]);

        $this->broadcastRemainingParts($proforma, $group, $partsNeeded);
    }

    /**
     * Create Partial records for all eligible shops and send PartialNotification.
     *
     * Eligible = role 'shop', NOT already applied to this proforma, NOT currently inboxed.
     */
    public function broadcastRemainingParts(Proforma $proforma, int $group, int $partsNeeded): void
    {
        // Shops that already applied (any group) — used their one submission
        $appliedIds = ProformaApplication::where('proforma_id', $proforma->id)
            ->pluck('application_by');

        // Shops currently inboxed for this proforma — committed to their assigned group
        $inboxedIds = Inbox::where('proforma_id', $proforma->id)
            ->pluck('user_id');

        $excludeIds = $appliedIds->merge($inboxedIds)->unique()->values();

        $eligibleShops = User::where('role', 'shop')
            ->whereNotIn('id', $excludeIds)
            ->get();

        $count = 0;
        foreach ($eligibleShops as $shop) {
            try {
                Partial::firstOrCreate([
                    'proforma_id' => $proforma->id,
                    'user_id'     => $shop->id,
                    'inbox_group' => $group,
                ], [
                    'parts_needed' => $partsNeeded,
                    'active'       => true,
                ]);

                $shop->notify(new PartialNotification($proforma, $group, $partsNeeded));
                $count++;
            } catch (\Throwable $e) {
                Log::warning("ProformaGroupService: failed to create partial for shop {$shop->id}", [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info("ProformaGroupService: partial broadcast complete", [
            'proforma_id'    => $proforma->id,
            'group'          => $group,
            'parts_needed'   => $partsNeeded,
            'shops_notified' => $count,
        ]);
    }

    /**
     * Get how many parts are priced for a given group.
     */
    public function getPricedCount(int $proformaId, int $group): int
    {
        return ProformaPartPrice::where('proforma_id', $proformaId)
            ->where('inbox_group', $group)
            ->count();
    }

    /**
     * Check if a group is fully complete (all parts priced).
     */
    public function isGroupComplete(Proforma $proforma, int $group): bool
    {
        $totalParts = $proforma->parts()->count();
        if ($totalParts === 0) {
            return false;
        }
        return $this->getPricedCount($proforma->id, $group) >= $totalParts;
    }
}
