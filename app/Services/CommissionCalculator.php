<?php

namespace App\Services;

use App\Models\Commission;
use App\Models\CommissionOverride;
use App\Models\Proforma;
use App\Models\ProformaApplication;
use Illuminate\Support\Collection;

class CommissionCalculator
{
    /**
     * True when the verify route treats this proforma as 'insurance'
     * (the only path where shop/garage commission overrides are applied).
     */
    public function usesInsuranceCommission(Proforma $proforma): bool
    {
        if ($proforma->proforma_type && str_starts_with($proforma->proforma_type, 'insurance_')) {
            return true;
        }

        $shops   = (int) ($proforma->required_number_of_shops ?? 0);
        $garages = (int) ($proforma->required_number_of_garages ?? 0);

        return $shops == 3 && $garages == 3;
    }

    /**
     * Shop/garage commission per application for an insurance proforma.
     * Single source of truth shared by the verify route and the admin preview.
     *
     * Each row: application, user, default, override (nullable), final.
     */
    public function insuranceApplicants(Proforma $proforma, ?Collection $applications = null, ?int $partsCount = null): Collection
    {
        $applications ??= ProformaApplication::where('proforma_id', $proforma->id)
            ->with('applicationBy')
            ->get();

        $partsCount ??= $proforma->parts()->count();
        $commissions = Commission::first();
        $overrides = CommissionOverride::where('proforma_id', $proforma->id)->pluck('amount', 'application_id');

        return $applications->map(function ($application) use ($commissions, $partsCount, $overrides) {
            $user = $application->applicationBy;
            if (!$user) {
                return null;
            }

            $default = $this->defaultAmount($application, $user->role, $commissions, $partsCount);
            $override = $overrides->has($application->id) ? (float) $overrides[$application->id] : null;

            return [
                'application' => $application,
                'user'        => $user,
                'default'     => $default,
                'override'    => $override,
                'final'       => $override ?? $default,
            ];
        })->filter()->values();
    }

    private function defaultAmount($application, ?string $role, ?Commission $commissions, int $proformaPartsCount): float
    {
        if ($role === 'garage') {
            return (float) ($commissions->garagePay ?? 0);
        }

        // Pro-rata for shops: scale by parts filled / total parts
        $shopPay     = (float) ($commissions->shopPay ?? 0);
        $totalParts  = (int) ($application->total_parts_count ?? 0);
        $filledParts = (int) ($application->filled_parts_count ?? 0);

        if ($totalParts <= 0) {
            $totalParts = $proformaPartsCount;
        }
        if ($filledParts <= 0 && $totalParts > 0) {
            $filledParts = $application->loadMissing('prices')->prices
                ->filter(fn ($p) => ($p->unit_price ?? 0) > 0 || $p->price_is_encrypted)
                ->count();
        }

        if ($totalParts > 0 && $filledParts > 0) {
            return round($shopPay * ($filledParts / $totalParts), 2);
        }
        if ($totalParts > 0) {
            return 0; // No actual part prices recorded — no commission
        }
        return $shopPay; // Legacy record: no parts data — full pay
    }
}
