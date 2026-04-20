
@extends('layouts.admin')
@section('content')
<!--start page wrapper -->
<div class="page-wrapper">
	<div class="page-content">
		<h3 class="">Spare Parts Shops List</h3>
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-body">
						<div class="row align-items-end mb-3 g-2">
							<div class="col-lg-3 col-md-4">
								<div class="position-relative">
									<input type="text" id="tableSearch" class="form-control ps-5 radius-30" placeholder="Search by name or phone...">
									<span class="position-absolute top-50 product-show translate-middle-y"><i class="bx bx-search"></i></span>
								</div>
							</div>
							<div class="col-lg-3 col-md-4">
								<select id="brandFilter" class="form-select radius-30">
									<option value="">All Brands</option>
									@foreach($brands as $brand)
										<option value="{{ $brand->id }}">{{ $brand->name }}</option>
									@endforeach
								</select>
							</div>
							<div class="col-auto ms-auto">
								<a href="/admin/add-spare-part-shop" type="button" class="btn btn-primary radius-30"><i class="bx bx-plus me-0"></i> Spare Part Shop</a>
							</div>
						</div>

						<div class="table-responsive lead-table">
							<table class="table mb-0 align-middle">
								<thead class="table-light">
									<tr>
										<th>
											<input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
										</th>
										<th>Name</th>
										<th>Phone</th>
										<th>Tin #</th>
										<th>Registered By</th>
										<th>Register Date</th>
										<th>License Expiry</th>
										<th>Actions</th>
									</tr>
								</thead>
								<tbody>
									@foreach($shops as $shop)
									@php
										// Example date for 'license_expire_date'
										$licenseExpireDate = \Carbon\Carbon::create($shop->license_expire_date);  // Change this to dynamically fetch the date from the DB
										$currentDate = \Carbon\Carbon::now();

										// Check if the date is expired or expiring soon
										$isExpired = $licenseExpireDate->lessThan($currentDate);  // Expired if less than current date
										$isExpiringSoon = !$isExpired && $licenseExpireDate->lessThanOrEqualTo($currentDate->copy()->addMonth());  // Less than 1 month away, but not expired

										$formattedDate = $licenseExpireDate->format('D M d,Y'); 
									@endphp
									

									 <!--<tr >-->
										<!--<td><input class="form-check-input" type="checkbox" value="" id="flexCheckDefault"></td>-->
										<!--<td>-->
										<!--	<div class="d-flex align-items-center" id="shopRow{{$shop->id}}"  data-bs-toggle="modal" data-bs-target="#shopDetailModal{{$shop->id}}">-->
										<!--		<div>-->
										<!--			<h6 class="mb-0 font-14">{{$shop->name}}</h6>-->
										<!--			<p class="mb-0 font-13 text-secondary">{{$shop->email}}</p>-->
										<!--		</div>-->
										<!--	</div>-->
										<!--</td>-->
										<!--<td>{{$shop->phone_number}}</td>-->
										<!--<td>{{$shop->tin_number}}</td>-->
										<!--<td>No one</td>-->
										<!--<td>{{$shop->created_at}}</td>-->
									
										<!--<td>-->
										<!--	@if($isExpired)-->
										<!--		<div class="badge rounded-pill bg-danger w-100">{{ $formattedDate }}</div>-->
										<!--	@elseif($isExpiringSoon)-->
										<!--		<div class="badge rounded-pill bg-warning w-100">{{ $formattedDate }}</div>-->
										<!--	@else-->
										<!--		<div class="badge rounded-pill bg-success w-100">{{ $formattedDate }}</div>-->
										<!--	@endif-->
										<!--</td>-->
									
										<!--<td>-->
											<!-- Delete button remains outside of the clickable row -->
											<!--<a href="{{ route('edit-shop', $shop->id) }}" class="btn radius-10 p-1">-->
											<!--	<i class="bx bx-edit me-0"></i>-->
											<!--</a>-->
											<!-- Keep Delete button separate so it remains clickable -->
											<!--<button type="button" class="btn radius-10 p-1 text-danger" data-bs-toggle="modal" data-bs-target="#singleDelete{{$shop->id}}">-->
											<!--	<i class="bx bx-trash me-0"></i>-->
											<!--</button>-->
									
						

<!-- Table Row with Clickable Modal -->
<tr data-brand-ids="{{ $shop->brands->pluck('id')->implode(',') }}">
    <td><input class="form-check-input" type="checkbox" value="" id="flexCheckDefault"></td>
    <td>
        <div class="d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#shopDetailModal{{$shop->id}}">
            <div>
                <h6 class="mb-0 font-14">{{$shop->name}}</h6>
                <p class="mb-0 font-13 text-secondary">{{$shop->email}}</p>
            </div>
        </div>
    </td>
    <td>{{$shop->phone_number}}</td>
    <td>{{$shop->tin_number}}</td>
    
    <td>No one</td>
    <td>{{$shop->created_at}}</td>
    <td>
        @if($isExpired)
            <div class="badge rounded-pill bg-danger w-100">{{ $formattedDate }}</div>
        @elseif($isExpiringSoon)
            <div class="badge rounded-pill bg-warning w-100">{{ $formattedDate }}</div>
        @else
            <div class="badge rounded-pill bg-success w-100">{{ $formattedDate }}</div>
        @endif
    </td>
    <td>
        <a href="{{ route('edit-shop', $shop->id) }}" class="btn radius-10 p-1" title="Edit">
            <i class="bx bx-edit me-0"></i>
        </a>
        <button type="button" class="btn radius-10 p-1 text-info" data-bs-toggle="modal" data-bs-target="#shopBrandsModal{{$shop->id}}" title="View Brands">
            <i class="bx bx-purchase-tag me-0"></i>
        </button>
        <button type="button" class="btn radius-10 p-1 text-danger" data-bs-toggle="modal" data-bs-target="#singleDelete{{$shop->id}}">
            <i class="bx bx-trash me-0"></i>
        </button>
    </td>
