@extends('layouts.insurance')
@section('content')

<div class="row justify-content-center">
    <div class="col-12 col-lg-7">

        <h3 class="mb-1">Price Encryption Setup</h3>
        <p class="text-secondary mb-4">
            When enabled, shop and garage prices are encrypted in the browser before reaching the server.
            <strong>Only you</strong> — using your Encryption PIN — can decrypt them.
            Not the admin, not the developer, nobody else.
        </p>

        {{-- ════ ACTIVE STATUS BANNER ════ --}}
        @if($user->has_encryption)
        <div class="alert alert-success d-flex align-items-center gap-2 mb-4">
            <i class="bx bx-shield-check fs-3"></i>
            <div>
                <strong>Encryption is active.</strong>
                Shops and garages are sending you encrypted prices.
                Use your PIN when viewing received proformas.
            </div>
        </div>

        {{-- ════ CHANGE PIN CARD (safe — same RSA keys, old proformas stay readable) ════ --}}
        <div class="card mb-4">
            <div class="card-header fw-semibold">
                <i class="bx bx-key me-2 text-primary"></i> Change Encryption PIN
            </div>
            <div class="card-body">
                <p class="text-secondary small mb-3">
                    Changing your PIN re-wraps the <em>same private key</em> with a new PIN.
                    <strong class="text-success">All previously encrypted proformas remain fully readable</strong>
                    with the new PIN.
                </p>
                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-4">
                        <label class="form-label">Current PIN</label>
                        <input type="password" id="cpOldPin" class="form-control" placeholder="Your current PIN">
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label">New PIN</label>
                        <input type="password" id="cpNewPin" class="form-control" placeholder="Min 8 characters" minlength="8">
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label">Confirm New PIN</label>
                        <input type="password" id="cpNewPinConfirm" class="form-control" placeholder="Repeat new PIN">
                    </div>
                </div>
                <button id="btnChangePin" class="btn btn-primary radius-30 px-4">
                    <i class="bx bx-refresh me-2"></i> Change PIN
                </button>
                <div id="cpStatus" class="mt-3 d-none">
                    <div class="d-flex align-items-center gap-2 text-secondary">
                        <div class="spinner-border spinner-border-sm"></div>
                        <span id="cpStatusText">Processing…</span>
                    </div>
                </div>
                <div id="cpSuccess" class="alert alert-success mt-3 d-none">
                    <i class="bx bx-check-circle me-2"></i> PIN changed successfully.
                    All encrypted proformas are still readable with your new PIN.
                </div>
                <div id="cpError" class="alert alert-danger mt-3 d-none"></div>
            </div>
        </div>

        {{-- ════ DANGER ZONE: Regenerate Keys ════ --}}
        <div class="card mb-4 border-danger">
            <div class="card-header bg-danger text-white fw-semibold">
                <i class="bx bx-error-alt me-2"></i> Danger Zone — Regenerate Keys
            </div>
            <div class="card-body">
                <div class="alert alert-danger mb-3 d-flex gap-2">
                    <i class="bx bx-error fs-4 flex-shrink-0 mt-1"></i>
                    <div>
                        <strong>This is a destructive action.</strong>
                        Regenerating keys creates a brand new RSA key pair.
                        <strong>Every previously encrypted proforma price will become permanently unreadable — forever.</strong>
                        Only do this if you have no proformas with encrypted prices yet,
                        or if you are comfortable losing access to old prices.
                    </div>
                </div>
                <button class="btn btn-danger radius-30 px-4" id="btnShowRegen">
                    <i class="bx bx-lock-open me-2"></i> I understand, show regenerate form
                </button>
                <div id="regenForm" class="d-none mt-3">
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label">New PIN</label>
                            <input type="password" id="encPin" class="form-control" placeholder="Minimum 8 characters" minlength="8">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Confirm New PIN</label>
                            <input type="password" id="encPinConfirm" class="form-control" placeholder="Repeat PIN">
                        </div>
                    </div>
                    <button id="btnGenerate" class="btn btn-danger radius-30 px-4">
                        <i class="bx bx-refresh me-2"></i> Regenerate Keys Now
                    </button>
                    <div id="genStatus" class="mt-3 d-none">
                        <div class="d-flex align-items-center gap-2 text-secondary">
                            <div class="spinner-border spinner-border-sm"></div>
                            <span id="genStatusText">Generating RSA-2048 key pair…</span>
                        </div>
                    </div>
                    <div id="genSuccess" class="alert alert-success mt-3 d-none">
                        <i class="bx bx-check-circle me-2"></i>
                        <strong>New keys saved.</strong> Remember: old encrypted proformas are no longer accessible.
                    </div>
                    <div id="genError" class="alert alert-danger mt-3 d-none"></div>
                </div>
            </div>
        </div>

        @else
        {{-- ════ FIRST-TIME SETUP ════ --}}
        <div class="card mb-4">
            <div class="card-header fw-semibold">
                <i class="bx bx-lock-alt me-2 text-primary"></i> Enable Encryption
            </div>
            <div class="card-body">
                <p class="text-secondary small mb-3">
                    Choose a PIN. This PIN is <strong>never sent to the server</strong> — it protects
                    your private key in the browser. If you forget your PIN, encrypted prices
                    cannot be recovered.
                </p>
                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label">Encryption PIN</label>
                        <input type="password" id="encPin" class="form-control" placeholder="Minimum 8 characters" minlength="8">
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Confirm PIN</label>
                        <input type="password" id="encPinConfirm" class="form-control" placeholder="Repeat PIN">
                    </div>
                </div>
                <button id="btnGenerate" class="btn btn-primary radius-30 px-4">
                    <i class="bx bx-lock-alt me-2"></i> Enable Encryption
                </button>
                <div id="genStatus" class="mt-3 d-none">
                    <div class="d-flex align-items-center gap-2 text-secondary">
                        <div class="spinner-border spinner-border-sm"></div>
                        <span id="genStatusText">Generating RSA-2048 key pair…</span>
                    </div>
                </div>
                <div id="genSuccess" class="alert alert-success mt-3 d-none">
                    <i class="bx bx-check-circle me-2"></i>
                    <strong>Encryption is now active!</strong>
                    Future price submissions will be encrypted. Enter your PIN when viewing a received proforma.
                </div>
                <div id="genError" class="alert alert-danger mt-3 d-none"></div>
            </div>
        </div>
        @endif

        {{-- ════ How it works ════ --}}
        <div class="card border-0 bg-light">
            <div class="card-body">
                <h6 class="fw-semibold mb-2"><i class="bx bx-info-circle me-1 text-primary"></i> How it works</h6>
                <ol class="text-secondary small mb-0">
                    <li>Your browser generates an <strong>RSA-2048 key pair</strong>.</li>
                    <li>The <strong>public key</strong> is stored on the server — shops/garages use it to encrypt prices.</li>
                    <li>Your <strong>private key</strong> is wrapped with AES-256 (derived from your PIN) and stored encrypted. <em>Nobody can read it without your PIN.</em></li>
                    <li>When you open a received proforma, enter your PIN → browser unwraps the private key → browser decrypts all prices. <strong>Plaintext prices never reach the server.</strong></li>
                    <li><strong>Change PIN</strong> only re-wraps the same key — old proformas stay readable. <strong>Regenerate Keys</strong> creates a new key — old proformas are permanently lost.</li>
                </ol>
            </div>
        </div>

    </div>
