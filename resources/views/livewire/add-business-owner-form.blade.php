{{-- resources/views/livewire/add-business-owner-form.blade.php --}}
<div>
    <div class="card">
        <div class="card-body p-4">
            <h5 class="card-title">Add Business Owner</h5>
            <hr/>

               {{-- Flash message display --}}
            @if(session()->has('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            {{-- <form wire:submit.prevent="submit" class="row g-3"> --}}
                <form wire:submit.prevent="submit" class="row g-3" enctype="multipart/form-data">

                <div class="col-md-6">
                    <label for="name" class="form-label">Name</label>
                    <input wire:model.live="name" type="text" class="form-control" id="name" placeholder="Your Name">
                    @error('name')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="phone_number" class="form-label">Phone Number</label>
                    <input wire:model.live="phone_number" type="text" class="form-control" id="phone_number" placeholder="09...">
                    @error('phone_number')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="tin_number" class="form-label">Tin #</label>
                    <input wire:model.live="tin_number" type="text" class="form-control" id="tin_number" placeholder="Your Company Tin #">
                    @error('tin_number')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="location" class="form-label">Location / Address</label>
                    <input wire:model.live="location" type="text" class="form-control" id="location" placeholder="">
                    @error('location')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                {{-- <div class="col-md-6">
                    <label for="business_license_number" class="form-label">Business License Proc. Number</label>
                    <input wire:model.live="business_license_number" type="text" class="form-control" id="business_license_number" placeholder="Proclamation Number">
                    @error('business_license_number')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="license_expiry_date" class="form-label">Business License Expiry Date</label>
                    <input wire:model.live="license_expiry_date" type="date" class="form-control" id="license_expiry_date">
                    @error('license_expiry_date')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="licenseBusiness" class="form-label">Business License Image</label>
                    <input wire:model.live="license_image" class="form-control" type="file" accept="image/*,.jpg,.png,.jpeg">
                    @error('license_image')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div> --}}




{{-- 
                <div class="col-md-6">
                    <label for="licenseShop" class="form-label">Business License Image</label>
                    <input type="file" name="license_image"  accept="image/*" required> <!-- Standard file input -->
                    @error('license_image')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div> --}}









                
{{--                 
                <div class="col-md-6">
                    <label for="stampBusiness" class="form-label">Stamp Image</label>
                    <input wire:model.live="stamp_image " class="form-control" type="file" accept="image/*,.jpg,.png,.jpeg">
                    @error('stamp_image')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                 --}}
                
                <div class="col-md-4">
                    <label for="email" class="form-label">Email</label>
                    <input wire:model.live="email" type="email" class="form-control" id="email" placeholder="Your Email">
                    @error('email')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="password" class="form-label">Password</label>
                    <input wire:model.live="password" type="password" class="form-control" id="password" placeholder="********">
                    @error('password')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <input wire:model.live="password_confirmation" type="password" class="form-control" id="password_confirmation" placeholder="Confirm Password">
                    @error('password_confirmation')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="pt-3">
                    <hr/>
                    <button type="submit" class="btn btn-primary radius-30 px-4">Add</button>
                    &nbsp
                    <a href="/marketer/business-owners" class="btn btn-outline-secondary radius-30 px-3">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

