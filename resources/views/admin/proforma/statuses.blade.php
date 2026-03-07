@extends('layouts.admin')

@section('content')
<div class="page-wrapper">
    <div class="page-content">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h4 class="mb-0"><i class="bx bx-task me-2"></i>Proforma Statuses</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Filters -->
                        <form method="GET" class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">All Statuses</option>
                                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="sent_to_owner" {{ request('status') == 'sent_to_owner' ? 'selected' : '' }}>Sent to Owner</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Processed By</label>
                                <select name="processed_by" class="form-select">
                                    <option value="">All Admins</option>
                                    @foreach($admins as $admin)
                                        <option value="{{ $admin->id }}" {{ request('processed_by') == $admin->id ? 'selected' : '' }}>{{ $admin->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Search</label>
                                <input type="text" name="search" class="form-control" placeholder="File # or customer" value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3 d-flex align-items-end gap-2">
                                <button type="submit" class="btn btn-primary"><i class="bx bx-search me-1"></i>Filter</button>
                                <a href="{{ url('/admin/proforma-statuses') }}" class="btn btn-outline-secondary">Reset</a>
                            </div>
                        </form>

                        <!-- Stats -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card border-0 bg-light-primary">
                                    <div class="card-body text-center py-3">
                                        <h3 class="mb-0">{{ $stats['total'] }}</h3>
                                        <small class="text-muted">Total Assigned</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-0 bg-light-info">
                                    <div class="card-body text-center py-3">
                                        <h3 class="mb-0">{{ $stats['published'] }}</h3>
                                        <small class="text-muted">Published</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-0 bg-light-success">
                                    <div class="card-body text-center py-3">
                                        <h3 class="mb-0">{{ $stats['completed'] }}</h3>
                                        <small class="text-muted">Completed</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-0 bg-light-warning">
                                    <div class="card-body text-center py-3">
                                        <h3 class="mb-0">{{ $stats['closed'] }}</h3>
                                        <small class="text-muted">Closed</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>File Number</th>
                                        <th>Customer</th>
                                        <th>Brand</th>
                                        <th>Status</th>
                                        <th>Processed By</th>
                                        <th>Floated At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($proformas as $index => $proforma)
                                    <tr>
                                        <td>{{ $proformas->firstItem() + $index }}</td>
                                        <td><strong>{{ $proforma->file_number }}</strong></td>
                                        <td>{{ $proforma->customer_name }}</td>
                                        <td>{{ $proforma->brand?->name ?? 'N/A' }}</td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'pending' => 'warning',
                                                    'published' => 'info',
                                                    'closed' => 'danger',
                                                    'completed' => 'success',
                                                    'sent_to_owner' => 'primary',
                                                ];
                                                $color = $statusColors[$proforma->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $color }}">{{ ucfirst(str_replace('_', ' ', $proforma->status)) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-dark">{{ $proforma->processedBy?->name ?? 'Unassigned' }}</span>
                                        </td>
                                        <td>{{ $proforma->updated_at?->format('d M Y, h:i A') }}</td>
                                        <td>
                                            <a href="/admin/proforma-details/{{ $proforma->id }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bx bx-show"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <i class="bx bx-info-circle me-1"></i>No proformas found matching the filters.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-3">
                            {{ $proformas->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
