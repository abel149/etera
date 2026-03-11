@extends('layouts.authentication')

@section('title', 'Sign Up — etera')

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
    </style>
@endsection

@section('content')

<div style="animation: etera-fade-in 0.6s ease-out">
    <div style="text-align: center; margin-bottom: 2rem;">
        <img src="{{ asset('assets/images/transparent.svg') }}" alt="etera" style="max-width: 120px; margin-bottom: 1rem;" class="d-xl-none">
        <h2 class="etera-heading" style="font-size: 1.5rem; margin-bottom: 0.5rem;">Create Your Account</h2>
        <p class="etera-subtext">Fill the form below to register as a <strong>Customer</strong>.</p>
    </div>

    <form id="businessRegisterForm" action="{{ route('register.business-owner') }}" method="POST" novalidate>
        @csrf
        <input type="hidden" name="role" value="others">

        {{-- Full Name --}}
        <div class="etera-input-group">
            <label>Full Name <span style="color:#dc3545">*</span></label>
            <input type="text"
                   class="etera-input {{ $errors->has('name') ? 'error' : '' }}"
                   name="name"
                   placeholder="Enter your full name"
                   value="{{ old('name') }}" required>
            @error('name')<div class="etera-error-text">{{ $message }}</div>@enderror
        </div>

        {{-- Email --}}
        <div class="etera-input-group">
            <label>Email Address <span style="color:var(--etera-text-muted); font-weight:400;">(optional)</span></label>
            <input type="email"
                   class="etera-input {{ $errors->has('email') ? 'error' : '' }}"
                   name="email"
                   placeholder="john@example.com"
                   value="{{ old('email') }}">
            @error('email')<div class="etera-error-text">{{ $message }}</div>@enderror
        </div>

        {{-- Phone Number --}}
        <div class="etera-input-group">
            <label>Phone Number <span style="color:#dc3545">*</span></label>
            <input type="tel"
                   class="etera-input {{ $errors->has('phone_number') ? 'error' : '' }}"
                   id="inputPhone"
                   name="phone_number"
                   placeholder="+251912345678"
                   value="{{ old('phone_number', '+251') }}" required>
            @error('phone_number')<div class="etera-error-text">{{ $message }}</div>@enderror
        </div>

        {{-- Password Row --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="etera-input-group">
                <label>Password (6 digits) <span style="color:#dc3545">*</span></label>
                <div class="etera-password-wrapper">
                    <input type="password"
                           id="password"
                           name="password"
                           class="etera-input {{ $errors->has('password') ? 'error' : '' }}"
                           placeholder="Enter 6-digit PIN"
                           maxlength="6"
                           inputmode="numeric"
                           autocomplete="new-password" required>
                    <button type="button" class="etera-password-toggle toggle-password" data-target="#password" tabindex="-1">
                        <i class='bx bx-hide'></i>
                    </button>
                </div>
                <div id="passwordError" class="etera-error-text" style="display:none;"></div>
                @error('password')<div class="etera-error-text">{{ $message }}</div>@enderror
            </div>

            <div class="etera-input-group">
                <label>Confirm Password <span style="color:#dc3545">*</span></label>
                <div class="etera-password-wrapper">
                    <input type="password"
                           id="password_confirmation"
                           name="password_confirmation"
                           class="etera-input"
                           placeholder="Confirm PIN"
                           maxlength="6"
                           inputmode="numeric"
                           autocomplete="new-password" required>
                    <button type="button" class="etera-password-toggle toggle-password" data-target="#password_confirmation" tabindex="-1">
                        <i class='bx bx-hide'></i>
                    </button>
                </div>
                <div id="confirmPasswordError" class="etera-error-text" style="display:none;"></div>
            </div>
        </div>

        {{-- Terms --}}
        <div style="margin-top: 0.5rem; margin-bottom: 1.25rem;">
            <label class="etera-toggle">
                <input type="checkbox" id="terms-check" name="terms" value="1" {{ old('terms') ? 'checked' : '' }} required>
                <span>I agree to the <a href="javascript:void(0);" id="openTermsModal" class="etera-link">Terms & Conditions</a></span>
            </label>
            @error('terms')<div class="etera-error-text" style="margin-top:4px;">{{ $message }}</div>@enderror
        </div>

        <button type="submit" id="submitBtn"
                class="etera-btn etera-btn-primary etera-btn-block etera-btn-lg">
            Sign Up
        </button>
    </form>

    <div style="text-align: center; margin-top: 1.5rem;">
        <p class="etera-subtext" style="font-size: 0.9rem;">
            Already have an account? <a href="/login" class="etera-link">Login here</a>
        </p>
    </div>

    <div class="etera-divider">or</div>

    <div style="text-align: center;">
        <p class="etera-subtext" style="font-size: 0.85rem;">
            <a href="{{ route('signup') }}" class="etera-link">Change role</a>
        </p>
    </div>
</div>

{{-- TERMS AND CONDITIONS MODAL --}}
<div id="termsModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h4>Terms and Conditions</h4>
            <button type="button" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <p style="color:#6b7280;">Last Updated: October 2025</p>
            <ol>
                <li><strong>Acceptance of Terms.</strong> By using etera, you confirm that you have read, understood, and agreed to these Terms and Conditions. If you do not agree, you must not use the platform.</li>
                <li><strong>Eligibility.</strong> You must be at least 18 years old or have legal parental/guardian consent to use etera.</li>
                <li><strong>Account Registration.</strong> To access certain features, you may be required to register an account. You agree to provide accurate information and to keep your login credentials secure.</li>
                <li><strong>Use of the Platform.</strong> You agree to use etera only for lawful purposes and in accordance with these Terms.</li>
                <li><strong>Product and Service Descriptions.</strong> etera strives to provide accurate descriptions of products and services.</li>
                <li><strong>Platform Role.</strong> etera acts solely as a facilitator of instant price quotes. We do not manufacture, stock, or sell any spare parts directly.</li>
                <li><strong>Intellectual Property.</strong> All content on etera is the property of etera or its licensors and is protected by applicable intellectual property laws.</li>
                <li><strong>Privacy.</strong> Your use of etera is governed by our Privacy Policy.</li>
                <li><strong>Changes to Terms.</strong> etera may update these Terms at any time. Continued use means you accept the updated Terms.</li>
                <li><strong>Contact Us.</strong> For questions or concerns, please contact us at the provided contact information.</li>
            </ol>
        </div>
    </div>
</div>

<script>
(function($){
    $(function(){
        // Modal Logic
        const $termsModal = $('#termsModal');
        $('#openTermsModal').on('click', function(e){ e.preventDefault(); $termsModal.addClass('show'); $('body').css('overflow', 'hidden'); });
        $('.modal-close').on('click', function(){ $termsModal.removeClass('show'); $('body').css('overflow', ''); });
        $termsModal.on('click', function(e){ if (e.target === this) { $termsModal.removeClass('show'); $('body').css('overflow', ''); } });

        // Password — digits only
        const weakPins = ["123456","111111","000000","654321","222222","333333","444444","555555","112233","121212","123123","987654","101010","246824","121314"];
        function isWeakPattern(pin) {
            if (weakPins.includes(pin)) return true;
            if (/^(\d)\1{5}$/.test(pin)) return true;
            if ("0123456789012345".indexOf(pin) !== -1) return true;
            if ("987654321098765".indexOf(pin) !== -1) return true;
            if (/^(\d)(\d)\1\2\1\2$/.test(pin)) return true;
            return false;
        }
        $('#password, #password_confirmation').on('input', function(){ $(this).val($(this).val().replace(/\D/g, '').slice(0,6)); });

        // Toggle show/hide
        $(document).on('click', '.toggle-password', function(e){
            e.preventDefault();
            const $input = $($(this).data('target'));
            const $icon = $(this).find('i');
            if ($input.attr('type') === 'password') { $input.attr('type', 'text'); $icon.removeClass('bx-hide').addClass('bx-show'); }
            else { $input.attr('type', 'password'); $icon.removeClass('bx-show').addClass('bx-hide'); }
        });

        // Password validation
        $('#password').on('blur input', function(){
            const val = $(this).val(), $err = $('#passwordError');
            $err.hide(); $(this).removeClass('error');
            if (!val.length) return;
            if (!/^\d{6}$/.test(val)) { $err.text('Password must be exactly 6 digits.').show(); $(this).addClass('error'); return; }
            if (isWeakPattern(val)) { $err.text('This PIN is too common. Choose a stronger one.').show(); $(this).addClass('error'); return; }
            $('#password_confirmation').trigger('input');
        });
        $('#password_confirmation').on('input blur', function(){
            const p = $('#password').val(), c = $(this).val(), $err = $('#confirmPasswordError');
            $err.hide(); $(this).removeClass('error');
            if (!c.length) return;
            if (!/^\d{6}$/.test(c)) { $err.text('Must be 6 digits.').show(); $(this).addClass('error'); return; }
            if (p !== c) { $err.text('Passwords do not match.').show(); $(this).addClass('error'); return; }
        });

        // Phone formatting
        $('#inputPhone').on('input', function() {
            let v = $(this).val().replace(/\D/g, '');
            if (!v.startsWith('251')) v = '251' + v.replace(/^251/, '');
            $(this).val('+' + v.substring(0, 12));
        });

        // Helpers
        function showErr($el, msg) { $el.addClass('error'); let $e = $el.siblings('.js-err'); if (!$e.length) { $e = $('<div class="etera-error-text js-err"></div>'); $el.after($e); } $e.text(msg).show(); }
        function clearErr($el) { $el.removeClass('error'); $el.siblings('.js-err').hide(); }

        // Blur validation
        $('input[name="name"]').on('blur', function(){ $(this).val().trim() === '' ? showErr($(this), 'Full name is required.') : clearErr($(this)); });
        $('input[name="email"]').on('blur', function(){ const v = $(this).val().trim(); if (v && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)) showErr($(this), 'Please enter a valid email.'); else clearErr($(this)); });
        $('#inputPhone').on('blur', function(){ const v = $(this).val().replace(/\D/g, ''); if (!v || v.length < 12) showErr($(this), 'Phone must be +251 followed by 9 digits.'); else if (!/^251[97]\d{8}$/.test(v)) showErr($(this), 'Must start with +2519 or +2517.'); else clearErr($(this)); });

        // Form submit
        $('#businessRegisterForm').on('submit', function(e){
            let err = false;
            const $n = $('input[name="name"]'); if ($n.val().trim() === '') { showErr($n, 'Full name is required.'); err = true; } else clearErr($n);
            const $em = $('input[name="email"]'), ev = $em.val().trim(); if (ev && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(ev)) { showErr($em, 'Please enter a valid email.'); err = true; } else clearErr($em);
            const $ph = $('#inputPhone'), pv = $ph.val().replace(/\D/g, ''); if (!pv || pv.length < 12) { showErr($ph, 'Phone must be +251 followed by 9 digits.'); err = true; } else if (!/^251[97]\d{8}$/.test(pv)) { showErr($ph, 'Must start with +2519 or +2517.'); err = true; } else clearErr($ph);
            $('#password').trigger('blur'); $('#password_confirmation').trigger('blur');
            if ($('#passwordError').is(':visible') || $('#confirmPasswordError').is(':visible')) err = true;
            if ($('#password').val().trim() === '') { $('#passwordError').text('Password is required.').show(); $('#password').addClass('error'); err = true; }
            if (!$('#terms-check').is(':checked')) { let $te = $('#terms-check').closest('.etera-toggle').siblings('.js-terms-err'); if (!$te.length) { $te = $('<div class="etera-error-text js-terms-err"></div>'); $('#terms-check').closest('div').append($te); } $te.text('You must accept the Terms & Conditions.').show(); err = true; } else { $('#terms-check').closest('div').find('.js-terms-err').hide(); }
            if (err) { e.preventDefault(); const $first = $('.error, .js-err:visible, .js-terms-err:visible').first(); if ($first.length) { $('html, body').animate({ scrollTop: $first.offset().top - 100 }, 300); $first.focus(); } return false; }
            $('#submitBtn').prop('disabled', true).text('Processing...');
        });
    });
})(jQuery);
</script>

@endsection
