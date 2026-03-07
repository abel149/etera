<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ETERA Invoice – {{ $invoice->sku }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        body { background: #f4f6f9; }
        .invoice-card { max-width: 700px; margin: 40px auto; }
        .brand-header {
            background: linear-gradient(135deg, #1a237e, #283593);
            color: #fff;
            padding: 30px;
            border-radius: 12px 12px 0 0;
        }
        .brand-header h2 { font-weight: 700; }
        .sku-badge {
            font-size: 1rem;
            background: rgba(255,255,255,0.2);
            padding: 4px 14px;
            border-radius: 20px;
        }

        .qr-section {
            text-align: center;
            padding: 20px 0;
        }

        @media print {
            body { background: #fff; }
            .no-print { display: none !important; }
            .invoice-card { margin: 0; box-shadow: none !important; }
        }
    </style>
</head>
<body>

@php
    $transactionUrl = url('/transaction/' . $invoice->sku);
    $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($transactionUrl);
@endphp

<div class="invoice-card card shadow-lg border-0">

    <!-- Header -->
    <div class="brand-header text-center">
        <h2 class="mb-1">ETERA</h2>
        <p class="mb-2 opacity-75">Platform Service Invoice</p>
        <span class="sku-badge">SKU: {{ $invoice->sku }}</span>
    </div>

    <div class="card-body p-4">

        <!-- Proforma Details -->
        <div class="row mb-4">
            <div class="col-6">
                <h6 class="text-muted text-uppercase mb-2">Proforma Details</h6>
                <p class="mb-1"><strong>File #:</strong> {{ $proforma->file_number }}</p>
                <p class="mb-1"><strong>Customer:</strong> {{ $proforma->customer_name }}</p>
                <p class="mb-1"><strong>Phone:</strong> {{ $proforma->customer_phone_number ?? 'N/A' }}</p>
            </div>

            <div class="col-6 text-end">
                <h6 class="text-muted text-uppercase mb-2">Invoice Info</h6>
                <p class="mb-1"><strong>Date:</strong> {{ $invoice->created_at->format('M d, Y') }}</p>
                <p class="mb-1"><strong>Type:</strong> {{ ucfirst(str_replace('_', ' ', $invoice->type)) }}</p>
                <p class="mb-1">
                    <strong>Status:</strong>
                    @if($invoice->is_paid)
                        <span class="badge bg-success">Paid</span>
                    @else
                        <span class="badge bg-warning text-dark">Unpaid</span>
                    @endif
                </p>
            </div>
        </div>

        <!-- Vehicle Info -->
        @if($proforma->brand)
        <div class="mb-4">
            <p class="mb-1">
                <strong>Vehicle:</strong>
                {{ $proforma->brand->name }} {{ $proforma->model }} ({{ $proforma->year }})
            </p>
            <p class="mb-0">
                <strong>Plate:</strong>
                {{ $proforma->license_plate_number ?? 'N/A' }}
            </p>
        </div>
        @endif

        <hr>

        <!-- Billing Table -->
        <table class="table table-bordered mb-0">
            <thead class="table-light">
                <tr>
                    <th>Description</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Platform Service Charge</td>
                    <td class="text-end">
                        {{ number_format($invoice->unit_price ?: $invoice->hourly_price, 2) }} Birr
                    </td>
                </tr>
                <tr>
                    <td>VAT ({{ $invoice->vat_rate }}%)</td>
                    <td class="text-end">
                        {{ number_format($invoice->vat_amount, 2) }} Birr
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="table-primary fw-bold">
                    <td>Total Amount</td>
                    <td class="text-end">
                        {{ number_format($invoice->total_amount, 2) }} Birr
                    </td>
                </tr>
            </tfoot>
        </table>

        <!-- QR Code Section -->
        <div class="qr-section">
            <hr>
            <p class="mb-2 small text-muted">Scan to verify this transaction</p>

            <img src="{{ $qrCodeUrl }}" alt="Transaction QR Code" width="160">

            <p class="mt-2 small">
                {{ $transactionUrl }}
            </p>
        </div>

        <!-- Print Button -->
        <div class="text-center mt-3 no-print">
            <button class="btn btn-primary" onclick="window.print()">
                <i class="fas fa-print me-1"></i> Print Invoice
            </button>
        </div>

    </div>

    <!-- Footer -->
    <div class="card-footer text-center text-muted small py-3">
        © <script>document.write(new Date().getFullYear())</script> ETERA. All rights reserved.
    </div>

</div>

</body>
</html>