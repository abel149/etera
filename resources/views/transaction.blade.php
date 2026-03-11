<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>etera Invoice – {{ $invoice->sku }}</title>

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #f9fafb 0%, #f1f8e9 50%, #e8f5e9 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            color: #1a1a2e;
        }

        .invoice-wrapper {
            width: 100%;
            max-width: 680px;
            animation: fadeUp 0.6s ease-out;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(24px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Header */
        .invoice-header {
            background: linear-gradient(135deg, #1b5e20, #2e7d32, #43a047);
            color: #fff;
            padding: 32px 36px;
            border-radius: 18px 18px 0 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .invoice-header::before {
            content: '';
            position: absolute;
            width: 300px; height: 300px;
            border-radius: 50%;
            background: rgba(255,255,255,0.04);
            top: -120px; right: -80px;
        }
        .invoice-header::after {
            content: '';
            position: absolute;
            width: 200px; height: 200px;
            border-radius: 50%;
            background: rgba(255,255,255,0.03);
            bottom: -60px; left: -40px;
        }
        .invoice-header > * { position: relative; z-index: 1; }
        .invoice-logo {
            font-size: 1.8rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 4px;
        }
        .invoice-subtitle {
            opacity: 0.85;
            font-size: 0.9rem;
            margin-bottom: 14px;
        }
        .invoice-badge {
            display: inline-block;
            background: rgba(255,255,255,0.18);
            backdrop-filter: blur(8px);
            padding: 6px 18px;
            border-radius: 999px;
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        /* Card Body */
        .invoice-body {
            background: #fff;
            padding: 32px 36px;
            border: 1px solid rgba(40,167,69,0.1);
            border-top: none;
        }

        /* Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-bottom: 28px;
        }
        @media (max-width: 500px) { .info-grid { grid-template-columns: 1fr; } }

        .info-section-title {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: #28a745;
            margin-bottom: 10px;
        }
        .info-row {
            font-size: 0.9rem;
            margin-bottom: 6px;
            color: #374151;
            line-height: 1.6;
        }
        .info-row strong { color: #1a1a2e; font-weight: 600; }
        .info-right { text-align: right; }
        @media (max-width: 500px) { .info-right { text-align: left; } }

        /* Status badge */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 700;
        }
        .status-paid { background: rgba(40,167,69,0.1); color: #1e7e34; }
        .status-unpaid { background: rgba(245,158,11,0.1); color: #b45309; }
        .status-dot { width: 6px; height: 6px; border-radius: 50%; }
        .status-paid .status-dot { background: #28a745; }
        .status-unpaid .status-dot { background: #f59e0b; }

        /* Divider */
        .invoice-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(40,167,69,0.2), transparent);
            margin: 24px 0;
        }

        /* Vehicle info bar */
        .vehicle-bar {
            background: linear-gradient(135deg, #f9fafb, #f1f8e9);
            border: 1px solid rgba(40,167,69,0.12);
            border-radius: 12px;
            padding: 14px 18px;
            margin-bottom: 24px;
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            font-size: 0.88rem;
        }
        .vehicle-bar span { color: #6b7280; }
        .vehicle-bar strong { color: #1a1a2e; }

        /* Billing Table */
        .billing-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }
        .billing-table th {
            background: #f9fafb;
            padding: 12px 18px;
            text-align: left;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6b7280;
            border-bottom: 1px solid #e5e7eb;
        }
        .billing-table th:last-child { text-align: right; }
        .billing-table td {
            padding: 14px 18px;
            font-size: 0.92rem;
            color: #374151;
            border-bottom: 1px solid #f3f4f6;
        }
        .billing-table td:last-child { text-align: right; font-variant-numeric: tabular-nums; }
        .billing-table tr:last-child td { border-bottom: none; }

        .billing-total {
            background: linear-gradient(135deg, rgba(40,167,69,0.06), rgba(32,201,151,0.06));
        }
        .billing-total td {
            font-weight: 700;
            font-size: 1rem;
            color: #1a1a2e;
            border-bottom: none;
        }

        /* QR Section */
        .qr-section {
            text-align: center;
            padding: 24px 0 8px;
        }
        .qr-label {
            font-size: 0.78rem;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            margin-bottom: 12px;
        }
        .qr-frame {
            display: inline-block;
            padding: 12px;
            border: 2px solid rgba(40,167,69,0.15);
            border-radius: 14px;
            background: #fff;
        }
        .qr-frame img {
            display: block;
            border-radius: 6px;
        }

        /* Print Button */
        .print-section { text-align: center; margin-top: 20px; }
        .print-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 14px 32px;
            border-radius: 50px;
            background: linear-gradient(135deg, #28a745, #20c997);
            color: #fff;
            font-size: 0.95rem;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 20px rgba(40,167,69,0.3);
            transition: all 0.3s ease;
        }
        .print-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(40,167,69,0.4);
        }
        .print-btn svg { width: 18px; height: 18px; }

        /* Footer */
        .invoice-footer {
            background: #f9fafb;
            border: 1px solid rgba(40,167,69,0.1);
            border-top: none;
            border-radius: 0 0 18px 18px;
            padding: 14px;
            text-align: center;
            font-size: 0.78rem;
            color: #9ca3af;
        }

        /* Print media */
        @media print {
            body { background: #fff; padding: 0; }
            .no-print { display: none !important; }
            .invoice-wrapper { max-width: 100%; }
            .invoice-body, .invoice-footer { border: none; }
            .invoice-header { border-radius: 0; }
            .invoice-footer { border-radius: 0; }
        }
    </style>
</head>
<body>

@php
    $transactionUrl = url('/transaction/' . $invoice->sku);
    $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($transactionUrl);
@endphp

<div class="invoice-wrapper">

    {{-- Header --}}
    <div class="invoice-header">
        <div class="invoice-logo">etera</div>
        <div class="invoice-subtitle">Platform Service Invoice</div>
        <span class="invoice-badge">Invoice #{{ $invoice->sku }}</span>
    </div>

    {{-- Body --}}
    <div class="invoice-body">

        {{-- Info Grid --}}
        <div class="info-grid">
            <div>
                <div class="info-section-title">Proforma Details</div>
                <div class="info-row"><strong>File #:</strong> {{ $proforma->file_number }}</div>
                <div class="info-row"><strong>Customer:</strong> {{ $proforma->customer_name }}</div>
                <div class="info-row"><strong>Phone:</strong> {{ $proforma->customer_phone_number ?? 'N/A' }}</div>
            </div>
            <div class="info-right">
                <div class="info-section-title">Invoice Info</div>
                <div class="info-row"><strong>Date:</strong> {{ $invoice->created_at->format('M d, Y') }}</div>
                <div class="info-row"><strong>Type:</strong> {{ ucfirst(str_replace('_', ' ', $invoice->type)) }}</div>
                <div class="info-row">
                    <strong>Status:</strong>
                    @if($invoice->is_paid)
                        <span class="status-badge status-paid"><span class="status-dot"></span> Paid</span>
                    @else
                        <span class="status-badge status-unpaid"><span class="status-dot"></span> Unpaid</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Vehicle Info --}}
        @if($proforma->brand)
        <div class="vehicle-bar">
            <div><span>Vehicle:</span> <strong>{{ $proforma->brand->name }} {{ $proforma->model }} ({{ $proforma->year }})</strong></div>
            <div><span>Plate:</span> <strong>{{ $proforma->license_plate_number ?? 'N/A' }}</strong></div>
        </div>
        @endif

        <div class="invoice-divider"></div>

        {{-- Billing Table --}}
        <table class="billing-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Platform Service Charge</td>
                    <td>{{ number_format($invoice->unit_price ?: $invoice->hourly_price, 2) }} Birr</td>
                </tr>
                <tr>
                    <td>VAT ({{ $invoice->vat_rate }}%)</td>
                    <td>{{ number_format($invoice->vat_amount, 2) }} Birr</td>
                </tr>
                <tr class="billing-total">
                    <td>Total Amount</td>
                    <td>{{ number_format($invoice->total_amount, 2) }} Birr</td>
                </tr>
            </tbody>
        </table>

        {{-- QR Code --}}
        <div class="invoice-divider"></div>
        <div class="qr-section">
            <div class="qr-label">Scan to verify this transaction</div>
            <div class="qr-frame">
                <img src="{{ $qrCodeUrl }}" alt="Transaction QR Code" width="150" height="150">
            </div>
        </div>

        {{-- Print --}}
        <div class="print-section no-print">
            <button class="print-btn" onclick="window.print()">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2M6 14h12v8H6v-8z"/></svg>
                Print Invoice
            </button>
        </div>

    </div>

    {{-- Footer --}}
    <div class="invoice-footer">
        © <script>document.write(new Date().getFullYear())</script> etera. All rights reserved.
    </div>

</div>

</body>

</html>
