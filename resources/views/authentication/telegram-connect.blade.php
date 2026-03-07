@extends('layouts.authentication')

@section('title', 'Connect Telegram — etera')

@section('branding')
<div class="text-center">
    <img src="{{ asset('assets/images/transparent.jpg') }}" alt="etera" style="width: 120px; border-radius: 20px;" class="mb-3">
    <h2 style="color: #fff; font-weight: 700;">Connect Telegram</h2>
    <p style="color: rgba(255,255,255,0.85);">Get instant notifications on your phone</p>
</div>
@endsection

@section('content')
<div class="text-center">
    <div style="font-size: 64px; margin-bottom: 16px;">
        <i class="bx bxl-telegram" style="color: #0088cc;"></i>
    </div>

    <h4 style="font-weight: 700; margin-bottom: 8px;">Connect Your Telegram</h4>
    <p class="text-muted mb-4">
        Link your Telegram account to receive <strong>instant proforma notifications</strong> directly on your phone.
    </p>

    <div class="d-grid gap-3">
        <a href="{{ $telegramLink }}" target="_blank" class="btn btn-lg" id="connect-telegram-btn"
           style="background: linear-gradient(135deg, #0088cc, #00aaee); color: #fff; border: none; border-radius: 12px; padding: 14px; font-weight: 600; font-size: 16px; transition: all 0.3s ease;">
            <i class="bx bxl-telegram"></i> Open Telegram & Connect
        </a>

        <a href="{{ $skipUrl }}" class="btn btn-outline-secondary btn-lg" 
           style="border-radius: 12px; padding: 12px; font-weight: 500;">
            Skip for Now
        </a>
    </div>

    <div class="mt-4 p-3" style="background: rgba(0,136,204,0.1); border-radius: 12px; border: 1px solid rgba(0,136,204,0.2);">
        <small class="text-muted">
            <i class="bx bx-info-circle"></i> 
            Click the button above, then press <strong>"Start"</strong> in Telegram to link your account.
            You can always connect later from your profile.
        </small>
    </div>
</div>
@endsection
