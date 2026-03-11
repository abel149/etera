@extends('layouts.authentication')

@section('title', 'Garage & Spare Part Registration — etera')

@section('branding')
    <img src="{{ asset('assets/images/transparent.svg') }}" class="etera-auth-logo" alt="etera">
    <h2 class="etera-heading etera-heading-lg" style="text-align:center; margin-bottom: 0.5rem;">
        Register Your Business
    </h2>
    <p class="etera-subtext" style="text-align:center; max-width: 360px; color: rgba(255,255,255,0.85);">
        Join etera as a Garage or Spare Part Shop and connect with insurances and car owners.
    </p>

    @include('partials.brand-globe')
@endsection

@section('styles')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    {{-- FilePond CSS --}}
    <link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">

    <style>
        /* Modal Overlay */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.4); display: none; justify-content: center; align-items: center; z-index: 9999; padding: 20px; }
        .modal-overlay.show { display: flex !important; }
        .modal-content { background: #fff; color: #1a1a2e; width: 90%; max-width: 650px; max-height: 90vh; overflow-y: auto; border-radius: 16px; padding: 25px; position: relative; animation: fadeIn 0.25s ease-in-out; border: 1px solid #c8e6c9; box-shadow: 0 8px 32px rgba(40,167,69,0.12); }
        .modal-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e5e7eb; padding-bottom: 10px; }
        .modal-close { background: none; border: none; font-size: 28px; cursor: pointer; color: #6b7280; }
        .modal-close:hover { color: #1a1a2e; }
        @keyframes fadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }

        /* FilePond Modern Theme */
        .filepond--root { margin-bottom: 0; font-family: 'Inter', sans-serif; }
        .filepond--drop-label {
            min-height: 140px;
            border-radius: var(--etera-radius-sm, 10px);
            border: 2px dashed rgba(40,167,69,0.35);
            background: linear-gradient(135deg, #f9fafb 0%, #f1f8e9 100%);
            color: var(--etera-text-muted, #6b7280);
            transition: all 0.3s ease;
        }
        .filepond--drop-label:hover {
            border-color: #28a745;
            background: linear-gradient(135deg, #fff 0%, #e8f5e9 100%);
        }
        .filepond--drop-label label {
            padding: 1.25em;
            cursor: pointer;
            font-size: 0.9rem;
            color: var(--etera-text-muted, #6b7280);
        }
        .filepond--label-action {
            text-decoration: none !important;
            color: #28a745;
            font-weight: 600;
            background: rgba(40,167,69,0.08);
            padding: 4px 12px;
            border-radius: 20px;
            transition: all 0.2s ease;
        }
        .filepond--label-action:hover {
            background: rgba(40,167,69,0.16);
        }
        .filepond--file {
            border-radius: 10px;
        }
        .filepond--file-info {
            font-size: 0.8rem;
        }
        .filepond--panel-root {
            background: transparent;
            border-radius: var(--etera-radius-sm, 10px);
        }
        .filepond--item-panel {
            background: linear-gradient(135deg, #28a745, #20c997) !important;
            border-radius: 10px !important;
        }
        .filepond--image-preview-wrapper {
            border-radius: 8px !important;
            overflow: hidden;
        }
        .filepond--image-preview {
            background-color: #f1f8e9;
        }
        .filepond--file-action-button {
            cursor: pointer;
        }
        .filepond--drip-blob {
            background-color: rgba(40,167,69,0.15);
        }
        .upload-label {
            font-size: 0.8rem;
            color: var(--etera-text-muted, #9ca3af);
            margin-top: 4px;
        }

        /* Select2 — etera theme */
        .select2-container--default .select2-selection--multiple {
            background: #f9fafb;
            border: 1px solid #d1d5db;
            min-height: 48px;
            border-radius: var(--etera-radius-sm, 10px);
            padding: 6px 10px;
            transition: all 0.3s ease;
        }
        .select2-container--default .select2-selection--multiple:focus-within,
        .select2-container--default.select2-container--focus .select2-selection--multiple {
            border-color: #28a745;
            box-shadow: 0 0 0 3px rgba(40,167,69,0.15);
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background: rgba(40,167,69,0.1);
            border: 1px solid rgba(40,167,69,0.25);
            color: #1a1a2e;
            border-radius: 6px;
            padding: 2px 8px;
            font-size: 0.85rem;
        }
        .select2-dropdown {
            background: #fff;
            border-color: #d1d5db;
            border-radius: 10px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.08);
            margin-top: 4px;
        }
        .select2-results__option { color: #1a1a2e; padding: 8px 12px; }
        .select2-results__option--highlighted { background: rgba(40,167,69,0.1) !important; color: #1a1a2e !important; }
        .select2-search__field { background: #f9fafb !important; color: #1a1a2e !important; }

        /* Grid helpers */
        .form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        @media (max-width: 576px) { .form-grid-2 { grid-template-columns: 1fr; } }
    </style>
@endsection

@section('content')

<div style="animation: etera-fade-in 0.6s ease-out">
    <div style="text-align: center; margin-bottom: 2rem;">
        <img src="{{ asset('assets/images/transparent.svg') }}" alt="etera" style="max-width: 120px; margin-bottom: 1rem;" class="d-xl-none">
        <h2 class="etera-heading" style="font-size: 1.5rem; margin-bottom: 0.5rem;">Garage & Spare Part Registration</h2>
        <p class="etera-subtext">Fill the form below to create your <strong>Garage or Spare Part Shop</strong> account.</p>
    </div>

    <form id="garageSparePartRegisterForm" action="{{ route('register.garage-sparepart') }}" method="POST" novalidate>
        @csrf

        {{-- Name & Phone --}}
        <div class="form-grid-2">
            <div class="etera-input-group">
                <label>Garage/Shop Name <span style="color:#dc3545">*</span></label>
                <input type="text" class="etera-input {{ $errors->has('name') ? 'error' : '' }}" name="name" placeholder="Business name" value="{{ old('name') }}" required autofocus>
                @error('name')<div class="etera-error-text">{{ $message }}</div>@enderror
            </div>
            <div class="etera-input-group">
                <label>Phone Number <span style="color:#dc3545">*</span></label>
                <input type="tel" class="etera-input {{ $errors->has('phone_number') ? 'error' : '' }}" id="inputPhone" name="phone_number" placeholder="+251912345678" value="{{ old('phone_number', '+251') }}" required>
                @error('phone_number')<div class="etera-error-text">{{ $message }}</div>@enderror
            </div>
        </div>

        {{-- Email --}}
        <div class="etera-input-group">
            <label>Email Address <span style="color:var(--etera-text-muted); font-weight:400;">(optional)</span></label>
            <input type="email" class="etera-input {{ $errors->has('email') ? 'error' : '' }}" name="email" placeholder="business@example.com" value="{{ old('email') }}">
            @error('email')<div class="etera-error-text">{{ $message }}</div>@enderror
        </div>

        {{-- TIN & Location --}}
        <div class="form-grid-2">
            <div class="etera-input-group">
                <label>TIN Number <span style="color:#dc3545">*</span></label>
                <input type="text" class="etera-input {{ $errors->has('tin_number') ? 'error' : '' }}" name="tin_number" placeholder="TIN Number" value="{{ old('tin_number') }}" required>
                @error('tin_number')<div class="etera-error-text">{{ $message }}</div>@enderror
            </div>
            <div class="etera-input-group">
                <label>Location <span style="color:#dc3545">*</span></label>
                <input type="text" class="etera-input {{ $errors->has('location') ? 'error' : '' }}" name="location" placeholder="Business location" value="{{ old('location') }}" required>
                @error('location')<div class="etera-error-text">{{ $message }}</div>@enderror
            </div>
        </div>

        {{-- License Expire Date --}}
        <div class="etera-input-group">
            <label>License Expire Date <span style="color:var(--etera-text-muted); font-weight:400;">(optional)</span></label>
            <input type="date" class="etera-input {{ $errors->has('license_expire_date') ? 'error' : '' }}" name="license_expire_date" value="{{ old('license_expire_date') }}">
            @error('license_expire_date')<div class="etera-error-text">{{ $message }}</div>@enderror
        </div>

        {{-- Password Row --}}
        <div class="form-grid-2">
            <div class="etera-input-group">
                <label>Password (6 digits) <span style="color:#dc3545">*</span></label>
                <div class="etera-password-wrapper">
                    <input type="password" id="password" name="password" class="etera-input {{ $errors->has('password') ? 'error' : '' }}" maxlength="6" inputmode="numeric" placeholder="6-digit PIN" required>
                    <button type="button" class="etera-password-toggle toggle-password" data-target="#password" tabindex="-1"><i class='bx bx-hide'></i></button>
                </div>
                <div id="passwordError" class="etera-error-text" style="display:none;"></div>
                @error('password')<div class="etera-error-text">{{ $message }}</div>@enderror
            </div>
            <div class="etera-input-group">
                <label>Confirm Password <span style="color:#dc3545">*</span></label>
                <div class="etera-password-wrapper">
                    <input type="password" id="password_confirmation" name="password_confirmation" class="etera-input" maxlength="6" inputmode="numeric" placeholder="Confirm PIN" required>
                    <button type="button" class="etera-password-toggle toggle-password" data-target="#password_confirmation" tabindex="-1"><i class='bx bx-hide'></i></button>
                </div>
                <div id="confirmPasswordError" class="etera-error-text" style="display:none;"></div>
            </div>
        </div>

        {{-- Business Type --}}
        <div class="etera-input-group">
            <label>Business Type <span style="color:#dc3545">*</span></label>
            <select class="etera-input {{ $errors->has('role') ? 'error' : '' }}" id="roleSelect" name="role" required style="cursor:pointer;">
                <option value="">Select Business Type</option>
                <option value="garage" {{ old('role') == 'garage' ? 'selected' : '' }}>Garage</option>
                <option value="shop" {{ old('role') == 'shop' ? 'selected' : '' }}>Spare Part Shop</option>
            </select>
            @error('role')<div class="etera-error-text">{{ $message }}</div>@enderror
        </div>

        {{-- Role Specific Fields --}}
        <div id="roleSpecificFields" style="display: none;">

            {{-- Document Uploads --}}
            <div id="sharedImageFields" style="display: none;">
                <div style="margin-bottom: 0.75rem;">
                    <label style="font-size: 0.9rem; font-weight: 700; color: var(--etera-text);">📎 Upload Required Documents</label>
                </div>
                <div class="form-grid-2">
                    <div>
                        <label style="display:block; font-size:.85rem; font-weight:600; color:var(--etera-text-soft); margin-bottom:6px;">Business License <span style="color:#dc3545">*</span></label>
                        <input type="file" class="filepond-license" name="image" accept="image/png, image/jpeg, image/jpg">
                        <input type="hidden" id="license_image_data" name="license_image_data" @if(old('license_image_data')) value="{{ old('license_image_data') }}" @endif required>
                        <div class="upload-label">JPG, PNG (Max: 10MB)</div>
                        @error('license_image_data')<div class="etera-error-text">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label style="display:block; font-size:.85rem; font-weight:600; color:var(--etera-text-soft); margin-bottom:6px;">Stamp Image <span style="color:#dc3545">*</span></label>
                        <input type="file" class="filepond-stamp" name="image" accept="image/png, image/jpeg, image/jpg">
                        <input type="hidden" id="stamp_image_data" name="stamp_image_data" @if(old('stamp_image_data')) value="{{ old('stamp_image_data') }}" @endif required>
                        <div class="upload-label">JPG, PNG (Max: 10MB)</div>
                        @error('stamp_image_data')<div class="etera-error-text">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            {{-- Shop Specific: Brand Selection --}}
            <div id="shopFields" style="display: none; margin-top: 1rem;">
                <div class="etera-input-group">
                    <label>Car Brands To Serve</label>
                    <select name="brands[]" id="brands-select" multiple>
                        <option value="all">Select All</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                        @endforeach
                    </select>
                    @error('brands')<div class="etera-error-text">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        {{-- Terms --}}
        <div style="margin-top: 1rem; margin-bottom: 1.25rem;">
            <label class="etera-toggle">
                <input type="checkbox" id="terms-check" name="terms" value="1" {{ old('terms') ? 'checked' : '' }} required>
                <span>I agree to the <a href="javascript:void(0);" id="openTermsModal" class="etera-link">Terms & Conditions</a></span>
            </label>
            @error('terms')<div class="etera-error-text" style="margin-top:4px;">{{ $message }}</div>@enderror
        </div>

        <button type="submit" id="submitBtn"
                class="etera-btn etera-btn-primary etera-btn-block etera-btn-lg">
            <span id="submitText">Sign Up</span>
        </button>
    </form>

    <div style="text-align: center; margin-top: 1.5rem;">
        <p class="etera-subtext" style="font-size: 0.9rem;">
            Already have an account? <a href="{{ route('login') }}" class="etera-link">Login here</a>
        </p>
    </div>
</div>

{{-- TERMS MODAL --}}
<div id="termsModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h4>Terms and Conditions</h4>
            <button type="button" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <p style="color:#6b7280;">Last Updated: October 2025</p>
            <ol>
                <li><strong>Acceptance of Terms.</strong> By using etera, you confirm that you have read, understood, and agreed to these Terms and Conditions.</li>
                <li><strong>Eligibility.</strong> You must be at least 18 years old or have legal parental/guardian consent to use etera.</li>
                <li><strong>Account Registration.</strong> You agree to provide accurate information and to keep your login credentials secure.</li>
                <li><strong>Use of the Platform.</strong> You agree to use etera only for lawful purposes and in accordance with these Terms.</li>
                <li><strong>Platform Role.</strong> etera acts solely as a facilitator. We do not manufacture, stock, or sell any spare parts directly.</li>
                <li><strong>Intellectual Property.</strong> All content on etera is the property of etera or its licensors.</li>
                <li><strong>Privacy.</strong> Your use of etera is governed by our Privacy Policy.</li>
                <li><strong>Changes to Terms.</strong> etera may update these Terms at any time. Continued use means you accept the updated Terms.</li>
                <li><strong>Contact Us.</strong> For questions or concerns, please contact us at the provided contact information.</li>
            </ol>
        </div>
        <div style="padding: 12px 25px; border-top: 1px solid #e5e7eb; text-align: right;">
            <button type="button" class="etera-btn etera-btn-primary" id="acceptTerms" style="padding: 10px 24px; font-size: 0.9rem;">I Accept</button>
        </div>
    </div>
</div>

{{-- FilePond JS --}}
<script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
<script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
<script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
<script src="https://unpkg.com/filepond@^4/dist/filepond.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {

    // Register FilePond Plugins
    FilePond.registerPlugin(
        FilePondPluginImagePreview,
        FilePondPluginFileValidateType,
        FilePondPluginFileValidateSize
    );

    const serverConfig = {
        process: {
            url: '{{ route("upload.part.image") }}',
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            onload: (response) => {
                const data = JSON.parse(response);
                if (data.success && data.files && data.files.length > 0) return data.files[0].temp_path;
                return 'Upload failed.';
            },
            onerror: (response) => {
                try { return JSON.parse(response).message || 'Upload error.'; }
                catch(e) { return 'Upload error.'; }
            },
        },
        revert: {
            url: '{{ route("upload.part.image.revert") }}',
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            onload: (response) => { const d = JSON.parse(response); if(d.success) return; return 'Revert failed.'; }
        },
        credits: false,
    };

    const pondOptions = {
        acceptedFileTypes: ['image/png', 'image/jpeg', 'image/jpg'],
        maxFileSize: '10MB',
        maxFiles: 1,
        server: serverConfig,
        allowRevert: true,
        imagePreviewHeight: 140,
        stylePanelLayout: 'compact',
        stylePanelAspectRatio: '3:2',
        labelIdle: '📷 Drag & Drop or <span class="filepond--label-action">Browse</span>',
        labelFileProcessing: 'Uploading...',
        labelFileProcessingComplete: '✓ Upload complete',
        labelFileProcessingAborted: 'Upload cancelled',
        labelTapToCancel: 'tap to cancel',
        labelTapToRetry: 'tap to retry',
        name: 'image',
    };

    // License FilePond
    const licensePond = FilePond.create(document.querySelector('.filepond-license'), pondOptions);
    licensePond.on('processfile', (error, file) => { if (!error) document.getElementById('license_image_data').value = file.serverId; });
    licensePond.on('removefile', () => { document.getElementById('license_image_data').value = ''; });

    // Stamp FilePond
    const stampPond = FilePond.create(document.querySelector('.filepond-stamp'), pondOptions);
    stampPond.on('processfile', (error, file) => { if (!error) document.getElementById('stamp_image_data').value = file.serverId; });
    stampPond.on('removefile', () => { document.getElementById('stamp_image_data').value = ''; });

    // Restore from old values
    @if(old('license_image_data'))
        try { licensePond.addFile('{{ asset("storage") }}/{{ old("license_image_data") }}', { metadata: { serverId: '{{ old("license_image_data") }}' } }); } catch(e) {}
    @endif
    @if(old('stamp_image_data'))
        try { stampPond.addFile('{{ asset("storage") }}/{{ old("stamp_image_data") }}', { metadata: { serverId: '{{ old("stamp_image_data") }}' } }); } catch(e) {}
    @endif

    // Role toggle
    const roleSelect = document.getElementById('roleSelect');
    const roleSpecificFields = document.getElementById('roleSpecificFields');
    const shopFields = document.getElementById('shopFields');
    const sharedImageFields = document.getElementById('sharedImageFields');

    function toggleRoleFields() {
        const role = roleSelect.value;
        roleSpecificFields.style.display = role ? 'block' : 'none';
        sharedImageFields.style.display = role ? 'block' : 'none';
        shopFields.style.display = role === 'shop' ? 'block' : 'none';
    }
    roleSelect.addEventListener('change', toggleRoleFields);
    if (roleSelect.value) toggleRoleFields();

    // Validation helpers
    function showErr(el, msg) {
        el.classList.add('error');
        let e = el.parentElement.querySelector('.js-ve');
        if (!e) { e = document.createElement('div'); e.className = 'etera-error-text js-ve'; (el.closest('.etera-password-wrapper') || el).parentElement.appendChild(e); }
        e.textContent = msg; e.style.display = 'block';
    }
    function clearErr(el) {
        el.classList.remove('error');
        const e = el.parentElement.querySelector('.js-ve') || (el.closest('.etera-password-wrapper') && el.closest('.etera-password-wrapper').parentElement.querySelector('.js-ve'));
        if (e) e.style.display = 'none';
    }

    // Blur validation
    document.querySelector('input[name="name"]').addEventListener('blur', function(){ !this.value.trim() ? showErr(this, 'Name is required.') : clearErr(this); });
    document.querySelector('input[name="email"]').addEventListener('blur', function(){
        const v = this.value.trim();
        if (v && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)) showErr(this, 'Enter a valid email.');
        else clearErr(this);
    });
    document.querySelector('input[name="tin_number"]').addEventListener('blur', function(){ !this.value.trim() ? showErr(this, 'TIN is required.') : clearErr(this); });
    document.querySelector('input[name="location"]').addEventListener('blur', function(){ !this.value.trim() ? showErr(this, 'Location is required.') : clearErr(this); });
    document.getElementById('password').addEventListener('blur', function(){
        const v = this.value; const $e = document.getElementById('passwordError');
        $e.style.display = 'none'; this.classList.remove('error');
        if (!v) return; if (!/^\d{6}$/.test(v)) { $e.textContent = 'Must be 6 digits.'; $e.style.display = 'block'; this.classList.add('error'); }
    });
    document.getElementById('password_confirmation').addEventListener('blur', function(){
        const p = document.getElementById('password').value, c = this.value;
        const $e = document.getElementById('confirmPasswordError');
        $e.style.display = 'none'; this.classList.remove('error');
        if (!c) return; if (c !== p) { $e.textContent = 'Passwords do not match.'; $e.style.display = 'block'; this.classList.add('error'); }
    });

    // Digits only for password
    document.querySelectorAll('#password, #password_confirmation').forEach(el => {
        el.addEventListener('input', function(){ this.value = this.value.replace(/\D/g, '').slice(0,6); });
    });

    // Phone formatting
    document.getElementById('inputPhone').addEventListener('input', function() {
        let v = this.value.replace(/\D/g, '');
        if (!v.startsWith('251')) v = '251' + v.replace(/^251/, '');
        this.value = '+' + v.substring(0, 12);
    });

    // Form submit validation
    document.getElementById('garageSparePartRegisterForm').addEventListener('submit', function(e) {
        let hasError = false;
        const nameEl = document.querySelector('input[name="name"]');
        if (!nameEl.value.trim()) { showErr(nameEl, 'Name is required.'); hasError = true; } else clearErr(nameEl);

        const phoneEl = document.getElementById('inputPhone');
        const pv = phoneEl.value.replace(/\D/g, '');
        if (!pv || pv.length < 12) { showErr(phoneEl, 'Phone must be +251 + 9 digits.'); hasError = true; }
        else if (!/^251[97]\d{8}$/.test(pv)) { showErr(phoneEl, 'Must start with +2519 or +2517.'); hasError = true; }
        else clearErr(phoneEl);

        // Email — optional, only validate format
        const emailEl = document.querySelector('input[name="email"]');
        const ev = emailEl.value.trim();
        if (ev && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(ev)) { showErr(emailEl, 'Enter a valid email.'); hasError = true; }
        else clearErr(emailEl);

        const tinEl = document.querySelector('input[name="tin_number"]');
        if (!tinEl.value.trim()) { showErr(tinEl, 'TIN is required.'); hasError = true; } else clearErr(tinEl);

        const locEl = document.querySelector('input[name="location"]');
        if (!locEl.value.trim()) { showErr(locEl, 'Location is required.'); hasError = true; } else clearErr(locEl);

        const pwEl = document.getElementById('password');
        if (!pwEl.value) { document.getElementById('passwordError').textContent = 'Password is required.'; document.getElementById('passwordError').style.display = 'block'; pwEl.classList.add('error'); hasError = true; }
        else if (!/^\d{6}$/.test(pwEl.value)) { document.getElementById('passwordError').textContent = 'Must be 6 digits.'; document.getElementById('passwordError').style.display = 'block'; pwEl.classList.add('error'); hasError = true; }

        const cpEl = document.getElementById('password_confirmation');
        if (!cpEl.value) { document.getElementById('confirmPasswordError').textContent = 'Confirm password.'; document.getElementById('confirmPasswordError').style.display = 'block'; cpEl.classList.add('error'); hasError = true; }
        else if (cpEl.value !== pwEl.value) { document.getElementById('confirmPasswordError').textContent = 'Does not match.'; document.getElementById('confirmPasswordError').style.display = 'block'; cpEl.classList.add('error'); hasError = true; }

        if (!roleSelect.value) { showErr(roleSelect, 'Select a business type.'); hasError = true; } else clearErr(roleSelect);

        // Validate images when role selected
        if (roleSelect.value) {
            const isLicProcessing = licensePond.getFiles().some(f => f.status !== 5);
            const isStProcessing = stampPond.getFiles().some(f => f.status !== 5);
            if (isLicProcessing || isStProcessing) { e.preventDefault(); alert('Please wait for uploads to complete.'); return false; }
            if (!document.getElementById('license_image_data').value) {
                let el = document.querySelector('.filepond-license')?.closest('div')?.querySelector('.js-ve');
                if (!el) { el = document.createElement('div'); el.className = 'etera-error-text js-ve'; document.querySelector('.filepond-license')?.closest('div')?.appendChild(el); }
                el.textContent = 'License image is required.'; el.style.display = 'block'; hasError = true;
            }
            if (!document.getElementById('stamp_image_data').value) {
                let el = document.querySelector('.filepond-stamp')?.closest('div')?.querySelector('.js-ve');
                if (!el) { el = document.createElement('div'); el.className = 'etera-error-text js-ve'; document.querySelector('.filepond-stamp')?.closest('div')?.appendChild(el); }
                el.textContent = 'Stamp image is required.'; el.style.display = 'block'; hasError = true;
            }
        }

        const termsCheck = document.getElementById('terms-check');
        if (!termsCheck.checked) {
            let te = termsCheck.closest('div').querySelector('.js-terms-err');
            if (!te) { te = document.createElement('div'); te.className = 'etera-error-text js-terms-err'; termsCheck.closest('div').appendChild(te); }
            te.textContent = 'You must accept the Terms & Conditions.'; te.style.display = 'block'; hasError = true;
        } else { const te = termsCheck.closest('div').querySelector('.js-terms-err'); if (te) te.style.display = 'none'; }

        if (hasError) {
            e.preventDefault();
            const first = document.querySelector('.error, .js-ve[style*="block"], .js-terms-err[style*="block"]');
            if (first) { first.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
            return false;
        }

        document.getElementById('submitBtn').disabled = true;
        document.getElementById('submitText').textContent = 'Processing...';
    });

    // Toggle password visibility
    document.querySelectorAll('.toggle-password').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const input = document.querySelector(this.dataset.target);
            const icon = this.querySelector('i');
            if (input.type === 'password') { input.type = 'text'; icon.classList.replace('bx-hide', 'bx-show'); }
            else { input.type = 'password'; icon.classList.replace('bx-show', 'bx-hide'); }
        });
    });

    // Modal
    document.getElementById('openTermsModal').addEventListener('click', function(){ document.getElementById('termsModal').classList.add('show'); });
    document.querySelector('.modal-close').addEventListener('click', function(){ document.getElementById('termsModal').classList.remove('show'); });
    document.getElementById('acceptTerms').addEventListener('click', function(){ document.getElementById('termsModal').classList.remove('show'); document.getElementById('terms-check').checked = true; });
    document.getElementById('termsModal').addEventListener('click', function(e){ if (e.target === this) this.classList.remove('show'); });
});
</script>

{{-- Select2 for Brands --}}
<script>
$(document).ready(function () {
    const $select = $('#brands-select');
    $select.select2({ placeholder: "Select car brands", closeOnSelect: false, width: '100%' });
    $select.on('change', function () {
        let values = $select.val() || [];
        if (values.includes('all')) {
            values = values.filter(v => v !== 'all');
            const allVals = $select.find('option').not('[value="all"]').map(function(){ return this.value; }).get();
            if (values.length === allVals.length) $select.val([]).trigger('change.select2');
            else $select.val(allVals).trigger('change.select2');
        }
    });
});
</script>

@endsection
