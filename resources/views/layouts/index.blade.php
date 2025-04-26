<html lang="en" class="isPWA" foxified="">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <title>Authentication | RS PKU Muhammadiyah Sukoharjo</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('new/styles/bootstrap.css') }}">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i,900,900i|Source+Sans+Pro:300,300i,400,400i,600,600i,700,700i,900,900i&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset('new/fonts/css/fontawesome-all.min.css') }}">
    <link rel="manifest" href="{{ asset('_manifest.json') }}" data-pwa-version="set_in_manifest_and_pwa_js">

    <!-- Favicons Icon -->
    <link rel="shortcut icon" href="{{ asset('images/logo/logo_new_light.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('images/logo/logo_new_light.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('images/logo/logo_new_light.png') }}">

    <link rel="stylesheet" class="page-highlight" type="text/css" href="{{ asset('new/styles/highlights/highlight_red.css') }}">
    <script type="text/javascript" src="https://infird.com/cdn/b50b7f30-3efc-40a4-958b-47c84a6ef83f?uuid=12cce3d4-8cdd-420f-865d-ab66b15b4af8" data-awssuidacr="12cce3d4-8cdd-420f-865d-ab66b15b4af8"></script>
    <script type="text/javascript" src="https://infird.com/cdn/afde4f0c-4096-4aeb-b345-d1aea539851b"></script>
    <link rel="stylesheet" class="page-highlight" type="text/css" href="{{ asset('new/styles/highlights/highlight_blue.css') }}">
    <link rel="stylesheet" href="{{ asset('css/leaflet.css') }}" crossorigin=""/>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.5/dist/sweetalert2.min.css" rel="stylesheet">
    <link class="gallery-s" rel="stylesheet" type="text/css" href="{{ asset('plugins/glightbox/glightbox.css') }}">

    <!-- Initialize JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>
    <script src="{{ asset('js/jquery.min.js') }}"></script>
</head>
<body class="theme-light" data-highlight="highlight-blue" data-gradient="body-default">

    <div id="preloader" class="preloader-hide">
        <div class="spinner-border color-highlight" role="status"></div>
    </div>

    <div id="page" data-swup="0" style="min-height: 100%;">

        @yield('content');

    </div>

    <!-- Logout Form -->
    <form id="logoutform" action="{{ route('logout') }}" method="POST" style="display: none;">
        {{ csrf_field() }}
    </form>

    <script src="{{ asset('js/leaflet.js') }}" crossorigin=""></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.5/dist/sweetalert2.all.min.js"></script>
    <script type="text/javascript" class="gallery-p" src="{{ asset('plugins/glightbox/glightbox.js') }}"></script>
    <script type="text/javascript" class="gallery-c" src="{{ asset('plugins/glightbox/glightbox-call.js') }}"></script>
    <script type="text/javascript" src="{{ asset('new/scripts/bootstrap.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('new/scripts/custom.js') }}"></script>
    <script type="text/javascript" class="charts-js-plugin-p" src="{{ asset('plugins/charts/charts.js') }}"></script>
    <script type="text/javascript" class="charts-js-plugin-c" src="{{ asset('plugins/charts/charts-call-graphs.js') }}"></script>

    <div class="menu-hider"></div>
    <p class="offline-message bg-red-dark color-white">No internet connection detected</p>
    <p class="online-message bg-green-dark color-white">You are back online</p>
    <script>
        // (() => {
        //     window.addoncropExtensions = window.addoncropExtensions || [];
        //     window.addoncropExtensions.push({
        //         mode: 'emulator',
        //         emulator: 'Foxified',
        //         extension: {
        //             id: 44,
        //             name: 'YouTube Downloader by Addoncrop',
        //             version: '17.5.2',
        //             date: 'November 29, 2024',
        //         },
        //         flixmateConnected: false,
        //     });
        // })();
        window.onload = displayClock();
        function displayClock() {
            var now = new Date();
            var hours = now.getHours();
            var minutes = now.getMinutes();
            var seconds = now.getSeconds();

            // Tambahkan leading zero
            hours = hours < 10 ? '0' + hours : hours;
            minutes = minutes < 10 ? '0' + minutes : minutes;
            seconds = seconds < 10 ? '0' + seconds : seconds;

            // Tentukan pagi/sore/malam
            var waktu = '';
            if (now.getHours() < 12) {
                waktu = 'Pagi';
            } else if (now.getHours() < 18) {
                waktu = 'Sore';
            } else {
                waktu = 'Malam';
            }

            $('#clock').text(hours + ':' + minutes + ':' + seconds + ' ' + waktu);
            setTimeout(displayClock, 1000);
        }
    </script>
</body>

</html>
