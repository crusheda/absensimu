@extends('layouts.home2')

@section('content')
<style>
    #map { height: 400px;},
    /*.webcam-selfi,*/
    .webcam-selfi video {
        /*display: inline-block; */
        width: auto;
        height: auto !important;
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
                {{-- <div class="alert alert-secondary">
                    <h6 class="text-center mb-3">Panduan Absensi</h6>
                    <i class="ti ti-arrow-narrow-right me-1">#</i>
                </div> --}}
                {{-- MAP --}}
                <input type="text" class="form-control" id="lokasi" hidden>
                <div id="map" class="mb-3"></div>
                {{-- PHOTO --}}
                <input type="hidden" name="image" id="image-capture" class="image-tag">
                <center><div id="webcam" class="webcam-selfi mb-3" hidden></div></center>
                {{-- <input type="button" class="form-control" value="Take Snapshot" onClick="take_snapshot()"> --}}
                <div id="hiddenButton" hidden>
                    <h6 class="text-center">Jadwal Tidak Ditemukan. Silakan menghubungi Admin.</h6>
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

<div class="modal animate__animated animate__rubberBand fade" id="modalSelfi" tabindex="-1" aria-hidden="true">
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
</div>

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
                if (res.jadwal != null) { // JIKA BELUM MENAMBAH JADWAL / BELUM DIVERIFIKASI OLEH KEPEGAWAIAN
                    $("#btn-ijin").prop('hidden',false);
                    if (res.ijin == null) {
                        $("#btn-ijin").prop('disabled',false).removeClass('btn-secondary btn-warning').addClass('btn-warning');
                    } else {
                        $("#btn-ijin").prop('disabled',true).removeClass('btn-secondary btn-warning').addClass('btn-secondary');
                    }
                    $("#prosesijin").prop('hidden',true);
                } else {
                    $("#btn-ijin").prop('hidden',true);
                    $("#prosesijin").prop('hidden',true);
                }

                if (jarak > 30) {
                    $("#hiddenButton").prop('hidden',true);
                    $("#hiddenButton1").prop('hidden',true);
                    $("#hiddenButton2").prop('hidden',true); // ABSEN + ONCALL MUNCUL
                } else {
                    // $("#map").prop('hidden',false); // INIT MAP
                    console.log("{{ Auth::user()->getPermission('absensi_oncall') }}");
                    if ("{{ Auth::user()->getPermission('absensi_oncall') }}" == true) { // USER MEMILIKI AKSES ONCALL
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
                            if (res.show == null) { // DATA ABSEN MASIH KOSONG
                                if (res.oncall == null) { // DATA ONCALL MASIH KOSONG
                                    $("#btn-mulai").prop('hidden',false);
                                    $("#btn-oncall-mulai").prop('disabled',false).removeClass('btn-secondary').addClass('btn-pink');
                                    $("#btn-shift-mulai").prop('disabled',false).removeClass('btn-secondary').addClass('btn-primary');
                                    // $("#btn-oncall-selesai").prop('disabled',true).removeClass('btn-danger').addClass('btn-secondary');
                                    // $("#btn-pulang-oc").prop('disabled',true).removeClass('btn-danger').addClass('btn-secondary');
                                    // $("#btn-pulang-oncall").prop('hidden',true).removeClass('btn-oren').addClass('btn-secondary');
                                    // $("#btn-oncall-pulang").prop('hidden',true).removeClass('btn-success').addClass('btn-secondary');
                                } else {
                                    $("#btn-selesai").prop('hidden',false);
                                    $("#btn-oncall-selesai").prop('disabled',false).removeClass('btn-secondary').addClass('btn-danger');
                                    $("#btn-shift-oncall").prop('disabled',false).removeClass('btn-secondary').addClass('btn-oren');
                                    $("#btn-oncall-shift").prop('disabled',false).removeClass('btn-secondary').addClass('btn-success');
                                    $("#btn-shift-selesai").prop('disabled',false).removeClass('btn-secondary').addClass('btn-danger');
                                }
                            } else {

                            }
                        }
                    } else { // KHUSUS USER TANPA AKSES ONCALL
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
                            $("#btn-masuk").prop('disabled',true);
                            $("#btn-pulang").prop('disabled',true);
                        } else {
                            $("#hiddenButton").prop('hidden',true);
                            $("#hiddenButton1").prop('hidden',true);
                            if (res.show == null) { // JIKA ABSEN HARI INI MASIH KOSONG
                                $("#btn-pulang").prop('disabled',true).removeClass('btn-danger').addClass('btn-secondary');
                                $("#btn-masuk").prop('disabled',false).removeClass('btn-secondary').addClass('btn-primary');
                            } else {
                                if (res.show.tgl_out == null) {
                                    $("#btn-pulang").prop('disabled',false).removeClass('btn-secondary').addClass('btn-danger');
                                    $("#btn-masuk").prop('disabled',true).removeClass('btn-primary').addClass('btn-secondary');
                                } else { // JIKA JADWAL ABSEN HARI SUDAH TERISI LENGKAP
                                    $("#btn-pulang").prop('disabled',true).removeClass('btn-danger').addClass('btn-secondary');
                                    $("#btn-masuk").prop('disabled',true).removeClass('btn-primary').addClass('btn-secondary');
                                }
                            }
                            $("#hiddenButton1").prop('hidden',false);
                        }
                    }
                }
            }
        })
    }

    function showIjin() {
        Webcam.reset('.webcam-selfi');
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
        Swal.fire({
            title: `Pesan Berhasil!`,
            text: 'Surat ijin dokter telah terkirim ke kepegawaian',
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
        Webcam.reset('.webcam-selfi');
        $("#prosesijin").prop('hidden',true);
        $("#btn-ijin").prop('hidden',false);
        map.remove();
        $("#map").prop('hidden',false);
        refreshMap(); // PESAN BERHASIL KIRIM MASIH TERTUMPUK DENGAN PESAN JARAK MAP (refreshMap)
        $("#btn-gps").prop('hidden',false);
    }

    function batalProsesIjin() {
        $("#webcam").prop('hidden',true);
        Webcam.reset('.webcam-selfi');
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
        $("#webcam").prop('hidden',true);
        Webcam.reset('.webcam-selfi');
        refreshMap();
    }

    function refreshMap() {
        const x = document.getElementById("lokasi");
        if (navigator.geolocation) {
            // navigator.geolocation.getCurrentPosition(showPosition);
            navigator.geolocation.getCurrentPosition(position => {
                const { latitude, longitude } = position.coords;
                // Show a map centered at latitude / longitude.
                map = L.map('map',{
                    keyboard: false,
                    zoomControl: false,
                    boxZoom: false,
                    doubleClickZoom: false,
                    tap: false,
                    touchZoom: false,
                    // center: [51.505, -0.09],
                    // zoom: 13,
                    // minZoom: 13,
                    scrollWheelZoom: false,
                    dragging: false,
                    doubleClickZoom: false,
                }).setView([position.coords.latitude, position.coords.longitude], 18);
                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    // attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                }).addTo(map);

                // Titik Lokasi GPS
                var marker = new L.Marker([position.coords.latitude, position.coords.longitude]);
                marker.addTo(map);

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
                                html: 'Silakan melakukan absensi!<br>Jarak Anda <b>'+res+' meter</b> dari titik lokasi',
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
                        url: "{{route('kepegawaian.absensi.executeBerangkat')}}",
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
            height: 600,
            width: 600,
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
            height: 600,
            width: 600,
            image_format: 'jpeg',
            jpeg_quality: 50,
            fps: 60,
            constraints: { facingMode: 'environment' }
        });
        Webcam.attach('.webcam-selfi');
    }
</script>
@endsection