</tr>

<!-- Modal for Full Row Click -->
<div class="modal fade" id="shopDetailModal{{$shop->id}}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow-lg border-0 rounded-4" style="background: #f8f9fa;">
            <div class="modal-header" style="background: #7A2CB4; color: white; border-top-left-radius: 10px; border-top-right-radius: 10px;">
				<h5 class="modal-title fw-bold"  style="color: white;">Insurance Details</h5>
             
				


				
				<button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-5">
                <div class="mb-4">
                    <p class="font-weight-semibold"><strong>Name:</strong> {{$shop->name}}</p>
                </div>
                <div class="mb-4">
                    <p class="font-weight-semibold"><strong>Email:</strong> {{$shop->email}}</p>
                </div>
                <div class="mb-4">
                    <p class="font-weight-semibold"><strong>Phone:</strong> {{$shop->phone_number}}</p>
                </div>
                <div class="mb-4">
                    <p class="font-weight-semibold"><strong>TIN:</strong> {{$shop->tin_number}}</p>
                </div>
                <div class="mb-4">
                    <p class="font-weight-semibold"><strong>Created:</strong> {{$shop->created_at}}</p>
                </div>
                <div class="mb-4">
                    <p class="font-weight-semibold"><strong>License Expiry:</strong> {{ $formattedDate }}</p>
                </div>
                <div class="row">
                    <!-- Business License Image -->
                    @if($shop->license_image)
                    <div class="col-md-6 mb-3">
                        <p class="font-weight-semibold"><strong>Business License:</strong></p>
                        <img src="{{ asset('storage/' . $shop->license_image) }}" alt="Business License Image" class="img-fluid rounded" style="max-width: 100%; height: auto;">
                    </div>
                    @else
                    <div class="col-md-6 mb-3">
                        <p>No business license image available.</p>
                    </div>
                    @endif

                    <!-- Stamp Image -->
                    @if($shop->stamp_image)
                    <div class="col-md-6 mb-3">
                        <p class="font-weight-semibold"><strong>Stamp:</strong></p>
                        <img src="{{ asset('storage/' . $shop->stamp_image) }}" alt="Stamp Image" class="img-fluid rounded" style="max-width: 100%; height: auto;">
                    </div>
                    @else
                    <div class="col-md-6 mb-3">
                        <p>No stamp image available.</p>
                    </div>
                    @endif
                </div>
            </div>

            <div class="modal-footer border-0" style="background: #f1f1f1;">
                <button type="button" class="btn btn-outline-primary radius-30 px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Delete Confirmation -->
<div class="modal fade" id="singleDelete{{$shop->id}}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">Are you sure you want to delete this Spare Part Shop?</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary radius-30" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('delete-shop', $shop->id) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-danger radius-30">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Shop Brands -->
<div class="modal fade" id="shopBrandsModal{{$shop->id}}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4">
            <div class="modal-header" style="background: #17a2b8; color: white; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <h5 class="modal-title fw-bold" style="color: white;"><i class="bx bx-purchase-tag me-1"></i> Brands — {{ $shop->name }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                @if($shop->brands->count())
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($shop->brands as $brand)
                            <span class="badge bg-primary rounded-pill px-3 py-2" style="font-size: 0.9rem;">{{ $brand->name }}</span>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted mb-0">No brands assigned to this shop.</p>
                @endif
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary radius-30 px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

										</td>
									</tr>
									
									
									@endforeach
								</tbody>

								
							</table>



							
						</div>
					</div>
				</div>
			</div>
		</div>
		<!--end row-->
	</div>
</div>
</div>
</div>
<!--end page wrapper -->

<!-- Selected Delete Modal -->
<div class="modal fade" id="selectedDelete" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Delete Selected Spare Part Shops</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">Are you sure you want to delete the selected Spare Part Shops?</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-outline-secondary radius-30" data-bs-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-danger radius-30">Delete</button>
			</div>
		</div>
	</div>
</div>
<!-- End Selected Delete Modal -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('tableSearch');
    const brandFilter = document.getElementById('brandFilter');
    const table = document.querySelector('.lead-table table tbody');
    if (!searchInput || !brandFilter || !table) return;

    function filterRows() {
        const query = searchInput.value.toLowerCase().trim();
        const selectedBrand = brandFilter.value;
        const rows = table.querySelectorAll('tr');

        rows.forEach(function (row) {
            const name = (row.querySelector('td:nth-child(2)')?.textContent || '').toLowerCase();
            const phone = (row.querySelector('td:nth-child(3)')?.textContent || '').toLowerCase();
            const brandIds = (row.getAttribute('data-brand-ids') || '').split(',');

            const matchesText = !query || name.includes(query) || phone.includes(query);
            const matchesBrand = !selectedBrand || brandIds.includes(selectedBrand);

            row.style.display = (matchesText && matchesBrand) ? '' : 'none';
        });
    }

    searchInput.addEventListener('input', filterRows);
    brandFilter.addEventListener('change', filterRows);
});
</script>
@endsection
