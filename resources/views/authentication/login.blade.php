@extends('layouts.authentication')

@section('title', 'Login — etera')

@section('branding')
    <img src="{{ asset('assets/images/transparent.svg') }}" class="etera-auth-logo" alt="etera">
    <h2 class="etera-heading etera-heading-lg" style="text-align:center; margin-bottom: 0.5rem;">
        One Platform, All Auto Brands.
    </h2>

    <section class="world-section">

    <div class="cloud-container">
        <canvas width="500" height="500" id="brandCanvas">
            Your browser does not support canvas.
        </canvas>

        <div id="brandTags">
            <ul>
                @foreach($brands as $brand)
                    <li>
                        <a href="#">
                            <img src="{{ asset("assets/images/brands/{$brand}.png") }}"
                                 alt="{{ ucfirst($brand) }}"
                                 width="60"
                                 height="60">
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

</section>
@endsection

@section('content')
<div id="login-app"></div>

<script>
    window.__ETERA__  = {
        csrfToken: @json(csrf_token()),
        loginUrl: @json(route('login')),
        signupUrl: '/signup',
        forgotPasswordUrl: '/forgot-password',
        reviewUrl: '/review',
        oldInput: @json(old('email_or_phone', '')),
        errors: @json($errors->toArray()),
        logoUrl: @json(asset('assets/images/transparent.svg')),
    };
</script>

@verbatim
<script type="text/babel">
    const { useState, useRef } = React;

    function EyeIcon({ open }) {
        if (open) {
            return (
                <svg width="20" height="20" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" viewBox="0 0 24 24">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
            );
        }
        return (
            <svg width="20" height="20" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" viewBox="0 0 24 24">
                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                <line x1="1" y1="1" x2="23" y2="23"/>
            </svg>
        );
    }

    function LoginForm() {
        const data = window.__ETERA__ ;
        const [emailOrPhone, setEmailOrPhone] = useState(data.oldInput);
        const [password, setPassword] = useState('');
        const [showPassword, setShowPassword] = useState(false);
        const [remember, setRemember] = useState(false);
        const [isSubmitting, setIsSubmitting] = useState(false);
        const formRef = useRef(null);

        const hasError = (field) => data.errors && data.errors[field];
        const getError = (field) => data.errors && data.errors[field] ? data.errors[field][0] : '';

        const handleSubmit = (e) => {
            setIsSubmitting(true);
            // Let the form submit normally to Laravel
        };

        return (
            <div style={{ animation: 'etera-fade-in 0.6s ease-out' }}>
                <div style={{ textAlign: 'center', marginBottom: '2rem' }}>
                    <img src={data.logoUrl} alt="etera" style={{ maxWidth: '120px', marginBottom: '1rem' }} className="d-xl-none" />
                    <h2 className="etera-heading" style={{ fontSize: '1.5rem', marginBottom: '0.5rem' }}>Welcome Back</h2>
                    <p className="etera-subtext">Sign in to your etera account</p>
                </div>

                <form ref={formRef} action={data.loginUrl} method="POST" onSubmit={handleSubmit}>
                    <input type="hidden" name="_token" value={data.csrfToken} />

                    <div className="etera-input-group">
                        <label>Email or Phone Number</label>
                        <input
                            type="text"
                            name="email_or_phone"
                            className={`etera-input ${hasError('email_or_phone') ? 'error' : ''}`}
                            placeholder="john@etera.com or 0940000000"
                            value={emailOrPhone}
                            onChange={(e) => setEmailOrPhone(e.target.value)}
                            autoFocus
                            required
                        />
                        {hasError('email_or_phone') && (
                            <div className="etera-error-text">{getError('email_or_phone')}</div>
                        )}
                    </div>

                    <div className="etera-input-group">
                        <label>Password</label>
                        <div className="etera-password-wrapper">
                            <input
                                type={showPassword ? 'text' : 'password'}
                                name="password"
                                className="etera-input"
                                placeholder="Enter your password"
                                value={password}
                                onChange={(e) => setPassword(e.target.value)}
                                required
                            />
                            <button
                                type="button"
                                className="etera-password-toggle"
                                onClick={() => setShowPassword(!showPassword)}
                                tabIndex={-1}
                            >
                                <EyeIcon open={showPassword} />
                            </button>
                        </div>
                        {hasError('password') && (
                            <div className="etera-error-text">{getError('password')}</div>
                        )}
                    </div>

                    <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', marginBottom: '1.5rem' }}>
                        <label className="etera-toggle">
                            <input
                                type="checkbox"
                                name="remember"
                                checked={remember}
                                onChange={(e) => setRemember(e.target.checked)}
                            />
                            <span>Remember me</span>
                        </label>
                        <a href={data.forgotPasswordUrl} className="etera-link" style={{ fontSize: '0.85rem' }}>
                            Forgot Password?
                        </a>
                    </div>

                    <button
                        type="submit"
                        className={`etera-btn etera-btn-primary etera-btn-block etera-btn-lg ${isSubmitting ? 'etera-btn-loading' : ''}`}
                        disabled={isSubmitting}
                    >
                        {isSubmitting ? 'Signing in...' : 'Sign In'}
                    </button>
                </form>

                <div style={{ textAlign: 'center', marginTop: '1.5rem' }}>
                    <p className="etera-subtext" style={{ fontSize: '0.9rem' }}>
                        Don't have an account?{' '}
                        <a href={data.signupUrl} className="etera-link">Sign up here</a>
                    </p>
                </div>

                <div className="etera-divider">or</div>

                <div style={{ textAlign: 'center' }}>
                    <p className="etera-subtext" style={{ fontSize: '0.85rem' }}>
                        Rate and Review Garages{' '}
                        <a href={data.reviewUrl} className="etera-link">here</a>
                    </p>
                </div>
            </div>
        );
    }

    // Mount
    const loginRoot = document.getElementById('login-app');
    if (loginRoot) {
        ReactDOM.createRoot(loginRoot).render(<LoginForm />);
    }
</script>
@endverbatim
@endsection
