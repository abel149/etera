<?php

namespace App\Livewire;

use App\Models\Partial;
use App\Models\Proforma;
use App\Models\ProformaApplication;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class AllProformasList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $filters = [
        'license'   => '',
        'type'      => 'default',
        'component' => 'Both',
        'car_type'  => 'All',
        'grade'     => 'All',
    ];

    public $sortBy = 'desc';

    public function updating($name, $value)
    {
        if (str_starts_with($name, 'filters.')) {
            $this->resetPage();
        }
    }

    public function clearFilters()
    {
        $this->filters = [
            'license'   => '',
            'type'      => 'default',
            'component' => 'Both',
            'car_type'  => 'All',
            'grade'     => 'All',
        ];

        $this->resetPage();
    }

    public function render()
    {
        $user   = Auth::user();
        $userId = $user->id;

        /**
         * Base Query
         */
        $query = Proforma::query()
            ->where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('proforma_type')
                  ->orWhere('proforma_type', '!=', 'insurance_garage_only');
            });

        /**
         * ✅ Brand filter — ONLY brands accepted by logged-in user
         */
        /**
 * Brand filter — uses car_brand_id (correct column)
 */
$acceptedBrandIds = $user->brands()->pluck('brands.id')->toArray();

if (!empty($acceptedBrandIds)) {
    $query->whereIn('car_brand_id', $acceptedBrandIds);
} else {
    // No brands assigned → return empty result
    $query->whereRaw('1 = 0');
}


        /**
         * Exclude already applied proformas
         */
        $appliedProformaIds = ProformaApplication::where('application_by', $userId)
            ->pluck('proforma_id')
            ->toArray();

        if (!empty($appliedProformaIds)) {
            $query->whereNotIn('id', $appliedProformaIds);
        }

        /**
         * Slot availability: show fresh proformas only when empty slots exist,
         * OR show if this user has an active Partial record for the proforma.
         */
        $query->where(function ($q) use ($userId) {
            $q->where(function ($freshQ) {
                // Non-insurance proformas (no required groups) are always visible
                $freshQ->where(function ($inner) {
                    $inner->where('required_number_of_shops', 0)
                          ->orWhereNull('required_number_of_shops');
                })
                // Insurance proformas: visible only if at least one empty group remains
                ->orWhere(function ($inner) {
                    $inner->where('required_number_of_shops', '>', 0)
                          ->whereRaw(
                              '(SELECT COUNT(DISTINCT inbox_group)
                                FROM proforma_part_prices
                                WHERE proforma_id = proformas.id
                                AND inbox_group IS NOT NULL)
                               < proformas.required_number_of_shops'
                          );
                });
            })
            // Always show if this user is still inboxed (covers partial-fill flow where
            // the shop needs to fill remaining parts in their assigned group)
            ->orWhereHas('inboxes', fn ($iq) =>
                $iq->where('user_id', $userId)
            )
            // Always show if this user has an active Partial broadcast record
            ->orWhereHas('partials', fn ($pq) =>
                $pq->where('user_id', $userId)->where('active', true)
            );
        });

        /**
         * Filter: Poster Type
         */
        switch ($this->filters['type']) {
            case 'insurance':
                $query->whereHas('poster', fn ($q) =>
                    $q->where('role', 'insurance')
                );
                break;

            case 'others':
                $query->whereHas('poster', fn ($q) =>
                    $q->whereIn('role', ['business_owner', 'garage', 'individual'])
                );
                break;
        }

        /**
         * Filter: License Plate OR Phone
         */
        if (!empty($this->filters['license'])) {
            $search = trim($this->filters['license']);

            $query->where(function ($q) use ($search) {
                $q->where('license_plate_number', 'like', "%{$search}%")
                  ->orWhereHas('poster', fn ($q2) =>
                      $q2->where('customer_phone_number', 'like', "%{$search}%")
                  );
            });
        }

        /**
         * Filter: Component
         */
        if ($this->filters['component'] !== 'Both') {
            $query->whereIn('id', function ($sub) {
                $sub->select('proforma_id')
                    ->from('proforma_part')
                    ->where('component', $this->filters['component']);
            });
        }

        /**
         * Filter: Car Type
         */
        if ($this->filters['car_type'] !== 'All') {
            $query->where('car_type', $this->filters['car_type']);
        }

        /**
         * Filter: Grade (partial match from proforma_part)
         */
        if ($this->filters['grade'] !== 'All') {
            $query->whereIn('id', function ($sub) {
                $sub->select('proforma_id')
                    ->from('proforma_part')
                    ->where('grade', 'LIKE', '%' . $this->filters['grade'] . '%');
            });
        }

        /**
         * Sort & Paginate
         */
        $proformas = $query
            ->orderBy('created_at', $this->sortBy)
            ->paginate(10);

        // Fetch active Partial records for this user, keyed by proforma_id
        $partialsByProformaId = Partial::where('user_id', $userId)
            ->where('active', true)
            ->whereIn('proforma_id', $proformas->pluck('id'))
            ->get()
            ->keyBy('proforma_id');

        return view('livewire.all-proformas-list', [
            'proformas'            => $proformas,
            'components'           => ['Both', 'Body Parts', 'Mechanical Parts'],
            'partialsByProformaId' => $partialsByProformaId,
        ]);
    }
}
