@extends('layouts.admin')

@section('content')
<!-- start page wrapper -->
<div class="page-wrapper">
    <div class="page-content">              
        <div class="card">
            <div class="card-body p-4">
                <h5 class="card-title">Add Spare Parts Shop</h5>
                <hr/>
                <form class="row g-3" action="{{ route('add-shop') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('POST')
                    
                    <!-- Name Field -->
                    <div class="col-md-6">
                        <label for="input1" class="form-label">Name</label>
                        <input name="name" type="text" class="form-control" id="input1" placeholder="Your Company" required>
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <!-- Phone Number Field -->
                    <div class="col-md-6">
                        <label for="input2" class="form-label">Phone Number</label>
                        <input name="phone_number" type="text" class="form-control" id="input2" placeholder="09..." required>
                        @error('phone_number')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <!-- Tin Number Field -->
                    <div class="col-md-6">
                        <label for="input3" class="form-label">Tin #</label>
                        <input name="tin_number" type="text" class="form-control" id="input3" placeholder="Your Company Tin #">
                        @error('tin_number')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <!-- Location Field -->
                    <div class="col-md-6">
                        <label for="input4" class="form-label">Location / Address</label>
                        <input name="location" type="text" class="form-control" id="input4" required>
                        @error('location')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <!-- ✅ BRANDS -->
                    <div class="col-md-6">
                        <label class="form-label">Car Brands To Serve</label>
                        <select name="brands[]" id="brands-select" class="form-select" multiple required>
                            <option value="all">Select All</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>
              
                    <!-- Business License Image -->
                    <div class="col-md-6">
                        <label for="license_image_fp" class="form-label">Business License Image</label>
                        <input type="file" name="license_image" id="license_image_fp" class="filepond-upload" accept="image/*" required>
                        <div id="license_image_fp_progress" style="display:none;" class="mt-2">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small class="upload-progress-text text-muted">Loading...</small>
                                <small class="upload-progress-pct text-muted fw-bold">0%</small>
                            </div>
                            <div class="progress" style="height:6px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated upload-progress-bar" role="progressbar" style="width:0%"></div>
                            </div>
                        </div>
                        @error('license_image')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Stamp Image -->
                    <div class="col-md-6">
                        <label for="stamp_image_fp" class="form-label">Stamp Image</label>
                        <input type="file" name="stamp_image" id="stamp_image_fp" class="filepond-upload" accept="image/*" required>
                        <div id="stamp_image_fp_progress" style="display:none;" class="mt-2">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small class="upload-progress-text text-muted">Loading...</small>
                                <small class="upload-progress-pct text-muted fw-bold">0%</small>
                            </div>
                            <div class="progress" style="height:6px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated upload-progress-bar" role="progressbar" style="width:0%"></div>
                            </div>
                        </div>
                        @error('stamp_image')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <!-- Email Field -->
                    <div class="col-md-4">
                        <label for="input8" class="form-label">Email</label>
                        <input name="email" type="email" class="form-control" id="input8" placeholder="Your Email">
                        @error('email')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Dealer Checkbox -->
                    <div class="col-md-6">
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="dealers" id="dealers" value="1">
                            <label class="form-check-label" for="dealers">
                                Is Dealer
                            </label>
                        </div>
                    </div>

                    <!-- Dual Service Checkbox -->
                    <div class="col-md-6">
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="shop_garage" id="shop_garage" value="1">
                            <label class="form-check-label" for="shop_garage">
                                Dual Service (Shop + Garage)
                            </label>
                        </div>
                    </div>

                    <hr/>                    
                    <div class="my-0">
                        <button type="submit" class="btn btn-primary radius-30 px-4"> Add </button>
                        &nbsp;
                        <a href="/admin/spare-part-shops" class="btn btn-outline-secondary radius-30 px-3"> Cancel </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- end page wrapper -->

<link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet" />
<link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet" />

<script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
<script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
<script src="https://unpkg.com/filepond/dist/filepond.js"></script>

<script>
$(document).ready(function () {

    const $select = $('#brands-select');

    $select.select2({
        placeholder: "Select car brands",
        closeOnSelect: false,
        width: '100%'
    });

    /**
     * ✅ HANDLE SELECT ALL RELIABLY
     */
    $select.on('change', function () {
        let values = $select.val() || [];

        // If "Select All" was chosen
        if (values.includes('all')) {

            // Remove "all" immediately
            values = values.filter(v => v !== 'all');

            const allBrandValues = $select.find('option')
                .not('[value="all"]')
                .map(function () {
                    return this.value;
                }).get();

            // 🔁 TOGGLE LOGIC
            if (values.length === allBrandValues.length) {
                // 🔴 All selected → CLEAR
                $select.val([]).trigger('change.select2');
            } else {
                // 🟢 Select ALL
                $select.val(allBrandValues).trigger('change.select2');
            }
        }
    });

    // Initialize FilePond with upload progress
    FilePond.registerPlugin(FilePondPluginImagePreview, FilePondPluginFileValidateType);
    document.querySelectorAll('.filepond-upload').forEach(el => {
        const pond = FilePond.create(el, {
            allowMultiple: false,
            acceptedFileTypes: ['image/*'],
            labelIdle: 'Drag & drop an image or <span class="filepond--label-action">Browse</span>',
            labelFileProcessing: 'Loading...',
            labelFileProcessingComplete: '✓ Ready',
            credits: false,
            storeAsFile: true,
            stylePanelLayout: 'compact',
            imagePreviewHeight: 150,
        });

        const progressWrap = document.getElementById(el.id + '_progress');
        const bar  = progressWrap && progressWrap.querySelector('.upload-progress-bar');
        const text = progressWrap && progressWrap.querySelector('.upload-progress-text');
        const pct  = progressWrap && progressWrap.querySelector('.upload-progress-pct');

        pond.on('addfilestart', () => {
            if (!progressWrap) return;
            progressWrap.style.display = 'block';
            bar.className = 'progress-bar progress-bar-striped progress-bar-animated upload-progress-bar';
            bar.style.width = '0%';
            text.textContent = 'Loading...';
            pct.textContent = '0%';
            let val = 0;
            el._uploadInterval = setInterval(() => {
                val = Math.min(val + 8, 85);
                bar.style.width = val + '%';
                pct.textContent = val + '%';
                if (val >= 85) clearInterval(el._uploadInterval);
            }, 40);
        });

        pond.on('addfile', (error) => {
            if (!progressWrap) return;
            clearInterval(el._uploadInterval);
            if (error) {
                bar.className = 'progress-bar upload-progress-bar bg-danger';
                bar.style.width = '100%';
                text.textContent = 'Failed to load';
                pct.textContent = '';
            } else {
                bar.className = 'progress-bar upload-progress-bar bg-success';
                bar.style.width = '100%';
                text.textContent = '✓ Image ready';
                pct.textContent = '100%';
            }
        });

        pond.on('removefile', () => {
            if (!progressWrap) return;
            clearInterval(el._uploadInterval);
            progressWrap.style.display = 'none';
            bar.style.width = '0%';
            bar.className = 'progress-bar progress-bar-striped progress-bar-animated upload-progress-bar';
            text.textContent = 'Loading...';
            pct.textContent = '0%';
        });
    });

});
</script>

@endsection
