@extends('layouts.index')

@section('content')

<style>
    /* #map { height: 300px;}, */
    /*.webcam-selfi,*/
    /* .webcam-selfi video {
        width: auto;
        height: 150px !important;
        border-radius: 15px;
    } */
    @media (max-width: 480px) {
        .webcam-container {
            max-width: 90%;
        }

        .webcam-selfie video {
            border-radius: 10px;
        }
    }
    #webcam {
        min-width: 300px;
        min-height: 400px;
    }
    .webcam-wrapper {
        width: 100vw;
        height: 100vh;
        overflow: hidden;
        display: flex;
        justify-content: center;
        align-items: center;
        background: black;
    }
    .webcam-selfie video {
        width: 100% !important;
        height: 100% !important;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        object-fit: cover;
    }
    .webcam-container {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 10px;
        width: 100%;
        max-width: 400px;
        margin: 0 auto;
    }
    .btn-pink {
        background-color:#FE52F2 !important;
        color:white !important;
    }
    .btn-oren {
        background-color:#A35904 !important;
        color:white !important;
    }
    .btn-kuning {
        background-color:#A1AB06 !important;
        color:white !important;
    }
</style>

@include('inc.topbutton')

@include('inc.footerbar')

<div class="page-content pb-4 mt-5">

    <div class="card position-fixed w-100 rounded-0" data-card-height="350" style="height: 350px;" id="map"></div>
    <div class="card card-style bg-transparent shadow-0 rounded-0 no-click" data-card-height="250" style="height: 250px;"></div>

    <div class="card card-style mx-0 mb-3 mt-4">
        {{-- <div class="divider"></div> --}}
        <div class="divider mx-auto mt-3 bg-gray-dark opacity-30 rounded-s mb-n1" style="height:5px; width:50px;"></div>
        <div class="content mb-2">
            <div id="alerts"></div>
            <div class="d-flex">
                <div class="align-self-center">
                    {{-- <h2 class="mb-0">Table of Contents</h2> --}}
                </div>
                <div class="align-self-center ms-auto">
                    {{-- <h6 class="mb-0 opacity-30 font-13">2hr, 25min</h6> --}}
                </div>
            </div>
        </div>

        <div class="content mb-0 mt-0">
            <div id="chapter-2" class="d-flex">
                <div class="align-self-center">
                    <h1 class="pe-3 font-40 font-900 color-mint-dark"><i class="fas fa-user-clock"></i></h1>
                </div>
                <div class="align-self-center">
                    <h4 class="mb-n1">E-Absensi</h4>
                    <p class="font-10 opacity-50">Lokasi Anda <u><b><a id="meter-gps"></a></b> Meter</u> dari Lokasi Absen</p>
                </div>
                {{-- <div class="align-self-center ms-auto">
                    <i class="fa fa-camera fa-1x color-blue-dark"></i>
                </div> --}}
            </div>
            <p style="text-align: justify;">
                Foto selfi hanya digunakan sebagai bukti bahwa sudah melakukan Absensi pada Jam Kerja dan berlokasi di radius yang sudah ditetapkan (30 Meter dari area Finger)
            </p>
        </div>

        <div class="divider mt-2 mb-2"></div>

        <div class="content mt-0 mb-4">
            {{-- PHOTO --}}
            <input type="hidden" name="image" id="image-capture" class="image-tag">
            <div class="webcam-container">
                <div id="webcam" class="webcam-selfie"></div>
            </div>
        </div>

    </div>

    <div class="card card-style mt-0 mb-3">
        <div id="loading-btn"><h5 class="pt-3 text-dark"><center><i class="fa fa-sync fa-spin me-1"></i> Memuat Tombol</center></h5></div>
        <div class="content libur cammapnone m-2" id="hiddenCard">
            <div id="hiddenButton1" hidden> {{-- SELAIN ONCALL --}}
                <center>
                    <div class="btn-group" style="width:100%" id="btn-biasa" hidden>
                        <button type="button" class="btn btn-danger text-uppercase font-900 btn-m btn-full rounded-sm shadow-xl" onclick="prosesPulang()" id="btn-pulang" disabled><i class="fas fa-angle-left me-2"></i>Absen Pulang</button>
                        <a href="#" class="icon icon-m rounded-s opacity-40 color-theme ms-2 me-2 mt-2"><i class="fa fa-minus"></i></a>
                        <button type="button" class="btn btn-primary text-uppercase font-900 btn-m btn-full rounded-sm shadow-xl" onclick="prosesMasuk()" id="btn-masuk" disabled>Absen Masuk<i class="fas fa-angle-right ms-2"></i></button>
                    </div>
                </center>
            </div>
            <div id="hiddenButton2" hidden> {{-- KHUSUS ONCALL --}}
                <center>
                    <div class="btn-group" style="width:100%" id="btn-mulai" hidden>
                        <button type="button" class="btn btn-pink text-uppercase font-900 btn-m btn-full rounded-sm shadow-xl" onclick="prosesMulaiOnCall()" id="btn-oncall-mulai" disabled><i class="ti ti-vaccine"></i> Mulai On Call</button>
                        <button type="button" class="btn btn-primary text-uppercase font-900 btn-m btn-full rounded-sm shadow-xl" onclick="prosesMasuk()" id="btn-shift-mulai" disabled><i class="ti ti-plane-arrival"></i> Absen Masuk</button>
                    </div>
                    <div class="btn-group" style="width:100%" id="btn-selesai" hidden>
                        <button type="button" class="btn btn-danger text-uppercase font-900 btn-m btn-full rounded-sm shadow-xl" onclick="prosesSelesaiOnCall()" id="btn-oncall-selesai" disabled><i class="ti ti-activity"></i> Selesai On Call</button>
                        <button type="button" class="btn btn-danger text-uppercase font-900 btn-m btn-full rounded-sm shadow-xl" onclick="prosesPulang()" id="btn-shift-selesai" disabled><i class="ti ti-plane-departure"></i> Absen Pulang</button>
                    </div>
                    <div class="btn-group" style="width:100%" id="btn-mix" hidden>
                        <button type="button" class="btn btn-success text-uppercase font-900 btn-m btn-full rounded-sm shadow-xl" onclick="prosesOnCallLanjutPulang()" id="btn-oncall-shift" disabled><i class="ti ti-phone-outgoing"></i> Selesai Oncall & Absen Pulang</button>
                        <button type="button" class="btn btn-oren text-uppercase font-900 btn-m btn-full rounded-sm shadow-xl" onclick="prosesPulangLanjutOnCall()" id="btn-shift-oncall" disabled><i class="ti ti-phone-incoming"></i> Absen Pulang & Selesai Oncall</button>
                    </div>
                </center>
            </div>
            <center>
                <button type="button" class="btn btn-warning text-white text-uppercase font-900 btn-m btn-full rounded-sm shadow-xl" style="width:100%" onclick="showIjin()" id="btn-ijin" disabled hidden><i class="fas fa-stethoscope me-1"></i> Pengajuan Ijin</button>
                <div id="prosesijin" hidden>
                    <div class="input-style has-borders no-icon mt-1 mb-4">
                        <textarea id="ket_ijin" placeholder="Tuliskan Keterangan Ijin"></textarea>
                        <label for="ket_ijin" class="color-highlight">Keterangan Ijin</label>
                        <em class="mt-n3">(Wajib)</em>
                    </div>
                    <div class="btn-group" style="width:100%">
                        <button type="button" class="btn btn-dark text-uppercase font-900 btn-m btn-full rounded-sm shadow-xl" onclick="batalProsesIjin()" id="btn-batal-proses-ijin"><i class="fa fa-times me-1"></i> Batal</button>
                        <a href="#" class="icon icon-m rounded-s opacity-40 color-theme ms-2 me-2 mt-2"><i class="fa fa-minus"></i></a>
                        <button type="button" class="btn btn-info text-white text-uppercase font-900 btn-m btn-full rounded-sm shadow-xl" onclick="prosesIjin()" id="btn-proses-ijin">Kirim Surat Ijin <i class="fas fa-paper-plane ms-1"></i></button>
                    </div>
                </div>
            </center>
        </div>
    </div>
    <div class="card card-style mt-0 mb-5">
        <div class="content"><h3>Mengalami Masalah?</h3>
            <p class="color-highlight font-12 mt-n2 mb-2">Jika Anda belum dapat melakukan Absensi</p>
            <p class="mb-2">
                Silakan refresh Halaman ini atau tekan tombol di bawah ini.
            </p>
            <button onclick="refreshMap()" class="btn bg-red-dark text-uppercase font-900 btn-m btn-full rounded-sm  shadow-xl contactSubmitButton"><i class="fas fa-location-arrow me-1"></i> Refresh GPS</button>
        </div>
    </div>

