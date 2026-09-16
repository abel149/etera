<?php

namespace App\Livewire;

use App\Models\Proforma;
use App\Models\ProformaApplication;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class MarketerViewProformas extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';

    public $filters = [
        'type'      => 'default',
        'car_type'  => 'All',
        'grade'     => 'All',
        'component' => 'Both',
    ];

    public $sortBy = 'desc';

    public function updating($name, $value)
    {
        if (str_starts_with($name, 'filters.') || $name === 'search') {
            $this->resetPage();
        }
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->filters = [
            'type'      => 'default',
            'car_type'  => 'All',
            'grade'     => 'All',
            'component' => 'Both',
        ];
        $this->resetPage();
    }

    public function render()
    {
        $user   = Auth::user();
        $userId = $user->id;

        $query = Proforma::query()
            ->where('status', 'published');

        // Exclude already applied proformas
        $appliedProformaIds = ProformaApplication::where('application_by', $userId)
            ->pluck('proforma_id')
            ->toArray();

        if (!empty($appliedProformaIds)) {
            $query->whereNotIn('id', $appliedProformaIds);
        }

        // Global search — searches the WHOLE list, not just the current page
        if (!empty($this->search)) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('file_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone_number', 'like', "%{$search}%")
                  ->orWhere('license_plate_number', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('year', 'like', "%{$search}%")
                  ->orWhereHas('poster', function ($pq) use ($search) {
                      $pq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('brand', function ($bq) use ($search) {
                      $bq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter: Poster Type
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

        // Filter: Car Type
        if ($this->filters['car_type'] !== 'All') {
            $query->where('car_type', $this->filters['car_type']);
        }

        // Filter: Grade (partial match from proforma_part)
        if ($this->filters['grade'] !== 'All') {
            $query->whereIn('id', function ($sub) {
                $sub->select('proforma_id')
                    ->from('proforma_part')
                    ->where('grade', 'LIKE', '%' . $this->filters['grade'] . '%');
            });
        }

        // Filter: Component
        if ($this->filters['component'] !== 'Both') {
            $query->whereIn('id', function ($sub) {
                $sub->select('proforma_id')
                    ->from('proforma_part')
                    ->where('component', $this->filters['component']);
            });
        }

        $proformas = $query->orderBy('created_at', $this->sortBy)->paginate(10);

        return view('livewire.marketer-view-proformas', [
            'proformas' => $proformas,
            'components' => ['Both', 'Body Parts', 'Mechanical Parts'],
        ]);
    }
}
