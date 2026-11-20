@extends('layouts.marketer')
@section('content')
<!--start page wrapper -->
<div class="page-wrapper">
	<div class="page-content">
		@php
			$myUsers = auth()->user()->myRegistrations;
			$myTotalUsers = $myUsers ? $myUsers->count() : 0;
			$myTotalInsurances = $myUsers ? $myUsers->where('role','insurance')->count() : 0;
			$myTotalShops = $myUsers ? $myUsers->where('role','shop')->count() : 0;
			$myTotalGarages = $myUsers ? $myUsers->where('role','garage')->count() : 0;
		@endphp

		<div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-3">
			<div class="col">
				<div class="card radius-12 border-start border-3 border-primary">
					<div class="card-body">
						<div class="d-flex align-items-center">
							<div>
								<p class="mb-1 text-secondary">Total Users Registered</p>
								<h3 class="mb-0 fw-bold">{{$myTotalUsers}}</h3>
							</div>
							<div class="ms-auto">
								<div class="widgets-icons rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width:54px;height:54px;font-size:1.5rem;">
									<i class='bx bx-group'></i>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col">
				<div class="card radius-12 border-start border-3 border-success">
					<div class="card-body">
						<div class="d-flex align-items-center">
							<div>
								<p class="mb-1 text-secondary">Insurances</p>
								<h3 class="mb-0 fw-bold">{{$myTotalInsurances}}</h3>
							</div>
							<div class="ms-auto">
								<div class="widgets-icons rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width:54px;height:54px;font-size:1.5rem;">
									<i class='bx bx-shield-quarter'></i>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col">
				<div class="card radius-12 border-start border-3 border-warning">
					<div class="card-body">
						<div class="d-flex align-items-center">
							<div>
								<p class="mb-1 text-secondary">Garages</p>
								<h3 class="mb-0 fw-bold">{{$myTotalGarages}}</h3>
							</div>
							<div class="ms-auto">
								<div class="widgets-icons rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center" style="width:54px;height:54px;font-size:1.5rem;">
									<i class='bx bx-wrench'></i>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col">
				<div class="card radius-12 border-start border-3 border-info">
					<div class="card-body">
						<div class="d-flex align-items-center">
							<div>
								<p class="mb-1 text-secondary">Shops</p>
								<h3 class="mb-0 fw-bold">{{$myTotalShops}}</h3>
							</div>
							<div class="ms-auto">
								<div class="widgets-icons rounded-circle bg-info bg-opacity-10 text-info d-flex align-items-center justify-content-center" style="width:54px;height:54px;font-size:1.5rem;">
									<i class='bx bx-store'></i>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="card radius-12 mt-4">
			<div class="card-body">
				<div class="d-flex align-items-center mb-3">
					<h5 class="mb-0">Recently Registered Users</h5>
				</div>
				<div class="table-responsive lead-table">
					<table class="table mb-0 align-middle table-hover">
						<thead class="table-light">
							<tr>
								<th>#</th>
								<th>Name</th>
								<th>Role</th>
								<th>Phone Number</th>
								<th>Registered</th>
							</tr>
						</thead>
						<tbody>
						@if($myUsers && $myUsers->count() > 0)
							@foreach($myUsers as $index => $user)
							<tr>
								<td>{{ $index + 1 }}</td>
								<td>
									<h6 class="mb-0 font-14">{{$user->name}}</h6>
								</td>
								<td>
									<span class="badge bg-light text-dark border">{{ucfirst($user->role)}}</span>
								</td>
								<td>{{$user->phone_number}}</td>
								<td>
									<span class="text-muted">{{$user->created_at->format('Y-m-d')}}</span>
									<small class="d-block text-secondary">{{$user->created_at->diffForHumans()}}</small>
								</td>
							</tr>
							@endforeach
						@else
							<tr>
								<td colspan="5" class="text-center text-muted py-4">
									<i class="bx bx-info-circle me-2 fs-5"></i>
									No users registered yet. Start by registering your first user!
								</td>
							</tr>
						@endif
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<!--end page wrapper -->

@endsection
