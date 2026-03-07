<!doctype html>
<html lang="en" class="light">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<link rel="icon" href="{{asset('favicon.ico')}}" type="image/x-icon"/>
	<link rel="icon" href="{{asset('assets/images/transparent.jpg')}}" type="image/jpeg"/>
	<link rel="stylesheet" href="{{asset('assets/plugins/notifications/css/lobibox.min.css')}}" />
	<link href="{{asset('assets/plugins/vectormap/jquery-jvectormap-2.0.2.css')}}" rel="stylesheet"/>
	<link href="{{asset('assets/plugins/simplebar/css/simplebar.css')}}" rel="stylesheet" />
	<link href="{{asset('assets/plugins/Drag-And-Drop/dist/imageuploadify.min.css')}}" rel="stylesheet" />
	<link href="{{asset('assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css')}}" rel="stylesheet" />
	<link href="{{asset('assets/plugins/metismenu/css/metisMenu.min.css')}}" rel="stylesheet"/>
	<link href="{{asset('assets/plugins/bs-stepper/css/bs-stepper.css')}}" rel="stylesheet" />
	<link href="{{asset('assets/css/pace.min.css')}}" rel="stylesheet"/>
	<script src="{{asset('assets/js/pace.min.js')}}"></script>
	<link href="{{asset('assets/css/bootstrap.min.css')}}" rel="stylesheet">
	<link href="{{asset('assets/css/bootstrap-extended.css')}}" rel="stylesheet">
	<link rel="stylesheet" href="{{asset('assets/plugins/select2/css/select2.min.css')}}" />

	<link rel="stylesheet" href="{{asset('assets/plugins/select2/css/select2-bootstrap-5-theme.min.css')}}" />
	<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
	<link href="{{asset('assets/css/app.css')}}" rel="stylesheet">
	<link href="{{asset('assets/css/icons.css')}}" rel="stylesheet">
	<link rel="stylesheet" href="{{asset('assets/css/dark-theme.css')}}"/>
	<link rel="stylesheet" href="{{asset('assets/css/semi-dark.css')}}"/>
	<link rel="stylesheet" href="{{asset('assets/css/header-colors.css')}}"/>

	<link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet" />
  	<link href="https://unpkg.com/filepond-plugin-file-preview/dist/filepond-plugin-file-preview.min.css" rel="stylesheet">
	<link href="https://unpkg.com/filepond-plugin-image-edit/dist/filepond-plugin-image-edit.css" rel="stylesheet"/>

	<style type="text/css">
		/* ============================================
		   BUSINESS-OWNER — BLACK TEXT OVERRIDES
		   (Admin-matching white/green theme via green-theme partial)
		   ============================================ */

		/* Force black text across all elements */
		body, .wrapper, .page-wrapper, .page-content { color: #1a1a1a !important; }
		h1, h2, h3, h4, h5, h6 { color: #1a1a1a !important; }
		p { color: #333 !important; }
		label, .form-label { color: #1a1a1a !important; }
		.form-control, .form-select { color: #1a1a1a !important; }
		.card, .card-body, .card-header { color: #1a1a1a !important; }
		.table, .table th, .table td { color: #1a1a1a !important; }
		.text-dark { color: #1a1a1a !important; }
		.text-muted { color: #555 !important; }
		.dropdown-item { color: #333 !important; }
		.dropdown-item:hover, .dropdown-item:focus { color: #1a1a1a !important; }
		.modal-content, .modal-body { color: #1a1a1a !important; }
		.accordion-button { color: #1a1a1a !important; }
		.accordion-body { color: #333 !important; }
		.user-name, .user-info p { color: #1a1a1a !important; }
		.menu-title { color: inherit !important; }
		.msg-name { color: #1a1a1a !important; }
		.msg-info { color: #555 !important; }

		/* Custom legacy */
		.bg-amber { background-color: #FFE500; }
		.bg-green { background-color: #29cc52; }
		.bg-dark-amber { background-color: #d6ba06; }
		.bg-dark-green { background-color: #068f28; }
		.bg-dark-primary { background-color: #3B5998; }
		.circle { height: 80px; width: 80px; }
	</style>
	<title>ETERA - User</title>
@include('partials.green-theme')
</head>

<body>
	<div class="wrapper">
		<div class="sidebar-wrapper" data-simplebar="true">
			<div class="sidebar-header">
				<div>

					<img src="{{asset('assets/images/transparent.svg')}}" class="logo-text" style="max-width: 7.5rem;" alt="ETERA">

				</div>
				<div class="toggle-icon ms-auto"><i class='bx bx-arrow-back'></i>
				</div>
			 </div>
			<ul class="metismenu" id="menu">
				<li>
					<a href="/business-owner">
						<div class="parent-icon"><i class='bx bx-home-alt'></i>
						</div>
						<div class="menu-title">Dashboard</div>
					</a>
				</li>
				<li>
					<a href="/business-owner/create-file">
						<div class="parent-icon"><i class="bx bx-message-square-edit"></i>
						</div>
						<div class="menu-title">Request Proforma</div>
					</a>
				</li>
				<li>
					<a href="/business-owner/received-proformas">
						<div class="parent-icon"><i class="bx bx-file"></i>
						</div>
                        <div class="menu-title">Received Proforma 
                            @if(auth()->user()->getReceivedProformasCount() > 0)
                                <span class="badge bg-primary ms-2">{{ auth()->user()->getReceivedProformasCount() }}</span>
                            @endif
                            @if(auth()->user()->getReturnedFromAdminCount() > 0)
                                <span class="badge bg-danger ms-2">{{ auth()->user()->getReturnedFromAdminCount() }}</span>
                            @endif
                        </div>
					</a>
				</li>
			</ul>
			</div>
		<header>
			<div class="app-container p-2 my-2"></div>
			<div class="topbar d-flex align-items-center">
				<nav class="navbar navbar-expand gap-3">
					<div class="mobile-toggle-menu"><i class='bx bx-menu'></i>
					</div>

					<div class="top-menu ms-auto">
						<ul class="navbar-nav align-items-center gap-1">

							<li class="nav-item dark-mode d-none d-sm-flex">
								<a class="nav-link dark-mode-icon" href="javascript:;"><i class='bx bx-moon'></i>
								</a>
							</li>

							<li class="nav-item dropdown dropdown-large">
								<a class="nav-link dropdown-toggle dropdown-toggle-nocaret position-relative" href="#" data-bs-toggle="dropdown"><span class="alert-count" style="{{ auth()->user()->unreadNotifications->count() == 0 ? 'display:none' : '' }}">{{auth()->user()->unreadNotifications->count()}}</span>
									<i class='bx bx-bell'></i>
								</a>
								<div class="dropdown-menu dropdown-menu-end">
									<a href="javascript:;">
										<div class="msg-header">
											<p class="msg-header-title">Notifications</p>
											<p class="msg-header-badge">{{auth()->user()->unreadNotifications->count()}} New</p>
										</div>
									</a>
									<div class="header-notifications-list">
                  @forelse(auth()->user()->unreadNotifications->take(10) as $notification)
										<a class="dropdown-item" href="javascript:;" data-notification-id="{{ $notification->id }}">
											<div class="d-flex align-items-center">
												<div class="user-online">
													<span style="font-size:24px;">🔔</span>
												</div>
												<div class="flex-grow-1 ms-2">
													<h6 class="msg-name">{{ $notification->data['file_number'] ?? 'Notification' }}<span class="msg-time float-end">{{ $notification->created_at->diffForHumans() }}</span></h6>
													<p class="msg-info">{{ $notification->data['message'] ?? 'New notification' }}</p>
												</div>
											</div>
										</a>
                  @empty
										<div class="text-center p-3 text-muted">No new notifications</div>
                  @endforelse
									</div>
									<a href="javascript:;">
										<div class="text-center msg-footer">
											<button id="mark-all-read-btn" onclick="markAllNotificationsRead()" @if(auth()->user()->unreadNotifications->count() == 0) disabled @endif class="btn btn-primary w-100">Mark All As Read</button>
										</div>
									</a>
										</div>
									</a>
								</div>
							</li>
							<li class="nav-item dropdown dropdown-large">



									<div class="header-message-list">

									</div>


							</li>
						</ul>
					</div>
					<div class="user-box dropdown px-3">
						<a class="d-flex align-items-center nav-link dropdown-toggle gap-3 dropdown-toggle-nocaret" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
							<img src="{{asset('assets/images/avatars/avatar-9.jpg')}}" class="user-img" alt="user avatar">
							<div class="user-info">
								<p class="user-name mb-0">{{auth()->user()->name}}</p>
								<p class="designattion mb-0">User</p>
							</div>
						</a>
						<ul class="dropdown-menu dropdown-menu-end">
							<li><a class="dropdown-item d-flex align-items-center" href="/business-owner/profile"><i class="bx bx-user fs-5"></i><span>Profile</span></a>
							</li>
							

							<li>
								<div class="dropdown-divider mb-0"></div>
							</li>
<li><form action="{{route('logout')}}" method="POST">
							        @csrf
							        @method('DELETE')
							    <button class="dropdown-item d-flex align-items-center" ><i class="bx bx-log-out-circle"></i><span>Logout</span></button>
							    </form>
							</li>
						</ul>
					</div>
				</nav>
			</div>
		</header>
		<div class="page-wrapper">
            <div class="page-content">
				@yield('content')
				
				</div>
		</div>
		<div class="overlay toggle-icon"></div>
		<a href="javaScript:;" class="back-to-top"><i class='bx bxs-up-arrow-alt'></i></a>
		
		<footer class="page-footer text-center">
    <p class="mb-1">
        © <script>document.write(new Date().getFullYear())</script>. All rights reserved.
    </p>

    <p class="mb-0">
        <!--Made by -->
        <!--<a href="https://www.primetechplc.com" target="_blank" rel="noopener">-->
        <!--    Prime Software-->
        <!--</a> -->
        <!--in collaboration with <strong>Beemnet Abraham</strong>.-->
    </p>
</footer>
	</div>
	<div class="modal" id="SearchModal" tabindex="-1">
		<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-md-down">
		  <div class="modal-content">
			<div class="modal-header gap-2">
			  <div class="position-relative popup-search w-100">
				<input class="form-control form-control-lg ps-5 border border-3 border-primary" type="search" placeholder="Search">
				<span class="position-absolute top-50 search-show ms-3 translate-middle-y start-0 top-50 fs-4"><i class='bx bx-search'></i></span>
			  </div>
			  <button type="button" class="btn-close d-md-none" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="search-list">
				   <p class="mb-1">Html Templates</p>
				   <div class="list-group">
					  <a href="javascript:;" class="list-group-item list-group-item-action active align-items-center d-flex gap-2 py-1"><i class='bx bxl-angular fs-4'></i>Best Html Templates</a>
					  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-vuejs fs-4'></i>Html5 Templates</a>
					  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-magento fs-4'></i>Responsive Html5 Templates</a>
					  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-shopify fs-4'></i>eCommerce Html Templates</a>
				   </div>
				   <p class="mb-1 mt-3">Web Designe Company</p>
				   <div class="list-group">
					  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-windows fs-4'></i>Best Html Templates</a>
					  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-dropbox fs-4' ></i>Html5 Templates</a>
					  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-opera fs-4'></i>Responsive Html5 Templates</a>
					  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-wordpress fs-4'></i>eCommerce Html Templates</a>
				   </div>
				   <p class="mb-1 mt-3">Software Development</p>
				   <div class="list-group">
					  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-mailchimp fs-4'></i>Best Html Templates</a>
					  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-zoom fs-4'></i>Html5 Templates</a>
					  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-sass fs-4'></i>Responsive Html5 Templates</a>
					  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-vk fs-4'></i>eCommerce Html Templates</a>
				   </div>
				   <p class="mb-1 mt-3">Online Shoping Portals</p>
				   <div class="list-group">
					  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-slack fs-4'></i>Best Html Templates</a>
					  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-skype fs-4'></i>Html5 Templates</a>
					  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-twitter fs-4'></i>Responsive Html5 Templates</a>
					  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-vimeo fs-4'></i>eCommerce Html Templates</a>
				   </div>
				</div>
			</div>
		  </div>
		</div>
	  </div>
	<div class="switcher-wrapper">
		<div class="switcher-btn"> <i class='bx bx-cog bx-spin'></i>
		</div>
		<div class="switcher-body">
			<div class="d-flex align-items-center">
				<h5 class="mb-0 text-uppercase">Theme Customizer</h5>
				<button type="button" class="btn-close ms-auto close-switcher" aria-label="Close"></button>
			</div>
			<hr/>
			<h6 class="mb-0">Theme Styles</h6>
			<hr/>
			<div class="d-flex align-items-center justify-content-between">
				<div class="form-check">
					<input class="form-check-input" type="radio" name="flexRadioDefault" id="lightmode" checked>
					<label class="form-check-label" for="lightmode">Light</label>
				</div>
				<div class="form-check">
					<input class="form-check-input" type="radio" name="flexRadioDefault" id="darkmode">
					<label class="form-check-label" for="darkmode">Dark</label>
				</div>
				<div class="form-check">
					<input class="form-check-input" type="radio" name="flexRadioDefault" id="semidark">
					<label class="form-check-label" for="semidark">Semi Dark</label>
				</div>
			</div>
			<hr/>
			<div class="form-check">
				<input class="form-check-input" type="radio" id="minimaltheme" name="flexRadioDefault">
				<label class="form-check-label" for="minimaltheme">Minimal Theme</label>
			</div>
			<hr/>
			<h6 class="mb-0">Header Colors</h6>
			<hr/>
			<div class="header-colors-indigators">
				<div class="row row-cols-auto g-3">
					<div class="col">
						<div class="indigator headercolor1" id="headercolor1"></div>
					</div>
					<div class="col">
						<div class="indigator headercolor2" id="headercolor2"></div>
					</div>
					<div class="col">
						<div class="indigator headercolor3" id="headercolor3"></div>
					</div>
					<div class="col">
						<div class="indigator headercolor4" id="headercolor4"></div>
					</div>
					<div class="col">
						<div class="indigator headercolor5" id="headercolor5"></div>
					</div>
					<div class="col">
						<div class="indigator headercolor6" id="headercolor6"></div>
					</div>
					<div class="col">
						<div class="indigator headercolor7" id="headercolor7"></div>
					</div>
					<div class="col">
						<div class="indigator headercolor8" id="headercolor8"></div>
					</div>
				</div>
			</div>
			<hr/>
			<h6 class="mb-0">Sidebar Colors</h6>
			<hr/>
			<div class="header-colors-indigators">
				<div class="row row-cols-auto g-3">
					<div class="col">
						<div class="indigator sidebarcolor1" id="sidebarcolor1"></div>
					</div>
					<div class="col">
						<div class="indigator sidebarcolor2" id="sidebarcolor2"></div>
					</div>
					<div class="col">
						<div class="indigator sidebarcolor3" id="sidebarcolor3"></div>
					</div>
					<div class="col">
						<div class="indigator sidebarcolor4" id="sidebarcolor4"></div>
					</div>
					<div class="col">
						<div class="indigator sidebarcolor5" id="sidebarcolor5"></div>
					</div>
					<div class="col">
						<div class="indigator sidebarcolor6" id="sidebarcolor6"></div>
					</div>
					<div class="col">
						<div class="indigator sidebarcolor7" id="sidebarcolor7"></div>
					</div>
					<div class="col">
						<div class="indigator sidebarcolor8" id="sidebarcolor8"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-exif-orientation/dist/filepond-plugin-image-exif-orientation.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-edit/dist/filepond-plugin-image-edit.js"></script>
<script src="https://unpkg.com/filepond@^4/dist/filepond.js"></script>

<script>
  FilePond.registerPlugin(
    FilePondPluginFileValidateType,
    FilePondPluginImageExifOrientation,
    FilePondPluginImagePreview,
    FilePondPluginImageEdit
  );

  document.addEventListener('DOMContentLoaded', function() {
    // The FilePond input element should have the id="image"
    const imageInput = document.querySelector('#image');

    if (imageInput) {
      // Fetch the CSRF token from the meta tag or hidden input provided by @csrf
      const csrfToken = document.querySelector('input[name="_token"]').value;

      FilePond.create(imageInput, {
        allowMultiple: true,
        credits: false,
        imageResizeMode: 'contain',
        imagePreviewMaxFileSize: '3MB',
        acceptedFileTypes: ['image/*'], // Image restriction applied
        server: {
          process: '/upload/image', // Assumed image upload route
          revert: '/delete',
          headers: {
            'X-CSRF-TOKEN': csrfToken,
          }
        }
      });
    }
  });
</script>

	<script src="{{asset('assets/js/bootstrap.bundle.min.js')}}"></script>
	<script src="{{asset('assets/js/jquery.min.js')}}"></script>
	<script src="{{asset('assets/plugins/simplebar/js/simplebar.min.js')}}"></script>
	<script src="{{asset('assets/plugins/metismenu/js/metisMenu.min.js')}}"></script>
	<script src="{{asset('assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js')}}"></script>
	<script src="{{asset('assets/plugins/bs-stepper/js/bs-stepper.min.js')}}"></script>
	<script src="{{asset('assets/plugins/bs-stepper/js/main.js')}}"></script>
	<script src="{{asset('assets/plugins/form-repeater/repeater.js')}}"></script>
	<script src="{{asset('assets/plugins/notifications/js/lobibox.min.js')}}"></script>
	<script src="{{asset('assets/plugins/notifications/js/notifications.min.js')}}"></script>
	<script src="{{asset('assets/plugins/notifications/js/notification-custom-script.js')}}"></script>
	<script src="{{asset('assets/plugins/Drag-And-Drop/dist/imageuploadify.min.js')}}"></script>
	<script src="{{asset('assets/plugins/repeater/jquery.repeater.min.js')}}"></script>
	<script>
		$(document).ready(function () {
			$('#image-uploadify').imageuploadify();
		})
	</script>
	<script>
        /* Create Repeater */
        $("#repeater").createRepeater({
            showFirstItemToDefault: true,
        });
    </script>
	<script>
		$(document).ready(function () {
			$("#show_hide_password a").on('click', function (event) {
				event.preventDefault();
				if ($('#show_hide_password input').attr("type") == "text") {
					$('#show_hide_password input').attr('type', 'password');
					$('#show_hide_password i').addClass("bx-hide");
					$('#show_hide_password i').removeClass("bx-show");
				} else if ($('#show_hide_password input').attr("type") == "password") {
					$('#show_hide_password input').attr('type', 'text');
					$('#show_hide_password i').removeClass("bx-hide");
					$('#show_hide_password i').addClass("bx-show");
				}
			});
		});
	</script>
	<script src="{{asset('assets/js/app.js')}}"></script>
	<script src="https://unpkg.com/feather-icons"></script>
	<script>
		feather.replace()
	</script>
	<script>
	// Auto-dismiss success flash messages after 5 seconds
	document.addEventListener('DOMContentLoaded', function(){
		const alerts = document.querySelectorAll('.alert.alert-success');
		if (!alerts) return;
		setTimeout(() => {
			alerts.forEach(el => {
				el.style.transition = 'opacity .4s ease';
				el.style.opacity = '0';
				setTimeout(() => el.remove(), 400);
			});
		}, 5000);
	});
	</script>
@include('partials.etera-scripts')
@include('partials.notification-polling')
@stack('scripts')
</body>

</html>