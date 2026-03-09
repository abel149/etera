@extends('layouts.authentication')

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
    <style>
        /* Modal Overlay */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.4); display: none; justify-content: center; align-items: center; z-index: 9999; padding: 20px; }
        .modal-overlay.show { display: flex !important; }
        .modal-content { background: #fff; color: #1a1a2e; width: 90%; max-width: 650px; max-height: 90vh; overflow-y: auto; border-radius: 16px; padding: 25px; position: relative; animation: fadeIn 0.25s ease-in-out; border: 1px solid #c8e6c9; box-shadow: 0 8px 32px rgba(40,167,69,0.12); }
        .modal-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e5e7eb; padding-bottom: 10px; }
        .modal-close { background: none; border: none; font-size: 28px; cursor: pointer; color: #6b7280; }
        .modal-close:hover { color: #1a1a2e; }
        @keyframes fadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
        /* FilePond — white/green theme */
        .filepond--root { margin-bottom: 0; font-family: inherit; }
        .filepond--drop-label { min-height: 120px; border-radius: 10px; border: 2px dashed #c8e6c9; background: #f9fafb; color: #6b7280; }
        .filepond--drop-label label { padding: 1em; cursor: pointer; }
        .filepond--label-action { text-decoration-color: #28a745; color: #28a745; font-weight: 500; }
        .filepond--file { background-color: #28a745; }
        .filepond--panel-root { background-color: #f9fafb; border-radius: 10px; }
        .filepond--item { width: calc(50% - 0.5em); }
        .filepond--image-preview { background-color: #f1f8e9; }
        .file-upload-instructions { font-size: 0.875rem; color: #9ca3af; margin-top: 0.25rem; }
        .upload-error { font-size: 0.875rem; margin-top: 0.25rem; display: none; }
        .filepond--processing-indicator { display: flex !important; }
        /* Form styling — white/green theme */
        .form-header h3 { color: #1a1a2e; font-weight: 700; }
        .form-header p { color: #6b7280; }
        .form-control, .form-select { background: #f9fafb; border: 1px solid #d1d5db; color: #1a1a2e; border-radius: 10px; padding: 12px 14px; font-size: 0.95rem; transition: all 0.3s ease; }
        .form-control:focus, .form-select:focus { border-color: #28a745; box-shadow: 0 0 0 3px rgba(40,167,69,0.15); background: #fff; color: #1a1a2e; }
        .form-control::placeholder { color: #9ca3af; }
        .form-label { color: #374151; font-weight: 600; font-size: 0.85rem; }
        .btn-primary { background: linear-gradient(135deg, #28a745, #20c997); border: none; border-radius: 50px; padding: 12px 24px; font-weight: 600; color: #fff; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(40,167,69,0.35); background: linear-gradient(135deg, #1e7e34, #17a2b8); }
        .text-info, .text-info:hover { color: #28a745 !important; }
        .text-danger { color: #dc3545 !important; }
        .text-muted { color: #6b7280 !important; }
        .input-group-text { background: #f9fafb; border: 1px solid #d1d5db; color: #6b7280; }
        .form-check-input:checked { background-color: #28a745; border-color: #28a745; }
        .invalid-feedback { color: #dc3545; }
        .form-text { color: #9ca3af; }
        .logo-text { filter: none; }
        .fw-bold { color: #1a1a2e; }
        /* Select2 — white/green theme */
        .select2-container--default .select2-selection--multiple { background: #f9fafb; border: 1px solid #d1d5db; color: #1a1a2e; min-height: 42px; border-radius: 10px; }
        .select2-container--default .select2-selection--multiple .select2-selection__choice { background: rgba(40,167,69,0.12); border: 1px solid rgba(40,167,69,0.3); color: #1a1a2e; border-radius: 6px; }
        .select2-dropdown { background: #fff; border-color: #d1d5db; border-radius: 10px; box-shadow: 0 4px 16px rgba(0,0,0,0.08); }
        .select2-results__option { color: #1a1a2e; }
        .select2-results__option--highlighted { background: rgba(40,167,69,0.12); color: #1a1a2e; }
        .select2-search__field { background: #f9fafb !important; color: #1a1a2e !important; }
        .spinner-border { color: #28a745; }
    </style>
@endsection

@section('content')


    <div class="form-body">
        <div class="form-header">
            <img src="{{ asset('assets/images/transparent.svg') }}" class="logo-text mb-4" style="max-width: 7.5rem;" alt="etera">
            <h3>Garage & Spare Part Registration</h3>
            <p class="mb-0">Please fill the below details to create your <strong>Garage or Spare Part Shop</strong> account.</p>
        </div>

        <div class="form-body">
            <form id="garageSparePartRegisterForm" class="row g-3" action="{{ route('register.garage-sparepart') }}" method="POST">
                @csrf

                <!-- Business Name -->
                <div class="col-md-6 col-12">
                    <label class="form-label">Garage/Shop Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autofocus>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Phone Number -->
                <div class="col-md-6 col-12">
                    <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                    <input type="tel" class="form-control @error('phone_number') is-invalid @enderror" id="inputPhone" name="phone_number" required value="{{ old('phone_number', '+251') }}">
                    @error('phone_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                
                <!-- Email -->
                <div class="col-12">
                    <label class="form-label">Email Address <span class="text-danger">*</span></label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required>
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- TIN Number (New Field) -->
                <div class="col-md-6 col-12">
                    <label class="form-label">TIN Number <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('tin_number') is-invalid @enderror" name="tin_number" required value="{{ old('tin_number') }}">
                    @error('tin_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Location -->
                <div class="col-md-6 col-12">
                    <label class="form-label">Location <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('location') is-invalid @enderror" name="location" required value="{{ old('location') }}">
                    @error('location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- License Expire Date (New Optional Field) -->
                <div class="col-md-6 col-12">
                    <label class="form-label">License Expire Date</label>
                    <input type="date" class="form-control @error('license_expire_date') is-invalid @enderror" name="license_expire_date" value="{{ old('license_expire_date') }}">
                    @error('license_expire_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Password -->
                <div class="col-md-6 col-12">
                    <label class="form-label">Password (6 digits) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" id="password" name="password" class="form-control border-end-0 @error('password') is-invalid @enderror" maxlength="6" inputmode="numeric" required>
                        <button type="button" class="input-group-text bg-transparent toggle-password" data-target="#password"><i class='bx bx-hide'></i></button>
                    </div>
                    @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <!-- Confirm Password -->
                <div class="col-md-6 col-12">
                    <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control border-end-0" maxlength="6" inputmode="numeric" required>
                        <button type="button" class="input-group-text bg-transparent toggle-password" data-target="#password_confirmation"><i class='bx bx-hide'></i></button>
                    </div>
                </div>
                
                
     

<div class="col-md-12" style="display: none">
    <label class="form-label">
        Please enter your business bank account number if you would like to receive payments for filling out insurance proformas. (Optional) 
    </label>

    <!-- Wrap the two fields in a row -->
    <div class="row g-3 mt-2">
        <!-- Bank Name -->
        <div class="col-md-6">
    <label class="form-label">Bank / Payment Provider</label>
    <select name="bank_name" id="bankSelect" class="form-select @error('bank_name') is-invalid @enderror">
        <option value="">Select Your Bank or Telebirr</option>

<!-- State-Owned Banks -->
<option value="Commercial Bank of Ethiopia">Commercial Bank of Ethiopia (CBE)</option>
<option value="Development Bank of Ethiopia">Development Bank of Ethiopia (DBE)</option>

<!-- Private Banks -->
<option value="Awash Bank">Awash Bank</option>
<option value="Dashen Bank">Dashen Bank</option>
<option value="Bank of Abyssinia">Bank of Abyssinia</option>
<option value="Wegagen Bank">Wegagen Bank</option>
<option value="Nib International Bank">Nib International Bank</option>
<option value="Cooperative Bank of Oromia">Cooperative Bank of Oromia</option>
<option value="Hibret Bank">Hibret Bank</option>
<option value="Bunna Bank">Bunna Bank</option>
<option value="Berhan Bank">Berhan Bank</option>
<option value="Enat Bank">Enat Bank</option>
<option value="Lion Bank">Lion Bank</option>
<option value="Zemen Bank">Zemen Bank</option>
<option value="Addis International Bank">Addis International Bank</option>
<option value="Abay Bank">Abay Bank</option>
<option value="Oromia International Bank">Oromia International Bank</option>
<option value="Construction and Business Bank">Construction and Business Bank (CBB)</option>
<option value="Amhara Bank">Amhara Bank</option>
<option value="Tsehay Bank">Tsehay Bank</option>
<option value="Ahadu Bank">Ahadu Bank</option>
<option value="Tsedey Bank">Tsedey Bank</option>
<option value="Siinqee Bank">Siinqee Bank</option>
<option value="Gadaa Bank">Gadaa Bank</option>
<option value="Sidama Bank">Sidama Bank</option>
<option value="Shabelle Bank">Shabelle Bank</option>
<option value="ZamZam Bank">ZamZam Bank</option>
<option value="Hijra Bank">Hijra Bank</option>
<option value="Debub Global Bank">Debub Global Bank</option>
<option value="Global Bank Ethiopia">Global Bank Ethiopia</option>

<!-- Mobile Wallet -->
<option value="Telebirr">Telebirr (Mobile Wallet)</option>

    </select>

    @error('bank_name')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>

        <!-- Account Number -->
        <div class="col-md-6">
            <label class="form-label">Account Number</label>
            <input type="text" 
                   name="account_number" 
                   value="{{ old('account_number') }}" 
                   class="form-control @error('account_number') is-invalid @enderror" 
                   placeholder="Enter Account Number" 
                   >
            @error('account_number')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

<!-- Include Select2 JS if not already included -->
<script>
    $(document).ready(function() {
        $('#bankSelect').select2({
            placeholder: "Search and select your bank or Telebirr",
            allowClear: true
        });
    });
</script>


   


                <!-- Business Type -->
                <div class="col-md-12">
                    <label class="form-label">Business Type <span class="text-danger">*</span></label>
                    <select required class="form-select @error('role') is-invalid @enderror" id="roleSelect" name="role">
                        <option value="">Select Business Type</option>
                        <option value="garage" {{ old('role') == 'garage' ? 'selected' : '' }}>Garage</option>
                        <option value="shop" {{ old('role') == 'shop' ? 'selected' : '' }}>Spare Part Shop</option>
                    </select>
                </div>

                <!-- Role Specific Fields -->
                <div id="roleSpecificFields" style="display: none; transition: all 0.3s ease-in-out;">

                    <!-- Documents Upload -->
                    <div id="sharedImageFields" class="row g-3 mb-4" style="display: none;">
                        <div class="col-12">
                            <h6 class="fw-bold mb-3">Upload Required Documents</h6>
                        </div>

                        <!-- License Image -->
                        <div class="col-md-6">
                            <label class="form-label">Business License Image <span class="text-danger">*</span></label>
                            <!-- NOTE: Changed name back to 'image' for FilePond process payload; controller must use license_image_data -->
                            <input type="file" class="filepond-license" name="image" accept="image/png, image/jpeg, image/jpg">
                            <input type="hidden" id="license_image_data" name="license_image_data" @if(old('license_image_data')) value="{{ old('license_image_data') }}" @endif required>
                            <div class="form-text file-upload-instructions">JPG, PNG (Max: 2MB)</div>
                            @error('license_image_data') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        <!-- Stamp Image -->
                        <div class="col-md-6">
                            <label class="form-label">Stamp Image <span class="text-danger">*</span></label>
                            <!-- NOTE: Changed name back to 'image' for FilePond process payload; controller must use stamp_image_data -->
                            <input type="file" class="filepond-stamp" name="image" accept="image/png, image/jpeg, image/jpg">
                            <input type="hidden" id="stamp_image_data" name="stamp_image_data" @if(old('stamp_image_data')) value="{{ old('stamp_image_data') }}" @endif required>
                            <div class="form-text file-upload-instructions">JPG, PNG (Max: 2MB)</div>
                            @error('stamp_image_data') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <!-- Shop Specific -->
                    <div id="shopFields" class="role-fields" style="display: none;">
                         <div class="col-md-12 mt-2">
                        <label class="form-label">Car Brands To Serve</label>
                        <select name="brands[]" id="brands-select" class="form-select" multiple>
                            <option value="all">Select All</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>

                            @error('brands') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <!-- Terms -->
                <div class="col-12 mt-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="terms-check" name="terms" value="1" required {{ old('terms') ? 'checked' : '' }}>
                        <label class="form-check-label" for="terms-check">I agree to <a href="javascript:void(0);" id="openTermsModal" class="text-info">Terms & Conditions</a></label>
                    </div>
                </div>

                <!-- Submit -->
                <div class="col-12 mt-4">
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                            <span id="submitText">Sign up</span>
                            <span id="submitSpinner" class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                        </button>
                    </div>
                </div>

                 <!-- Login Link -->
                <div class="col-12">
                    <div class="text-center mt-3">
                        <p class="mb-2">Already have an account? <a href="{{ route('login') }}" class="text-info">Login here</a></p>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <!-- Modal HTML kept as is ... -->
    <div id="termsModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Terms and Conditions</h4>
                <button type="button" class="modal-close">&times;</button>
            </div>
            
        <div class="modal-body">
            <p class="text-muted">Last Updated: October 2025</p>

            <ol>
                    
                <li>
                    <strong>Acceptance of Terms.</strong>
                    By using etera, you confirm that you have read, understood, and agreed to these Terms and Conditions. 
                    If you do not agree, you must not use the platform.
                </li>

                <li>
                    <strong>Eligibility.</strong>
                    You must be at least 18 years old or have legal parental/guardian consent to use etera. 
                    You represent that you have the authority to enter into this agreement.
                </li>

                <li>
                    <strong>Account Registration.</strong>
                    To access certain features, you may be required to register an account. 
                    You agree to provide accurate information and to keep your login credentials secure.
                </li>

                <li>
                    <strong>Use of the Platform.</strong>
                    You agree to use etera only for lawful purposes and in accordance with these Terms. 
                    You must not misuse the platform or attempt unauthorized access.
                </li>

                <li>
                    <strong>Product and Service Descriptions.</strong>
                    etera strives to provide accurate descriptions of products and services. 
                    However, we do not warrant that descriptions or other content are error-free, complete, or current.
                </li>

                <li>
                    <strong>Platform Role and Provider Responsibility.</strong>
                    etera acts solely as a facilitator of instant price quotes provided by spare part providers registered on our platform. 
                    We do not manufacture, stock, or sell any spare parts directly.<br><br>
                    All products and services listed are offered by independent providers. 
                    etera is not responsible for the quality, condition, availability, or delivery of any parts sold, 
                    nor for any store’s return, refund, or warranty policies. 
                    Any disputes or claims regarding a product must be resolved directly with the provider.
                </li>

                <li>
                    <strong>Orders and Availability.</strong>
                    All orders are subject to acceptance and availability. 
                    We reserve the right to refuse or cancel any order at our discretion.
                </li>

                <li>
                    <strong>Intellectual Property.</strong>
                    All content on etera—including logos, text, graphics, and software—is the property of etera or its licensors 
                    and is protected by applicable intellectual property laws.
                </li>

                <li>
                    <strong>User Content.</strong>
                    You may submit content such as feedback and reviews. 
                    By doing so, you grant etera a non-exclusive, royalty-free license to use, reproduce, and display such content.
                </li>

                <li>
                    <strong>Prohibited Conduct.</strong>
                    You agree not to:
                    <ul>
                        <li>Violate any laws or regulations</li>
                        <li>Infringe intellectual property rights</li>
                        <li>Transmit harmful or malicious code</li>
                        <li>Use automated systems to access the platform</li>
                    </ul>
                </li>

                <li>
                    <strong>Third-Party Links.</strong>
                    etera may contain links to third-party websites. 
                    We are not responsible for the content, policies, or practices of those sites.
                </li>

                <li>
                    <strong>Limitation of Liability.</strong>
                    etera is not liable for any indirect, incidental, or consequential damages arising from your use of the platform. 
                    Our total liability is limited to the amount paid by you for the relevant product or service.
                </li>

                <li>
                    <strong>Pricing and Payments.</strong>
                    All prices on etera are subject to change without prior notice. 
                    The price at checkout is the final price. 
                    Payments must be made using the available payment methods.
                </li>

                <li>
                    <strong>Refunds and Cancellations.</strong>
                    etera does not sell spare parts directly and is not responsible for providers’ refund or cancellation policies. 
                    All refund or cancellation requests must be directed to the provider.
                </li>

                <li>
                    <strong>Privacy.</strong>
                    Your use of etera is governed by our Privacy Policy, which explains how your information is collected and used.
                </li>

                <li>
                    <strong>Changes to Terms.</strong>
                    etera may update these Terms at any time. 
                    Continued use of the platform following changes means you accept the updated Terms.
                </li>

                <li>
                    <strong>Governing Law.</strong>
                    These Terms are governed by the laws of your relevant jurisdiction.
                </li>

                <li>
                    <strong>Contact Us.</strong>
                    For questions or concerns, please contact us at the provided contact information.
                </li>
            
            </ol>
        </div>
        <div class="modal-body">
            <p class="text-muted">Last Updated: October 2025</p>

            <ol>
                    
                <li>
                    <strong>Acceptance of Terms.</strong>
                    By using etera, you confirm that you have read, understood, and agreed to these Terms and Conditions. 
                    If you do not agree, you must not use the platform.
                </li>

                <li>
                    <strong>Eligibility.</strong>
                    You must be at least 18 years old or have legal parental/guardian consent to use etera. 
                    You represent that you have the authority to enter into this agreement.
                </li>

                <li>
                    <strong>Account Registration.</strong>
                    To access certain features, you may be required to register an account. 
                    You agree to provide accurate information and to keep your login credentials secure.
                </li>

                <li>
                    <strong>Use of the Platform.</strong>
                    You agree to use etera only for lawful purposes and in accordance with these Terms. 
                    You must not misuse the platform or attempt unauthorized access.
                </li>

                <li>
                    <strong>Product and Service Descriptions.</strong>
                    etera strives to provide accurate descriptions of products and services. 
                    However, we do not warrant that descriptions or other content are error-free, complete, or current.
                </li>

                <li>
                    <strong>Platform Role and Provider Responsibility.</strong>
                    etera acts solely as a facilitator of instant price quotes provided by spare part providers registered on our platform. 
                    We do not manufacture, stock, or sell any spare parts directly.<br><br>
                    All products and services listed are offered by independent providers. 
                    etera is not responsible for the quality, condition, availability, or delivery of any parts sold, 
                    nor for any store’s return, refund, or warranty policies. 
                    Any disputes or claims regarding a product must be resolved directly with the provider.
                </li>

                <li>
                    <strong>Orders and Availability.</strong>
                    All orders are subject to acceptance and availability. 
                    We reserve the right to refuse or cancel any order at our discretion.
                </li>

                <li>
                    <strong>Intellectual Property.</strong>
                    All content on etera—including logos, text, graphics, and software—is the property of etera or its licensors 
                    and is protected by applicable intellectual property laws.
                </li>

                <li>
                    <strong>User Content.</strong>
                    You may submit content such as feedback and reviews. 
                    By doing so, you grant etera a non-exclusive, royalty-free license to use, reproduce, and display such content.
                </li>

                <li>
                    <strong>Prohibited Conduct.</strong>
                    You agree not to:
                    <ul>
                        <li>Violate any laws or regulations</li>
                        <li>Infringe intellectual property rights</li>
                        <li>Transmit harmful or malicious code</li>
                        <li>Use automated systems to access the platform</li>
                    </ul>
                </li>

                <li>
                    <strong>Third-Party Links.</strong>
                    etera may contain links to third-party websites. 
                    We are not responsible for the content, policies, or practices of those sites.
                </li>

                <li>
                    <strong>Limitation of Liability.</strong>
                    etera is not liable for any indirect, incidental, or consequential damages arising from your use of the platform. 
                    Our total liability is limited to the amount paid by you for the relevant product or service.
                </li>

                <li>
                    <strong>Pricing and Payments.</strong>
                    All prices on etera are subject to change without prior notice. 
                    The price at checkout is the final price. 
                    Payments must be made using the available payment methods.
                </li>

                <li>
                    <strong>Refunds and Cancellations.</strong>
                    etera does not sell spare parts directly and is not responsible for providers’ refund or cancellation policies. 
                    All refund or cancellation requests must be directed to the provider.
                </li>

                <li>
                    <strong>Privacy.</strong>
                    Your use of etera is governed by our Privacy Policy, which explains how your information is collected and used.
                </li>

                <li>
                    <strong>Changes to Terms.</strong>
                    etera may update these Terms at any time. 
                    Continued use of the platform following changes means you accept the updated Terms.
                </li>

                <li>
                    <strong>Governing Law.</strong>
                    These Terms are governed by the laws of your relevant jurisdiction.
                </li>

                <li>
                    <strong>Contact Us.</strong>
                    For questions or concerns, please contact us at the provided contact information.
                </li>
            
            </ol>
        </div>
            <div class="modal-footer"><button type="button" class="btn btn-primary" id="acceptTerms">I Accept</button></div>
        </div>
    </div>

    <!-- Scripts -->
    <!-- FilePond Assets -->
    <link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
    <script src="https://unpkg.com/filepond@^4/dist/filepond.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Register Plugins
            FilePond.registerPlugin(
                FilePondPluginImagePreview,
                FilePondPluginFileValidateType,
                FilePondPluginFileValidateSize
            );

            // --- Server Configuration updated for TempController response ---
            const serverConfig = {
                process: {
                    url: '{{ route("upload.part.image") }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    // Handle the response from TempController::uploadPartImage
                    onload: (response) => {
                        const data = JSON.parse(response);
                        if (data.success && data.files && data.files.length > 0) {
                            // Return the temp_path to FilePond, which sets it as serverId
                            return data.files[0].temp_path;
                        }
                        // If upload failed but status is 200, return generic error message
                        return 'Upload failed or path missing.';
                    },
                    onerror: (response) => {
                        console.error("File upload failed:", response);
                        // Return the error message from the JSON response if available
                        try {
                            const errorData = JSON.parse(response);
                            return errorData.message || 'Server error during upload.';
                        } catch (e) {
                            return 'Server error during upload.';
                        }
                    },
                },
                revert: {
                    url: '{{ route("upload.part.image.revert") }}',
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    // Send the serverId (which is the temp_path) as the body content
                    onload: (response) => {
                         // Revert doesn't need to return a value, just confirm success
                        const data = JSON.parse(response);
                        if(data.success) {
                            return; // Signal success
                        }
                        return 'Revert failed.';
                    }
                },
                fetch: (url, load, error, abort, headers) => {
                    // This function is required for restoring files based on the serverId (temp_path)
                    const request = new XMLHttpRequest();
                    request.open('GET', url);
                    
                    // You might need to add headers here if your storage link requires authentication
                    // request.setRequestHeader('Authorization', 'Bearer ' + someToken); 

                    request.onload = function() {
                        if (request.status >= 200 && request.status < 300) {
                            // Load the response body (file contents)
                            load(request.responseText);
                        } else {
                            error('Could not fetch file, status ' + request.status);
                        }
                    };
                    
                    request.onerror = function() {
                        error('Network error during file fetch.');
                    };

                    request.send();
                    
                    return { abort: () => { abort(); } };
                },
                credits: false,
            };

            // Common FilePond Options
            const pondOptions = {
                acceptedFileTypes: ['image/png', 'image/jpeg', 'image/jpg'],
                maxFileSize: '2MB',
                maxFiles: 1,
                server: serverConfig,
                allowRevert: true,
                labelIdle: 'Drag & Drop or <span class="filepond--label-action">Browse</span>',
                labelFileProcessing: 'Uploading...', // Text shown during processing
                labelFileProcessingComplete: 'Upload complete',
                labelFileProcessingAborted: 'Upload cancelled',
                labelTapToCancel: 'tap to cancel',
                labelTapToRetry: 'tap to retry',
                // This ensures the FilePond upload is sent with the input's name attribute
                // For multiple filepond instances, this should usually be a generic name like 'image'
                // and the hidden input carries the final path.
                name: 'image',
            };

            // 1. License Upload
            const licensePond = FilePond.create(document.querySelector('.filepond-license'), pondOptions);

            // On successful upload, FilePond returns the server ID (which is the temp path)
            licensePond.on('processfile', (error, file) => {
                if (!error) {
                    // file.serverId contains the temp_path returned by serverConfig.onload
                    document.getElementById('license_image_data').value = file.serverId;
                }
            });
            licensePond.on('removefile', (error, file) => {
                // Clear hidden input on file removal/revert
                document.getElementById('license_image_data').value = '';
            });

            // 2. Stamp Upload
            const stampPond = FilePond.create(document.querySelector('.filepond-stamp'), pondOptions);

            stampPond.on('processfile', (error, file) => {
                if (!error) {
                    // file.serverId contains the temp_path returned by serverConfig.onload
                    document.getElementById('stamp_image_data').value = file.serverId;
                }
            });
            stampPond.on('removefile', (error, file) => {
                // Clear hidden input on file removal/revert
                document.getElementById('stamp_image_data').value = '';
            });

            // --- Role Logic ---
            const roleSelect = document.getElementById('roleSelect');
            const roleSpecificFields = document.getElementById('roleSpecificFields');
            const shopFields = document.getElementById('shopFields');
            const sharedImageFields = document.getElementById('sharedImageFields');
            const brandsSelect = document.getElementById('multiple-select-clear-field');

            function toggleRoleFields() {
                const role = roleSelect.value;
                const isRoleSelected = !!role;

                // Toggle visibility of the whole block and image fields
                roleSpecificFields.style.display = isRoleSelected ? 'block' : 'none';
                sharedImageFields.style.display = isRoleSelected ? 'flex' : 'none';

                if (role === 'garage') {
                    if(shopFields) shopFields.style.display = 'none';
                    if(brandsSelect) brandsSelect.disabled = true;
                    // Ensure the 'brands' input is cleared if switching from shop to garage
                    $('select#multiple-select-clear-field').val(null).trigger('change');
                } else if (role === 'shop') {
                    if(shopFields) shopFields.style.display = 'block';
                    if(brandsSelect) brandsSelect.disabled = false;
                } else {
                    if(shopFields) shopFields.style.display = 'none';
                }
            }
            roleSelect.addEventListener('change', toggleRoleFields);
            if(roleSelect.value) toggleRoleFields();

            // --- File Restoration Logic (if validation fails) ---
            // If old data exists, restore the filepond instance.
            // FilePond needs the full path to FETCH the file data from the server.
            @if(old('license_image_data'))
                try {
                    const licenseData = '{{ old("license_image_data") }}';
                    if (licenseData) {
                        // FilePond Fetch/Load expects the path as the source URL.
                        // Since we are using asset('storage') for the full path in the addFile call, 
                        // we must ensure the `fetch` handler in serverConfig is correctly implemented 
                        // to handle this URL format if the serverId isn't the direct URL.
                        licensePond.addFile('{{ asset("storage") }}/' + licenseData, {
                             // This metadata tells FilePond what the server ID is.
                            metadata: { serverId: licenseData }
                        });
                    }
                } catch (e) {
                    console.error('Error restoring license image:', e);
                }
            @endif

            @if(old('stamp_image_data'))
                try {
                    const stampData = '{{ old("stamp_image_data") }}';
                    if (stampData) {
                        stampPond.addFile('{{ asset("storage") }}/' + stampData, {
                            metadata: { serverId: stampData }
                        });
                    }
                } catch (e) {
                    console.error('Error restoring stamp image:', e);
                }
            @endif


            // --- Form Submit Intercept ---
            const form = document.getElementById('garageSparePartRegisterForm');

            // Helper functions for validation errors
            function showFieldError(input, msg) {
                input.classList.add('is-invalid');
                let errEl = input.parentElement.querySelector('.js-val-error');
                if (!errEl) {
                    errEl = document.createElement('div');
                    errEl.className = 'text-danger mt-1 js-val-error';
                    // For input-group wrappers, append after the input group
                    const parent = input.closest('.input-group') || input;
                    parent.parentElement.appendChild(errEl);
                }
                errEl.textContent = msg;
                errEl.style.display = 'block';
            }
            function clearFieldError(input) {
                input.classList.remove('is-invalid');
                const errEl = input.parentElement.querySelector('.js-val-error') ||
                              (input.closest('.input-group') && input.closest('.input-group').parentElement.querySelector('.js-val-error'));
                if (errEl) errEl.style.display = 'none';
            }

            // Real-time blur validation
            document.querySelector('input[name="name"]').addEventListener('blur', function() {
                if (!this.value.trim()) showFieldError(this, 'Garage/Shop name is required.');
                else clearFieldError(this);
            });
            document.querySelector('input[name="email"]').addEventListener('blur', function() {
                const v = this.value.trim();
                if (!v) showFieldError(this, 'Email address is required.');
                else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)) showFieldError(this, 'Please enter a valid email address.');
                else clearFieldError(this);
            });
            document.querySelector('input[name="tin_number"]').addEventListener('blur', function() {
                if (!this.value.trim()) showFieldError(this, 'TIN number is required.');
                else clearFieldError(this);
            });
            document.querySelector('input[name="location"]').addEventListener('blur', function() {
                if (!this.value.trim()) showFieldError(this, 'Location is required.');
                else clearFieldError(this);
            });
            document.getElementById('password').addEventListener('blur', function() {
                const v = this.value;
                if (!v) showFieldError(this, 'Password is required.');
                else if (!/^\d{6}$/.test(v)) showFieldError(this, 'Password must be exactly 6 digits.');
                else clearFieldError(this);
            });
            document.getElementById('password_confirmation').addEventListener('blur', function() {
                const p = document.getElementById('password').value;
                const c = this.value;
                if (!c) showFieldError(this, 'Please confirm your password.');
                else if (c !== p) showFieldError(this, 'Passwords do not match.');
                else clearFieldError(this);
            });

            form.addEventListener('submit', function(e) {
                let hasError = false;

                // Validate name
                const nameInput = document.querySelector('input[name="name"]');
                if (!nameInput.value.trim()) {
                    showFieldError(nameInput, 'Garage/Shop name is required.');
                    hasError = true;
                } else { clearFieldError(nameInput); }

                // Validate phone
                const phoneInput = document.getElementById('inputPhone');
                const phoneVal = phoneInput.value.replace(/\D/g, '');
                if (!phoneVal || phoneVal.length < 12) {
                    showFieldError(phoneInput, 'Phone number must be +251 followed by 9 digits.');
                    hasError = true;
                } else if (!/^251[97]\d{8}$/.test(phoneVal)) {
                    showFieldError(phoneInput, 'Phone must start with +2519 or +2517.');
                    hasError = true;
                } else { clearFieldError(phoneInput); }

                // Validate email
                const emailInput = document.querySelector('input[name="email"]');
                const emailVal = emailInput.value.trim();
                if (!emailVal) {
                    showFieldError(emailInput, 'Email address is required.');
                    hasError = true;
                } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailVal)) {
                    showFieldError(emailInput, 'Please enter a valid email address.');
                    hasError = true;
                } else { clearFieldError(emailInput); }

                // Validate TIN number
                const tinInput = document.querySelector('input[name="tin_number"]');
                if (!tinInput.value.trim()) {
                    showFieldError(tinInput, 'TIN number is required.');
                    hasError = true;
                } else { clearFieldError(tinInput); }

                // Validate location
                const locInput = document.querySelector('input[name="location"]');
                if (!locInput.value.trim()) {
                    showFieldError(locInput, 'Location is required.');
                    hasError = true;
                } else { clearFieldError(locInput); }

                // Validate password
                const pwInput = document.getElementById('password');
                const pwVal = pwInput.value;
                if (!pwVal) {
                    showFieldError(pwInput, 'Password is required.');
                    hasError = true;
                } else if (!/^\d{6}$/.test(pwVal)) {
                    showFieldError(pwInput, 'Password must be exactly 6 digits.');
                    hasError = true;
                } else { clearFieldError(pwInput); }

                // Validate confirm password
                const cpInput = document.getElementById('password_confirmation');
                if (!cpInput.value) {
                    showFieldError(cpInput, 'Please confirm your password.');
                    hasError = true;
                } else if (cpInput.value !== pwVal) {
                    showFieldError(cpInput, 'Passwords do not match.');
                    hasError = true;
                } else { clearFieldError(cpInput); }

                // Validate role selection
                const roleSelect = document.getElementById('roleSelect');
                if (!roleSelect.value) {
                    showFieldError(roleSelect, 'Please select a business type.');
                    hasError = true;
                } else { clearFieldError(roleSelect); }

                // Validate images (only if role is selected = images are visible)
                if (roleSelect.value) {
                    const licenseVal = document.getElementById('license_image_data').value;
                    const stampVal = document.getElementById('stamp_image_data').value;

                    // Check if any file is still processing
                    const isLicenseProcessing = licensePond.getFiles().some(f => f.status !== 5);
                    const isStampProcessing = stampPond.getFiles().some(f => f.status !== 5);

                    if (isLicenseProcessing || isStampProcessing) {
                        e.preventDefault();
                        alert('Please wait for all image uploads to complete before submitting.');
                        return false;
                    }

                    if (!licenseVal) {
                        hasError = true;
                        let licErr = document.querySelector('.filepond-license')?.closest('.col-md-6')?.querySelector('.js-val-error');
                        if (!licErr) {
                            licErr = document.createElement('div');
                            licErr.className = 'text-danger mt-1 js-val-error';
                            document.querySelector('.filepond-license')?.closest('.col-md-6')?.appendChild(licErr);
                        }
                        licErr.textContent = 'Business license image is required.';
                        licErr.style.display = 'block';
                    }
                    if (!stampVal) {
                        hasError = true;
                        let stErr = document.querySelector('.filepond-stamp')?.closest('.col-md-6')?.querySelector('.js-val-error');
                        if (!stErr) {
                            stErr = document.createElement('div');
                            stErr.className = 'text-danger mt-1 js-val-error';
                            document.querySelector('.filepond-stamp')?.closest('.col-md-6')?.appendChild(stErr);
                        }
                        stErr.textContent = 'Stamp image is required.';
                        stErr.style.display = 'block';
                    }
                }

                // Validate terms
                const termsCheck = document.getElementById('terms-check');
                if (!termsCheck.checked) {
                    let termsErr = termsCheck.closest('.form-check').querySelector('.js-val-error');
                    if (!termsErr) {
                        termsErr = document.createElement('div');
                        termsErr.className = 'text-danger mt-1 js-val-error';
                        termsCheck.closest('.form-check').appendChild(termsErr);
                    }
                    termsErr.textContent = 'You must accept the Terms & Conditions.';
                    termsErr.style.display = 'block';
                    hasError = true;
                } else {
                    const termsErr = termsCheck.closest('.form-check').querySelector('.js-val-error');
                    if (termsErr) termsErr.style.display = 'none';
                }

                if (hasError) {
                    e.preventDefault();
                    // Scroll to first error
                    const firstErr = document.querySelector('.is-invalid, .js-val-error[style*="block"]');
                    if (firstErr) {
                        firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        if (firstErr.focus) firstErr.focus();
                    }
                    return false;
                }

                // Show spinner and disable button
                document.getElementById('submitBtn').disabled = true;
                document.getElementById('submitText').textContent = 'Processing...';
                document.getElementById('submitSpinner').style.display = 'inline-block';
            });

            // Password Toggle & Phone formatting (Keep existing logic)
             $('#inputPhone').on('input', function() {
                let value = $(this).val().replace(/\D/g, '');
                if (!value.startsWith('251')) value = '251' + value.replace(/^251/, '');
                $(this).val('+' + value.substring(0, 12));
            });
             $('.toggle-password').on('click', function(){
                const input = $($(this).data('target'));
                const icon = $(this).find('i');
                if(input.attr('type')==='password'){
                    input.attr('type','text'); icon.removeClass('bx-hide').addClass('bx-show');
                } else {
                    input.attr('type','password'); icon.removeClass('bx-show').addClass('bx-hide');
                }
            });

            // Modal Logic (Keep existing)
             $('#openTermsModal').on('click', function() { $('#termsModal').addClass('show'); });
             $('.modal-close, #acceptTerms').on('click', function() {
                 $('#termsModal').removeClass('show');
                 if(this.id === 'acceptTerms') $('#terms-check').prop('checked', true);
             });
        });
    </script>
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

});
</script>

    <!-- Google Maps API (if needed) -->
    @if(config('services.google.maps_api_key'))
        <script async defer src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&callback=initMap"></script>
    @endif
@endsection
