@extends('layouts.insurance')
@section('content')

<style type="text/css">
.card-stamp {
    position: absolute;
    bottom: 3rem;
    left: 0;
    width: calc(var(7rem)* 1);
    height: calc(var(7rem)* 1);
    max-height: 100%;
    border-top-left-radius: 4px;
    opacity: .3;
    overflow: hidden;
    pointer-events: none;
    z-index:5;
}

.card-stamp-icon {
    background: rgba(255, 255, 255, 0.5);
    color: rgba(0, 255, 255, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 100rem;
    width: calc(var(7rem)* 1);
    height: calc(var(7rem)* 1);
    position: relative;
    top: calc(var(7rem)* -.25);
    left: calc(var(7rem)* -.25);
    font-size: calc(var(7rem)* .75);
    transform: rotate(-10deg);
}
.invoice-card {
    border-radius: 12px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}
.invoice-header {
    background-color: #1976d2;
    color: white;
    padding: 24px;
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.invoice-title {
    font-size: 2.25rem;
    font-weight: 700;
    margin: 0;
}
.invoice-details {
    padding: 24px;
}
.invoice-details p {
    margin-bottom: 8px;
    font-size: 1rem;
}
.invoice-details strong {
    font-weight: 600;
    color: #333;
}
.table-container {
    overflow-x: auto;
}
.invoice-table th,
.invoice-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #e0e0e0;
}
.invoice-table thead th {
    background-color: #f5f5f5;
    font-weight: 600;
}
.invoice-summary {
    padding: 24px;
    border-bottom-left-radius: 12px;
    border-bottom-right-radius: 12px;
    background-color: #f9f9f9;
}
.invoice-summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    font-size: 1rem;
}
.invoice-summary-row strong {
    font-weight: 600;
}
.grand-total {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1976d2;
    border-top: 2px solid #1976d2;
    padding-top: 16px;
    margin-top: 16px;
}
.download-button {
    background-color: #1976d2;
    color: white;
    border-radius: 20px;
    padding: 10px 24px;
    font-size: 1rem;
    transition: background-color 0.3s;
}
.download-button:hover {
    background-color: #1565c0;
}
.center-content {
    display: flex;
    justify-content: center;
    align-items: center;
}

.company-stamp {
    position: absolute;
    bottom: 160px;
    left: 4px;
    width: 300px;
    height:300px;
    opacity: 0.7;
    transform: rotate(10deg);
    pointer-events: none;
}

.profile-pic.stamp-image {
    width: 200px;
    height: 200px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #ccc;
}

