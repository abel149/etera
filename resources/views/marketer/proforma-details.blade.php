@extends('layouts.marketer')
@section('content')

    <style type="text/css">
        .player audio {
            width: 100%;
            border-radius: 6px;
            margin: 0;
            padding: 0;
            border: none;
        }

        /* Table Styles */
        .table-container {
            margin-top: 20px;
            overflow-y: hidden;
            overflow-x: auto;
            white-space: nowrap;
        }

        .basic-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #ddd;
            font-family: Arial, sans-serif;
        }

        .basic-table th,
        .basic-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            white-space: normal;
        }

        .basic-table th {
            background-color: #f4f4f4;
            color: #333;
            font-weight: bold;
            text-transform: uppercase;
        }

        .basic-table tr:hover {
            background-color: #f1f1f1;
        }
    </style>

    <div class="single-page-header" data-background-image="{{ asset('asset/images/banner-auto-insurance.jpg') }}">
        
        <div class="page-wrapper">
        <div class="page-content">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="single-page-header-inner">
                        <div class="left-side">
                            <div class="header-details">
                                <h5>File #: {{ $proforma->file_number ?? 'N/A' }}</h5>
                                <ul>
                                    <li><i class="icon-feather-credit-card"></i> Plate Number:
                                        {{ $proforma->license_plate_number ?? 'N/A' }}</li>
                                    <li><i class="icon-feather-settings"></i> Chassis: {{ $proforma->chassis_number ?? 'N/A' }}</li>
                                    <li><i class="icon-material-outline-directions-car"></i> Year:
                                        {{ $proforma->year ?? 'N/A' }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(isset($proforma->images) && count($proforma->images) > 0)
        <div style="text-align: center; margin-bottom: 20px;">
            <p><strong>Proforma has {{ $proforma->images->count() }} image(s)</strong></p>
        </div>
    @endif

    <div class="container">
        <div class="row">
            <div class="col-xl-4 col-lg-4">
                <div class="sidebar-container">
                    <div class="sidebar-widget">
                        <div class="job-overview">
                            <div class="job-overview-headline">Proforma Summary</div>
                            <div class="job-overview-inner">
                                <ul>
                                    <li>
                                        <i class="icon-material-outline-directions-car"></i>
                                        <span>{{ $proforma->brand->name ?? 'N/A' }}</span>
                                        <h5>{{ $proforma->model ?? 'N/A' }}</h5>
                                    </li>
                                    <li>
                                        <i class="icon-material-outline-business"></i>
                                        <span>Posted By</span>
                                        <h5>{{ ucfirst($proforma->poster->role ?? 'N/A') }}</h5>
                                    </li>
                                    <li>
                                        <i class="icon-material-outline-access-time"></i>
                                        <span>Date Posted</span>
                                        <h5>{{ $proforma->created_at->diffForHumans() ?? 'N/A' }}</h5>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    @if (isset($proforma->audios) && count($proforma->audios))
                        <div>
                            <div class="job-overview margin-bottom-10">
                                <div class="job-overview-headline">Audio</div>
                            </div>
                            @foreach ($proforma->audios as $audio)
                                <div class="player">
                                    <audio controls>
                                        <source src="{{ $audio->url ?? '' }}" type="audio/mp3">
                                    </audio>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if ($proforma->voice_note_path ?? false)
                        <div>
                            <div class="job-overview margin-bottom-10">
                                <div class="job-overview-headline">Voice Note</div>
                            </div>
                            <div class="player">
                                <audio controls>
                                    <source src="{{ url('storage/' . $proforma->voice_note_path) }}" type="audio/webm">
                                    Your browser does not support the audio element.
                                </audio>
                            </div>
                        </div>
                    @endif
                    <br>

                    @if (isset($proforma->videos) && count($proforma->videos))
                        <div class="job-overview margin-bottom-10">
                            <div class="job-overview-headline">Video</div>
                        </div>
                        <div>
                            @foreach ($proforma->videos as $video)
                                <video controls style="width: 100%; height: auto; max-height: 300px;">
                                    <source src="{{ $video->url ?? '' }}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-xl-8 col-lg-8">
                <div class="table-container">
                    <table class="basic-table">
                        <thead>
                            <tr>
                                <th style="width: 1%;">#</th>
                                <th>Components</th>
                                <th>Part Name and Number</th>
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
                                        <td data-label="Index">{{ $loop->index + 1 }}</td>
                                        <td data-label="Component">{{ $part->component ?? 'N/A' }}</td>
                                        <td data-label="Part #">{{ $part->number ?? 'N/A' }}</td>
                                        <td data-label="Grade">{{ $part->grade ?? 'N/A' }}</td>
                                        <td data-label="Condition">{{ $part->condition ?? 'N/A' }}</td>
                                        <td data-label="Country">{{ $part->country ?? 'N/A' }}</td>
                                        <td data-label="Qty">{{ $part->quantity ?? 0 }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7" class="text-center">
                                        No parts found for this proforma.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                @php
                    $shopInboxes   = $proforma->inboxes->filter(fn($i) => optional($i->user)->role === 'shop');
                    $garageInboxes = $proforma->inboxes->filter(fn($i) => optional($i->user)->role === 'garage');
                    $shopApps   = $proforma->applications->where('from', 'shop');
                    $garageApps = $proforma->applications->where('from', 'garage');
                    $progress   = $proforma->partsPricingProgress();
                    $reqShops   = (int) ($proforma->required_number_of_shops ?? 0);
                    $reqGarages = (int) ($proforma->required_number_of_garages ?? 0);
                    $isChereta  = $proforma->isEteraCheretaMode();
                    $hasPartials = $shopApps->contains(fn($a) => (int)($a->filled_parts_count ?? 0) < (int)($a->total_parts_count ?? 0) && (int)($a->total_parts_count ?? 0) > 0);
                @endphp

                {{-- Parts Pricing Progress (clarifies remaining=0 with partials) --}}
                @if (!$isChereta && $progress['total'] > 0)
                    @php
                        $pct = $progress['total'] > 0 ? min(100, round(($progress['filled'] / $progress['total']) * 100)) : 0;
                        $allFilled = $progress['filled'] >= $progress['total'];
                    @endphp
                    <div class="card radius-10 margin-top-20">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h6 class="mb-0"><i class="icon-material-outline-trending-up me-1"></i> Parts Pricing Progress</h6>
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
                                    <i class="icon-material-outline-info me-1"></i>
                                    All shop slots are taken, but some quotes are still <strong>partial</strong>.
                                    This proforma will complete once all parts are priced.
                                </p>
                            @elseif ($allFilled)
                                <p class="text-muted small mt-2 mb-0">
                                    <i class="icon-material-outline-check me-1"></i>
                                    All required parts have been priced.
                                </p>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Inboxed Shops & Garages --}}
                <div class="card radius-10 margin-top-20">
                    <div class="card-body">
                        <h6 class="mb-3"><i class="icon-material-outline-business me-1"></i> Inboxed Shops & Garages</h6>
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
                <div class="card radius-10 margin-top-20">
                    <div class="card-body">
                        <h6 class="mb-3"><i class="icon-material-outline-clipboard me-1"></i> Submitted Applications</h6>
                        @if ($shopApps->isEmpty() && $garageApps->isEmpty())
                            <p class="text-muted mb-0">No applications submitted yet.</p>
                        @else
                            <div class="table-container">
                                <table class="basic-table">
                                    <thead>
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

                <div class="alert alert-info margin-top-20">
                    <i class="icon-material-outline-info"></i>
                    This is a read-only view. Marketers cannot submit quotes for proformas.
                </div>

                <div class="margin-top-20">
                    <a href="/marketer/proformas" class="button ripple-effect">
                        <i class="icon-material-outline-arrow-back"></i> Back to Proforma List
                    </a>
                </div>
            </div>
        </div>
    </div>

@endsection
