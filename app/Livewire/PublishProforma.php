<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;

class PublishProforma extends Component
{
    public $selectedInsuranceShop = null;
    public $selectedClientShop1 = null;
    public $selectedClientShop2 = null;

    public $selectedInsuranceGarage = null;
    public $selectedClientGarage1 = null;
    public $selectedClientGarage2 = null;
    public $proforma;

    public function mount($proforma)
    {
        $this->proforma = $proforma;

        // Pre-populate admin-inboxed slots from DB so admin sees current state
        $adminShopInboxes = $proforma->inboxes()
            ->where('source', 'admin')
            ->whereHas('user', fn($q) => $q->where('role', 'shop'))
            ->orderBy('created_at', 'asc')
            ->pluck('user_id')
            ->values();

        $adminGarageInboxes = $proforma->inboxes()
            ->where('source', 'admin')
            ->whereHas('user', fn($q) => $q->where('role', 'garage'))
            ->orderBy('created_at', 'asc')
            ->pluck('user_id')
            ->values();

        $this->selectedClientShop1   = $adminShopInboxes[0]   ?? null;
        $this->selectedClientShop2   = $adminShopInboxes[1]   ?? null;
        $this->selectedClientGarage1 = $adminGarageInboxes[0] ?? null;
        $this->selectedClientGarage2 = $adminGarageInboxes[1] ?? null;
    }

    public function updated($propertyName)
    {
    }

    public function render()
    {

        $shops = User::where('role', 'shop')->orderBy('name', 'asc')->get();
        $garages = User::where('role', 'garage')->orderBy('name', 'asc')->get();

        return view('livewire.publish-proforma', compact('shops', 'garages'));
    }
}