/* Invoice Link */
.invoice-link-card {
    text-align: center;
    padding: 1.5rem;
    margin-top: 1.5rem;
    background: linear-gradient(135deg, #f0fdf4, #dcfce7);
    border-radius: 16px;
    border: 1px dashed #6ee7b7;
    animation: fadeSlideUp 0.7s ease-out;
}
.btn-invoice {
    background: linear-gradient(135deg, #10b981, #059669);
    color: #fff;
    border: none;
    padding: 12px 32px;
    border-radius: 50px;
    font-size: 1rem;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 14px rgba(5, 150, 105, 0.3);
}
.btn-invoice:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(5, 150, 105, 0.4);
    color: #fff;
    text-decoration: none;
}

</style>

@if(auth()->user()->has_encryption)
<div id="decryptPanel" class="alert alert-warning d-flex align-items-start gap-3 mb-3 p-3" role="alert">
    <i class="bx bx-lock fs-2 flex-shrink-0 mt-1"></i>
    <div class="flex-grow-1">
        <div class="fw-semibold mb-1">Prices are encrypted &mdash; enter your Encryption PIN to view them.</div>
        <div class="d-flex flex-wrap gap-2 align-items-center">
            <input type="password" id="decryptPin" class="form-control form-control-sm"
                   placeholder="Encryption PIN" style="max-width:220px;">
            <button id="btnDecrypt" class="btn btn-warning btn-sm px-4 radius-30">
                <i class="bx bx-lock-open me-1"></i> Decrypt Prices
            </button>
        </div>
        <div id="decryptError" class="text-danger small mt-2 d-none"></div>
    </div>
</div>
@endif

<div class="card">
    <div class="card-body">
    
        <div class="row">
            @if(!$proforma->isGarageOnlyInsurance())
            <div class="col-12 col-md-6 mx-auto">
                <h4 class="mb-3 steper-title text-center">Spare Part Shops</h4>
                @foreach($applications as $application)
                @if($application->applicationBy->role == 'shop')
                <div class="col-lg-12 mb-3">
                    <div class="card shadow application-card"
                         data-application-id="{{ $application->id }}"
                         data-store-id="{{ $application->applicationBy->store_id }}"
                         data-tin-number="{{ $application->applicationBy->tin_number }}"
                         data-location="{{ $application->applicationBy->location }}"
                         data-shop-name="{{ $application->applicationBy->name }}"
                         data-phone="{{ $application->applicationBy->phone_number ?? 'N/A' }}"
                         data-stamp-image="{{ $application->applicationBy->stamp_image ? asset('storage/' . $application->applicationBy->stamp_image) : asset('assets/images/stamp.png') }}"
                         data-discount="{{ $application->discount ?? 0 }}"
                         data-notes="{{ $application->notes ?? '' }}">

                         
                        <div class="card-stamp">
                            @if($application->applicationBy->stamp_image)
                            <img class="profile-pic stamp-image" src="{{ asset('storage/' . $application->applicationBy->stamp_image) }}" alt="Stamp" />
                            @else
                            <img class="profile-pic stamp-image" src="{{ asset('assets/images/stamp.png') }}" alt="No Stamp Here" />
                            @endif
                        </div>
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                <div class="">
                                    <img src="{{asset('assets/images/avatars/avatar-9.jpg')}}" class="rounded-circle" width="40" height="40" alt="">
                                </div>
                                <div class="ms-2">
                                    <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#details"><h6 class="mb-0 font-17">{{$application->applicationBy->name}}</h6></a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-3 px-4 pb-0">
                            <div class="row mb-3">
                                <div class="col-6">
                                    <span><b class="font-17">Store ID: </b>
                                        <span class="text-secondary font-16">{{ $application->applicationBy->store_id }}</span>
                                    </span>
                                </div>
                                <div class="col-6">
                                    <span><b class="font-17">Tin #: </b>
                                        <span class="text-secondary font-16">{{ $application->applicationBy->tin_number }}</span>
                                    </span>
                                </div>
                                <div class="col-10">
                                    <span><b class="font-17">Location: </b>
                                        <span class="text-secondary font-16">{{ $application->applicationBy->location }}</span>
                                    </span>
                                </div>
                            </div>
                            <div class="invoice mb-1" style="overflow-y: hidden; overflow-x: auto; white-space: nowrap;">
                                <table style="font-size: 10px; border-collapse: collapse; width: 100%;">
                                    <thead>
                                        <tr>
                                            <th style="text-align: left; padding: 4px;">No</th>
                                            <th style="text-align: left; padding: 4px;">Part Name and Number</th>
                                            <th style="text-align: left; padding: 4px;">Condition</th>
                                            <th style="text-align: left; padding: 4px;">Grade</th>
                                            <th style="text-align: left; padding: 4px;">Country</th>
                                            <th style="text-align: left; padding: 4px;">Qty</th>
                                            <th style="text-align: left; padding: 4px;">Unit Price</th>
                                            <th style="text-align: left; padding: 4px;">Total Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $shopSubtotal = 0; @endphp
                                        @foreach($proforma->parts as $part)
                                        @php
                                            $partPrice     = $application->prices->values()->get($loop->index);
                                            $isEncPart     = $partPrice && !empty($partPrice->price_is_encrypted);
                                            $hasPrice      = $partPrice && !$isEncPart && $partPrice->unit_price > 0;
                                            $unitPrice     = $hasPrice ? (float)$partPrice->unit_price : 0;
                                            $totalPrice    = $hasPrice ? $unitPrice * $part->quantity : 0;
                                            $shopSubtotal += $totalPrice;
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->index + 1 }}</td>
                                            <td>{{ $part->number }}</td>
                                            <td>{{ $part->condition ?? 'N/A' }}</td>
                                            <td>{{ $part->grade }}</td>
                                            <td>{{ $part->country }}</td>
                                            <td>{{ $part->quantity }}</td>
                                            @if($isEncPart)
                                            <td class="enc-unit-cell" data-price-id="{{ $partPrice->id }}" data-qty="{{ $part->quantity }}">
                                                <span class="enc-unit-price" data-price-id="{{ $partPrice->id }}">
                                                    <i class="bx bx-lock text-warning"></i> <em class="text-warning">Encrypted</em>
                                                </span>
                                            </td>
                                            <td class="enc-total-cell" data-price-id="{{ $partPrice->id }}">
                                                <span class="enc-part-total" data-price-id="{{ $partPrice->id }}">
                                                    <em class="text-warning">—</em>
                                                </span>
                                            </td>
                                            @elseif($hasPrice)
                                            <td>{{ number_format($unitPrice, 2) }} ETB</td>
                                            <td>{{ number_format($totalPrice, 2) }} ETB</td>
                                            @else
                                            <td class="text-muted fst-italic">— Not available</td>
                                            <td class="text-muted">—</td>
                                            @endif
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        @php
                                        $discountPct = (float)($application->discount ?? 0);
                                        $discountAmt = ($shopSubtotal * $discountPct) / 100;
                                        $netTotal = $shopSubtotal - $discountAmt;
                                        @endphp
                                        <tr>
                                            <td colspan="7"></td>
                                            <td>SUBTOTAL</td>
                                            <td class="shop-subtotal-val" data-app-id="{{ $application->id }}">{{ number_format($shopSubtotal, 2) }} ETB</td>
                                        </tr>
                                        <tr>
                                            <td colspan="7"></td>
                                            <td>DISCOUNT</td>
                                            <td class="shop-discount-val" data-app-id="{{ $application->id }}" data-discount-pct="{{ $discountPct }}">{{ number_format($discountAmt, 2) }} ETB ({{ $discountPct }}%)</td>
                                        </tr>
                                        <tr>
                                            <td colspan="7"></td>
                                            <td>NET TOTAL</td>
                                            <td class="shop-nettotal-val" data-app-id="{{ $application->id }}">{{ number_format($netTotal, 2) }} ETB</td>
                                        </tr>
                                    </tfoot>
                                </table>

                                <p style="font-size: 9px; margin-top: 4px;">
                                    <strong class="text-danger">NOTE:</strong> All prices not including VAT
                                </p>

                                <div style="font-size: 10px; margin-top: 2px;">
                                    <strong>Discount:</strong> {{$application->discount}} %
                                </div>
                            </div>
                            @if($application->notes)
                            <div style="margin: 10px 0 4px; background: rgba(13,148,136,0.06); border-left: 3px solid #4dd0c4; border-radius: 0 6px 6px 0; padding: 8px 12px;">
                                <span style="font-size:10px; font-weight:600; color:#4dd0c4; display:block; margin-bottom:3px;"><i class="bx bx-message-detail" style="margin-right:3px;"></i>Applicant Notes</span>
                                <span style="font-size:11px; color:#ccc; white-space:pre-wrap;">{{ $application->notes }}</span>
                            </div>
                            @endif
                        </div>
                        <div class="card-footer text-end">
                            <button class="btn btn-outline-primary select-shop-btn" data-application-id="{{ $application->id }}">Select</button>
                        </div>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
            
            @endif
            @if(!$proforma->isShopOnlyInsurance())
            <div class="col-12 col-md-6 mx-auto">
                <h4 class="mb-3 steper-title text-center">Garages</h4>
                @foreach($applications as $application)
                @if($application->applicationBy->role == 'garage')
                <div class="col-lg-12 mb-3">
                    <div class="card shadow garage-card"
                         data-application-id="{{ $application->id }}"
                         data-store-id="{{ $application->applicationBy->store_id }}"
                         data-tin-number="{{ $application->applicationBy->tin_number }}"
                         data-location="{{ $application->applicationBy->location }}"
                         data-garage-name="{{ $application->applicationBy->name }}"
                         data-phone="{{ $application->applicationBy->phone_number ?? 'N/A' }}"
                         data-stamp-image="{{ $application->applicationBy->stamp_image ? asset('storage/' . $application->applicationBy->stamp_image) : asset('assets/images/stamp.png') }}"
                         data-discount="{{ $application->discount ?? 0 }}"
                         data-amount="{{ $application->amount ?? 0 }}"
                         data-encrypted-amount="{{ $application->encrypted_amount ?? '' }}"
                         data-amount-is-encrypted="{{ $application->amount_is_encrypted ? '1' : '0' }}"
                         data-notes="{{ $application->notes ?? '' }}">

                        <div class="card-stamp">
                            @if($application->applicationBy->stamp_image)
                            <img class="profile-pic stamp-image" src="{{ asset('storage/' . $application->applicationBy->stamp_image) }}" alt="Stamp" />
                            @else
                            <img class="profile-pic stamp-image" src="{{ asset('assets/images/stamp.png') }}" alt="No Stamp Here" />
                            @endif
                        </div>
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                <div class="">
                                    <img src="{{asset('assets/images/avatars/avatar-9.jpg')}}" class="rounded-circle" width="40" height="40" alt="">
                                </div>
                                <div class="ms-2">
                                    <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#details"><h6 class="mb-0 font-17">{{$application->applicationBy->name}}</h6></a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-3 px-4 pb-0">
                            <div class="row mb-3">
                                <div class="col-6">
                                    <span><b class="font-17">Store ID: </b>
                                        <span class="text-secondary font-16">{{ $application->applicationBy->store_id }}</span>
                                    </span>
                                </div>
                                <div class="col-6">
                                    <span><b class="font-17">Tin #: </b>
                                        <span class="text-secondary font-16">{{ $application->applicationBy->tin_number }}</span>
                                    </span>
                                </div>
                                <div class="col-10">
                                    <span><b class="font-17">Location: </b>
                                        <span class="text-secondary font-16">{{ $application->applicationBy->location }}</span>
                                    </span>
                                </div>
                            </div>
                            <div class="invoice mb-1" style="overflow-y: hidden; overflow-x: auto; white-space: nowrap;">
                                <table style="font-size: 10px; border-collapse: collapse; width: 100%;">
                                    <thead>
                                        <tr>
                                            <th style="text-align: left; padding: 4px;">No</th>
                                            <th style="text-align: left; padding: 4px;">Service Name</th>
                                            <th style="text-align: left; padding: 4px;">Description</th>
                                            <th style="text-align: left; padding: 4px;">Service Type</th>
                                            <th style="text-align: left; padding: 4px;">Estimate Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $garageAmount    = (float) ($application->amount ?? 0);
                                        $isEncAmt        = !empty($application->amount_is_encrypted);
                                        $garageDiscountPct = (float)($application->discount ?? 0);
                                        $garageDiscountAmt = ($garageAmount * $garageDiscountPct) / 100;
                                        $garageNetTotal    = $garageAmount - $garageDiscountAmt;
                                        @endphp
                                        <tr>
                                            <td style="padding: 4px;">1</td>
                                            <td style="padding: 4px;">Garage Repair Service</td>
                                            <td style="padding: 4px;">Complete repair service</td>
                                            <td style="padding: 4px;">Full Service</td>
                                            <td style="padding: 4px;" class="price-cell" data-app-id="{{ $application->id }}">
                                                @if($isEncAmt)
                                                    <span class="encrypted-price" data-app-id="{{ $application->id }}">
                                                        <i class="bx bx-lock text-warning"></i> <em class="text-warning">Encrypted</em>
                                                    </span>
                                                @else
                                                    {{ number_format($garageAmount, 2) }} ETB
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="4" style="padding: 4px; font-weight: bold;">SUBTOTAL</td>
                                            <td style="padding: 4px; font-weight: bold;" class="price-cell" data-app-id="{{ $application->id }}">
                                                @if($isEncAmt)
                                                    <span class="encrypted-price" data-app-id="{{ $application->id }}">
                                                        <i class="bx bx-lock text-warning"></i> <em class="text-warning">Encrypted</em>
                                                    </span>
                                                @else
                                                    {{ number_format($garageAmount, 2) }} ETB
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" style="padding: 4px; font-weight: bold;">DISCOUNT</td>
                                            <td style="padding: 4px; font-weight: bold;">
                                                @if(!$isEncAmt)
                                                    {{ number_format($garageDiscountAmt, 2) }} ETB ({{ $garageDiscountPct }}%)
                                                @else
                                                    <em class="text-warning">—</em>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" style="padding: 4px; font-weight: bold;">NET TOTAL</td>
                                            <td style="padding: 4px; font-weight: bold;" class="price-cell" data-app-id="{{ $application->id }}">
                                                @if($isEncAmt)
                                                    <span class="encrypted-price" data-app-id="{{ $application->id }}">
                                                        <i class="bx bx-lock text-warning"></i> <em class="text-warning">Encrypted</em>
                                                    </span>
                                                @else
                                                    {{ number_format($garageNetTotal, 2) }} ETB
                                                @endif
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                                <p style="font-size: 9px; margin-top: 4px;">
                                    <strong class="text-danger">NOTE:</strong> All prices not including VAT
                                </p>
                            </div>
                            @if($application->notes)
                            <div style="margin: 10px 16px 4px; background: rgba(13,148,136,0.06); border-left: 3px solid #4dd0c4; border-radius: 0 6px 6px 0; padding: 8px 12px;">
                                <span style="font-size:10px; font-weight:600; color:#4dd0c4; display:block; margin-bottom:3px;"><i class="bx bx-message-detail" style="margin-right:3px;"></i>Applicant Notes</span>
                                <span style="font-size:11px; color:#ccc; white-space:pre-wrap;">{{ $application->notes }}</span>
                            </div>
                            @endif
                        </div>
                        <div class="card-footer text-end">
                            <button class="btn btn-outline-primary select-garage-btn" data-application-id="{{ $application->id }}">Select</button>
                        </div>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
            @endif
        </div>

