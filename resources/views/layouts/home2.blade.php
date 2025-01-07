<!DOCTYPE html>
<html lang="en">
<head>

	<!-- Meta -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, minimal-ui, viewport-fit=cover">
	<meta name="theme-color" content="#2196f3">
    <meta name="description" content="Sistem Elektronik Absensi Rumah Sakit PKU Muhammadiyah Sukoharjo" />
    <meta name="keywords" content="absensi, simrsmu, sistem absensi, pkuskh, rspkuskh, sistem pku, sistem absensi rumah sakit, rumah sakit pku, pku muhammadiyah sukoharjo, absensi pku sukoharjo, absen rs pku sukoharjo">
    <meta name="author" content="Yussuf Faisal" />
    <meta name="robots" content="" />
	<meta property="og:title" content="" />
	<meta property="og:description" content="" />
	<meta property="og:image" content=""/>
	<meta name="format-detection" content="telephone=no">

    <!-- Favicons Icon -->
    <link rel="shortcut icon" href="{{ asset('images/logo/logo_new_light.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('images/logo/logo_new_light.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('images/logo/logo_new_light.png') }}">

    <!-- Title -->
	<title>Capture | E-Absensi</title>

    <!-- Stylesheets -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/swiper/swiper-bundle.min.css') }}">

    <!-- Tabler Icons -->
    <link rel="stylesheet" href="{{ asset('fonts/tabler-icons.min.css') }}"><!-- [Feather Icons] https://feathericons.com -->
    <link rel="stylesheet" href="{{ asset('fonts/feather.css') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&amp;display=swap" rel="stylesheet">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

    <!-- Other CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.5/dist/sweetalert2.min.css" rel="stylesheet">

    <!-- Initialize JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>
    <script src="{{ asset('js/jquery.min.js') }}"></script>

</head>
<body>
<div class="page-wraper">

    <div id="preloader">
        <div class="spinner"></div>
    </div>

	<!-- Header -->
    <header class="header">
        <div class="main-bar">
            <div class="container">
                <div class="header-content">
                    <div class="left-content">
                        <a href="{{ route("dashboard") }}" class="back-btn">
                            <svg width="18" height="18" viewBox="0 0 10 16" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M9.03033 0.46967C9.2966 0.735936 9.3208 1.1526 9.10295 1.44621L9.03033 1.53033L2.561 8L9.03033 14.4697C9.2966 14.7359 9.3208 15.1526 9.10295 15.4462L9.03033 15.5303C8.76406 15.7966 8.3474 15.8208 8.05379 15.6029L7.96967 15.5303L0.96967 8.53033C0.703403 8.26406 0.679197 7.8474 0.897052 7.55379L0.96967 7.46967L7.96967 0.46967C8.26256 0.176777 8.73744 0.176777 9.03033 0.46967Z" fill="#a19fa8"/>
							</svg>
                        </a>
                    </div>
                    <div class="mid-content">
                        {{-- PAGE TITLE --}}
                        <h5 class="mb-0"></h5>
                    </div>
                    <div class="right-content">
                        <a href="javascript:void(0);" class="menu-toggler">
							<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path opacity="0.4" d="M16.0755 2H19.4615C20.8637 2 22 3.14585 22 4.55996V7.97452C22 9.38864 20.8637 10.5345 19.4615 10.5345H16.0755C14.6732 10.5345 13.537 9.38864 13.537 7.97452V4.55996C13.537 3.14585 14.6732 2 16.0755 2Z" fill="#a19fa8"/>
								<path fill-rule="evenodd" clip-rule="evenodd" d="M4.53852 2H7.92449C9.32676 2 10.463 3.14585 10.463 4.55996V7.97452C10.463 9.38864 9.32676 10.5345 7.92449 10.5345H4.53852C3.13626 10.5345 2 9.38864 2 7.97452V4.55996C2 3.14585 3.13626 2 4.53852 2ZM4.53852 13.4655H7.92449C9.32676 13.4655 10.463 14.6114 10.463 16.0255V19.44C10.463 20.8532 9.32676 22 7.92449 22H4.53852C3.13626 22 2 20.8532 2 19.44V16.0255C2 14.6114 3.13626 13.4655 4.53852 13.4655ZM19.4615 13.4655H16.0755C14.6732 13.4655 13.537 14.6114 13.537 16.0255V19.44C13.537 20.8532 14.6732 22 16.0755 22H19.4615C20.8637 22 22 20.8532 22 19.44V16.0255C22 14.6114 20.8637 13.4655 19.4615 13.4655Z" fill="#a19fa8"/>
							</svg>
						</a>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- Header End -->

    @include('inc.home.sidebar')

    <!-- Page Content -->
    <div class="page-content bottom-content">
        <div class="container">

            @yield('content')

        </div>
    </div>
    <!-- Page Content End-->

    @include('inc.home.menubar')
    @include('inc.home.themecolor')

	<!-- PWA Offcanvas --> <!-- BUTTON ADD TO HOME SCREEN -->
	{{-- <div class="offcanvas offcanvas-bottom pwa-offcanvas">
		<div class="container">
			<div class="offcanvas-body small">
				<img class="logo" src="assets/images/icon.png" alt="">
				<h5 class="title">Jobie on Your Home Screen</h5>
				<p class="pwa-text">Install Jobie job portal mobile app template to your home screen for easy access, just like any other app</p>
				<a href="javascrpit:void(0);" class="btn btn-sm btn-secondary pwa-btn">Add to Home Screen</a>
				<a href="javascrpit:void(0);" class="btn btn-sm pwa-close light btn-danger ms-2">Maybe later</a>
			</div>
		</div>
	</div>
	<div class="offcanvas-backdrop pwa-backdrop"></div> --}}
	<!-- PWA Offcanvas End -->

</div>
<!--**********************************
    Scripts
***********************************-->
{{-- <script src="index.js" defer></script> --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.js"/> --}}
{{-- <script src="assets/js/jquery.js"></script> --}}
{{-- <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script> --}}
<script src="{{ asset('assets/js/settings.js') }}"></script>
<script src="{{ asset('assets/js/custom.js') }}"></script>
<script src="{{ asset('assets/js/dz.carousel.js') }}"></script><!-- Swiper -->
<script src="{{ asset('assets/vendor/swiper/swiper-bundle.min.js') }}"></script><!-- Swiper -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.5/dist/sweetalert2.all.min.js"></script>
</body>
</html>