</div>
{{-- <div class="page-content header-clear-medium">

    <div class="card card-style preload-img" id="map" data-card-height="150"></div>

    <div class="card card-style">
        <div class="content">
            <div class="row mb-2 mt-n2">
                <div class="col-6 text-start">
                    <h4 class="font-700 text-uppercase font-12 opacity-50 mt-1">Project Detail</h4>
                </div>
                <div class="col-6 text-end">
                    <a href="#" data-menu="menu-manage" class="font-14 color-theme icon icon-xxs"><i class="fa fa-cog fa-spin"></i></a>
                </div>
            </div>
            <div class="divider mb-3"></div>
            <div class="d-flex">
                <div class="w-35 border-right pe-3 border-blue-dark">
                    <a href="#"><img src="images/empty.png" data-src="images/pictures/faces/4s.png" width="70" class="rounded-circle preload-img"></a>
                    <h6 class="color-blue-dark font-14 font-600 mt-2 text-center">Johnatan D</h6>
                    <p class="color-blue-dark mt-n3 font-9 font-400 text-center mb-0 pt-1">Group Manager</p>
                </div>
                <div class="w-65 ps-3">
                    <h4>Web Dev Team - 1</h4>
                    <p class="color-blue-dark mt-n3 font-10 pt-1 mb-3">website-launch@domain.com</p>
                    <a href="#"><img src="images/empty.png" data-src="images/pictures/faces/1s.png" width="40" class="rounded-circle preload-img me-n3"></a>
                    <a href="#"><img src="images/empty.png" data-src="images/pictures/faces/2s.png" width="40" class="rounded-circle preload-img me-n3"></a>
                    <a href="#"><img src="images/empty.png" data-src="images/pictures/faces/3s.png" width="40" class="rounded-circle preload-img me-n3"></a>
                    <a href="#"><img src="images/empty.png" data-src="images/pictures/faces/4s.png" width="40" class="rounded-circle preload-img me-n3"></a>
                </div>
            </div>
            <div class="divider mt-4 mb-3"></div>
            <div class="d-flex">
                <div class="align-self-center w-100">
                    <h5>Website Launch</h5>
                    <div class="progress mt-2 mb-1" style="height:3px;">
                        <div class="progress-bar border-0 bg-blue-dark text-start ps-2"
                            role="progressbar" style="width: 80%"
                            aria-valuenow="10" aria-valuemin="0"
                            aria-valuemax="100">
                        </div>
                    </div>
                    <div class="row mb-0">
                        <div class="col-6 text-start">
                            <p class="mb-n1 font-12 opacity-60">Developing</p>
                        </div>
                        <div class="col-6 text-end">
                            <p class="mb-n1 font-12 opacity-60">3/7</p>
                        </div>
                    </div>
                </div>
            </div>
            <a href="#" data-menu="menu-manage" class="btn btn-full btn-sm rounded-sm bg-highlight font-700 text-uppercase mt-3">Project Settings</a>
        </div>
    </div>

</div> --}}

@include('inc.setting');

