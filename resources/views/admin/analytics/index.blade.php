@extends('layouts.admin')

@section('content')
<div class="container py-4 page-wrapper">

    <h2 class="mb-4">Analytics Dashboard</h2>

    {{-- GARAGES & SHOPS --}}
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Garages & Shops</h4>
            <input type="text" id="garageSearch" class="form-control w-25" placeholder="Search Shop/User Name">
        </div>
        <div class="card-body table-responsive">
            <table class="table table-striped align-middle" id="garageTable">
                <thead class="table-dark">
                    <tr>
                        <th>User</th>
                        <th>Role</th>
                        <th>Filled Applications</th>
                        <th>Filled Proformas</th>
                        <th>Total Payments</th>
                        <th>Pending Payments</th>
                        <th>Paid Payments</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($garageShopUsers as $user)
                    <tr>
                        <td class="user-name">{{ $user->user->name }}</td>
                        <td>{{ ucfirst($user->role) }}</td>
                        <td>{{ $user->filled_applications ?? 0 }}</td>
                        <td>{{ $user->filled_proformas ?? 0 }}</td>
                        <td>{{ number_format($user->total_earned ?? 0, 2) }} ETB</td>
                        <td class="{{ ($user->remaining ?? 0) > 0 ? 'text-danger' : 'text-success' }}">
                            {{ number_format($user->remaining ?? 0, 2) }} ETB
                        </td>
                        <td class="text-success">{{ number_format($user->total_paid ?? 0, 2) }} ETB</td>
                        <td>
                            <button class="btn btn-primary btn-sm transaction-btn" data-user="{{ $user->user->id }}">
                                <i class="bi bi-arrow-left-right"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted">No records found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- INSURANCES --}}
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Insurances <small class="text-muted fs-6 ms-2">— invoice totals include claim officers</small></h4>
            <input type="text" id="insuranceSearch" class="form-control w-25" placeholder="Search User Name">
        </div>
        <div class="card-body table-responsive">
            <table class="table table-striped align-middle" id="insuranceTable">
                <thead class="table-dark">
                    <tr>
                        <th></th>
                        <th>User</th>
                        <th>Role</th>
                        <th>Total Invoiced</th>
                        <th>Pending (to Etera)</th>
                        <th>Paid (to Etera)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($insuranceUsers as $user)
                    {{-- Main insurance row --}}
                    <tr class="insurance-row" style="cursor:pointer;"
                        data-bs-toggle="collapse"
                        data-bs-target="#agents-{{ $user->user->id }}"
                        title="{{ $user->agent_breakdown->isNotEmpty() ? 'Click to view claim officers' : '' }}">
                        <td class="text-center">
                            @if($user->agent_breakdown->isNotEmpty())
                                <i class="bx bx-chevron-right text-primary toggle-icon"></i>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="user-name fw-semibold">
                            {{ $user->user->name }}
                            @if($user->agent_breakdown->isNotEmpty())
                                <span class="badge bg-secondary ms-1 fw-normal" style="font-size:0.7rem;">
                                    {{ $user->agent_breakdown->count() }} claim officer{{ $user->agent_breakdown->count() > 1 ? 's' : '' }}
                                </span>
                            @endif
                        </td>
                        <td>{{ ucfirst($user->role) }}</td>
                        <td>{{ number_format($user->insurance_proforma_total ?? 0, 2) }} ETB</td>
                        <td class="{{ ($user->insurance_proforma_unpaid ?? 0) > 0 ? 'text-danger' : 'text-success' }}">
                            {{ number_format($user->insurance_proforma_unpaid ?? 0, 2) }} ETB
                        </td>
                        <td class="text-success">{{ number_format($user->insurance_proforma_paid ?? 0, 2) }} ETB</td>
                        <td>
                            <button class="btn btn-primary btn-sm transaction-btn" data-user="{{ $user->user->id }}">
                                <i class="bi bi-arrow-left-right"></i>
                            </button>
                            <form method="POST"
                                  action="{{ route('finance.receivePayment', $user->user->id) }}"
                                  class="d-inline confirm-popup" data-word="PAYMENT RECEIVED">
                                @csrf
                                <button type="button" class="btn btn-outline-success btn-sm popup-btn"
                                    {{ ($user->insurance_proforma_unpaid ?? 0) <= 0 ? 'disabled' : '' }}
                                    title="Mark payment received">
                                    <i class="bi bi-check-circle"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    {{-- Expandable agent breakdown sub-rows --}}
                    @if($user->agent_breakdown->isNotEmpty())
                    <tr class="collapse" id="agents-{{ $user->user->id }}">
                        <td colspan="7" class="p-0">
                            <table class="table table-sm mb-0" style="background:#f8f9fa;">
                                <thead>
                                    <tr class="table-light text-muted" style="font-size:0.82rem;">
                                        <th style="width:32px;"></th>
                                        <th>Claim Officer</th>
                                        <th>Invoices</th>
                                        <th>Total Invoiced</th>
                                        <th>Pending</th>
                                        <th>Paid</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($user->agent_breakdown as $agent)
                                <tr style="font-size:0.88rem;">
                                    <td></td>
                                    <td>
                                        <i class="bx bx-user text-secondary me-1"></i>
                                        {{ $agent->name }}
                                    </td>
                                    <td class="text-muted">{{ $agent->count }}</td>
                                    <td>{{ number_format($agent->total, 2) }} ETB</td>
                                    <td class="{{ $agent->pending > 0 ? 'text-danger' : 'text-success' }}">
                                        {{ number_format($agent->pending, 2) }} ETB
                                    </td>
                                    <td class="text-success">{{ number_format($agent->paid, 2) }} ETB</td>
                                    <td>
                                        <form method="POST"
                                              action="{{ route('finance.receivePayment', $agent->id) }}"
                                              class="d-inline confirm-popup" data-word="PAYMENT RECEIVED">
                                            @csrf
                                            <button type="button" class="btn btn-outline-success btn-sm popup-btn"
                                                {{ $agent->pending <= 0 ? 'disabled' : '' }}
                                                title="Mark claim officer payment received">
                                                <i class="bi bi-check-circle"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    @endif
                @empty
                    <tr><td colspan="7" class="text-center text-muted">No records found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- TRANSACTION & INVOICE MODALS --}}
    @foreach(array_merge($garageShopUsers->all(), $insuranceUsers->all()) as $summary)
        @php
            $transactions = $summary->transactions;
            $invoices = $summary->invoices;
        @endphp
        <div class="modal fade" id="transactionModal{{ $summary->user->id }}" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Transactions for {{ $summary->user->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        {{-- INVOICES TABLE --}}
                        @if($invoices)
                        

                            <h5 class="mb-2">Invoices</h5>
                            <div class="table-responsive mb-3">
                                <table class="table table-bordered table-striped">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Date</th>
                                            <th>Invoice ID</th>
                                            <th>Subtotal</th>
                                            <th>VAT</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($invoices as $inv)
                                            <tr>
                                                <td>{{ optional($inv->created_at)->format('Y-m-d') ?? '-' }}</td>
                                                <td>{{ $inv->id ?? '-' }}</td>
                                                <td>{{ number_format($inv->subtotal ?? 0, 2) }} ETB</td>
                                                <td>{{ number_format($inv->vat_amount ?? 0, 2) }} ETB</td>
                                                <td>{{ number_format($inv->total_amount ?? 0, 2) }} ETB</td>
                                                <td>
                                                    <span class="badge {{ $inv->is_paid ? 'bg-success' : 'bg-warning text-dark' }}">
                                                        {{ $inv->is_paid ? 'Paid' : 'Unpaid' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        {{-- MARK AS PAID --}}
                        <form method="GET"
action="{{ route('finance.markPaid', $summary->user->id) }}"
      class="mb-2 confirm-popup"
      data-word="PAID">

    <button type="button"
            class="btn btn-success btn-sm popup-btn"
            {{ $summary->remaining <= 0 ? 'disabled' : '' }}>
        Mark as Paid
    </button>
</form>


                        {{-- PAYMENT RECEIVED --}}
                        @if($summary->role === 'insurance')
<form method="POST"
      action="{{ route('finance.receivePayment', $summary->user->id) }}"
      class="mb-3 confirm-popup"
      data-word="PAYMENT RECEIVED">
    @csrf

    <button type="button"
            class="btn btn-primary btn-sm popup-btn"
            {{ $summary->insurance_proforma_unpaid <= 0 ? 'disabled' : '' }}>
        Payment Received from insurance
    </button>
</form>
@endif


                        {{-- TRANSACTIONS TABLE --}}
                        @if($transactions->isEmpty())
                            <p class="text-center text-muted">No transactions found.</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-bordered" id="transactionTable{{ $summary->user->id }}">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Description</th>
                                            <th>Insured</th>
                                            <th>Amount</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($transactions as $row)
                                            <tr class="{{ $row['reason'] === 'Payment' ? 'table-info fw-bold' : '' }}">
                                                <td>{{ $row['reason'] }} <br><small class="text-muted">{{ $row['type'] }}</small></td>
                                                <td>
                                                    @if($row['is_insured'] === true)
                                                        <span class="badge bg-info">Insured</span>
                                                    @elseif($row['is_insured'] === false)
                                                        <span class="badge bg-secondary">Uninsured</span>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td class="{{ $row['amount'] >= 0 ? 'text-success fw-bold' : 'text-danger fw-bold' }}">
                                                    {{ number_format($row['amount'], 2) }} ETB
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($row['date'])->format('Y-m-d h:i A') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    @endforeach

</div>

<script>
document.addEventListener("DOMContentLoaded", function () {

    // Transaction modal buttons
    document.querySelectorAll(".transaction-btn").forEach(btn => {
        btn.addEventListener("click", function () {
            const userId = this.dataset.user;
            const modalEl = document.getElementById("transactionModal" + userId);
            if (!modalEl) return console.error("Modal not found for user:", userId);
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        });
    });

    // Search Garage & Shops
    document.getElementById("garageSearch").addEventListener("keyup", function () {
        const filter = this.value.toLowerCase();
        document.querySelectorAll("#garageTable tbody tr").forEach(row => {
            row.style.display = row.querySelector(".user-name").textContent.toLowerCase().includes(filter) ? "" : "none";
        });
    });

    // Search Insurances (only insurance-row parent rows)
    document.getElementById("insuranceSearch").addEventListener("keyup", function () {
        const filter = this.value.toLowerCase();
        document.querySelectorAll("#insuranceTable tbody tr.insurance-row").forEach(row => {
            const nameCell = row.querySelector(".user-name");
            const match = nameCell && nameCell.textContent.toLowerCase().includes(filter);
            row.style.display = match ? "" : "none";
            // Also hide the collapse agent row if parent is hidden
            const target = row.getAttribute("data-bs-target");
            if (target) {
                const agentRow = document.querySelector(target)?.closest("tr");
                if (agentRow) agentRow.style.display = match ? "" : "none";
            }
        });
    });

    // Rotate chevron icon when agent breakdown is expanded/collapsed
    document.querySelectorAll(".insurance-row[data-bs-target]").forEach(row => {
        const target = row.getAttribute("data-bs-target");
        const collapseEl = document.querySelector(target);
        const icon = row.querySelector(".toggle-icon");
        if (!collapseEl || !icon) return;
        collapseEl.addEventListener("show.bs.collapse", () => {
            icon.style.transform = "rotate(90deg)";
            icon.style.transition = "transform 0.2s";
        });
        collapseEl.addEventListener("hide.bs.collapse", () => {
            icon.style.transform = "rotate(0deg)";
        });
    });

});

document.addEventListener("DOMContentLoaded", function () {

    // Select all forms that should have confirmation popup
    document.querySelectorAll(".confirm-popup").forEach(form => {
        const requiredWord = form.dataset.word; // e.g., "PAID" or "PAYMENT RECEIVED"

        // Button inside the form
        const btn = form.querySelector(".popup-btn");
        if (!btn) return;

        btn.addEventListener("click", function (e) {
            e.stopPropagation();

            // Close any open Bootstrap modal first to avoid focus trap conflicts
            const openModal = btn.closest('.modal');
            let bsModal = null;
            if (openModal) {
                bsModal = bootstrap.Modal.getInstance(openModal);
                if (bsModal) bsModal.hide();
            }

            setTimeout(function() {
                Swal.fire({
                    title: "Confirm Action",
                    text: "Type exactly: " + requiredWord,
                    input: "text",
                    inputPlaceholder: requiredWord,
                    inputAttributes: {
                        autocapitalize: "off",
                        autocomplete: "off"
                    },
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Confirm",
                    cancelButtonText: "Cancel",
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        const input = Swal.getInput();
                        if (input) input.focus();
                    },
                    preConfirm: (value) => {
                        if (!value || value.trim().toUpperCase() !== requiredWord) {
                            Swal.showValidationMessage(
                                "You must type exactly: " + requiredWord
                            );
                            return false;
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Use HTMLFormElement.prototype.submit to avoid JS errors
                        HTMLFormElement.prototype.submit.call(form);
                    } else if (bsModal) {
                        // Re-open the modal if user cancelled
                        bsModal.show();
                    }
                });
            }, 300); // Small delay to let Bootstrap modal close cleanly
        });
    });

});
</script>

@endsection