</div>

<script>{!! file_get_contents(base_path('resources/js/e2e-encryption.js')) !!}</script>
<script>
// ── Shared helper: POST JSON with CSRF ───────────────────────────
 async function postJSON(url, body) {
    const r = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify(body),
    });
    return r.json();
}

// ── Generate / Regenerate Keys ─────────────────────────────────
const btnGenerate = document.getElementById('btnGenerate');
if (btnGenerate) {
    btnGenerate.addEventListener('click', async () => {
        const pin        = document.getElementById('encPin').value.trim();
        const pinConfirm = document.getElementById('encPinConfirm').value.trim();
        const genStatus  = document.getElementById('genStatus');
        const genSuccess = document.getElementById('genSuccess');
        const genError   = document.getElementById('genError');
        const statusText = document.getElementById('genStatusText');

        genSuccess.classList.add('d-none');
        genError.classList.add('d-none');

        if (pin.length < 8) {
            genError.textContent = 'PIN must be at least 8 characters.';
            genError.classList.remove('d-none');
            return;
        }
        if (pin !== pinConfirm) {
            genError.textContent = 'PINs do not match.';
            genError.classList.remove('d-none');
            return;
        }

        genStatus.classList.remove('d-none');
        btnGenerate.disabled = true;

        try {
            statusText.textContent = 'Generating RSA-2048 key pair…';
            const keyPair = await E2EEncryption.generateKeyPair();

            statusText.textContent = 'Exporting public key…';
            const publicKeyB64 = await E2EEncryption.exportPublicKey(keyPair.publicKey);

            statusText.textContent = 'Wrapping private key with PIN…';
            const { encrypted, iv, salt } = await E2EEncryption.encryptPrivateKey(keyPair.privateKey, pin);

            statusText.textContent = 'Saving to server…';
            const data = await postJSON('{{ route("insurance.encryption.setup.save") }}', {
                public_key:            publicKeyB64,
                encrypted_private_key: encrypted,
                key_iv:                iv,
                key_salt:              salt,
            });

            genStatus.classList.add('d-none');
            if (data.success) {
                genSuccess.classList.remove('d-none');
                document.getElementById('encPin').value        = '';
                document.getElementById('encPinConfirm').value = '';
            } else {
                throw new Error(data.message || 'Server error.');
            }
        } catch (err) {
            genStatus.classList.add('d-none');
            genError.textContent = 'Error: ' + err.message;
            genError.classList.remove('d-none');
        } finally {
            btnGenerate.disabled = false;
        }
    });
}

