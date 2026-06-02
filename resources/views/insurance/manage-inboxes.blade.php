@extends('layouts.insurance')
@section('content')

@php
    $shopGroup1Ids   = $shopInboxes->get(1,   collect())->map(fn($i) => $i->user->id ?? null)->filter()->values()->toArray();
    $shopGroup2Ids   = $shopInboxes->get(2,   collect())->map(fn($i) => $i->user->id ?? null)->filter()->values()->toArray();
    $shopGroup3Ids   = $shopInboxes->get(3,   collect())->map(fn($i) => $i->user->id ?? null)->filter()->values()->toArray();
    $garageGroup1Ids = $garageInboxes->get(1, collect())->map(fn($i) => $i->user->id ?? null)->filter()->values()->toArray();
    $garageGroup2Ids = $garageInboxes->get(2, collect())->map(fn($i) => $i->user->id ?? null)->filter()->values()->toArray();
    $garageGroup3Ids = $garageInboxes->get(3, collect())->map(fn($i) => $i->user->id ?? null)->filter()->values()->toArray();
    $partnerIds      = $spare_part_partners->pluck('id')->toArray();
    $garagePartnerIds= $garage_partners->pluck('id')->toArray();
@endphp

<div class="container-fluid py-4">

    {{-- Back + Title --}}
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ url('/insurance') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bx bx-arrow-back me-1"></i> Back
        </a>
        <h4 class="mb-0">Manage Inboxes — File #{{ $proforma->file_number }}</h4>
        <span class="badge rounded-pill
            {{ $proforma->status === 'pending' ? 'bg-warning text-dark' : ($proforma->status === 'completed' ? 'bg-success' : 'bg-secondary') }}">
            {{ ucfirst($proforma->status) }}
        </span>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    {{-- Proforma Summary --}}
    <div class="card radius-10 mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-sm-4"><small class="text-muted d-block">Customer</small><strong>{{ $proforma->customer_name }}</strong></div>
                <div class="col-sm-4"><small class="text-muted d-block">Car</small><strong>{{ $proforma->brand?->name }} {{ $proforma->model }} {{ $proforma->year }}</strong></div>
                <div class="col-sm-4"><small class="text-muted d-block">License Plate</small><strong>{{ $proforma->license_plate_number }}</strong></div>
            </div>
        </div>
    </div>

    {{-- Applied users info --}}
    @if($shopApplications->isNotEmpty() || $garageApplications->isNotEmpty())
    <div class="alert alert-info">
        <strong>Already applied (read-only):</strong>
        @foreach($shopApplications as $app)
            <span class="badge bg-primary me-1">{{ $app->applicationBy?->name ?? 'Shop #'.$app->application_by }} (shop)</span>
        @endforeach
        @foreach($garageApplications as $app)
            <span class="badge bg-secondary me-1">{{ $app->applicationBy?->name ?? 'Garage #'.$app->application_by }} (garage)</span>
        @endforeach
        <div class="mt-1 small text-muted">These users have already submitted quotes and cannot be re-added to the inbox.</div>
    </div>
    @endif

    <form method="POST" action="{{ route('insurance.manage-inboxes.update', $proforma) }}">
        @csrf

        <div class="row g-4">

            {{-- ── SHOP GROUPS ─────────────────────────────────── --}}
            <div class="col-12 col-xl-6">
                <div class="card radius-10 h-100">
                    <div class="card-header bg-light"><strong><i class="bx bx-store me-1"></i> Shop Inbox Groups</strong></div>
                    <div class="card-body">
                        <p class="text-muted small mb-3">
                            Each group is mutually exclusive — when one shop in a group applies, the others in that group are removed.
                            Clear a group entirely to free that slot for admin.
                        </p>

                        {{-- Group 1 --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Group 1 <span class="badge bg-info text-dark ms-1">Partners</span></label>
                            <select name="shop_group_1[]" id="shopGroup1" class="form-select" multiple>
                                @foreach($all_shops as $shop)
                                    <option value="{{ $shop->id }}"
                                        {{ in_array($shop->id, $shopGroup1Ids) ? 'selected' : '' }}
                                        {{ in_array($shop->id, $partnerIds) ? 'data-partner=1' : '' }}>
                                        {{ $shop->name }}{{ in_array($shop->id, $partnerIds) ? ' ★' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">★ = your partner</div>
                        </div>

                        {{-- Group 2 --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Group 2</label>
                            <select name="shop_group_2[]" id="shopGroup2" class="form-select" multiple>
                                @foreach($all_shops as $shop)
                                    <option value="{{ $shop->id }}"
                                        {{ in_array($shop->id, $shopGroup2Ids) ? 'selected' : '' }}>
                                        {{ $shop->name }}{{ in_array($shop->id, $partnerIds) ? ' ★' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Group 3 --}}
                        <div class="mb-0">
                            <label class="form-label fw-semibold">Group 3</label>
                            <select name="shop_group_3[]" id="shopGroup3" class="form-select" multiple>
                                @foreach($all_shops as $shop)
                                    <option value="{{ $shop->id }}"
                                        {{ in_array($shop->id, $shopGroup3Ids) ? 'selected' : '' }}>
                                        {{ $shop->name }}{{ in_array($shop->id, $partnerIds) ? ' ★' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── GARAGE GROUPS ────────────────────────────────── --}}
            <div class="col-12 col-xl-6">
                <div class="card radius-10 h-100">
                    <div class="card-header bg-light"><strong><i class="bx bx-wrench me-1"></i> Garage Inbox Groups</strong></div>
                    <div class="card-body">
                        <p class="text-muted small mb-3">
                            Each group is mutually exclusive — when one garage in a group applies, the others in that group are removed.
                            Clear a group entirely to free that slot for admin.
                        </p>

                        {{-- Group 1 --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Group 1 <span class="badge bg-info text-dark ms-1">Partners</span></label>
                            <select name="garage_group_1[]" id="garageGroup1" class="form-select" multiple>
                                @foreach($all_garages as $garage)
                                    <option value="{{ $garage->id }}"
                                        {{ in_array($garage->id, $garageGroup1Ids) ? 'selected' : '' }}>
                                        {{ $garage->name }}{{ in_array($garage->id, $garagePartnerIds) ? ' ★' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">★ = your partner</div>
                        </div>

                        {{-- Group 2 --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Group 2</label>
                            <select name="garage_group_2[]" id="garageGroup2" class="form-select" multiple>
                                @foreach($all_garages as $garage)
                                    <option value="{{ $garage->id }}"
                                        {{ in_array($garage->id, $garageGroup2Ids) ? 'selected' : '' }}>
                                        {{ $garage->name }}{{ in_array($garage->id, $garagePartnerIds) ? ' ★' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Group 3 --}}
                        <div class="mb-0">
                            <label class="form-label fw-semibold">Group 3</label>
                            <select name="garage_group_3[]" id="garageGroup3" class="form-select" multiple>
                                @foreach($all_garages as $garage)
                                    <option value="{{ $garage->id }}"
                                        {{ in_array($garage->id, $garageGroup3Ids) ? 'selected' : '' }}>
                                        {{ $garage->name }}{{ in_array($garage->id, $garagePartnerIds) ? ' ★' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

        </div>{{-- end row --}}

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary px-4">
                <i class="bx bx-save me-1"></i> Save Changes
            </button>
            <a href="{{ url('/insurance') }}" class="btn btn-outline-secondary px-4">Cancel</a>
        </div>

    </form>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function () {
    var select2Config = {
        theme: 'bootstrap-5',
        placeholder: 'Select or search...',
        allowClear: true,
        width: '100%',
    };
    ['#shopGroup1','#shopGroup2','#shopGroup3','#garageGroup1','#garageGroup2','#garageGroup3'].forEach(function(id){
        $(id).select2(select2Config);
    });
});
</script>
@endpush
