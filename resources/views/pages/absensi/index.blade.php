@extends('layouts.home2')

@section('content')
<style>
    #map { height: 400px;},
    /*.webcam-selfi,*/
    .webcam-selfi video {
        /*display: inline-block; */
        width: auto;
        height: 0px !important;
        border-radius: 15px;
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
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                {{-- PHOTO --}}
                <input type="hidden" name="image" id="image-capture" class="image-tag">
                <div id="webcam" class="webcam-selfi mb-3" hidden></div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                {{-- <div class="alert alert-secondary">
                    <h5 class="text-center mb-3">Panduan Absensi</h5>
                    <small><b><a class="text-danger">WAJIB</a> menggunakan browser di bawah ini</b></small><br>
                    <i class="ti ti-arrow-narrow-right me-1">Iphone : Safari / Google Chrome</i><br>
                    <i class="ti ti-arrow-narrow-right me-1">Android : Firefox</i>
                </div> --}}
                {{-- MAP --}}
                <input type="text" class="form-control" id="lokasi" hidden>
                <div id="map" class="mb-3"></div>
                {{-- <input type="button" class="form-control" value="Take Snapshot" onClick="take_snapshot()"> --}}
                <div id="hiddenButton" hidden>
                    <h6 class="text-center">Jadwal tidak ditemukan atau belum tervalidasi. Silakan menghubungi Admin.</h6>
                </div>
                <div id="hiddenButton1" hidden> {{-- SELAIN ONCALL --}}
                    <center>
                        <div class="btn-group btn-block mb-3" id="btn-biasa" hidden>
                            <button type="button" class="btn btn-danger" onclick="prosesPulang()" id="btn-pulang" disabled><i class="ti ti-plane-departure"></i> Absen Pulang</button>
                            <button type="button" class="btn btn-primary" onclick="prosesMasuk()" id="btn-masuk" disabled><i class="ti ti-plane-arrival"></i> Absen Masuk</button>
                        </div>
                    </center>
                </div>
                <div id="hiddenButton2" hidden> {{-- KHUSUS ONCALL --}}
                    <center>
                        <div class="btn-group btn-block mb-3" id="btn-mulai" hidden>
                            <button type="button" class="btn btn-pink" onclick="prosesMulaiOnCall()" id="btn-oncall-mulai" disabled><i class="ti ti-vaccine"></i> Mulai On Call</button>
                            <button type="button" class="btn btn-primary" onclick="prosesMasuk()" id="btn-shift-mulai" disabled><i class="ti ti-plane-arrival"></i> Absen Masuk</button>
                        </div>
                        <div class="btn-group btn-block mb-3" id="btn-selesai" hidden>
                            <button type="button" class="btn btn-danger" onclick="prosesSelesaiOnCall()" id="btn-oncall-selesai" disabled><i class="ti ti-activity"></i> Selesai On Call</button>
                            <button type="button" class="btn btn-danger" onclick="prosesPulang()" id="btn-shift-selesai" disabled><i class="ti ti-plane-departure"></i> Absen Pulang</button>
                        </div>
                        <div class="btn-group btn-block mb-3" id="btn-mix" hidden>
                            <button type="button" class="btn btn-success" onclick="prosesOnCallLanjutPulang()" id="btn-oncall-shift" disabled><i class="ti ti-phone-outgoing"></i> Selesai Oncall & Absen Pulang</button>
                            <button type="button" class="btn btn-oren" onclick="prosesPulangLanjutOnCall()" id="btn-shift-oncall" disabled><i class="ti ti-phone-incoming"></i> Absen Pulang & Selesai Oncall</button>
                        </div>
                    </center>
                </div>
                <button type="button" class="btn btn-block btn-warning mb-3" onclick="showIjin()" id="btn-ijin" disabled><i class="ti ti-stethoscope"></i> Pengajuan Ijin Sakit</button>
                <div class="btn-group btn-block" id="prosesijin" hidden>
                    <button type="button" class="btn btn-dark" onclick="batalProsesIjin()" id="btn-batal-proses-ijin"><i class="ti ti-arrow-back"></i> Batal</button>
                    <button type="button" class="btn btn-info" onclick="prosesIjin()" id="btn-proses-ijin"><i class="ti ti-send"></i> Kirim Surat Ijin</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- <div class="modal animate__animated animate__rubberBand fade" id="modalSelfi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-simple modal-add-new-address modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    Selfi Photo
                </h4>
            </div>
            <div class="modal-body">
                <div id="results" style="height:auto;width:auto"></div>
            </div>
            <div class="modal-footer">
                <button type="submit" id="btn-proses" class="btn btn-primary me-sm-3 me-1" onclick="prosesAbsen()"><i class="fa fa-trash me-1" style="font-size:13px"></i> Hapus</button>
                <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times me-1" style="font-size:13px"></i> Batal</button>
            </div>
        </div>
    </div>
</div> --}}

