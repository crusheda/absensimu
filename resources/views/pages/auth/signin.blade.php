<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <title>Authentication | RS PKU Muhammadiyah Sukoharjo</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('styles/bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('fonts/bootstrap-icons.css') }}">

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@500;600;700;800&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="manifest" href="_manifest.json">
    <meta id="theme-check" name="theme-color" content="#FFFFFF">

    <!-- Favicons Icon -->
    <link rel="shortcut icon" href="{{ asset('images/logo/logo_new_light.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('images/logo/logo_new_light.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('images/logo/logo_new_light.png') }}">
    <script src="{{ asset('js/jquery.min.js') }}"></script>
</head>

<body class="theme-light">

<div id="preloader">
    <div class="spinner-border color-highlight" role="status"></div>
</div>

<div id="page" class="" style="background: url('/images/wallpaper.jpg');background-repeat: no-repeat;background-size: 100% 100%;">

	<!-- Main Sidebar-->
        @include('inc.sidebar')
	<!-- Menu Highlights-->
        @include('inc.highlight')

    <!-- Your Page Content Goes Here-->
    <div class="page-content">
        <div class="card card-style mb-0 bg-transparent shadow-0 mx-0 rounded-0" style="height: 800px"> <!-- data-card-height="cover" -->
			<div class="card-center">
                <center><img src="{{ asset('/images/logo/logo_simrsmu_new_kop_31.png') }}" width="200" class="pt-3" alt=""></center>
				<div class="">
					<div class="content">
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <h1 class="text-center font-800 font-30 mb-2">Masuk <b class="text-primary">E-Absensi</b></h1>
                            <p class="text-center font-13 mt-n2 mb-3">Silakan masuk ke sistem menggunakan Akun <a href="https://simrsmu.com/"><b>Simrsmu</b></a></p>
                            <div class="form-custom form-label form-icon mb-3">
                                <i class="bi bi-person-circle font-14"></i>
                                <input type="text" class="form-control rounded-xs" id="c1" name="name" value="{{ old('name') }}" placeholder="Masukkan Username" autocomplete="name" required/>
                                <label for="c1" class="color-theme">Username</label>
                                <span>(Wajib)</span>
                            </div>
                            <div class="form-custom form-label form-icon mb-3">
                                <i class="bi bi-asterisk font-12"></i>
                                <input type="password" class="form-control rounded-xs" id="c2" name="password" value="{{ old('password') }}" autocomplete="current-password" placeholder="Masukkan Password" required/>
                                <label for="c2" class="color-theme">Password</label>
                                <span>(Wajib)</span>
                            </div>
                            <div class="d-flex">
                                <div>
                                    {{-- <a href="https://simrsmu.com/lupapassword" class="color-theme opacity-30 font-12">Lupa Password</a> --}}
                                    <a href="https://simrsmu.com/lupapassword" class='btn rounded-sm btn-m gradient-red text-uppercase font-700 mt-1 btn-full shadow-bg shadow-bg-s'><i class="bi bi-arrow-clockwise me-2"></i>Lupa Password</a>
                                </div>
                                <div class="ms-auto">
                                    <button type="submit" class='btn rounded-sm btn-m gradient-green text-uppercase font-700 mt-1 btn-full shadow-bg shadow-bg-s'><i class="bi bi-box-arrow-in-left me-2"></i>Masuk</button>
                                    {{-- <a href="page-register-2.html" class="color-theme opacity-30 font-12">~</a> --}}
                                </div>
                            </div>
                        </form>
					</div>
				</div>
			</div>
		</div>

    </div>
	<!-- End of Page Content-->

	<div class="offcanvas offcanvas-bottom rounded-m offcanvas-detached" id="menu-install-pwa-ios">
	   <div class="content">
			 <img src="{{ asset('images/logo/logo_new_light.png') }}" alt="img" width="80" class="rounded-l mx-auto my-4">
		  <h1 class="text-center font-800 font-20">Add E-Absensi to Home Screen</h1>
		  <p class="boxed-text-xl">
			  Install E-Absensi on your home screen, and access it just like a regular app. Open your Safari menu and tap "Add to Home Screen".
		  </p>
		   <a href="#" class="pwa-dismiss close-menu gradient-blue shadow-bg shadow-bg-s btn btn-s btn-full text-uppercase font-700  mt-n2" data-bs-dismiss="offcanvas">Maybe Later</a>
	   </div>
   </div>

   <div class="offcanvas offcanvas-bottom rounded-m offcanvas-detached" id="menu-install-pwa-android">
	   <div class="content">
		   <img src="{{ asset('images/logo/logo_new_light.png') }}" alt="img" width="80" class="rounded-m mx-auto my-4">
		   <h1 class="text-center font-700 font-20">Install E-Absensi</h1>
		   <p class="boxed-text-l">
			   Install E-Absensi to your Home Screen to enjoy a unique and native experience.
		   </p>
		   <a href="#" class="pwa-install btn btn-m rounded-s text-uppercase font-900 gradient-highlight shadow-bg shadow-bg-s btn-full">Add to Home Screen</a><br>
		   <a href="#" data-bs-dismiss="offcanvas" class="pwa-dismiss close-menu color-theme text-uppercase font-900 opacity-50 font-11 text-center d-block mt-n1">Maybe later</a>
	   </div>
   </div>

</div>
<!--End of Page ID-->

<script src="{{ asset('scripts/bootstrap.min.js') }}"></script>
<script src="{{ asset('scripts/custom.js') }}"></script>
</body>
