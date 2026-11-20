@extends('layouts.sparepart')
@section('content')
<div class="container margin-top-45 margin-bottom-45">
    <div class="row">
        <div class="col-md-12">
            <style>
                .profile-img-box {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    gap: 8px;
                }
                .profile-img-box img {
                    width: 140px;
                    height: 140px;
                    object-fit: cover;
                    border-radius: 10px;
                    border: 1px solid #e4e4e4;
                }
                .profile-img-label {
                    font-size: 0.85rem;
                    color: #666;
                }
                .fullscreen-modal {
                    display: none;
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background-color: rgba(0, 0, 0, 0.8);
                    justify-content: center;
                    align-items: center;
                    z-index: 1000;
                }
                .fullscreen-modal img {
                    max-width: 100%;
                    max-height: 100%;
                    object-fit: contain;
                }
                .close-modal {
                    position: absolute;
                    top: 20px;
                    right: 20px;
                    color: white;
                    font-size: 30px;
                    background: none;
                    border: none;
                    cursor: pointer;
                    z-index: 1001;
                }
            </style>

            <!-- Counters -->
            <div class="row margin-bottom-30">
                <div class="col-xl-12">
                    <div class="counters-container">
                        <div class="single-counter">
                            <i class="icon-line-awesome-files-o"></i>
                            <div class="counter-inner">
                                <h3><span class="counter">{{\App\Models\ProformaApplication::where('application_by',auth()->id())->count()}}</span></h3>
                                <span class="counter-title">Total Applied Proformas</span>
                            </div>
                        </div>
                        <div class="single-counter">
                            <i class="icon-line-awesome-money"></i>
                            <div class="counter-inner">
                                <h3><span class="counter">{{auth()->user()->balance}}</span></h3>
                                <span class="counter-title">Total Gained Money</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
            </div>
            <script>
                setTimeout(function() {
                    document.querySelector('.alert-success')?.remove();
                }, 3000);
            </script>
            @endif

            <form action="{{ route('user.profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <!-- My Account -->
                <div class="col-xl-12 padding-left-0 padding-right-0">
                    <div class="dashboard-box margin-top-0">
                        <div class="headline">
                            <h3><i class="icon-material-outline-account-circle"></i> My Account</h3>
                        </div>
                        <div class="content with-padding padding-bottom-0">

                            <!-- Read-only documents -->
                            <div class="row margin-bottom-20">
                                <div class="col-auto">
                                    <div class="profile-img-box">
                                        @if(auth()->user()->stamp_image)
                                            <img class="stamp-image" src="{{ asset('storage/' . auth()->user()->stamp_image) }}" alt="Stamp" style="cursor: pointer;" onclick="openFullScreen('stamp-image')" />
                                        @else
                                            <img class="stamp-image" src="{{asset('assets/images/stamp.png')}}" alt="No Stamp Here" />
                                        @endif
                                        <span class="profile-img-label">Stamp</span>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <div class="profile-img-box">
                                        @if(auth()->user()->license_image)
                                            <img class="license-image" src="{{ asset('storage/' . auth()->user()->license_image) }}" alt="Business License" style="cursor: pointer;" onclick="openFullScreen('license-image')" />
                                        @else
                                            <img class="license-image" src="{{asset('assets/images/license.png')}}" alt="No License Here" />
                                        @endif
                                        <span class="profile-img-label">Business License</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Fields -->
                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="submit-field">
                                        <h5>Name</h5>
                                        <input type="text" value="{{auth()->user()->name}}" name="name" class="with-border">
                                        @error('name')
                                            <span class="text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="submit-field">
                                        <h5>Phone number</h5>
                                        <input value="{{auth()->user()->phone_number}}" type="text" name="phone_number" class="with-border">
                                        @error('phone_number')
                                            <span class="text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="submit-field">
                                        <h5>Tin Number</h5>
                                        <input value="{{auth()->user()->tin_number}}" type="text" name="tin_number" class="with-border">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Password & Security -->
                <div class="col-xl-12 padding-left-0 padding-right-0">
                    <div class="dashboard-box">
                        <div class="headline">
                            <h3><i class="icon-material-outline-lock"></i> Password &amp; Security</h3>
                        </div>
                        <div class="content with-padding">
                            <div class="row">
                                <div class="col-xl-4">
                                    <div class="submit-field">
                                        <h5>Current Password</h5>
                                        <input type="password" class="with-border" name="current_password" autocomplete="current-password">
                                        @error('current_password')
                                            <span class="text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-xl-4">
                                    <div class="submit-field">
                                        <h5>New Password</h5>
                                        <input type="password" class="with-border" name="password" autocomplete="new-password">
                                        @error('password')
                                            <span class="text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-xl-4">
                                    <div class="submit-field">
                                        <h5>Repeat New Password</h5>
                                        <input type="password" class="with-border" name="password_confirmation" autocomplete="new-password">
                                        @error('password_confirmation')
                                            <span class="text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Save -->
                <div class="col-xl-12 padding-left-0 padding-right-0">
                    <button type="submit" class="button radius-30 ripple-effect big margin-top-30">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Fullscreen Modal -->
<div id="fullscreenModal" class="fullscreen-modal">
    <button class="close-modal" type="button" onclick="closeFullScreen()">×</button>
    <img id="fullscreenImage" src="" alt="Full Screen" />
</div>

<script>
    function openFullScreen(imageClass) {
        var image = document.querySelector('.' + imageClass);
        if (!image || !image.src) return;
        document.getElementById('fullscreenImage').src = image.src;
        document.getElementById('fullscreenModal').style.display = 'flex';
    }

    function closeFullScreen() {
        document.getElementById('fullscreenModal').style.display = 'none';
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
@endsection