<script>
    var map;
    $(document).ready(function() {
        refreshMap();
        // validation();
    })

    function init(jarak) {
        $.ajax({
            url: "/api/kepegawaian/absensi/{{ Auth::user()->id }}",
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.jadwal != null) { // JIKA SUDAH MENAMBAH JADWAL & TELAH DIVALIDASI OLEH KEPEGAWAIAN
                    $("#btn-ijin").prop('hidden',false);
                    if (res.show == null && res.ijin == null) {
                        $("#btn-ijin").prop('disabled',false).removeClass('btn-secondary btn-warning').addClass('btn-warning');
                    } else {
                        $("#btn-ijin").prop('disabled',true).removeClass('btn-secondary btn-warning').addClass('btn-secondary');
                        console.log('IJIN SUDAH TERISI UNTUK HARI INI');
                    }
                    $("#prosesijin").prop('hidden',true);
                } else {
                    $("#btn-ijin").prop('hidden',true);
                    $("#prosesijin").prop('hidden',true);
                    console.log('JADWAL TIDAK VALID');
                }

                if (jarak > 30) {
                    $("#hiddenButton").prop('hidden',true);
                    $("#hiddenButton1").prop('hidden',true);
                    $("#hiddenButton2").prop('hidden',true); // ABSEN + ONCALL MUNCUL
                    console.log('TITIK LOKASI GPS LEBIH DARI 30 METER');
                } else {
                    if ("{{ Auth::user()->getPermission('absensi_oncall') }}" == true || "{{ Auth::user()->getPermission('absensi_oncall') }}" || "{{ Auth::user()->getPermission('absensi_oncall') }}" != '') { // USER MEMILIKI AKSES ONCALL
                        console.log('USER ONCALL');
                        $("#btn-biasa").prop('hidden',true);
                        $("#hiddenButton1").prop('hidden',true);
                        if (res.jadwal == null) { // JIKA BELUM MENAMBAH JADWAL / BELUM DIVERIFIKASI OLEH KEPEGAWAIAN
                            $("#hiddenButton").prop('hidden',false); // PERINGATAN JADWAL BELUM TERISI
                            $("#hiddenButton2").prop('hidden',true);
                        } else {
                            $("#hiddenButton2").prop('hidden',false); // ABSEN + ONCALL MUNCUL
                            $("#hiddenButton").prop('hidden',true);
                            $("#btn-mulai").prop('hidden',true);
                            $("#btn-selesai").prop('hidden',true);
                            $("#btn-mix").prop('hidden',true);
                            if (res.ijin == null) { // JIKA BELUM MENGAJUKAN SURAT IJIN
                                if (res.show == null) { // DATA ABSEN MASIH KOSONG
                                    if (res.oncall == null) { // DATA ONCALL MASIH KOSONG
                                        $("#btn-mulai").prop('hidden',false);
                                        $("#btn-oncall-mulai").prop('disabled',false).removeClass('btn-secondary').addClass('btn-pink');
                                        $("#btn-shift-mulai").prop('disabled',false).removeClass('btn-secondary').addClass('btn-primary');
                                    } else {
                                        $("#btn-selesai").prop('hidden',false);
                                        $("#btn-oncall-selesai").prop('disabled',false).removeClass('btn-secondary').addClass('btn-danger');
                                        $("#btn-shift-oncall").prop('disabled',false).removeClass('btn-secondary').addClass('btn-oren');
                                        $("#btn-oncall-shift").prop('disabled',false).removeClass('btn-secondary').addClass('btn-success');
                                        $("#btn-shift-selesai").prop('disabled',false).removeClass('btn-secondary').addClass('btn-danger');
                                    }
                                } else {

                                }
                            } else {
                                $("#btn-mulai").prop('hidden',false);
                                $("#btn-oncall-mulai").prop('disabled',true).removeClass('btn-pink').addClass('btn-secondary');
                                $("#btn-shift-mulai").prop('disabled',true).removeClass('btn-primary').addClass('btn-secondary');
                            }
                        }
                    } else { // KHUSUS USER TANPA AKSES ONCALL
                        console.log('USER BIASA');
                        // INIT DISABLED BUTTON ONCALL
                        $("#btn-mulai").prop('hidden',true);
                        $("#btn-selesai").prop('hidden',true);
                        $("#btn-mix").prop('hidden',true);
                        $("#hiddenButton2").prop('hidden',true);
                        // $("#btn-shift-mulai").prop('disabled',true);
                        // $("#btn-shift-selesai").prop('disabled',true);
                        // $("#btn-oncall-mulai").prop('disabled',true);
                        // $("#btn-oncall-selesai").prop('disabled',true);
                        // $("#btn-shift-oncall").prop('disabled',true);
                        // $("#btn-oncall-shift").prop('disabled',true);
                        // $("#btn-ijin-oc").prop('disabled',true);
                        // EXECUTE
                        if (res.jadwal == null) { // JIKA BELUM MENAMBAH JADWAL / BELUM DIVERIFIKASI OLEH KEPEGAWAIAN
                            $("#hiddenButton1").prop('hidden',true);
                            $("#hiddenButton").prop('hidden',false);
                            $("#btn-biasa").prop('hidden',true);
                            $("#btn-masuk").prop('disabled',true);
                            $("#btn-pulang").prop('disabled',true);
                            console.log('TIDAK ADA JADWAL DITEMUKAN');
                            // stopCameraMap();
                        } else {
                            console.log('JADWAL DITEMUKAN');
                            $("#hiddenButton1").prop('hidden',true);
                            $("#hiddenButton").prop('hidden',true);
                            $("#btn-biasa").prop('hidden',false);
                            if (res.ijin == null) { // JIKA IJIN MASIH KOSONG
                                th = new Date().getHours(); // get Jam = 0-23
                                tm = new Date().getMinutes(); // get Menit = 0-59
                                ts = new Date().getSeconds(); // get Detik = 0-59
                                if (res.show == null) { // JIKA ABSEN HARI INI MASIH KOSONG
                                    const thisD = new Date().toLocaleDateString('en-CA'); // YYYY-MM-DD
                                    const tomorrow = new Date();
                                    tomorrow.setDate(tomorrow.getDate() + 1);
                                    const thisT = tomorrow.toLocaleDateString('en-CA');
                                    dbMasuk = new Date(thisD+' '+res.shift.berangkat);
                                    dbPulang = new Date(thisD+' '+res.shift.pulang);
                                    if (res.shift.pulang > res.shift.berangkat) { // JIKA ABSENSI TIDAK LEWAT HARI
                                        if (th >= dbPulang.getHours()) { // Jika Jam Absen Masuk Lebih dari sama dgn Jam pulang
                                            $("#btn-pulang").prop('disabled',true).removeClass('btn-danger').addClass('btn-secondary');
                                            $("#btn-masuk").prop('disabled',true).removeClass('btn-primary').addClass('btn-secondary');
                                            console.log('Jam Absen Masuk Lebih dari sama dgn Jam pulang');
                                        } else { // JIKA ABSENSI SEBELUM JAM PULANG
                                            if (th >= dbMasuk.getHours() - 1) { // MINIMAL ABSENSI 1 JAM SEBELUM JAM MASUK
                                                $("#btn-pulang").prop('disabled',true).removeClass('btn-danger').addClass('btn-secondary');
                                                $("#btn-masuk").prop('disabled',false).removeClass('btn-secondary').addClass('btn-primary');
                                            } else { // JIKA ABSENSI DILUAR ANTARA JAM MASUK DAN JAM PULANG
                                                $("#btn-pulang").prop('disabled',true).removeClass('btn-danger').addClass('btn-secondary');
                                                $("#btn-masuk").prop('disabled',true).removeClass('btn-primary').addClass('btn-secondary');
                                                console.log('Jam Absen Masuk Tidak pada/antara jam masuk (-1 jam) dan jam pulang');
                                            }
                                        }
                                    } else { // JIKA ABSENSI LEWAT HARI (MALAM ke PAGI)
                                        if (th >= dbMasuk.getHours() - 1) {
                                            $("#btn-pulang").prop('disabled',true).removeClass('btn-danger').addClass('btn-secondary');
                                            $("#btn-masuk").prop('disabled',false).removeClass('btn-secondary').addClass('btn-primary');
                                        } else {
                                            $("#btn-pulang").prop('disabled',true).removeClass('btn-danger').addClass('btn-secondary');
                                            $("#btn-masuk").prop('disabled',true).removeClass('btn-primary').addClass('btn-secondary');
                                        }
                                    }
                                } else { // ABSEN PULANG
                                    if (res.show.tgl_out == null) {
                                        now = new Date().toLocaleDateString('en-CA'); // YYYY-MM-DD
                                        dbPulang = new Date(res.show.ref_jam_pulang);
                                        dayOut = dbPulang.toLocaleDateString('en-CA');
                                        if (now == dayOut) {
                                            if (th >= dbPulang.getHours()) {

                                            } else {

                                            }
                                        } else {

                                        }
                                    } else { // JIKA JADWAL ABSEN HARI SUDAH TERISI LENGKAP
                                        $("#btn-pulang").prop('disabled',true).removeClass('btn-danger').addClass('btn-secondary');
                                        $("#btn-masuk").prop('disabled',true).removeClass('btn-primary').addClass('btn-secondary');
                                    }
                                }
                            } else {
                                $("#btn-pulang").prop('disabled',true).removeClass('btn-danger').addClass('btn-secondary');
                                $("#btn-masuk").prop('disabled',true).removeClass('btn-primary').addClass('btn-secondary');
                            }
                            $("#hiddenButton1").prop('hidden',false);
                        }
                    }
                }
            }
        })
    }

    function showIjin() {
        $("#webcam").prop('hidden',false);
        $("#hiddenButton").prop('hidden',true);
        $("#hiddenButton1").prop('hidden',true);
        $("#hiddenButton2").prop('hidden',true);
        $("#map").prop('hidden',true);
        $("#btn-ijin").prop('hidden',true);
        $("#prosesijin").prop('hidden',false);
        $("#btn-gps").prop('hidden',true);
        Swal.fire({
            title: `Pesan Lanjutan!`,
            html: 'Silakan foto surat ijin dokter lalu tekan tombol <b class="text-info"><u>KIRIM</u></b>',
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
                    // INIT
                    Webcam.snap( function(data_uri) {
                        $("#image-capture").val(data_uri);
                    } );
                    var save = new FormData();
                    save.append('image',$("#image-capture").val());
                    save.append('lokasi',$("#lokasi").val());
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
                                $("#webcam").prop('hidden',true);
                                $("#prosesijin").prop('hidden',true);
                                $("#btn-ijin").prop('hidden',false);
                                map.remove();
                                $("#map").prop('hidden',false);
                                refreshMap(); // PESAN BERHASIL KIRIM MASIH TERTUMPUK DENGAN PESAN JARAK MAP (refreshMap)
                                $("#btn-gps").prop('hidden',false);
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
        $("#webcam").prop('hidden',true);
        $("#prosesijin").prop('hidden',true);
        $("#btn-ijin").prop('hidden',false);
        map.remove();
        $("#map").prop('hidden',false);
        refreshMap();
        $("#btn-gps").prop('hidden',false);
    }

    function reaccurate() {
        map.remove();
        // $("#map").prop('hidden',true);
        // $("#webcam").prop('hidden',true);
        // Webcam.reset('.webcam-selfi');
        refreshMap();
    }

    function refreshMap() {
        const x = document.getElementById("lokasi");
        // if (map) {
        //     map.remove();
        // }
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

            getLocationPromise.then((location) => {
                console.log('latitude : '+lat);
                console.log('longitude : '+long);
                // ATTENTION
                var save = new FormData();
                save.append('lokasi',lat + ", " + long);
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
                            // Swal.fire({
                            //     title: `Anda berada di dalam area Rumah Sakit`,
                            //     html: 'Anda sudah di area Absensi!<br>Jarak Anda <b>'+res+' meter</b> dari titik lokasi',
                            //     icon: `success`,
                            //     showConfirmButton: false,
                            //     showCancelButton: false,
                            //     allowOutsideClick: true,
                            //     allowEscapeKey: false,
                            //     timer: 5000,
                            //     timerProgressBar: true,
                            //     backdrop: `rgba(26,27,41,0.8)`,
                            // });
                            $("#map").prop('hidden',true);
                            $("#webcam").prop('hidden',false);
                            startFrontCamera();
                        }
                    }
                })
            }).catch((err) => {
                console.log(err)
            })
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
                                init();
                                reaccurate();
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
                }
            }
        })
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
                    var save = new FormData();
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
                                init();
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
                }
            }
        })
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

    function startFrontCamera() {
        Webcam.reset('.webcam-selfi');
        Webcam.set({
            height: '600',
            width: 0,
            image_format: 'jpeg',
            jpeg_quality: 50,
            fps: 60,
            flip_horiz: true
        });
        Webcam.attach('.webcam-selfi');
    }

    function startRearCamera() {
        Webcam.reset('.webcam-selfi');
        Webcam.set({
            height: '600',
            width: 0,
            image_format: 'jpeg',
            jpeg_quality: 50,
            fps: 60,
            constraints: { facingMode: 'environment' }
        });
        Webcam.attach('.webcam-selfi');
    }

    function stopCameraMap() {
        map.remove();
        $("#map").prop('hidden',false);
        Webcam.reset('.webcam-selfi');
        $("#webcam").prop('hidden',true);
    }
</script>
@endsection
