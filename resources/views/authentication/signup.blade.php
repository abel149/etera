@extends('layouts.authentication')

@section('title', 'Sign Up — etera')

@section('styles')
    <style>
        :root {
            --etera-violet: #6d28d9;
            --etera-violet-dark: #4c1d95;
            --etera-violet-soft: rgba(109, 40, 217, 0.10);
            --etera-violet-gradient: linear-gradient(135deg, #6d28d9 0%, #9333ea 45%, #c026d3 100%);
        }

        .etera-auth-form-side {
            background: radial-gradient(900px circle at 10% 10%, rgba(109,40,217,0.10), transparent 55%),
                        radial-gradient(900px circle at 90% 30%, rgba(147,51,234,0.10), transparent 55%),
                        linear-gradient(180deg, #ffffff 0%, #fbfaff 100%);
        }

        .etera-role-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        @media (max-width: 640px) {
            .etera-role-grid {
                grid-template-columns: 1fr;
            }
        }

        .etera-role-card {
            position: relative;
            border: 1px solid rgba(109, 40, 217, 0.20);
            background: linear-gradient(180deg, #ffffff 0%, rgba(109, 40, 217, 0.03) 100%);
            border-radius: 18px;
            padding: 18px 18px 16px;
            box-shadow: 0 10px 30px rgba(76, 29, 149, 0.10);
            overflow: hidden;
        }

        .etera-role-card::before {
            content: '';
            position: absolute;
            inset: -2px;
            background: var(--etera-violet-gradient);
            opacity: 0;
            transition: opacity 0.25s ease;
            z-index: 0;
        }

        .etera-role-card::after {
            content: '';
            position: absolute;
            inset: 1px;
            background: #fff;
            border-radius: 17px;
            z-index: 0;
        }

        .etera-role-card:hover {
            transform: translateY(-3px);
            border-color: rgba(109, 40, 217, 0.55);
            box-shadow: 0 16px 44px rgba(76, 29, 149, 0.18);
        }

        .etera-role-card:hover::before {
            opacity: 1;
        }

        .etera-role-card > * {
            position: relative;
            z-index: 1;
        }

        .etera-role-card .role-icon {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            background: var(--etera-violet-soft);
            color: var(--etera-violet);
            margin-bottom: 12px;
            font-size: 22px;
        }

        .etera-role-card h5 {
            font-size: 1.05rem;
            font-weight: 800;
            margin-bottom: 6px;
            color: #111827;
        }

        .etera-role-card p {
            margin: 0;
            color: #6b7280;
            line-height: 1.55;
            font-size: 0.92rem;
        }

        .etera-role-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 12px;
            padding: 8px 12px;
            border-radius: 999px;
            border: 1px solid rgba(109, 40, 217, 0.18);
            background: rgba(109, 40, 217, 0.06);
            color: var(--etera-violet-dark);
            font-weight: 700;
            font-size: 0.82rem;
        }

        .etera-role-pill i {
            font-size: 16px;
        }

        .etera-link {
            color: var(--etera-violet);
        }

        .etera-link:hover {
            color: var(--etera-violet-dark);
        }
    </style>
@endsection

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
                <div className="etera-role-pill">
                    <span>Continue</span>
                    <i className="bx bx-right-arrow-alt"></i>
                </div>
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