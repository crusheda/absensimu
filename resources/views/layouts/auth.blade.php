<!DOCTYPE html>
<html lang="en">
<head>

    <!-- Meta -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, minimal-ui, viewport-fit=cover">
	<meta name="theme-color" content="#2196f3">
	<meta name="author" content="" />
    <meta name="keywords" content="" />
    <meta name="robots" content="" />
	<meta name="description" content=""/>
	<meta property="og:title" content="" />
	<meta property="og:description" content="" />
	<meta property="og:image" content=""/>
	<meta name="format-detection" content="telephone=no">

    <!-- Favicons Icon -->
	<link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/images/favicon.png') }}" />

    <!-- Title -->
	<title>E-Absensi | RS PKU Muhammadiyah Sukoharjo</title>

    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/swiper/swiper-bundle.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.css') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&amp;family=Racing+Sans+One&amp;display=swap" rel="stylesheet">

</head>
<body>
<div class="page-wraper">

    <!-- Preloader -->
	<div id="preloader">
		<div class="spinner"></div>
	</div>
    <!-- Preloader end-->

    <!-- Page Content -->
    <div class="page-content">

        @yield('content')

    </div>
    <!-- Page Content End -->

    <!-- Footer -->
    <footer class="footer fixed">
        <div class="container">
            {{-- <a href="register.html" class="btn btn-primary light btn-rounded text-primary d-block">Buat Akun</a> --}}
        </div>
    </footer>
    <!-- Footer End -->

    {{-- Theme Color --}}
    @include('inc.auth.themecolor')
</div>
<!--**********************************
    Scripts
***********************************-->
<script src="{{ asset('assets/js/jquery.js') }}"></script>
<script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/js/settings.js') }}"></script>
<script src="{{ asset('assets/js/custom.js') }}"></script>
</body>
</html>