// ── Show regenerate form toggle ────────────────────────────────
const btnShowRegen = document.getElementById('btnShowRegen');
if (btnShowRegen) {
    btnShowRegen.addEventListener('click', () => {
        const f = document.getElementById('regenForm');
        if (f) f.classList.toggle('d-none');
    });
}

// ── Change PIN ───────────────────────────────────────────────
const btnChangePin = document.getElementById('btnChangePin');
if (btnChangePin) {
    btnChangePin.addEventListener('click', async () => {
        const oldPin   = document.getElementById('cpOldPin').value.trim();
        const newPin   = document.getElementById('cpNewPin').value.trim();
        const newPin2  = document.getElementById('cpNewPinConfirm').value.trim();
        const cpStatus  = document.getElementById('cpStatus');
        const cpSuccess = document.getElementById('cpSuccess');
        const cpError   = document.getElementById('cpError');
        const cpText    = document.getElementById('cpStatusText');

        cpSuccess.classList.add('d-none');
        cpError.classList.add('d-none');

        if (!oldPin) { cpError.textContent = 'Enter your current PIN.'; cpError.classList.remove('d-none'); return; }
        if (newPin.length < 8) { cpError.textContent = 'New PIN must be at least 8 characters.'; cpError.classList.remove('d-none'); return; }
        if (newPin !== newPin2) { cpError.textContent = 'New PINs do not match.'; cpError.classList.remove('d-none'); return; }

        cpStatus.classList.remove('d-none');
        btnChangePin.disabled = true;

        try {
            // 1. Fetch the current encrypted private key from server
            cpText.textContent = 'Fetching private key from server…';
            const keyResp = await fetch('{{ route("insurance.encryption.private-key") }}');
            if (!keyResp.ok) throw new Error('Could not fetch private key.');
            const keyBlob = await keyResp.json();

            // 2. Decrypt with old PIN
            cpText.textContent = 'Decrypting with current PIN…';
            const privateKey = await E2EEncryption.decryptPrivateKey(
                keyBlob.encrypted_private_key, keyBlob.key_iv, keyBlob.key_salt, oldPin
            );

            // 3. Re-encrypt with new PIN
            cpText.textContent = 'Re-wrapping with new PIN…';
            const { encrypted, iv, salt } = await E2EEncryption.encryptPrivateKey(privateKey, newPin);

            // 4. Save — only the wrapped key changes, public key stays the same
            cpText.textContent = 'Saving to server…';
            const data = await postJSON('{{ route("insurance.encryption.change-pin") }}', {
                encrypted_private_key: encrypted,
                key_iv:                iv,
                key_salt:              salt,
            });

            cpStatus.classList.add('d-none');
            if (data.success) {
                cpSuccess.classList.remove('d-none');
                document.getElementById('cpOldPin').value        = '';
                document.getElementById('cpNewPin').value        = '';
                document.getElementById('cpNewPinConfirm').value = '';
            } else {
                throw new Error(data.message || 'Server error.');
            }
        } catch (err) {
            cpStatus.classList.add('d-none');
            cpError.textContent = err.message.includes('decrypt') || err.message.includes('operation')
                ? 'Wrong current PIN. Please try again.'
                : 'Error: ' + err.message;
            cpError.classList.remove('d-none');
        } finally {
            btnChangePin.disabled = false;
        }
    });
}
</script>

@endsection