@include('components.proforma-media', ['proforma' => $proforma])

        {{-- Invoice Link --}}
        @if($proforma->proformaInvoice && $proforma->proformaInvoice->sku)
            <div class="invoice-link-card">
                <a href="{{ url('/transaction/' . $proforma->proformaInvoice->sku) }}" class="btn-invoice" target="_blank">
                    <i class="bx bx-file"></i> View Invoice
                </a>
            </div>
        @endif

    </div>
</div>

<!-- Modal (unchanged) -->
<div class="modal fade" id="details" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">User Name</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Modal content unchanged -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary radius-30" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


@endsection

<script>{!! file_get_contents(base_path('resources/js/e2e-encryption.js')) !!}</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle Spare Part Shop select buttons
    document.querySelectorAll('.select-shop-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const applicationId = this.getAttribute('data-application-id');
            const card = document.querySelector(`.application-card[data-application-id="${applicationId}"]`);
            if (card) {
                openPrintPage(card);
            }
        });
    });
    
    // Handle Garage select buttons
    document.querySelectorAll('.select-garage-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const applicationId = this.getAttribute('data-application-id');
            const card = document.querySelector(`.garage-card[data-application-id="${applicationId}"]`);
            if (card) {
                openPrintPages(card);
            }
        });
    });
});

