@extends('layouts.marketer')
@section('content')

    <div class="page-wrapper">
        <div class="page-content">

            {{-- Header card --}}
            <div class="card radius-12 mb-3">
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div>
                            <h4 class="mb-1 fw-bold">File #{{ $proforma->file_number ?? 'N/A' }}</h4>
                            <div class="d-flex flex-wrap gap-3 text-secondary">
                                <span><i class="bx bx-credit-card me-1"></i>Plate: {{ $proforma->license_plate_number ?? 'N/A' }}</span>
                                <span><i class="bx bx-cog me-1"></i>Chassis: {{ $proforma->chassis_number ?? 'N/A' }}</span>
                                <span><i class="bx bx-calendar me-1"></i>Year: {{ $proforma->year ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <a href="/marketer/proformas" class="btn btn-outline-secondary">
                            <i class="bx bx-arrow-back"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                {{-- Left column: summary + media --}}
                <div class="col-xl-4 col-lg-4">
                    <div class="card radius-12">
                        <div class="card-body">
                            <h6 class="mb-3"><i class="bx bx-file me-1"></i> Proforma Summary</h6>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item px-0">
                                    <i class="bx bx-car me-2 text-primary"></i>
                                    <span class="text-secondary">{{ $proforma->brand->name ?? 'N/A' }}</span>
                                    <h6 class="mb-0 mt-1">{{ $proforma->model ?? 'N/A' }}</h6>
                                </li>
                                <li class="list-group-item px-0">
                                    <i class="bx bx-buildings me-2 text-primary"></i>
                                    <span class="text-secondary">Posted By</span>
                                    <h6 class="mb-0 mt-1">{{ ucfirst($proforma->poster->role ?? 'N/A') }}</h6>
                                </li>
                                <li class="list-group-item px-0">
                                    <i class="bx bx-time-five me-2 text-primary"></i>
                                    <span class="text-secondary">Date Posted</span>
                                    <h6 class="mb-0 mt-1">{{ $proforma->created_at->diffForHumans() ?? 'N/A' }}</h6>
                                </li>
                            </ul>
                        </div>
                    </div>

                    @if (isset($proforma->audios) && count($proforma->audios))
                        <div class="card radius-12">
                            <div class="card-body">
                                <h6 class="mb-3"><i class="bx bx-microphone me-1"></i> Audio</h6>
                                @foreach ($proforma->audios as $audio)
                                    <audio controls class="w-100 mb-2">
                                        <source src="{{ $audio->url ?? '' }}" type="audio/mp3">
                                    </audio>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if ($proforma->voice_note_path ?? false)
                        <div class="card radius-12">
                            <div class="card-body">
                                <h6 class="mb-3"><i class="bx bx-microphone me-1"></i> Voice Note</h6>
                                <audio controls class="w-100">
                                    <source src="{{ url('storage/' . $proforma->voice_note_path) }}" type="audio/webm">
                                    Your browser does not support the audio element.
                                </audio>
                            </div>
                        </div>
                    @endif

                    @if (isset($proforma->videos) && count($proforma->videos))
                        <div class="card radius-12">
                            <div class="card-body">
                                <h6 class="mb-3"><i class="bx bx-video me-1"></i> Video</h6>
                                @foreach ($proforma->videos as $video)
                                    <video controls class="w-100 rounded" style="max-height: 300px;">
                                        <source src="{{ $video->url ?? '' }}" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Right column: parts + progress + inboxes + applications --}}
                <div class="col-xl-8 col-lg-8">

                    {{-- Parts table --}}
                    <div class="card radius-12">
                        <div class="card-body">
                            <h6 class="mb-3"><i class="bx bx-cogs me-1"></i> Requested Parts</h6>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 1%;">#</th>
                                            <th>Component</th>
                                            <th>Part Number</th>
                                            <th>Grade</th>
                                            <th>Condition</th>
                                            <th>Country</th>
                                            <th>Qty</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(isset($proforma->parts) && count($proforma->parts) > 0)
                                            @foreach ($proforma->parts as $part)
                                                <tr>
                                                    <td>{{ $loop->index + 1 }}</td>
                                                    <td>{{ $part->component ?? 'N/A' }}</td>
                                                    <td>{{ $part->number ?? 'N/A' }}</td>
                                                    <td>{{ $part->grade ?? 'N/A' }}</td>
                                                    <td>{{ $part->condition ?? 'N/A' }}</td>
                                                    <td>{{ $part->country ?? 'N/A' }}</td>
                                                    <td><span class="badge bg-secondary">{{ $part->quantity ?? 0 }}</span></td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="7" class="text-center text-muted py-3">
                                                    No parts found for this proforma.
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    @php
                        $shopInboxes   = $proforma->inboxes->filter(fn($i) => optional($i->user)->role === 'shop');
                        $garageInboxes = $proforma->inboxes->filter(fn($i) => optional($i->user)->role === 'garage');
                        $shopApps   = $proforma->applications->where('from', 'shop');
                        $garageApps = $proforma->applications->where('from', 'garage');
                        $progress   = $proforma->partsPricingProgress();
                        $isChereta  = $proforma->isEteraCheretaMode();
                        $hasPartials = $shopApps->contains(fn($a) => (int)($a->filled_parts_count ?? 0) < (int)($a->total_parts_count ?? 0) && (int)($a->total_parts_count ?? 0) > 0);
                    @endphp

                    {{-- Parts Pricing Progress --}}
                    @if (!$isChereta && $progress['total'] > 0)
                        @php
                            $pct = $progress['total'] > 0 ? min(100, round(($progress['filled'] / $progress['total']) * 100)) : 0;
                            $allFilled = $progress['filled'] >= $progress['total'];
                        @endphp
                        <div class="card radius-12">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <h6 class="mb-0"><i class="bx bx-line-chart me-1"></i> Parts Pricing Progress</h6>
                                    <span class="badge {{ $allFilled ? 'bg-success' : 'bg-warning text-dark' }}">
                                        {{ $progress['filled'] }} / {{ $progress['total'] }} parts filled
                                    </span>
                                </div>
                                <div class="progress" style="height: 10px;">
                                    <div class="progress-bar {{ $allFilled ? 'bg-success' : 'bg-warning' }}"
                                         role="progressbar"
                                         style="width: {{ $pct }}%;"
                                         aria-valuenow="{{ $pct }}"
                                         aria-valuemin="0"
                                         aria-valuemax="100"></div>
                                </div>
                                @if (!$allFilled && $proforma->remaining_shops == 0 && $hasPartials)
                                    <p class="text-muted small mt-2 mb-0">
                                        <i class="bx bx-info-circle me-1"></i>
                                        All shop slots are taken, but some quotes are still <strong>partial</strong>.
                                        This proforma will complete once all parts are priced.
                                    </p>
                                @elseif ($allFilled)
                                    <p class="text-muted small mt-2 mb-0">
                                        <i class="bx bx-check me-1"></i>
                                        All required parts have been priced.
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Inboxed Shops & Garages --}}
                    <div class="card radius-12">
                        <div class="card-body">
                            <h6 class="mb-3"><i class="bx bx-buildings me-1"></i> Inboxed Shops & Garages</h6>
                            @if ($shopInboxes->isEmpty() && $garageInboxes->isEmpty())
                                <p class="text-muted mb-0">No shops or garages have been inboxed for this proforma.</p>
                            @else
                                <div class="row g-3">
                                    @if ($shopInboxes->isNotEmpty())
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="bx bx-store me-1 text-primary"></i>
                                                <strong>Shops ({{ $shopInboxes->count() }})</strong>
                                            </div>
                                            <ul class="list-group list-group-flush">
                                                @foreach ($shopInboxes as $inbox)
                                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-1">
                                                        <span>{{ $inbox->user?->name ?? 'N/A' }}</span>
                                                        <span class="badge {{ $inbox->source === 'admin' ? 'bg-info text-dark' : 'bg-primary' }}">
                                                            {{ ucfirst($inbox->source ?? 'N/A') }}
                                                            @if ($inbox->inbox_group)
                                                                · Grp {{ $inbox->inbox_group }}
                                                            @endif
                                                        </span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                    @if ($garageInboxes->isNotEmpty())
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="bx bx-wrench me-1 text-secondary"></i>
                                                <strong>Garages ({{ $garageInboxes->count() }})</strong>
                                            </div>
                                            <ul class="list-group list-group-flush">
                                                @foreach ($garageInboxes as $inbox)
                                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-1">
                                                        <span>{{ $inbox->user?->name ?? 'N/A' }}</span>
                                                        <span class="badge {{ $inbox->source === 'admin' ? 'bg-info text-dark' : 'bg-primary' }}">
                                                            {{ ucfirst($inbox->source ?? 'N/A') }}
                                                            @if ($inbox->inbox_group)
                                                                · Grp {{ $inbox->inbox_group }}
                                                            @endif
                                                        </span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Submitted Applications --}}
                    <div class="card radius-12">
                        <div class="card-body">
                            <h6 class="mb-3"><i class="bx bx-clipboard me-1"></i> Submitted Applications</h6>
                            @if ($shopApps->isEmpty() && $garageApps->isEmpty())
                                <p class="text-muted mb-0">No applications submitted yet.</p>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Applicant</th>
                                                <th>Type</th>
                                                <th>Source</th>
                                                <th>Parts Filled</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $i = 1; @endphp
                                            @foreach ($shopApps as $app)
                                                @php
                                                    $filled = (int) ($app->filled_parts_count ?? 0);
                                                    $total  = (int) ($app->total_parts_count ?? 0);
                                                    $isPartial = $total > 0 && $filled < $total;
                                                @endphp
                                                <tr>
                                                    <td>{{ $i++ }}</td>
                                                    <td>{{ $app->applicationBy?->name ?? 'N/A' }}</td>
                                                    <td><span class="badge bg-primary">Shop</span></td>
                                                    <td>{{ ucfirst($app->application_source ?? 'N/A') }}</td>
                                                    <td>{{ $filled }} / {{ $total ?: '—' }}</td>
                                                    <td>
                                                        @if ($isPartial)
                                                            <span class="badge bg-warning text-dark">Partial</span>
                                                        @elseif ($total > 0 && $filled >= $total)
                                                            <span class="badge bg-success">Complete</span>
                                                        @else
                                                            <span class="badge bg-secondary">Submitted</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                            @foreach ($garageApps as $app)
                                                @php
                                                    $filled = (int) ($app->filled_parts_count ?? 0);
                                                    $total  = (int) ($app->total_parts_count ?? 0);
                                                    $isPartial = $total > 0 && $filled < $total;
                                                @endphp
                                                <tr>
                                                    <td>{{ $i++ }}</td>
                                                    <td>{{ $app->applicationBy?->name ?? 'N/A' }}</td>
                                                    <td><span class="badge bg-secondary">Garage</span></td>
                                                    <td>{{ ucfirst($app->application_source ?? 'N/A') }}</td>
                                                    <td>{{ $filled }} / {{ $total ?: '—' }}</td>
                                                    <td>
                                                        @if ($isPartial)
                                                            <span class="badge bg-warning text-dark">Partial</span>
                                                        @elseif ($total > 0 && $filled >= $total)
                                                            <span class="badge bg-success">Complete</span>
                                                        @else
                                                            <span class="badge bg-secondary">Submitted</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="alert alert-info d-flex align-items-center">
                        <i class="bx bx-info-circle me-2 fs-5"></i>
                        <span>This is a read-only view. Marketers cannot submit quotes for proformas.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
