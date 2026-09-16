<div>
    <div class="page-wrapper">
        <div class="page-content">
            <h3>All Proformas</h3>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            {{-- Search bar + sort --}}
                            <div class="page-breadcrumb d-flex align-items-center mb-3">
                                <form wire:submit.prevent>
                                    <div class="row row-cols-auto g-2">
                                        <div class="col">
                                            <div class="position-relative">
                                                <input type="text"
                                                       wire:model.live.debounce.300ms="search"
                                                       class="form-control ps-5"
                                                       placeholder="Search by file #, plate, customer, car, poster...">
                                                <span class="position-absolute top-50 product-show translate-middle-y">
                                                    <i class="bx bx-search"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-white">
                                                    <i class="bx bx-filter"></i> Type
                                                </button>
                                                <div class="btn-group" role="group">
                                                    <button id="typeDropdown" type="button" class="btn btn-white dropdown-toggle dropdown-toggle-nocaret px-1" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class='bx bx-chevron-down'></i>
                                                    </button>
                                                    <ul class="dropdown-menu" aria-labelledby="typeDropdown">
                                                        <li><a class="dropdown-item" href="#" wire:click.prevent="$set('filters.type','default')">Default</a></li>
                                                        <li><a class="dropdown-item" href="#" wire:click.prevent="$set('filters.type','insurance')">Insurance</a></li>
                                                        <li><a class="dropdown-item" href="#" wire:click.prevent="$set('filters.type','others')">Others</a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-white">
                                                    <i class="bx bx-filter"></i> Sort
                                                </button>
                                                <div class="btn-group" role="group">
                                                    <button id="sortDropdown" type="button" class="btn btn-white dropdown-toggle dropdown-toggle-nocaret px-1" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class='bx bx-chevron-down'></i>
                                                    </button>
                                                    <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                                                        <li><a class="dropdown-item" href="#" wire:click.prevent="$set('sortBy','desc')">Latest</a></li>
                                                        <li><a class="dropdown-item" href="#" wire:click.prevent="$set('sortBy','asc')">Oldest</a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <button type="button" class="btn btn-outline-secondary" wire:click="clearFilters">
                                                <i class="bx bx-x"></i> Clear
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <div class="ms-auto">
                                    <h5>Total: <span class="text-primary">{{ $proformas->total() }}</span></h5>
                                </div>
                            </div>

                            {{-- Proforma table --}}
                            <div class="table-responsive lead-table">
                                <table class="table mb-0 align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>File #</th>
                                            <th>Poster</th>
                                            <th>Customer</th>
                                            <th>Car</th>
                                            <th>License Plate</th>
                                            <th>Remaining</th>
                                            <th>Created</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($proformas as $proforma)
                                            <tr>
                                                <td>{{ $proforma->file_number ?? 'N/A' }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="{{ asset('assets/images/avatars/avatar-9.jpg') }}" class="rounded-circle" width="40" height="40" alt="">
                                                        <div class="ms-2">
                                                            <h6 class="mb-0 font-14">
                                                                {{ $proforma->poster?->name ?? 'Unknown' }}
                                                                @if($proforma->poster?->role === 'insurance_agent')
                                                                    - {{ $proforma->poster?->parentInsurance?->name ?? 'Unknown' }} (Agent)
                                                                @else
                                                                    - {{ ucfirst($proforma->poster?->role) }}
                                                                @endif
                                                            </h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div>
                                                            <h6 class="mb-0 font-14">{{ $proforma->customer_name ?? 'N/A' }}</h6>
                                                            <p class="mb-0 font-13 text-secondary">{{ $proforma->customer_phone_number ?? 'N/A' }}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div>
                                                            <h6 class="mb-0 font-14">{{ $proforma->brand?->name ?? 'N/A' }}</h6>
                                                            <p class="mb-0 font-13 text-secondary">{{ $proforma->model ?? 'N/A' }} ({{ $proforma->year ?? 'N/A' }})</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $proforma->license_plate_number ?? 'N/A' }}</td>
                                                <td>
                                                    @if($proforma->isEteraCheretaMode())
                                                        <span class="badge rounded-pill bg-info">Chereta</span>
                                                    @else
                                                        <span class="badge rounded-pill {{ $proforma->remaining_shops > 0 ? 'bg-success' : 'bg-secondary' }}">
                                                            {{ $proforma->remaining_shops }} Shop{{ $proforma->remaining_shops == 1 ? '' : 's' }}
                                                        </span>
                                                        @if($proforma->remaining_garages > 0)
                                                            <span class="badge rounded-pill bg-success">
                                                                {{ $proforma->remaining_garages }} Garage{{ $proforma->remaining_garages == 1 ? '' : 's' }}
                                                            </span>
                                                        @endif
                                                    @endif
                                                </td>
                                                <td>{{ $proforma->created_at?->format('d M Y') ?? 'N/A' }}</td>
                                                <td>
                                                    <a href="/marketer/proforma-details?proforma={{ $proforma->id }}"
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="bx bx-show me-0"></i> View
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center">
                                                    <div class="d-flex align-items-center justify-content-center">
                                                        <div>
                                                            <h6 class="mb-0 font-14">
                                                                @if(!empty($this->search))
                                                                    No proformas found for "{{ $this->search }}"
                                                                @else
                                                                    No proformas found
                                                                @endif
                                                            </h6>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            {{-- Pagination --}}
                            <div class="mt-3">
                                {{ $proformas->onEachSide(1)->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
