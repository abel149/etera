@extends('layouts.authentication')

@section('title', 'Sign Up — etera')

@section('branding')
    <img src="{{ asset('assets/images/transparent.svg') }}" class="etera-auth-logo" alt="etera">
    <h2 class="etera-heading etera-heading-lg" style="text-align:center; margin-bottom: 0.5rem;">
        Join the etera Network
    </h2>
    <p class="etera-subtext" style="text-align:center; max-width: 360px; color: rgba(255,255,255,0.85);">
        Choose the account type that best describes your business to get started.
    </p>

    @include('partials.brand-globe')
@endsection

@section('content')
<div id="signup-app"></div>

<script>
    window.__ETERA__  = {
        logoUrl: @json(asset('assets/images/transparent.svg')),
        signupBusinessOwnerUrl: @json(route('signup.business-owner')),
        signupGarageSparepartUrl: @json(route('signup.garage-sparepart')),
        loginUrl: '/login',
    };
</script>

@verbatim
<script type="text/babel">
    const { useState, useEffect } = React;

    function RoleCard({ icon, title, description, href, delay }) {
        const [visible, setVisible] = useState(false);

        useEffect(() => {
            const timer = setTimeout(() => setVisible(true), delay);
            return () => clearTimeout(timer);
        }, [delay]);

        return (
            <a
                href={href}
                className="etera-role-card"
                style={{
                    opacity: visible ? 1 : 0,
                    transform: visible ? 'translateY(0)' : 'translateY(30px)',
                    transition: 'all 0.5s cubic-bezier(0.4, 0, 0.2, 1)',
                }}
            >
                <div className="role-icon">{icon}</div>
                <h5>{title}</h5>
                <p>{description}</p>
            </a>
        );
    }

    function SignupPage() {
        const data = window.__ETERA__ ;

        return (
            <div style={{ animation: 'etera-fade-in 0.6s ease-out' }}>
                <div style={{ textAlign: 'center', marginBottom: '2rem' }}>
                    <img src={data.logoUrl} alt="etera" style={{ maxWidth: '100px', marginBottom: '1rem' }} className="d-xl-none" />
                    <h2 className="etera-heading" style={{ fontSize: '1.5rem', marginBottom: '0.5rem' }}>
                        Create an Account
                    </h2>
                    <p className="etera-subtext">Select your registration type</p>
                </div>

                <div className="etera-role-grid">
                    <RoleCard
                        icon={<i className="bx bx-briefcase-alt"></i>}
                        title="Others"
                        description="Register as an individual business owner or general user."
                        href={data.signupBusinessOwnerUrl}
                        delay={100}
                    />
                    <RoleCard
                        icon={<i className="bx bx-wrench"></i>}
                        title="Garage / Spare Part Shop"
                        description="Register your auto repair garage service or auto parts sales business."
                        href={data.signupGarageSparepartUrl}
                        delay={250}
                    />
                </div>

                <div style={{ textAlign: 'center', marginTop: '2rem' }}>
                    <p className="etera-subtext" style={{ fontSize: '0.9rem' }}>
                        Already have an account?{' '}
                        <a href={data.loginUrl} className="etera-link">Sign in here</a>
                    </p>
                </div>
            </div>
        );
    }

    // Mount
    const root = document.getElementById('signup-app');
    if (root) {
        ReactDOM.createRoot(root).render(<SignupPage />);
    }
</script>
@endverbatim
@endsection