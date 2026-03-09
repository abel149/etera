@extends('layouts.authentication')

@section('branding')
    <img src="{{ asset('assets/images/transparent.svg') }}" class="etera-auth-logo" alt="etera">
    <h2 class="etera-heading etera-heading-lg" style="text-align:center; margin-bottom: 0.5rem;">
        Join etera Today
    </h2>
    <p class="etera-subtext" style="text-align:center; max-width: 360px; color: rgba(255,255,255,0.85);">
        Register as a customer and start sourcing auto parts across all brands.
    </p>

    @include('partials.brand-globe')
@endsection

@section('styles')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
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
        .form-text { color: #9ca3af; }
        .logo-text { filter: none; }
        .fw-bold { color: #1a1a2e; }
    </style>
@endsection

@section('content')

        <div class="form-body">

            
            <div class="form-header">
                <img src="{{ asset('assets/images/transparent.svg') }}" 
                    class="logo-text mb-4" 
                    style="max-width: 7.5rem;" 
                    alt="etera">
                <h3>Create Your account today</h3>
                <p class="mb-0">Fill the form below to register as a <b>Customers.</b></p>
            </div>

            <div class="form-body">
                <form id="businessRegisterForm" class="row g-3" action="{{ route('register.business-owner') }}" method="POST" novalidate>
                    @csrf
                    <input type="hidden" name="role" value="others">

                    <div class="col-md-6 col-12">
                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror"
                               name="name" 
                               placeholder="Full Name"
                               value="{{ old('name') }}" required>
                        @error('first_name')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>


                    <div class="col-12">
                        <label class="form-label">Email Address <span class="text-muted">(optional)</span></label>
                        <input type="email" 
                               class="form-control @error('email') is-invalid @enderror"
                               name="email" 
                               placeholder="Email Address"
                               value="{{ old('email') }}">
                        @error('email')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>

                    <div class="col-md-6 col-12">
                        <label for="inputPhone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control" id="inputPhone" name="phone_number" placeholder="+251912345678 or +251712345678" required value="+251">
                    </div>

                    <div class="col-md-6 col-12">
                        <label class="form-label">Password (6 digits) <span class="text-danger">*</span></label>
                        <div class="input-group" id="show_hide_password">
                            <input 
                                type="password" 
                                id="password"
                                name="password"
                                class="form-control border-end-0 @error('password') is-invalid @enderror"
                                placeholder="Enter 6-digit PIN"
                                maxlength="6"
                                inputmode="numeric"
                                autocomplete="new-password"
                                required
                            >
                            <a href="javascript:;" class="input-group-text bg-transparent toggle-password" data-target="#password">
                                <i class='bx bx-hide'></i>
                            </a>
                        </div>
                        <small id="passwordHelp" class="form-text text-muted">Must be exactly 6 digits.</small>
                        <div id="passwordError" class="text-danger mt-1" style="display:none;"></div>
                        @error('password')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>

                    <div class="col-md-6 col-12">
                        <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                        <div class="input-group" id="show_hide_confirm_password">
                            <input 
                                type="password" 
                                id="password_confirmation"
                                name="password_confirmation"
                                class="form-control border-end-0"
                                placeholder="Confirm 6-digit PIN"
                                maxlength="6"
                                inputmode="numeric"
                                autocomplete="new-password"
                                required
                            >
                            <a href="javascript:;" class="input-group-text bg-transparent toggle-password" data-target="#password_confirmation">
                                <i class='bx bx-hide'></i>
                            </a>
                        </div>
                        <div id="confirmPasswordError" class="text-danger mt-1" style="display:none;"></div>
                    </div>


                    <div class="col-12 mt-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="terms-check"
                                   name="terms" 
                                   value="1" required>
                            <label class="form-check-label" for="terms-check">
                                I agree to the <a href="javascript:void(0);" id="openTermsModal" class="text-info">Terms & Conditions</a>
                            </label>
                            @error('terms')<span class="text-danger d-block mt-1">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="col-12 mt-4">
                        <div class="d-grid">
                            <button id="submitBtn" type="submit" class="btn btn-primary">Sign up</button>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="text-center mt-3">
                            <p class="mb-2">Already have an account?
                                <a href="/login" class="text-info">Login here</a>
                            </p>

                            <p class="mb-0 small">
                                <a href="{{ route('signup') }}" class="text-info">Change role</a>
                                
                            </p>
                        </div>
                    </div>

                </form>

            </div>
        </div>

    <!-- TERMS AND CONDITIONS MODAL -->
<div id="termsModal" class="modal-overlay">
    <div class="modal-content">

        <div class="modal-header">
            <h4>Terms and Conditions</h4>
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

    </div>
</div>
<!-- END MODAL -->




<script>
    // Self-invoking function to avoid global scope pollution
    (function($){
        // Ensure the DOM is fully loaded before executing scripts
        $(function(){

            // --- MODAL LOGIC (New Addition) ---
            const $termsModal = $('#termsModal');
            const $body = $('body');

            // Function to show the modal
            function showModal() {
                $termsModal.addClass('show');
                $body.css('overflow', 'hidden'); // Prevent background scroll
            }

            // Function to hide the modal
            function hideModal() {
                $termsModal.removeClass('show');
                $body.css('overflow', '');
            }

            // Show Modal on link click
            $('#openTermsModal').on('click', function(e) {
                e.preventDefault();
                showModal();
            });

            // Hide Modal on close button click or data-dismiss click (using .modal-close)
            $('.modal-close').on('click', function() {
                hideModal();
            });

            // Hide Modal on overlay click
            $termsModal.on('click', function(e) {
                // Check if the click target is the modal overlay itself (not the content inside)
                if (e.target === this) {
                    hideModal();
                }
            });
            // --- END MODAL LOGIC ---

            // --- PASSWORD RULES (Existing Logic) ---
            const weakPins = [
                "123456","111111","000000","654321","222222","333333","444444","555555",
                "112233","121212","123123","987654","101010","246824","121314"
            ];

            function isRepeated(pin) {
                return /^(\d)\1{5}$/.test(pin); // 6 repeated digits
            }

            function isAscending(pin) {
                // check for ascending sequence like 012345 or 123456
                const seq = "0123456789012345";
                return seq.indexOf(pin) !== -1;
            }

            function isDescending(pin) {
                const seq = "987654321098765";
                return seq.indexOf(pin) !== -1;
            }

            function isAlternating(pin) {
                return /^(\d)(\d)\1\2\1\2$/.test(pin); // e.g. 121212
            }

            function isWeakPattern(pin) {
                if (weakPins.includes(pin)) return true;
                if (isRepeated(pin)) return true;
                if (isAscending(pin)) return true;
                if (isDescending(pin)) return true;
                if (isAlternating(pin)) return true;
                return false;
            }

            // Restrict input to digits only as user types and enforce maxlength
            $('#password, #password_confirmation').on('input', function(){
                const cleaned = $(this).val().replace(/\D/g, '').slice(0,6);
                if ( $(this).val() !== cleaned ) {
                    $(this).val(cleaned);
                }
            });

            // Toggle show/hide for any .toggle-password anchor
            $(document).on('click', '.toggle-password', function(e){
                e.preventDefault();
                const targetSelector = $(this).data('target');
                const $input = $(targetSelector);
                const $icon = $(this).find('i');

                if ($input.attr('type') === 'password') {
                    $input.attr('type', 'text');
                    $icon.removeClass('bx-hide').addClass('bx-show');
                } else {
                    $input.attr('type', 'password');
                    $icon.removeClass('bx-show').addClass('bx-hide');
                }
            });

            // Real-time validation for password field
            $('#password').on('blur input', function(){
                const val = $(this).val();
                const $input = $(this);
                const $err = $('#passwordError');
                $err.hide().text('');
                $input.removeClass('is-invalid');

                if (val.length === 0) {
                    return;
                }

                if (!/^\d{6}$/.test(val)) {
                    $err.text('Password must be exactly 6 digits.').show();
                    $input.addClass('is-invalid');
                    return;
                }

                if (isWeakPattern(val)) {
                    $err.text('This PIN is too common or weak. Choose a stronger one.').show();
                    $input.addClass('is-invalid');
                    return;
                }
                
                // Also re-validate confirmation on password change
                $('#password_confirmation').trigger('input');
            });

            // Real-time validation for confirm password
            $('#password_confirmation').on('input blur', function(){
                const p = $('#password').val();
                const c = $(this).val();
                const $input = $(this);
                const $err = $('#confirmPasswordError');
                $err.hide().text('');
                $input.removeClass('is-invalid');

                if (c.length === 0) return;

                if (!/^\d{6}$/.test(c)) {
                    $err.text('Confirmation must be 6 digits.').show();
                    $input.addClass('is-invalid');
                    return;
                }

                if (p !== c) {
                    $err.text('Passwords do not match.').show();
                    $input.addClass('is-invalid');
                    return;
                }
            });

            // Phone number formatting
            $('#inputPhone').on('input', function() {
                let value = $(this).val().replace(/\D/g, '');
                if (!value.startsWith('251')) value = '251' + value.replace(/^251/, '');
                $(this).val('+' + value.substring(0, 12));
            });

            // Helper: show/clear field error
            function showFieldError($input, msg) {
                $input.addClass('is-invalid');
                let $err = $input.siblings('.js-error');
                if ($err.length === 0) {
                    $err = $('<div class="text-danger mt-1 js-error"></div>');
                    $input.after($err);
                }
                $err.text(msg).show();
            }
            function clearFieldError($input) {
                $input.removeClass('is-invalid');
                $input.siblings('.js-error').hide();
            }

            // Real-time blur validation for individual fields
            $('input[name="name"]').on('blur', function() {
                if ($(this).val().trim() === '') {
                    showFieldError($(this), 'Full name is required.');
                } else {
                    clearFieldError($(this));
                }
            });
            $('input[name="email"]').on('blur', function() {
                const v = $(this).val().trim();
                if (v && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)) {
                    showFieldError($(this), 'Please enter a valid email address.');
                } else {
                    clearFieldError($(this));
                }
            });
            $('#inputPhone').on('blur', function() {
                const v = $(this).val().replace(/\D/g, '');
                if (!v || v.length < 12) {
                    showFieldError($(this), 'Phone number must be +251 followed by 9 digits.');
                } else if (!/^251[97]\d{8}$/.test(v)) {
                    showFieldError($(this), 'Phone must start with +2519 or +2517.');
                } else {
                    clearFieldError($(this));
                }
            });

            // Form submit validation
            $('#businessRegisterForm').on('submit', function(e){
                let hasError = false;

                // Validate name
                const $name = $('input[name="name"]');
                if ($name.val().trim() === '') {
                    showFieldError($name, 'Full name is required.');
                    hasError = true;
                } else { clearFieldError($name); }

                // Validate email (optional – only check format if provided)
                const $email = $('input[name="email"]');
                const emailVal = $email.val().trim();
                if (emailVal && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailVal)) {
                    showFieldError($email, 'Please enter a valid email address.');
                    hasError = true;
                } else { clearFieldError($email); }

                // Validate phone
                const $phone = $('#inputPhone');
                const phoneVal = $phone.val().replace(/\D/g, '');
                if (!phoneVal || phoneVal.length < 12) {
                    showFieldError($phone, 'Phone number must be +251 followed by 9 digits.');
                    hasError = true;
                } else if (!/^251[97]\d{8}$/.test(phoneVal)) {
                    showFieldError($phone, 'Phone must start with +2519 or +2517.');
                    hasError = true;
                } else { clearFieldError($phone); }

                // Trigger password validation
                $('#password').trigger('blur');
                $('#password_confirmation').trigger('blur');

                if ($('#passwordError').is(':visible') || $('#confirmPasswordError').is(':visible')) {
                    hasError = true;
                }

                // Validate password not empty
                if ($('#password').val().trim() === '') {
                    $('#passwordError').text('Password is required.').show();
                    $('#password').addClass('is-invalid');
                    hasError = true;
                }

                // Validate terms
                if (!$('#terms-check').is(':checked')) {
                    let $termsErr = $('#terms-check').closest('.form-check').find('.js-error');
                    if ($termsErr.length === 0) {
                        $termsErr = $('<div class="text-danger mt-1 js-error"></div>');
                        $('#terms-check').closest('.form-check').append($termsErr);
                    }
                    $termsErr.text('You must accept the Terms & Conditions.').show();
                    hasError = true;
                } else {
                    $('#terms-check').closest('.form-check').find('.js-error').hide();
                }

                if (hasError) {
                    e.preventDefault();
                    // Scroll to first error
                    const $firstErr = $('.is-invalid, .js-error:visible').first();
                    if ($firstErr.length) {
                        $('html, body').animate({ scrollTop: $firstErr.offset().top - 100 }, 300);
                        $firstErr.focus();
                    }
                    return false;
                }

                return true;
            });

        });
    })(jQuery);
</script>

@endsection