function openPrintPage(card) {
    // Extract data from the specific card
    const storeId = card.dataset.storeId || "N/A";
    const tinNumber = card.dataset.tinNumber || "N/A";
    const location = card.dataset.location || "N/A";
    const shopName = card.dataset.shopName || "N/A";
    const phoneNumber = card.dataset.phone || "N/A";
    const stampImage = card.dataset.stampImage || "{{ asset('assets/images/stamp.png') }}";
    const discountPct = parseFloat(card.dataset.discount) || 0;
    const applicantNotes = card.dataset.notes || "";
    
    // Get proforma data
    const customerName = "{{ $proforma->customer_name ?? 'N/A' }}";
    const customerPhone = "{{ $proforma->customer_phone_number ?? 'N/A' }}";
    const brand = "{{ $proforma->brand->name ?? 'N/A' }}";
    const year = "{{ $proforma->year ?? 'N/A' }}";
    const plate = "{{ $proforma->license_plate_number ?? 'N/A' }}";
    const createdAt = "{{ $proforma->proformaInvoice?->created_at->format('M d, Y') }}";
    
    // Extract table data from THIS card
    const table = card.querySelector("table");
    const rows = table?.querySelectorAll("tbody tr") || [];
    const partsData = [];
    
    rows.forEach((row, index) => {
        const cells = row.querySelectorAll("td");
        if (cells.length >= 8) {
            partsData.push({
                no: index + 1,
                partNumber: cells[1].textContent.trim(),
                condition: cells[2].textContent.trim(),
                grade: cells[3].textContent.trim(),
                country: cells[4].textContent.trim(),
                quantity: cells[5].textContent.trim(),
                unitPrice: cells[6].textContent.trim(),
                total: cells[7].textContent.trim()
            });
        }
    });
    
    // Calculate totals
    const parseETB = (value) => parseFloat(value.replace(/[^0-9.-]+/g, "")) || 0;
    const subtotal = partsData.reduce((sum, p) => sum + parseETB(p.total), 0);
    const discountAmt = (subtotal * discountPct) / 100;
    const netTotal = subtotal - discountAmt;
    const grandTotal = netTotal;
    
    const formatETB = (num) => {
        return num.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + " ETB";
    };
    
    // Open print window
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="utf-8" />
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>etera - Spare Parts Invoice</title>
            <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700' type='text/css'>
            <link rel="stylesheet" href="{{ asset('assets/invoice/vendor/bootstrap/css/bootstrap.min.css') }}"/>
            <link rel="stylesheet" href="{{ asset('assets/invoice/vendor/font-awesome/css/all.min.css') }}"/>
            <link rel="stylesheet" href="{{ asset('assets/invoice/css/stylesheet.css') }}"/>
            <style>
                .table th, .table td { padding: 8px; }
                .text-end { text-align: right; }
                .stamp-image {
                    width: 200px;
                    height: 200px;
                    border-radius: 50%;
                    object-fit: cover;
                    border: 2px solid #ccc;
                }
                .company-stamp {
                    position: absolute;
                    bottom: 160px;
                    left: 4px;
                    width: 30px;
                    height:30px;
                    opacity: 0.7;
                    transform: rotate(10deg);
                    pointer-events: none;
                }
                .card-stamp {
                    position: absolute;
                    top: 3rem;
                    left: 5rem;
                    opacity: .3;
                    z-index:5;
                }
                .invoice-container { position: relative; }
                .text-primary { color: #1976d2 !important; }
                .border-top { border-top: 2px solid #1976d2 !important; }
            </style>
        </head>
        <body>
            <div class="container-fluid invoice-container">
                <header>
                    <div class="row align-items-center gy-3">
                        <div class="col-sm-7 text-center text-sm-start">
                            <h4 class="text-7 mb-0 text-primary">Proforma Invoice</h4>
                        </div>
                        <div class="col-sm-5 text-center text-sm-end">
                            <h6 class="mb-0">Shop: ${shopName}</h6>
                        </div>
                    </div>
                    <hr>
                </header>

                <main>
                    <div class="row">
                        <div class="col-sm-6"><strong>Date:</strong> ${new Date().toLocaleDateString()}</div>
                        <div class="col-sm-6 text-sm-end"><strong>Invoice No:</strong> ${Math.floor(Math.random() * 100000)}</div>
                    </div>
                    <hr>

                    <div class="row gy-3 align-items-start">
                        <div class="col-sm-6">
                            <p class="mb-1"><strong>Store ID:</strong> ${storeId}</p>
                            <p class="mb-1"><strong>Shop Name:</strong> ${shopName}</p>
                            <p class="mb-1"><strong>Tin #:</strong> ${tinNumber}</p>
                            <p class="mb-1"><strong>Location:</strong> ${location}</p>
                            <p class="mb-1"><strong>Phone:</strong> ${phoneNumber}</p>
                        </div>
                        <div class="col-sm-6 text-sm-end">
                            <p class="mb-1"><strong>Customer:</strong> ${customerName}</p>
                            <p class="mb-1"><strong>Customer Phone:</strong> ${customerPhone}</p>
                            <p class="mb-1"><strong>Car:</strong> ${year} ${brand} [${plate}]</p>
                            <br><br>
                            <strong>Author:</strong>
                            <address>
                                etera<br />
                                portal.eteraet.com<br />
                                Addis Ababa, Ethiopia
                            </address>
                        </div>
                    </div>
                    

                    <div class="table-responsive mt-4">
                        <table class="table border">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Part Name & Number</th>
                                    <th>Condition</th>
                                    <th>Grade</th>
                                    <th>Country</th>
                                    <th>Qty</th>
                                    <th>Unit Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${partsData.map(part => `
                                    <tr>
                                        <td>${part.no}</td>
                                        <td>${part.partNumber}</td>
                                        <td>${part.condition}</td>
                                        <td>${part.grade}</td>
                                        <td>${part.country}</td>
                                        <td>${part.quantity}</td>
                                        <td>${part.unitPrice}</td>
                                        <td>${part.total}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="7" class="text-end"><strong>SUBTOTAL:</strong></td>
                                    <td class="text-end">${formatETB(subtotal)}</td>
                                </tr>
                                <tr>
                                    <td colspan="7" class="text-end"><strong>DISCOUNT:</strong></td>
                                    <td class="text-end">${formatETB(discountAmt)} (${discountPct}%)</td>
                                </tr>
                                <tr>
                                    <td colspan="7" class="text-end"><strong>NET TOTAL:</strong></td>
                                    <td class="text-end">${formatETB(netTotal)}</td>
                                </tr>
                                <tr style="background-color: #e3f2fd; font-weight: bold; border-top: 2px solid #1976d2;">
                                    <td colspan="7" class="text-end"><strong>GRAND TOTAL:</strong></td>
                                    <td class="text-end text-primary" style="font-size: 1.1em;">${formatETB(grandTotal)}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <p class="text-danger mt-4"><strong>NOTE:</strong> All prices not including VAT</p>
                    ${applicantNotes ? `<div style="margin-top:16px;background:#f0fdf9;border-left:4px solid #14b8a6;border-radius:0 6px 6px 0;padding:10px 16px;"><p style="font-size:0.8rem;font-weight:700;color:#0f766e;margin-bottom:4px;">&#128172; Applicant Notes</p><p style="font-size:0.9rem;margin:0;white-space:pre-wrap;color:#1f2937;">${applicantNotes}</p></div>` : ''}

                    <div class="card-stamp">
                        <img class="stamp-image" src="${stampImage}" alt="Stamp" />
                    </div>
                </main>

                <footer class="text-center mt-4">
                    <p><strong>NOTE:</strong> Price is NOT including 15% VAT.</p>
                    <div class="btn-group btn-group-sm d-print-none">
                        <a href="javascript:window.print()" class="btn btn-light border text-black-50 shadow-none">
                            <i class="fa fa-print"></i> Print & Download
                        </a>
                    </div>
                </footer>
            </div>
        </body>
        </html>
    `);
    printWindow.document.close();
}

function openPrintPages(card) {
    // Extract data from the specific garage card
    const storeId = card.dataset.storeId || "N/A";
    const tinNumber = card.dataset.tinNumber || "N/A";
    const location = card.dataset.location || "N/A";
    const garageName = card.dataset.garageName || "N/A";
    const phoneNumber = card.dataset.phone || "N/A";
    const stampImage = card.dataset.stampImage || "{{ asset('assets/images/stamp.png') }}";
    const discountPct = parseFloat(card.dataset.discount) || 0;
    const amount = parseFloat(card.dataset.amount) || 0;
    const applicantNotes = card.dataset.notes || "";
    
    // Get proforma data
    const customerName = "{{ $proforma->customer_name ?? 'N/A' }}";
    const customerPhone = "{{ $proforma->customer_phone_number ?? 'N/A' }}";
    const brand = "{{ $proforma->brand->name ?? 'N/A' }}";
    const year = "{{ $proforma->year ?? 'N/A' }}";
    const plate = "{{ $proforma->license_plate_number ?? 'N/A' }}";
    const createdAt = "{{ $proforma->proformaInvoice?->created_at->format('M d, Y') }}";
    
    // Calculate garage totals
    const discountAmt = (amount * discountPct) / 100;
    const netTotal = amount - discountAmt;
    
    const formatETB = (num) => {
        return num.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + " ETB";
    };
    
    // Open print window
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="utf-8" />
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>etera - Garage Service Invoice</title>
            <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700' type='text/css'>
            <link rel="stylesheet" href="{{ asset('assets/invoice/vendor/bootstrap/css/bootstrap.min.css') }}"/>
            <link rel="stylesheet" href="{{ asset('assets/invoice/vendor/font-awesome/css/all.min.css') }}"/>
            <link rel="stylesheet" href="{{ asset('assets/invoice/css/stylesheet.css') }}"/>
            <style>
                .table th, .table td { padding: 8px; }
                .text-end { text-align: right; }
                .stamp-image {
                    width: 200px;
                    height: 200px;
                    border-radius: 50%;
                    object-fit: cover;
                    border: 2px solid #ccc;
                }
                .company-stamp {
                    position: absolute;
                    bottom: 160px;
                    left: 4px;
                    width: 300px;
                    height:300px;
                    opacity: 0.7;
                    transform: rotate(10deg);
                    pointer-events: none;
                }
                .card-stamp {
                    position: absolute;
                    top: 3rem;
                    left: 5rem;
                    opacity: .3;
                    z-index:5;
                }
                .invoice-container { position: relative; }
                .text-primary { color: #1976d2 !important; }
                .border-top { border-top: 2px solid #1976d2 !important; }
            </style>
        </head>
        <body>
            <div class="container-fluid invoice-container">
                <header>
                    <div class="row align-items-center gy-3">
                        <div class="col-sm-7 text-center text-sm-start">
                            <h4 class="text-7 mb-0 text-primary">Garage Service Invoice</h4>
                        </div>
                        <div class="col-sm-5 text-center text-sm-end">
                            <h6 class="mb-0">Garage: ${garageName}</h6>
                        </div>
                    </div>
                    <hr>
                </header>

                <main>
                    <div class="row">
                        <div class="col-sm-6"><strong>Date:</strong> ${new Date().toLocaleDateString()}</div>
                        <div class="col-sm-6 text-sm-end"><strong>Invoice No:</strong> ${Math.floor(Math.random() * 100000)}</div>
                    </div>
                    <hr>

                    <div class="row gy-3 align-items-start">
                        <div class="col-sm-6">
                            <p class="mb-1"><strong>Store ID:</strong> ${storeId}</p>
                            <p class="mb-1"><strong>Garage Name:</strong> ${garageName}</p>
                            <p class="mb-1"><strong>Tin #:</strong> ${tinNumber}</p>
                            <p class="mb-1"><strong>Location:</strong> ${location}</p>
                            <p class="mb-1"><strong>Phone:</strong> ${phoneNumber}</p>
                        </div>
                        <div class="col-sm-6 text-sm-end">
                            <p class="mb-1"><strong>Customer:</strong> ${customerName}</p>
                            <p class="mb-1"><strong>Customer Phone:</strong> ${customerPhone}</p>
                            <p class="mb-1"><strong>Car:</strong> ${year} ${brand} [${plate}]</p>
                            <br><br>
                            <strong>Author:</strong>
                            <address>
                                etera<br />
                                portal.eteraet.com<br />
                                Addis Ababa, Ethiopia
                            </address>
                        </div>
                    </div>

                    <div class="table-responsive mt-4">
                        <table class="table border">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Service Name</th>
                                    <th>Description</th>
                                    <th>Service Type</th>
                                    <th>Estimate Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>Garage Repair Service</td>
                                    <td>Complete repair service</td>
                                    <td>Full Service</td>
                                    <td>${formatETB(amount)}</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-end"><strong>SUBTOTAL:</strong></td>
                                    <td class="text-end">${formatETB(amount)}</td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end"><strong>DISCOUNT:</strong></td>
                                    <td class="text-end">${formatETB(discountAmt)} (${discountPct}%)</td>
                                </tr>
                                <tr style="background-color: #e3f2fd; font-weight: bold; border-top: 2px solid #1976d2;">
                                    <td colspan="4" class="text-end"><strong>NET TOTAL:</strong></td>
                                    <td class="text-end text-primary" style="font-size: 1.1em;">${formatETB(netTotal)}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <p class="text-danger mt-4"><strong>NOTE:</strong> All prices not including VAT</p>
                    ${applicantNotes ? `<div style="margin-top:16px;background:#f0fdf9;border-left:4px solid #14b8a6;border-radius:0 6px 6px 0;padding:10px 16px;"><p style="font-size:0.8rem;font-weight:700;color:#0f766e;margin-bottom:4px;">&#128172; Applicant Notes</p><p style="font-size:0.9rem;margin:0;white-space:pre-wrap;color:#1f2937;">${applicantNotes}</p></div>` : ''}

                    <div class="card-stamp">
                        <img class="stamp-image" src="${stampImage}" alt="Stamp" />
                    </div>
                </main>

                <footer class="text-center mt-4">
                    <p><strong>NOTE:</strong> Price is NOT including 15% VAT.</p>
                    <div class="btn-group btn-group-sm d-print-none">
                        <a href="javascript:window.print()" class="btn btn-light border text-black-50 shadow-none">
                            <i class="fa fa-print"></i> Print & Download
                        </a>
                    </div>
                </footer>
            </div>
        </body>
        </html>
    `);
    printWindow.document.close();
}

// etera Receipt for Insured Proformas
function openPrintingPage() {
    // This part is for the etera Receipt
    const customerName = "{{ $proforma->customer_name ?? 'N/A' }}";
    const customerPhone = "{{ $proforma->customer_phone_number ?? 'N/A' }}";
    const createdAt = "{{ $proforma->proformaInvoice?->created_at->format('M d, Y') }}";
    const brand = "{{ $proforma->brand->name ?? 'N/A' }}";
    const year = "{{ $proforma->year ?? 'N/A' }}";
    const description = "{{ $proforma->description ?? 'N/A' }}";

    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="utf-8" />
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>etera - Receipt</title>
            <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700' type='text/css'>
            <link rel="stylesheet" href="{{ asset('assets/invoice/vendor/bootstrap/css/bootstrap.min.css') }}"/>
            <link rel="stylesheet" href="{{ asset('assets/invoice/vendor/font-awesome/css/all.min.css') }}"/>
            <link rel="stylesheet" href="{{ asset('assets/invoice/css/stylesheet.css') }}"/>
            <style>
                .table th, .table td { padding: 8px; }
                .text-end { text-align: right; }
                .stamp-image {
                    width: 200px;
                    height: 200px;
                    border-radius: 50%;
                    object-fit: cover;
                    border: 2px solid #ccc;
                    position: absolute;
                    top: 10rem;
                    left: 17rem;
                    opacity: .8;
                    z-index:5;
                }
                .invoice-container { position: relative; }
            </style>
        </head>
        <body>
            <div class="container-fluid invoice-container">
                <header>
                    <div class="row align-items-center gy-3">
                        <div class="col-sm-7 text-center text-sm-start">
                            <img id="logo" src="{{ asset('assets/invoice/images/transparent.png') }}" height="70" width="200" alt="etera" />
                        </div>
                        <div class="col-sm-5 text-center text-sm-end">
                            <h4 class="text-7 mb-0">etera - Receipt</h4>
                        </div>
                    </div>
                    <hr>
                </header>

                <main>
                    <div class="row gy-3 align-items-start">
                        <div class="col-sm-6">
                            <p class="mb-1"><strong>etera:</strong></p>
                            <p class="mb-1"><strong>Phone:</strong> phone</p>
                            <p class="mb-1"><strong>TIN:</strong> TIN</p>
                            <p class="mb-1"><strong>Date:</strong> ${createdAt}</p>
                        </div>
                        <div class="col-sm-6 text-sm-end">
                            <strong>Customer:</strong> ${customerName}<br>
                            <strong>Phone:</strong> ${customerPhone}
                        </div>
                    </div>
        @php
            $baseAmount = $proforma->proformaInvoice?->unit_price;
            $vatRate = 15;
            $vatAmount = ($baseAmount * $vatRate) / 100;
            $totalAmount = $baseAmount + $vatAmount;
        @endphp
	<div class="table-responsive mt-4">
          <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Platform Service Charge</td>
                    <td class="text-end">{{ number_format($baseAmount, 2) }} Birr</td>
                </tr>
                <tr>
                    <td>VAT ({{ $vatRate }}%)</td>
                    <td class="text-end">{{ number_format($vatAmount, 2) }} Birr</td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="table-success">
                    <th>Total Paid Amount</th>
                    <th class="text-end">{{ number_format($totalAmount, 2) }} Birr</th>
                </tr>
            </tfoot>
        </table>

                         <img src="{{ asset('assets/invoice/images/stamp.png') }}" class="stamp-image" alt="Stamp">
                    </div>
                </main>

                <footer class="text-center mt-4">
                    <div class="btn-group btn-group-sm d-print-none">
                        <a href="javascript:window.print()" class="btn btn-light border text-black-50 shadow-none">
                            <i class="fa fa-print"></i> Print & Download
                        </a>
                    </div>
                </footer>
            </div>
        </body>
        </html>
    `);
    printWindow.document.close();
}

@php
    // Build encrypted data maps in PHP to avoid complex expressions inside @json()
    $encryptedAppsMap = [];
    foreach ($applications as $_app) {
        if (!empty($_app->amount_is_encrypted) && $_app->encrypted_amount) {
            $encryptedAppsMap[$_app->id] = $_app->encrypted_amount;
        }
    }
    $encryptedPricesMap = [];
    foreach ($applications->where('from', 'shop') as $_app) {
        foreach ($_app->prices as $_price) {
            if (!empty($_price->price_is_encrypted) && $_price->encrypted_unit_price) {
                $encryptedPricesMap[$_price->id] = [
                    'id'     => $_price->id,
                    'cipher' => $_price->encrypted_unit_price,
                    'qty'    => $_price->quantity,
                    'app_id' => $_app->id,
                ];
            }
        }
    }
@endphp
// ── E2E Decryption ───────────────────────────────────────────────────────────────────
// Garage encrypted amounts: { application_id: encrypted_amount_ciphertext }
const _encryptedApps = @json($encryptedAppsMap);

// Shop encrypted part prices: { price_id: { id, cipher, qty, app_id } }
const _encryptedPrices = @json($encryptedPricesMap);

// ── Shared: apply all DOM decryption updates given a CryptoKey ───────────────
async function applyDecryption(privateKey) {
    const fmt = n => n.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}) + ' ETB';

    for (const [appId, cipher] of Object.entries(_encryptedApps)) {
        if (!cipher) continue;
        const amount = parseFloat(await E2EEncryption.decryptValue(cipher, privateKey));
        document.querySelectorAll(`.encrypted-price[data-app-id="${appId}"]`).forEach(el => {
            el.outerHTML = `<span>${fmt(amount)}</span>`;
        });
        // Update garage card dataset so openPrintPages reads the decrypted value, not 0
        const garageCard = document.querySelector(`.garage-card[data-application-id="${appId}"]`);
        if (garageCard) {
            garageCard.dataset.amount = amount;
            garageCard.dataset.amountIsEncrypted = '0';
        }
    }

    const appSubtotals = {};
    for (const [priceId, data] of Object.entries(_encryptedPrices)) {
        if (!data.cipher) continue;
        const unitPrice = parseFloat(await E2EEncryption.decryptValue(data.cipher, privateKey));
        const partTotal = unitPrice * (data.qty || 1);

        document.querySelectorAll(`.enc-unit-price[data-price-id="${priceId}"]`).forEach(el => {
            el.outerHTML = `<span>${fmt(unitPrice)}</span>`;
        });
        document.querySelectorAll(`.enc-part-total[data-price-id="${priceId}"]`).forEach(el => {
            el.outerHTML = `<span>${fmt(partTotal)}</span>`;
        });

        const appId = data.app_id;
        if (appId) appSubtotals[appId] = (appSubtotals[appId] || 0) + partTotal;
    }

    for (const [appId, subtotal] of Object.entries(appSubtotals)) {
        const discountCell = document.querySelector(`.shop-discount-val[data-app-id="${appId}"]`);
        const discountPct  = discountCell ? parseFloat(discountCell.dataset.discountPct) || 0 : 0;
        const discountAmt  = (subtotal * discountPct) / 100;
        const netTotal     = subtotal - discountAmt;

        const subtotalCell = document.querySelector(`.shop-subtotal-val[data-app-id="${appId}"]`);
        const netTotalCell = document.querySelector(`.shop-nettotal-val[data-app-id="${appId}"]`);
        if (subtotalCell) subtotalCell.textContent = fmt(subtotal);
        if (discountCell) discountCell.textContent = `${fmt(discountAmt)} (${discountPct}%)`;
        if (netTotalCell) netTotalCell.textContent = fmt(netTotal);
    }

    const panel = document.getElementById('decryptPanel');
    if (panel) panel.innerHTML =
        '<div class="alert alert-success mb-0"><i class="bx bx-check-circle me-2"></i>Prices decrypted successfully.</div>';
}

document.addEventListener('DOMContentLoaded', function() {
    const btnDecrypt = document.getElementById('btnDecrypt');
    const decryptPin = document.getElementById('decryptPin');
    const decryptErr = document.getElementById('decryptError');

    if (!btnDecrypt) return;

    btnDecrypt.addEventListener('click', async function() {
        const pin = decryptPin ? decryptPin.value.trim() : '';
        if (!pin) {
            decryptErr.textContent = 'Please enter your Encryption PIN.';
            decryptErr.classList.remove('d-none');
            return;
        }
        decryptErr.classList.add('d-none');
        btnDecrypt.disabled = true;
        btnDecrypt.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Decrypting…';

        try {
            const resp = await fetch('{{ route("insurance.encryption.private-key") }}');
            if (!resp.ok) throw new Error('Could not load private key from server.');
            const keyBlob = await resp.json();

            const privateKey = await E2EEncryption.decryptPrivateKey(
                keyBlob.encrypted_private_key,
                keyBlob.key_iv,
                keyBlob.key_salt,
                pin
            );

            await applyDecryption(privateKey);

        } catch (err) {
            decryptErr.textContent = 'Decryption failed — check your PIN and try again. (' + err.message + ')';
            decryptErr.classList.remove('d-none');
            btnDecrypt.disabled = false;
            btnDecrypt.innerHTML = '<i class="bx bx-lock-open me-1"></i> Decrypt Prices';
        }
    });
});

// ── Etera-Chereta dropdown functionality
document.addEventListener('DOMContentLoaded', function() {
    const proformaTypeSelect = document.getElementById('insuranceProformaType');
    const eteraCheretaDropdown = document.getElementById('insuranceEteraCheretaDropdown');

    function toggleEteraCheretaDropdown() {
        if (proformaTypeSelect && proformaTypeSelect.value === '-1') {
            eteraCheretaDropdown.style.display = 'block';
        } else if (eteraCheretaDropdown) {
            eteraCheretaDropdown.style.display = 'none';
        }
    }

    if (proformaTypeSelect) {
        proformaTypeSelect.addEventListener('change', toggleEteraCheretaDropdown);
        toggleEteraCheretaDropdown();
    }
});
</script>