<script>
    var map;
    let isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
    let facing = isMobile ? 'user' : 'environment'; // default: kamera depan di HP, belakang di desktop
    // var setcam = 1;
    $(document).ready(function() {
        Webcam.set({
            // width: window.innerWidth,
            // height: window.innerHeight,
            width: 640,
            height: 480,
            image_format: 'jpeg',
            jpeg_quality: 90,
            constraints: {
                facingMode: facing,
                width: { ideal: 640 },
                height: { ideal: 480 }
            }
        });
        Webcam.attach( '#webcam' );

        refreshMap();
        // validation();
    })

    function refreshMap() {
        const x = document.getElementById("lokasi");
        // console.log(map);
        if (map) {
            map.remove();
        }
        if (navigator.geolocation) {
            // navigator.geolocation.getCurrentPosition(showPosition);
            var lat,long;// Creating a promise out of the function
            let getLocationPromise = new Promise((resolve, reject) => {
                if(navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function (position) {
                        lat = position.coords.latitude
                        long = position.coords.longitude
                        map = L.map('map',{
                            keyboard: false,
                            zoomControl: true,
                            boxZoom: true,
                            doubleClickZoom: false,
                            tap: false,
                            touchZoom: false,
                            enableHighAccuracy: true,
                            scrollWheelZoom: false,
                            dragging: true,
                            doubleClickZoom: false,
                        }).setView([position.coords.latitude, position.coords.longitude], 18);
                        // center: [51.505, -0.09],
                        // zoom: 13,
                        // minZoom: 13,
                        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 19,
                            // attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                        }).addTo(map);

                        // Titik Lokasi GPS
                        var marker = new L.Marker([position.coords.latitude, position.coords.longitude]);
                        marker.addTo(map).bindPopup("Titik Lokasi Anda").openPopup();

                        // Radius
                        var circle = L.circle(["{{ $list['profil_rs']->coord_lat }}","{{ $list['profil_rs']->coord_long }}"], { // RSPKUSKH COORD : -7.677851238136329, 110.83968584828327
                            color: 'red',
                            fillColor: '#f03',
                            fillOpacity: 0.5,
                            radius: 30 // RADIUS 30 M
                        }).addTo(map);

                        $("#lokasi").val(position.coords.latitude + ", " + position.coords.longitude);
                        // console.log(position.coords.latitude, position.coords.longitude) //test...

                        // console.log("LATLONG1: ", lat, long) //test...

                        // Resolving the values which I need
                        resolve({latitude: lat,
                                longitude: long})
                    })

                } else {
                    reject("Browser Anda tidak support Geolocation API. Silakan mengganti browser!")
                }
            })

            // navigator.permissions.query({ name: 'geolocation' }).then(res => {
            //     if(res.state != "granted"){ // IZIN MAP / GPS DITOLAK
            //         pesanError('Anda belum mengaktifkan izin Lokasi untuk Absensi. Silakan Aktifkan terlebih dahulu lalu lakukan Refresh Kembali dan pastikan Anda menggunakan Browser yang sesuai :<br><ul><li><u>Android = Firefox</u></li><li><u>IOS = Chrome/Safari</u></li></ul>');
            //         $('.cammapnone').prop('hidden',true);
            //         $('#btn-reload-page').prop('hidden',false);
            //         Webcam.reset('#webcam');
            //     } else { // MAP / GPS DIIZINKAN
            //         $('.cammapnone').prop('hidden',false);
            //         $('#btn-reload-page').prop('hidden',true);
                    getLocationPromise.then((location) => {
                        console.log('latitude : '+lat);
                        console.log('longitude : '+long);
                        // ATTENTION
                        var save = new FormData();
                        save.append('lokasi',lat + ", " + long);
                        save.append('user',"{{ Auth::user()->id }}");
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            url: "{{route('kepegawaian.absensi.init')}}",
                            method: 'POST',
                            data: save,
                            cache: false,
                            contentType: false,
                            processData: false,
                            dataType: 'json',
                            success: function(res) {
                                $('#meter-gps').text(res.distance);
                                $('#loading-btn').empty();
                                // init(res);
                                // --------------------------------------
                                if (res.jadwal != null) { // JIKA SUDAH MENAMBAH JADWAL & TELAH DIVALIDASI OLEH KEPEGAWAIAN
                                    if (res.jadwal.progress == 2) { // JIKA JADWAL SUDAH VERIFIKASI NAMUN BELUM DIVALIDASI
                                        $("#btn-ijin").prop('hidden',false);
                                        $("#btn-ijin").prop('disabled',true).removeClass('btn-warning').addClass('btn-secondary');
                                        $("#prosesijin").prop('hidden',true);
                                        $("#hiddenButton1").prop('hidden',true);
                                        $("#hiddenButton2").prop('hidden',true);
                                        pesanError('Jadwal telah diverifikasi oleh Atasan tetapi belum dilakukan Validasi oleh bagian Kepegawaian. Silakan menghubungi bagian Kepegawaian.');
                                        console.log('JADWAL SUDAH DIVERIFIKASI/BELUM DIVALIDASI');
                                    } else { // JIKA JADWAL SUDAH DIVALIDASI
                                        // INITIATE ------------------------
                                        th = new Date().getHours(); // get Jam = 0-23
                                        tm = new Date().getMinutes(); // get Menit = 0-59
                                        ts = new Date().getSeconds(); // get Detik = 0-59
                                        if (res.shift) { // JIKA SHIFT DITEMUKAN DAN ADA
                                            if (res.shift.berangkat == '00:00:00' && res.shift.pulang == '00:00:00') {
                                                Swal.fire({
                                                    title: `Jadwal Hari Ini `+res.shift.shift,
                                                    html: 'Selamat berlibur hari ini dan beraktivitas kembali di kemudian hari',
                                                    icon: `warning`,
                                                    showConfirmButton: false,
                                                    showCancelButton: false,
                                                    allowOutsideClick: false,
                                                    allowEscapeKey: false,
                                                    timer: 5000,
                                                    timerProgressBar: true,
                                                    backdrop: `rgba(26,27,41,0.8)`,
                                                });
                                                $(".libur").prop('hidden',true); // ABSEN BIASA
                                                Webcam.reset('#webcam');
                                                console.log('JADWAL HARI INI LIBUR = 00:00 - 00:00 WIB');
                                                pesanSukses('Selamat berlibur hari ini dan beraktivitas kembali di kemudian hari');
                                            } else {
                                                if (res.distance > 30) {
                                                    Swal.fire({
                                                        title: `Anda berada di luar area Rumah Sakit`,
                                                        html: 'Mendekatlah ke lokasi Absensi!<br>Jarak Anda <b>'+res.distance+' meter</b> dari titik lokasi',
                                                        icon: `error`,
                                                        showConfirmButton: false,
                                                        showCancelButton: false,
                                                        allowOutsideClick: true,
                                                        allowEscapeKey: false,
                                                        timer: 5000,
                                                        timerProgressBar: true,
                                                        backdrop: `rgba(26,27,41,0.8)`,
                                                    });
                                                    $("#hiddenButton1").prop('hidden',true); // ABSEN BIASA
                                                    $("#hiddenButton2").prop('hidden',true); // ABSEN + ONCALL
                                                    $("#btn-ijin").prop('hidden',false);
                                                    if (res.show == null && res.ijin == null) {
                                                        $("#btn-ijin").prop('disabled',false).removeClass('btn-secondary btn-warning').addClass('btn-warning');
                                                    } else {
                                                        $("#btn-ijin").prop('disabled',true).removeClass('btn-secondary btn-warning').addClass('btn-secondary');
                                                        console.log('IJIN SUDAH TERISI UNTUK HARI INI');
                                                        pesanSukses('Absensi Hari ini telah terisi dengan Ijin. Silakan melakukan Absensi kembali pada hari selanjutnya. Terima Kasih.');
                                                    }
                                                    $("#prosesijin").prop('hidden',true);
                                                    console.log('TITIK LOKASI GPS LEBIH DARI 30 METER');
                                                } else {
                                                    if ("{{ Auth::user()->getPermission('absensi_oncall') }}" == true || "{{ Auth::user()->getPermission('absensi_oncall') }}" || "{{ Auth::user()->getPermission('absensi_oncall') }}" != '') { // USER MEMILIKI AKSES ONCALL
                                                        console.log('USER ONCALL');
                                                        // $("#btn-biasa").prop('hidden',true);
                                                        // $("#hiddenButton1").prop('hidden',true);
                                                        // $("#hiddenButton2").prop('hidden',false); // ABSEN + ONCALL MUNCUL
                                                        // $("#hiddenButton").prop('hidden',true);
                                                        // $("#btn-mulai").prop('hidden',true);
                                                        // $("#btn-selesai").prop('hidden',true);
                                                        // $("#btn-mix").prop('hidden',true);
                                                        // if (res.ijin == null) { // JIKA BELUM MENGAJUKAN SURAT IJIN
                                                        //     if (res.show == null) { // DATA ABSEN MASIH KOSONG
                                                        //         if (res.oncall == null) { // DATA ONCALL MASIH KOSONG
                                                        //             $("#btn-mulai").prop('hidden',false);
                                                        //             $("#btn-oncall-mulai").prop('disabled',false).removeClass('btn-secondary').addClass('btn-pink');
                                                        //             $("#btn-shift-mulai").prop('disabled',false).removeClass('btn-secondary').addClass('btn-primary');
                                                        //         } else {
                                                        //             $("#btn-selesai").prop('hidden',false);
                                                        //             $("#btn-oncall-selesai").prop('disabled',false).removeClass('btn-secondary').addClass('btn-danger');
                                                        //             $("#btn-shift-oncall").prop('disabled',false).removeClass('btn-secondary').addClass('btn-oren');
                                                        //             $("#btn-oncall-shift").prop('disabled',false).removeClass('btn-secondary').addClass('btn-success');
                                                        //             $("#btn-shift-selesai").prop('disabled',false).removeClass('btn-secondary').addClass('btn-danger');
                                                        //         }
                                                        //     } else {

                                                        //     }
                                                        // } else {
                                                        //     $("#btn-mulai").prop('hidden',false);
                                                        //     $("#btn-oncall-mulai").prop('disabled',true).removeClass('btn-pink').addClass('btn-secondary');
                                                        //     $("#btn-shift-mulai").prop('disabled',true).removeClass('btn-primary').addClass('btn-secondary');
                                                        // }
                                                    } else { // KHUSUS USER TANPA AKSES ONCALL
                                                        console.log('USER BIASA');
                                                        // INIT DISABLED BUTTON ONCALL
                                                        $("#btn-mulai").prop('hidden',true);
                                                        $("#btn-selesai").prop('hidden',true);
                                                        $("#btn-mix").prop('hidden',true);
                                                        $("#hiddenButton2").prop('hidden',true);
                                                        // INIT
                                                        $("#btn-pulang").prop('disabled',true).removeClass('btn-danger').addClass('btn-secondary');
                                                        $("#btn-masuk").prop('disabled',true).removeClass('btn-primary').addClass('btn-secondary');
                                                        $("#btn-biasa").prop('hidden',false);
                                                        $("#hiddenButton1").prop('hidden',false);
                                                        // EXECUTE
                                                        // console.log(res);
                                                        if (res.ijin == null) { // JIKA IJIN MASIH KOSONG
                                                            if (res.showMalam != null && res.show == null) { // JIKA MASIH ADA JAGA SHIFT YANG BELUM TERSELESAIKAN (KHUSUS LEWAT HARI)
                                                                now = new Date().toLocaleDateString('en-CA'); // YYYY-MM-DD
                                                                dbPulang = new Date(res.showMalam.ref_jam_pulang);
                                                                dayOut = dbPulang.toLocaleDateString('en-CA'); // YYYY-MM-DD
                                                                dbMasuk = new Date(now+' '+res.shift.berangkat);
                                                                dbShiftPulang = new Date(now+' '+res.shift.pulang);
                                                                console.log(res.shift);
                                                                console.log(res.showMalam);
                                                                console.log(res.show);
                                                                if (th < dbPulang.getHours() - 1) {
                                                                    $("#btn-pulang").prop('disabled',true).removeClass('btn-danger').addClass('btn-secondary');
                                                                    $("#btn-masuk").prop('disabled',true).removeClass('btn-primary').addClass('btn-secondary');
                                                                    pesanError(`Absen Pulang Anda Hari ini (Pukul ${res.showMalam.ref_jam_pulang} WIB) masih terkunci, Silakan menunggu`);
                                                                } else {
                                                                    if (th >= dbPulang.getHours() - 1 && th < dbMasuk.getHours() - 1) {
                                                                        $("#btn-pulang").prop('disabled',false).removeClass('btn-secondary').addClass('btn-danger');
                                                                        $("#btn-masuk").prop('disabled',true).removeClass('btn-primary').addClass('btn-secondary');
                                                                        console.log('BISA ABSEN PULANG LEWAT HARI');
                                                                    } else {
                                                                        if (th >= dbMasuk.getHours() - 1) { //  && th <= dbShiftPulang.getHours()
                                                                            $("#btn-pulang").prop('disabled',true).removeClass('btn-danger').addClass('btn-secondary');
                                                                            $("#btn-masuk").prop('disabled',false).removeClass('btn-secondary').addClass('btn-primary');
                                                                            console.log('BISA ABSEN MASUK');
                                                                        } else {
                                                                            $("#btn-pulang").prop('disabled',true).removeClass('btn-danger').addClass('btn-secondary');
                                                                            $("#btn-masuk").prop('disabled',true).removeClass('btn-primary').addClass('btn-secondary');
                                                                            console.log('ABSEN MASUK HARI INI MASIH TERKUNCI');
                                                                            pesanError(`Absen Masuk Anda Hari ini (Pukul ${res.shift.berangkat} WIB) masih terkunci, Silakan menunggu`);
                                                                        }
                                                                    }
                                                                }
                                                            } else { // JIKA JAGA SHIFT CLEAR SEMUA
                                                                if (res.showMalam == null && res.show == null) { // JIKA ABSEN HARI INI MASIH KOSONG
                                                                    const thisD = new Date().toLocaleDateString('en-CA'); // YYYY-MM-DD
                                                                    const tomorrow = new Date();
                                                                    tomorrow.setDate(tomorrow.getDate() + 1);
                                                                    const thisT = tomorrow.toLocaleDateString('en-CA');
                                                                    if (res.shift.pulang > res.shift.berangkat) { // JIKA ABSENSI TIDAK LEWAT HARI
                                                                        dbMasuk = new Date(thisD+' '+res.shift.berangkat);
                                                                        dbPulang = new Date(thisD+' '+res.shift.pulang);
                                                                        // console.log(dbMasuk);
                                                                        // console.log(dbPulang);
                                                                        if (th >= dbPulang.getHours()) { // Jika Jam Absen Masuk Lebih dari sama dgn Jam pulang
                                                                            $("#btn-pulang").prop('disabled',true).removeClass('btn-danger').addClass('btn-secondary');
                                                                            $("#btn-masuk").prop('disabled',true).removeClass('btn-primary').addClass('btn-secondary');
                                                                            console.log('Jam Absen Masuk Lebih dari sama dgn Jam pulang');
                                                                            pesanError(`Jam Absen Masuk sudah terlewati. Absen Masuk TIDAK BOLEH melebihi Jam Pulang.`);
                                                                        } else { // JIKA ABSENSI SEBELUM JAM PULANG
                                                                            if (th >= dbMasuk.getHours() - 1) { // MINIMAL ABSENSI 1 JAM SEBELUM JAM MASUK
                                                                                $("#btn-pulang").prop('disabled',true).removeClass('btn-danger').addClass('btn-secondary');
                                                                                $("#btn-masuk").prop('disabled',false).removeClass('btn-secondary').addClass('btn-primary');
                                                                                console.log('BISA ABSEN MASUK');
                                                                            } else { // JIKA ABSENSI DILUAR ANTARA JAM MASUK DAN JAM PULANG
                                                                                $("#btn-pulang").prop('disabled',true).removeClass('btn-danger').addClass('btn-secondary');
                                                                                $("#btn-masuk").prop('disabled',true).removeClass('btn-primary').addClass('btn-secondary');
                                                                                console.log('Jam Absen Masuk Tidak pada/antara jam masuk (-1 jam) dan jam pulang');
                                                                                pesanError(`Jam Absen Masuk saat ini TIDAK pada/antara jam masuk yang ditetapkan (yaitu -1 jam sebelum Referensi Jam Masuk) dan wajib tidak lebih dari jam pulang yang seharusnya.`);
                                                                            }
                                                                        }
                                                                    } else { // JIKA ABSENSI MASUK LEWAT HARI (MALAM ke PAGI)
                                                                        dbMasuk = new Date(thisD+' '+res.shift.berangkat).toLocaleDateString('en-CA');
                                                                        dbMasukOri = new Date(thisD+' '+res.shift.berangkat);
                                                                        if (thisD == dbMasuk) {
                                                                            if (th >= dbMasukOri.getHours() - 1) {
                                                                                $("#btn-pulang").prop('disabled',true).removeClass('btn-danger').addClass('btn-secondary');
                                                                                $("#btn-masuk").prop('disabled',false).removeClass('btn-secondary').addClass('btn-primary');
                                                                            } else {
                                                                                $("#btn-pulang").prop('disabled',true).removeClass('btn-danger').addClass('btn-secondary');
                                                                                $("#btn-masuk").prop('disabled',true).removeClass('btn-primary').addClass('btn-secondary');
                                                                            }
                                                                        } else {
                                                                            $("#btn-pulang").prop('disabled',true).removeClass('btn-danger').addClass('btn-secondary');
                                                                            $("#btn-masuk").prop('disabled',true).removeClass('btn-primary').addClass('btn-secondary');
                                                                        }
                                                                    }
                                                                } else { // ABSEN PULANG
                                                                    if (res.show.tgl_in != null && res.show.tgl_out == null) {
                                                                        now = new Date().toLocaleDateString('en-CA'); // YYYY-MM-DD
                                                                        dbPulang = new Date(res.show.ref_jam_pulang);
                                                                        // console.log(dbPulang.toLocaleTimeString());
                                                                        console.log(th);
                                                                        console.log(dbPulang.getHours() + 2);
                                                                        console.log(tm);
                                                                        console.log(dbPulang.getMinutes());
                                                                        dayOut = dbPulang.toLocaleDateString('en-CA'); // YYYY-MM-DD
                                                                        if (now == dayOut) { // ABSEN PULANG POSISI SAAT INI DI HARI YANG SAMA
                                                                            if (th >= dbPulang.getHours() - 1) {
                                                                                if (th <= dbPulang.getHours() + 2) {
                                                                                    if (th == dbPulang.getHours() + 2) {
                                                                                        if (tm <= dbPulang.getMinutes()) {
                                                                                            console.log('sampai sini');
                                                                                            $("#btn-pulang").prop('disabled',false).removeClass('btn-secondary').addClass('btn-danger');
                                                                                            $("#btn-masuk").prop('disabled',true).removeClass('btn-primary').addClass('btn-secondary');
                                                                                            pesanWarning(`Silakan melakukan Absen Pulang dari Pukul ${dbPulang.toLocaleTimeString()} WIB sampai dengan maksimal 2 Jam setelahnya.`);
                                                                                        } else {
                                                                                            $("#btn-pulang").prop('disabled',true).removeClass('btn-danger').addClass('btn-secondary');
                                                                                            $("#btn-masuk").prop('disabled',true).removeClass('btn-primary').addClass('btn-secondary');
                                                                                            pesanError(`Absen Pulang telah terlewati (Absen Pulang seharusnya pada Pukul ${dbPulang.toLocaleTimeString()} sampai dengan 2 Jam setelahnya). Silakan melakukan Absensi kembali pada hari selanjutnya. Terima Kasih.`);
                                                                                        }
                                                                                    } else {
                                                                                        $("#btn-pulang").prop('disabled',false).removeClass('btn-secondary').addClass('btn-danger');
                                                                                        $("#btn-masuk").prop('disabled',true).removeClass('btn-primary').addClass('btn-secondary');
                                                                                        pesanWarning(`Silakan melakukan Absen Pulang dari Pukul ${dbPulang.toLocaleTimeString()} WIB sampai dengan maksimal 2 Jam setelahnya.`);
                                                                                    }
                                                                                } else {
                                                                                    $("#btn-pulang").prop('disabled',true).removeClass('btn-danger').addClass('btn-secondary');
                                                                                    $("#btn-masuk").prop('disabled',true).removeClass('btn-primary').addClass('btn-secondary');
                                                                                    pesanError(`Absen Pulang telah terlewati (Absen Pulang seharusnya pada Pukul ${dbPulang.toLocaleTimeString()} sampai dengan 2 Jam setelahnya). Silakan melakukan Absensi kembali pada hari selanjutnya. Terima Kasih.`);
                                                                                }
                                                                            } else {
                                                                                if (th >= dbPulang.getHours() + 2) {
                                                                                    $("#btn-pulang").prop('disabled',true).removeClass('btn-danger').addClass('btn-secondary');
                                                                                    $("#btn-masuk").prop('disabled',true).removeClass('btn-primary').addClass('btn-secondary');
                                                                                    pesanError(`Absen Pulang telah terlewati (Absen Pulang seharusnya pada Pukul ${dbPulang.toLocaleTimeString()} sampai dengan 2 jam setelahnya). Silakan melakukan Absensi kembali pada hari selanjutnya. Terima Kasih.`);
                                                                                } else {
                                                                                    $("#btn-pulang").prop('disabled',true).removeClass('btn-danger').addClass('btn-secondary');
                                                                                    $("#btn-masuk").prop('disabled',true).removeClass('btn-primary').addClass('btn-secondary');
                                                                                    pesanWarning(`Absen Pulang hari ini akan tersedia mulai pada ${res.show.ref_jam_pulang}.`);
                                                                                }
                                                                            }
                                                                        } else { // ABSEN PULANG POSISI SAAT INI MASIH HARI YANG BERBEDA
                                                                            $("#btn-pulang").prop('disabled',true).removeClass('btn-danger').addClass('btn-secondary');
                                                                            $("#btn-masuk").prop('disabled',true).removeClass('btn-primary').addClass('btn-secondary');
                                                                            pesanWarning(`Absensi masuk hari ini sudah terisi namun Jam Pulang TIDAK VALID. Silakan menunggu Jam Pulang yang sudah ditetapkan.`);
                                                                        }
                                                                    } else { // JIKA JADWAL ABSEN HARI SUDAH TERISI LENGKAP
                                                                        $("#btn-pulang").prop('disabled',true).removeClass('btn-danger').addClass('btn-secondary');
                                                                        $("#btn-masuk").prop('disabled',true).removeClass('btn-primary').addClass('btn-secondary');
                                                                        pesanSukses('Absensi Masuk dan Pulang Hari ini telah selesai dilakukan. Silakan kembali Absensi pada hari selanjutnya. Terima Kasih.');
                                                                    }
                                                                }
                                                            }
                                                        } else {
                                                            $("#btn-pulang").prop('disabled',true).removeClass('btn-danger').addClass('btn-secondary');
                                                            $("#btn-masuk").prop('disabled',true).removeClass('btn-primary').addClass('btn-secondary');
                                                            console.log('IJIN SUDAH TERISI YAA');
                                                            pesanSukses('Absensi Hari ini telah terisi dengan Ijin. Silakan melakukan Absensi kembali pada hari selanjutnya. Terima Kasih.');
                                                        }
                                                    }
                                                }
                                            }
                                        } else { // JIKA MASIH TERDAPAT ABSEN YANG BELUM CLEAR (LEWAT HARI)
                                            if (res.distance > 30) {
                                                Swal.fire({
                                                    title: `Anda berada di luar area Rumah Sakit`,
                                                    html: 'Mendekatlah ke lokasi Absensi!<br>Jarak Anda <b>'+res.distance+' meter</b> dari titik lokasi',
                                                    icon: `error`,
                                                    showConfirmButton: false,
                                                    showCancelButton: false,
                                                    allowOutsideClick: true,
                                                    allowEscapeKey: false,
                                                    timer: 5000,
                                                    timerProgressBar: true,
                                                    backdrop: `rgba(26,27,41,0.8)`,
                                                });
                                                $("#hiddenButton1").prop('hidden',true); // ABSEN BIASA
                                                $("#hiddenButton2").prop('hidden',true); // ABSEN + ONCALL
                                                $("#btn-ijin").prop('hidden',false);
                                                if (res.show == null && res.ijin == null) {
                                                    $("#btn-ijin").prop('disabled',true).removeClass('btn-secondary btn-warning').addClass('btn-secondary');
                                                    console.log('IJIN BELUM TERISI UNTUK HARI INI DAN HARI INI LIBUR');
                                                    pesanWarning('Jadwal Hari ini adalah LIBUR. Silakan melakukan Absensi kembali pada hari selanjutnya. Terima Kasih.');
                                                } else {
                                                    $("#btn-ijin").prop('disabled',true).removeClass('btn-secondary btn-warning').addClass('btn-secondary');
                                                    console.log('IJIN SUDAH TERISI UNTUK HARI INI');
                                                    pesanSukses('Absensi Hari ini telah terisi dengan Ijin. Silakan melakukan Absensi kembali pada hari selanjutnya. Terima Kasih.');
                                                }
                                                $("#prosesijin").prop('hidden',true);
                                                console.log('TITIK LOKASI GPS LEBIH DARI 30 METER');
                                            } else {
                                                // INIT DISABLED BUTTON ONCALL
                                                $("#btn-mulai").prop('hidden',true);
                                                $("#btn-selesai").prop('hidden',true);
                                                $("#btn-mix").prop('hidden',true);
                                                $("#hiddenButton2").prop('hidden',true);
                                                // INIT
                                                $("#btn-pulang").prop('disabled',true).removeClass('btn-danger').addClass('btn-secondary');
                                                $("#btn-masuk").prop('disabled',true).removeClass('btn-primary').addClass('btn-secondary');
                                                $("#btn-biasa").prop('hidden',false);
                                                $("#hiddenButton1").prop('hidden',false);
                                                if (res.showMalam != null && res.show == null) { // JIKA ABSEN MALAM BELUM SELESAI DAN ABSEN HARI INI MASIH KOSONG
                                                    if (res.showMalam.tgl_in != null && res.showMalam.tgl_out == null) {
                                                        now = new Date().toLocaleDateString('en-CA'); // YYYY-MM-DD
                                                        dbPulang = new Date(res.showMalam.ref_jam_pulang);
                                                        // console.log(res.showMalam);
                                                        // console.log(dbPulang.toLocaleTimeString());
                                                        // console.log(dbPulang.getHours() + 2);
                                                        // console.log(tm);
                                                        // console.log(dbPulang.getMinutes());
                                                        dayOut = dbPulang.toLocaleDateString('en-CA'); // YYYY-MM-DD
                                                        if (now == dayOut) {
                                                            if (th >= dbPulang.getHours() - 1) {
                                                                if (th <= dbPulang.getHours() + 2) {
                                                                    if (th == dbPulang.getHours() + 2) {
                                                                        if (tm <= dbPulang.getMinutes()) {
                                                                            console.log('sampai sini');
                                                                            $("#btn-pulang").prop('disabled',false).removeClass('btn-secondary').addClass('btn-danger');
                                                                            $("#btn-masuk").prop('disabled',true).removeClass('btn-primary').addClass('btn-secondary');
                                                                            pesanWarning(`Silakan melakukan Absen Pulang dari Pukul ${dbPulang.toLocaleTimeString()} WIB sampai dengan maksimal 2 Jam setelahnya.`);
                                                                        } else {
                                                                            $("#btn-pulang").prop('disabled',true).removeClass('btn-danger').addClass('btn-secondary');
                                                                            $("#btn-masuk").prop('disabled',true).removeClass('btn-primary').addClass('btn-secondary');
                                                                            pesanError(`Absen Pulang telah terlewati (Absen Pulang seharusnya pada Pukul ${dbPulang.toLocaleTimeString()} sampai dengan 2 Jam setelahnya). Silakan melakukan Absensi kembali pada hari selanjutnya. Terima Kasih.`);
                                                                        }
                                                                    } else {
                                                                        $("#btn-pulang").prop('disabled',false).removeClass('btn-secondary').addClass('btn-danger');
                                                                        $("#btn-masuk").prop('disabled',true).removeClass('btn-primary').addClass('btn-secondary');
                                                                        pesanWarning(`Silakan melakukan Absen Pulang dari Pukul ${dbPulang.toLocaleTimeString()} WIB sampai dengan maksimal 2 Jam setelahnya.`);
                                                                    }
                                                                } else {
                                                                    $("#btn-pulang").prop('disabled',true).removeClass('btn-danger').addClass('btn-secondary');
                                                                    $("#btn-masuk").prop('disabled',true).removeClass('btn-primary').addClass('btn-secondary');
                                                                    pesanError(`Absen Pulang telah terlewati (Absen Pulang seharusnya pada Pukul ${dbPulang.toLocaleTimeString()} sampai dengan 2 Jam setelahnya). Silakan melakukan Absensi kembali pada hari selanjutnya. Terima Kasih.`);
                                                                }
                                                            } else {
                                                                if (th >= dbPulang.getHours() + 2) {
                                                                    $("#btn-pulang").prop('disabled',true).removeClass('btn-danger').addClass('btn-secondary');
                                                                    $("#btn-masuk").prop('disabled',true).removeClass('btn-primary').addClass('btn-secondary');
                                                                    pesanError(`Absen Pulang telah terlewati (Absen Pulang seharusnya pada Pukul ${dbPulang.toLocaleTimeString()} sampai dengan 2 jam setelahnya). Silakan melakukan Absensi kembali pada hari selanjutnya. Terima Kasih.`);
                                                                } else {
                                                                    $("#btn-pulang").prop('disabled',true).removeClass('btn-danger').addClass('btn-secondary');
                                                                    $("#btn-masuk").prop('disabled',true).removeClass('btn-primary').addClass('btn-secondary');
                                                                    pesanWarning(`Absen Pulang hari ini akan tersedia mulai pada ${res.showMalam.ref_jam_pulang}.`);
                                                                }
                                                            }
                                                        } else {
                                                            $("#btn-pulang").prop('disabled',true).removeClass('btn-danger').addClass('btn-secondary');
                                                            $("#btn-masuk").prop('disabled',true).removeClass('btn-primary').addClass('btn-secondary');
                                                            pesanWarning(`Absensi masuk hari ini sudah terisi namun Jam Pulang TIDAK VALID. Silakan menunggu Jam Pulang yang sudah ditetapkan.`);
                                                        }
                                                    } else { // JIKA JADWAL ABSEN HARI SUDAH TERISI LENGKAP
                                                        $("#btn-pulang").prop('disabled',true).removeClass('btn-danger').addClass('btn-secondary');
                                                        $("#btn-masuk").prop('disabled',true).removeClass('btn-primary').addClass('btn-secondary');
                                                        pesanSukses('Absensi Masuk dan Pulang Hari ini telah selesai dilakukan. Silakan kembali Absensi pada hari selanjutnya. Terima Kasih.');
                                                    }
                                                } else { // JIKA SHIFT TIDAK DITEMUKAN
                                                    Swal.fire({
                                                        title: `Jadwal Hari Ini `+res.shift.shift,
                                                        html: 'Selamat berlibur hari ini dan beraktivitas kembali di kemudian hari',
                                                        icon: `warning`,
                                                        showConfirmButton: false,
                                                        showCancelButton: false,
                                                        allowOutsideClick: false,
                                                        allowEscapeKey: false,
                                                        timer: 5000,
                                                        timerProgressBar: true,
                                                        backdrop: `rgba(26,27,41,0.8)`,
                                                    });
                                                    $(".libur").prop('hidden',true); // ABSEN BIASA
                                                    Webcam.reset('#webcam');
                                                    console.log('JADWAL HARI INI LIBUR = 00:00 - 00:00 WIB');
                                                    pesanSukses('Selamat berlibur hari ini dan beraktivitas kembali di kemudian hari');
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    if (res.showMalam != null && res.show == null) { // JIKA ABSEN MALAM BELUM SELESAI DAN ABSEN HARI INI MASIH KOSONG DAN JADWAL BULAN BERIKUTNYA BELUM TERBUAT
                                        // INIT DISABLED BUTTON ONCALL
                                        $("#btn-mulai").prop('hidden',true);
                                        $("#btn-selesai").prop('hidden',true);
                                        $("#btn-mix").prop('hidden',true);
                                        $("#hiddenButton2").prop('hidden',true);
                                        // INIT
                                        $("#btn-pulang").prop('disabled',true).removeClass('btn-danger').addClass('btn-secondary');
                                        $("#btn-masuk").prop('disabled',true).removeClass('btn-primary').addClass('btn-secondary');
                                        $("#btn-biasa").prop('hidden',false);
                                        $("#hiddenButton1").prop('hidden',false);
                                        if (res.showMalam.tgl_in != null && res.showMalam.tgl_out == null) {
                                            now = new Date().toLocaleDateString('en-CA'); // YYYY-MM-DD
                                            dbPulang = new Date(res.showMalam.ref_jam_pulang);
                                            // console.log(res.showMalam);
                                            // console.log(dbPulang.toLocaleTimeString());
                                            // console.log(dbPulang.getHours() + 2);
                                            // console.log(tm);
                                            // console.log(dbPulang.getMinutes());
                                            dayOut = dbPulang.toLocaleDateString('en-CA'); // YYYY-MM-DD
                                            if (now == dayOut) {
                                                if (th >= dbPulang.getHours() - 1) {
                                                    if (th <= dbPulang.getHours() + 2) {
                                                        if (th == dbPulang.getHours() + 2) {
                                                            if (tm <= dbPulang.getMinutes()) {
                                                                console.log('sampai sini');
                                                                $("#btn-pulang").prop('disabled',false).removeClass('btn-secondary').addClass('btn-danger');
                                                                $("#btn-masuk").prop('disabled',true).removeClass('btn-primary').addClass('btn-secondary');
                                                                pesanWarning(`Silakan melakukan Absen Pulang dari Pukul ${dbPulang.toLocaleTimeString()} WIB sampai dengan maksimal 2 Jam setelahnya.`);
                                                            } else {
                                                                $("#btn-pulang").prop('disabled',true).removeClass('btn-danger').addClass('btn-secondary');
                                                                $("#btn-masuk").prop('disabled',true).removeClass('btn-primary').addClass('btn-secondary');
                                                                pesanError(`Absen Pulang telah terlewati (Absen Pulang seharusnya pada Pukul ${dbPulang.toLocaleTimeString()} sampai dengan 2 Jam setelahnya). Silakan melakukan Absensi kembali pada hari selanjutnya. Terima Kasih.`);
                                                            }
                                                        } else {
                                                            $("#btn-pulang").prop('disabled',false).removeClass('btn-secondary').addClass('btn-danger');
                                                            $("#btn-masuk").prop('disabled',true).removeClass('btn-primary').addClass('btn-secondary');
                                                            pesanWarning(`Silakan melakukan Absen Pulang dari Pukul ${dbPulang.toLocaleTimeString()} WIB sampai dengan maksimal 2 Jam setelahnya.`);
                                                        }
                                                    } else {
                                                        $("#btn-pulang").prop('disabled',true).removeClass('btn-danger').addClass('btn-secondary');
                                                        $("#btn-masuk").prop('disabled',true).removeClass('btn-primary').addClass('btn-secondary');
                                                        pesanError(`Absen Pulang telah terlewati (Absen Pulang seharusnya pada Pukul ${dbPulang.toLocaleTimeString()} sampai dengan 2 Jam setelahnya). Silakan melakukan Absensi kembali pada hari selanjutnya. Terima Kasih.`);
                                                    }
                                                } else {
                                                    if (th >= dbPulang.getHours() + 2) {
                                                        $("#btn-pulang").prop('disabled',true).removeClass('btn-danger').addClass('btn-secondary');
                                                        $("#btn-masuk").prop('disabled',true).removeClass('btn-primary').addClass('btn-secondary');
                                                        pesanError(`Absen Pulang telah terlewati (Absen Pulang seharusnya pada Pukul ${dbPulang.toLocaleTimeString()} sampai dengan 2 jam setelahnya). Silakan melakukan Absensi kembali pada hari selanjutnya. Terima Kasih.`);
                                                    } else {
                                                        $("#btn-pulang").prop('disabled',true).removeClass('btn-danger').addClass('btn-secondary');
                                                        $("#btn-masuk").prop('disabled',true).removeClass('btn-primary').addClass('btn-secondary');
                                                        pesanWarning(`Absen Pulang hari ini akan tersedia mulai pada ${res.showMalam.ref_jam_pulang}.`);
                                                    }
                                                }
                                            } else {
                                                $("#btn-pulang").prop('disabled',true).removeClass('btn-danger').addClass('btn-secondary');
                                                $("#btn-masuk").prop('disabled',true).removeClass('btn-primary').addClass('btn-secondary');
                                                pesanWarning(`Absensi masuk hari ini sudah terisi namun Jam Pulang TIDAK VALID. Silakan menunggu Jam Pulang yang sudah ditetapkan.`);
                                            }
                                        } else { // JIKA JADWAL ABSEN HARI SUDAH TERISI LENGKAP
                                            $("#btn-pulang").prop('disabled',true).removeClass('btn-danger').addClass('btn-secondary');
                                            $("#btn-masuk").prop('disabled',true).removeClass('btn-primary').addClass('btn-secondary');
                                            pesanSukses('Absensi Masuk dan Pulang Hari ini telah selesai dilakukan. Silakan kembali Absensi pada hari selanjutnya. Terima Kasih.');
                                        }
                                    } else { // JIKA JADWAL DINAS BULAN INI TIDAK DITEMUKAN
                                        $("#btn-ijin").prop('hidden',false);
                                        $("#btn-ijin").prop('disabled',true).removeClass('btn-warning').addClass('btn-secondary');
                                        $("#prosesijin").prop('hidden',true);
                                        $("#hiddenButton1").prop('hidden',true);
                                        $("#hiddenButton2").prop('hidden',true);
                                        $("#alerts").empty().append(`<div class="card card-style bg-red-dark alert-dismissible show shadow-bg shadow-bg-m fade p-0 rounded-m">
                                            <div class="content my-3">
                                                <div class="d-flex">
                                                    <div class="align-self-center">
                                                        <i class="bi bi-exclamation-triangle font-36 color-white d-block"></i>
                                                    </div>
                                                    <div class="align-self-center">
                                                        <p class="color-white mb-0 font-500 font-14 ps-3 pe-4 line-height-s">
                                                            Perhatian! <br> Jadwal tidak ditemukan atau belum terverifikasi oleh Atasan. Silakan menghubungi Admin Jadwal.
                                                        </p>
                                                    </div>
                                                    <div class="ms-auto">
                                                        <button type="button" class="btn-close opacity-20 font-11 mt-n2 me-n2" data-bs-dismiss="alert" aria-label="Close"></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>`);
                                        console.log('JADWAL TIDAK VALID/TIDAK DITEMUKAN/BELUM DIVERIFIKASI');
                                    }
                                }
                                // ---------------------------------------------
                            }
                        })
                    }).catch((err) => {
                        console.log(err)
                    })
                // } // END OF NAVIGATOR GEOLOCATION PERMISSION
            // }); // END OF NAVIGATOR GEOLOCATION PERMISSION
        } else {
            alert("Browser Anda Tidak Support.");
        }
    }

    function backupRefreshMap() {
        const x = document.getElementById("lokasi");
        if (navigator.geolocation) {
            // navigator.geolocation.getCurrentPosition(showPosition);
            navigator.geolocation.getCurrentPosition(position => {
                const { latitude, longitude } = position.coords;
                map = L.map('map',{
                    keyboard: false,
                    zoomControl: false,
                    boxZoom: false,
                    doubleClickZoom: false,
                    tap: false,
                    touchZoom: false,
                    enableHighAccuracy: true,
                    scrollWheelZoom: false,
                    dragging: false,
                    doubleClickZoom: false,
                }).setView([position.coords.latitude, position.coords.longitude], 18);
                // center: [51.505, -0.09],
                // zoom: 13,
                // minZoom: 13,
                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    // attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                }).addTo(map);

                // Titik Lokasi GPS
                var marker = new L.Marker([position.coords.latitude, position.coords.longitude]);
                marker.addTo(map).bindPopup("Titik Lokasi Anda").openPopup();

                // Radius
                var circle = L.circle(["{{ $list['profil_rs']->coord_lat }}","{{ $list['profil_rs']->coord_long }}"], { // RSPKUSKH COORD : -7.677851238136329, 110.83968584828327
                    color: 'red',
                    fillColor: '#f03',
                    fillOpacity: 0.5,
                    radius: 30 // RADIUS 30 M
                }).addTo(map);

                $("#lokasi").val(position.coords.latitude + ", " + position.coords.longitude);

                // ATTENTION
                var save = new FormData();
                save.append('lokasi',position.coords.latitude + ", " + position.coords.longitude);
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{route('kepegawaian.absensi.getDistance')}}",
                    method: 'POST',
                    data: save,
                    cache: false,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function(res) {
                        init(res);
                        if (res > 30) { // RADIUS 30 M
                            Swal.fire({
                                title: `Anda berada di luar area Rumah Sakit`,
                                html: 'Mendekatlah ke lokasi Absensi!<br>Jarak Anda <b>'+res+' meter</b> dari titik lokasi',
                                icon: `error`,
                                showConfirmButton: false,
                                showCancelButton: false,
                                allowOutsideClick: true,
                                allowEscapeKey: false,
                                timer: 5000,
                                timerProgressBar: true,
                                backdrop: `rgba(26,27,41,0.8)`,
                            });
                            $("#map").prop('hidden',false);
                            $("#webcam").prop('hidden',true);
                            Webcam.reset('.webcam-selfi');
                        } else {
                            Swal.fire({
                                title: `Anda berada di dalam area Rumah Sakit`,
                                html: 'Anda sudah di area Absensi!<br>Jarak Anda <b>'+res+' meter</b> dari titik lokasi',
                                icon: `success`,
                                showConfirmButton: false,
                                showCancelButton: false,
                                allowOutsideClick: true,
                                allowEscapeKey: false,
                                timer: 5000,
                                timerProgressBar: true,
                                backdrop: `rgba(26,27,41,0.8)`,
                            });
                            $("#map").prop('hidden',true);
                            $("#webcam").prop('hidden',false);
                            startFrontCamera();
                        }
                    }
                })
            });
        } else {
            alert("Browser Anda Tidak Support.");
        }
    }

    function prosesMasuk() {
        $('#btn-masuk').prop('disabled',true);
        if ("{{ Auth::user()->getPermission('absensi_oncall') }}" == true) {
            oncall = true;
        } else {
            oncall = false;
        }
        // VALIDATION
        $.ajax({
            url: "/api/kepegawaian/absensi/validate/jadwal/{{ Auth::user()->id }}/"+oncall,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.code == 200) { // JIKA SYARAT ABSEN TERPENUHI
                    // INIT
                    Webcam.snap( function(data_uri) {
                        $("#image-capture").val(data_uri);
                        console.log(data_uri);
                    } );
                    console.log($("#image-capture").val());
                    var save = new FormData();
                    save.append('image',$("#image-capture").val());
                    save.append('lokasi',$("#lokasi").val());
                    save.append('lewat_hari',res.lewat_hari);
                    save.append('kd_shift',res.kd_shift);
                    save.append('nm_shift',res.nm_shift);
                    save.append('berangkat',res.berangkat);
                    save.append('pulang',res.pulang);
                    save.append('pegawai',"{{ Auth::user()->id }}");
                    // SAVING DATA
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: "{{route('kepegawaian.absensi.executeBerangkat')}}",
                        method: 'POST',
                        data: save,
                        cache: false,
                        contentType: false,
                        processData: false,
                        dataType: 'json',
                        success: function(ex) {
                            if (ex.code == 200) {
                                // const Toast = Swal.mixin({
                                //     toast: true,
                                //     position: "center",
                                //     showConfirmButton: false,
                                //     timer: 3000,
                                //     timerProgressBar: true,
                                //     didOpen: (toast) => {
                                //         toast.onmouseenter = Swal.stopTimer;
                                //         toast.onmouseleave = Swal.resumeTimer;
                                //     }
                                // });
                                // Toast.fire({
                                //     icon: "success",
                                //     title: `Pesan Berhasil!`,
                                //     text: ex.message
                                // });
                                refreshMap();
                                Swal.fire({
                                    title: `Pesan Berhasil!`,
                                    text: ex.message,
                                    icon: `success`,
                                    showConfirmButton: false,
                                    showCancelButton: false,
                                    allowOutsideClick: false,
                                    allowEscapeKey: false,
                                    timer: 3000,
                                    timerProgressBar: true,
                                    backdrop: `rgba(26,27,41,0.8)`,
                                });
                            } else {
                                Swal.fire({
                                    title: `Pesan Error!`,
                                    text: ex.message,
                                    icon: `error`,
                                    showConfirmButton: false,
                                    showCancelButton: false,
                                    allowOutsideClick: false,
                                    allowEscapeKey: false,
                                    timer: 3000,
                                    timerProgressBar: true,
                                    backdrop: `rgba(26,27,41,0.8)`,
                                });
                            }
                        }
                    })
                } else { // JIKA SYARAT ABSEN TIDAK TERPENUHI
                    Swal.fire({
                        title: `Pesan Error`,
                        text: res.message,
                        icon: `warning`,
                        showConfirmButton: false,
                        showCancelButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        timer: 3000,
                        timerProgressBar: true,
                        backdrop: `rgba(26,27,41,0.8)`,
                    });
                    refreshMap();
                }
            }
        })
        $('#btn-masuk').prop('disabled',false);
    }

    function prosesPulang() {
        // VALIDATION
        $.ajax({
            url: "/api/kepegawaian/absensi/validate/jadwal/{{ Auth::user()->id }}/pulang",
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                console.log(res);
                if (res.code == 200) { // JIKA SYARAT ABSEN TERPENUHI
                    // INIT
                    Webcam.snap( function(data_uri) {
                        $("#image-capture").val(data_uri);
                        console.log(data_uri);
                    } );
                    console.log($("#image-capture").val());
                    var save = new FormData();
                    save.append('image',$("#image-capture").val());
                    save.append('lokasi',$("#lokasi").val());
                    save.append('pegawai',"{{ Auth::user()->id }}");
                    // SAVING DATA
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: "{{route('kepegawaian.absensi.executePulang')}}",
                        method: 'POST',
                        data: save,
                        cache: false,
                        contentType: false,
                        processData: false,
                        dataType: 'json',
                        success: function(ex) {
                            if (ex.code == 200) {
                                Swal.fire({
                                    title: `Pesan Berhasil!`,
                                    text: ex.message,
                                    icon: `success`,
                                    showConfirmButton: false,
                                    showCancelButton: false,
                                    allowOutsideClick: false,
                                    allowEscapeKey: false,
                                    timer: 3000,
                                    timerProgressBar: true,
                                    backdrop: `rgba(26,27,41,0.8)`,
                                });
                                refreshMap();
                            } else {
                                Swal.fire({
                                    title: `Pesan Error!`,
                                    text: ex.message,
                                    icon: `error`,
                                    showConfirmButton: false,
                                    showCancelButton: false,
                                    allowOutsideClick: true,
                                    allowEscapeKey: false,
                                    // timer: 3000,
                                    // timerProgressBar: true,
                                    backdrop: `rgba(26,27,41,0.8)`,
                                });
                            }
                        }
                    })
                } else { // JIKA SYARAT ABSEN TIDAK TERPENUHI
                    Swal.fire({
                        title: `Pesan Error`,
                        html: res.message,
                        icon: `warning`,
                        showConfirmButton: false,
                        showCancelButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        timer: 3000,
                        timerProgressBar: true,
                        backdrop: `rgba(26,27,41,0.8)`,
                    });
                    refreshMap();
                }
            }
        })
    }

    function showIjin() {
        $("#webcam").prop('hidden',false);
        // $("#hiddenButton").prop('hidden',true);
        $("#hiddenButton1").prop('hidden',true);
        $("#hiddenButton2").prop('hidden',true);
        // $("#map").prop('hidden',true);
        $("#btn-ijin").prop('hidden',true);
        $("#prosesijin").prop('hidden',false);
        // $("#btn-gps").prop('hidden',true);
        $('#ket_ijin').val('');
        Swal.fire({
            title: `Pesan Lanjutan!`,
            html: 'Silakan foto surat ijin lalu tekan tombol <b class="text-info"><u>KIRIM</u></b>',
            icon: `warning`,
            showConfirmButton: false,
            showCancelButton: false,
            allowOutsideClick: true,
            allowEscapeKey: true,
            timer: 8000,
            timerProgressBar: true,
            backdrop: `rgba(26,27,41,0.8)`,
        });
        startRearCamera();
    }

    function prosesIjin() {
        // VALIDATION
        $.ajax({
            url: "/api/kepegawaian/absensi/validate/ijin/{{ Auth::user()->id }}",
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.code == 200) { // JIKA SYARAT ABSEN TERPENUHI
                    if ($('#ket_ijin').val().trim() != '') {
                        // INIT
                        Webcam.snap( function(data_uri) {
                            $("#image-capture").val(data_uri);
                        } );
                        var save = new FormData();
                        save.append('image',$("#image-capture").val());
                        save.append('lokasi',$("#lokasi").val());
                        save.append('lewat_hari',res.lewat_hari);
                        save.append('kd_shift',res.kd_shift);
                        save.append('nm_shift',res.nm_shift);
                        save.append('berangkat',res.berangkat);
                        save.append('pulang',res.pulang);
                        save.append('keterangan',$('#ket_ijin').val());
                        save.append('pegawai',"{{ Auth::user()->id }}");
                        // SAVING DATA
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            url: "{{route('kepegawaian.absensi.executeIjin')}}",
                            method: 'POST',
                            data: save,
                            cache: false,
                            contentType: false,
                            processData: false,
                            dataType: 'json',
                            success: function(ex) {
                                if (ex.code == 200) {
                                    Swal.fire({
                                        title: `Pesan Berhasil!`,
                                        text: ex.message,
                                        icon: `success`,
                                        showConfirmButton: false,
                                        showCancelButton: false,
                                        allowOutsideClick: false,
                                        allowEscapeKey: false,
                                        timer: 3000,
                                        timerProgressBar: true,
                                        backdrop: `rgba(26,27,41,0.8)`,
                                    });
                                    // $("#webcam").prop('hidden',true);
                                    $("#prosesijin").prop('hidden',true);
                                    $("#btn-ijin").prop('hidden',false);
                                    // map.remove();
                                    // $("#map").prop('hidden',false);
                                    refreshMap(); // PESAN BERHASIL KIRIM MASIH TERTUMPUK DENGAN PESAN JARAK MAP (refreshMap)
                                    startFrontCamera();
                                    // $("#btn-gps").prop('hidden',false);
                                }
                            }
                        })
                    } else {
                        Swal.fire({
                            title: `Pesan Error`,
                            text: 'Silakan masukkan keterangan ijin pada input yang sudah disediakan',
                            icon: `warning`,
                            showConfirmButton: false,
                            showCancelButton: false,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            timer: 3000,
                            timerProgressBar: true,
                            backdrop: `rgba(26,27,41,0.8)`,
                        });
                    }
                } else { // JIKA SYARAT ABSEN TIDAK TERPENUHI
                    Swal.fire({
                        title: `Pesan Error`,
                        text: res.message,
                        icon: `warning`,
                        showConfirmButton: false,
                        showCancelButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        timer: 3000,
                        timerProgressBar: true,
                        backdrop: `rgba(26,27,41,0.8)`,
                    });
                }
            }
        })

        // Swal.fire({
        //     title: `Pesan Berhasil!`,
        //     text: 'Surat ijin dokter telah terkirim ke kepegawaian',
        //     icon: `success`,
        //     showConfirmButton: false,
        //     showCancelButton: false,
        //     allowOutsideClick: false,
        //     allowEscapeKey: false,
        //     timer: 3000,
        //     timerProgressBar: true,
        //     backdrop: `rgba(26,27,41,0.8)`,
        // });

    }

    function batalProsesIjin() {
        // $("#webcam").prop('hidden',true);
        $("#prosesijin").prop('hidden',true);
        $("#btn-ijin").prop('hidden',false);
        // map.remove();
        // $("#map").prop('hidden',false);
        refreshMap();
        startFrontCamera();
        // $("#btn-gps").prop('hidden',false);
    }

    function showSelfi() {
        // Webcam.set({
        //     height: 480,
        //     width: 0,
        //     image_format: 'jpeg',
        //     jpeg_quality: 80
        // });
        // Webcam.attach('.webcam-selfi1');
        $('#modalSelfi').modal('show');
    }

    function take_snapshot() {
        Webcam.snap( function(data_uri) {
            $(".image-tag").val(data_uri);
            document.getElementById('results').innerHTML = '<img src="'+data_uri+'"/>';
        } );

        // Swal.fire({
        //     title: `Absensi Berhasil`,
        //     text: 'Selamat beraktivitas!',
        //     icon: `success`,
        //     showConfirmButton: false,
        //     showCancelButton: false,
        //     allowOutsideClick: false,
        //     allowEscapeKey: false,
        //     timer: 3000,
        //     timerProgressBar: true,
        //     backdrop: `rgba(26,27,41,0.8)`,
        // });
    }

    // function swapCamera() {
    //     console.log(setcam);
    //     if (setcam == 1) {
    //         startRearCamera();
    //         setcam = 2;
    //     } else {
    //         startFrontCamera();
    //         setcam = 1;
    //     }
    // }

    function startFrontCamera() {
        Webcam.reset('#webcam');
        Webcam.set({
            // width: window.innerWidth,
            // height: window.innerHeight,
            width: 640,
            height: 480,
            image_format: 'jpeg',
            jpeg_quality: 90,
            flip_horiz: false,
            constraints: {
                facingMode: facing,
                width: { ideal: 640 },
                height: { ideal: 480 }
            }
        });
        Webcam.attach('#webcam');
    }

    function startFrontCameraFlip() {
        Webcam.reset('#webcam');
        Webcam.set({
            // width: window.innerWidth,
            // height: window.innerHeight,
            width: 640,
            height: 480,
            image_format: 'jpeg',
            jpeg_quality: 90,
            flip_horiz: true,
            constraints: {
                facingMode: facing,
                width: { ideal: 640 },
                height: { ideal: 480 }
            }
        });
        Webcam.attach('#webcam');
    }

    function startRearCamera() {
        Webcam.reset('#webcam');
        Webcam.set({
            width: 640,
            height: 480,
            image_format: 'jpeg',
            jpeg_quality: 90,
            fps: 60,
            constraints: {
                facingMode: 'environment',
                width: { ideal: 640 },
                height: { ideal: 480 }
            }
        });
        Webcam.attach('#webcam');
    }

    function stopCameraMap() {
        map.remove();
        $("#map").prop('hidden',false);
        Webcam.reset('#webcam');
        $("#webcam").prop('hidden',true);
    }

    function convertM2H(t) {
        var hours = Number.parseInt(t / 60);
        var minutes = t % 60; // assuming t is the time in minutes

        return `${String(hours).padStart(2,"0")}:${String(minutes).padStart(2,"0")}`
    }

    // TEXT MESSAGE
    function pesanError(message) {
        $("#alerts").empty().append(`<div class="card bg-red-dark alert-dismissible show shadow-bg shadow-bg-m fade p-0 mb-3 rounded-m">
                                    <div class="content my-3">
                                        <div class="d-flex">
                                            <div class="align-self-center">
                                                <i class="bi bi-exclamation-triangle font-36 color-white d-block"></i>
                                            </div>
                                            <div class="align-self-center">
                                                <p style="text-align: justify;" class="color-white mb-0 font-500 font-13 ps-3 pe-4 line-height-s">
                                                    Perhatian! <br> ${message}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>`);
    }
    function pesanWarning(message) {
        $("#alerts").empty().append(`<div class="card bg-brown-dark alert-dismissible show shadow-bg shadow-bg-m fade p-0 mb-3 rounded-m">
                                    <div class="content my-3">
                                        <div class="d-flex">
                                            <div class="align-self-center">
                                                <i class="bi bi-exclamation-triangle font-36 color-white d-block"></i>
                                            </div>
                                            <div class="align-self-center">
                                                <p style="text-align: justify;" class="color-white mb-0 font-500 font-13 ps-3 pe-4 line-height-s">
                                                    ${message}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>`);
    }
    function pesanSukses(message) {
        console.log(message);
        $("#alerts").empty().append(`<div class="card bg-blue-dark alert-dismissible show shadow-bg shadow-bg-m fade p-0 mb-3 rounded-m">
                                    <div class="content my-3">
                                        <div class="d-flex">
                                            <div class="align-self-center">
                                                <i class="bi bi-exclamation-triangle font-36 color-white d-block"></i>
                                            </div>
                                            <div class="align-self-center">
                                                <p style="text-align: justify;" class="color-white mb-0 font-500 font-13 ps-3 pe-4 line-height-s">
                                                    ${message}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>`);
    }
</script>
@endsection
