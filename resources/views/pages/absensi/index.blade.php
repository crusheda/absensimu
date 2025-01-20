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
                <center><div id="webcam" class="webcam-selfi mb-3"></div></center>
                {{-- <input type="button" class="form-control" value="Take Snapshot" onClick="take_snapshot()"> --}}
                <div id="hiddenButton" hidden>
                    <h6 class="text-center">Jadwal Tidak Ditemukan. Silakan menghubungi Admin.</h6>
                </div>
                <div id="hiddenButton1" hidden> {{-- SELAIN ONCALL --}}
                    <center>
                        <button type="button" class="btn btn-secondary me-2 mb-3" onclick="prosesIjin()" id="btn-ijin" disabled><i class="ti ti-stethoscope"></i> Ijin Sakit</button>
                        <button type="button" class="btn btn-danger me-2 mb-3" onclick="prosesPulang()" id="btn-pulang" disabled><i class="ti ti-plane-departure"></i> Absen Pulang</button>
                        <button type="button" class="btn btn-primary mb-3" onclick="prosesMasuk()" id="btn-masuk" disabled><i class="ti ti-plane-arrival"></i> Absen Masuk</button>
                    </center>
                </div>
                <div id="hiddenButton2" hidden> {{-- KHUSUS ONCALL --}}
                    <center>
                        <button type="button" class="btn btn-secondary me-2 mb-3" onclick="prosesIjin()" id="btn-ijin-oc" disabled><i class="ti ti-stethoscope"></i> Ijin Sakit</button>
                        <button type="button" class="btn btn-primary me-2 mb-3" onclick="prosesMasuk()" id="btn-masuk-oc" disabled><i class="ti ti-plane-arrival"></i> Absen Masuk</button>
                        <button type="button" class="btn btn-danger me-2 mb-3" onclick="prosesPulang()" id="btn-pulang-oc" disabled><i class="ti ti-plane-departure"></i> Absen Pulang</button>
                        <button type="button" class="btn btn-secondary me-2 mb-3" onclick="prosesMulaiOnCall()" id="btn-oncall-mulai" disabled><i class="ti ti-activity"></i> Mulai On Call</button>
                        <button type="button" class="btn btn-secondary me-2 mb-3" onclick="prosesSelesaiOnCall()" id="btn-oncall-selesai" disabled><i class="ti ti-activity"></i> Selesai On Call</button>
                        <button type="button" class="btn btn-secondary me-2 mb-3" onclick="prosesPulangLanjutOnCall()" id="btn-pulang-oncall" disabled><i class="ti ti-activity"></i> Absen Pulang & Selesai Oncall</button>
                        <button type="button" class="btn btn-secondary mb-3" onclick="prosesOnCallLanjutPulang()" id="btn-oncall-pulang" disabled><i class="ti ti-activity"></i> Selesai Oncall & Absen Pulang</button>
                    </center>
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
        init();
        refreshMap();
        startFrontCamera();
        // validation();
    })

    function init() {
        $.ajax({
            url: "/api/kepegawaian/absensi/{{ Auth::user()->id }}",
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                console.log("{{ Auth::user()->getPermission('absensi_oncall') }}");
                // console.log(res);
                if ("{{ Auth::user()->getPermission('absensi_oncall') }}" == true) { // USER MEMILIKI AKSES ONCALL
                    $("#hiddenButton1").prop('hidden',true);
                    if (res.jadwal == null) { // JIKA BELUM MENAMBAH JADWAL / BELUM DIVERIFIKASI OLEH KEPEGAWAIAN
                        $("#hiddenButton").prop('hidden',false);
                        $("#hiddenButton2").prop('hidden',true);
                    } else {
                        $("#hiddenButton2").prop('hidden',false);
                        $("#hiddenButton").prop('hidden',true);
                        if (res.show == null && res.oncall == null) { // DATA ABSEN MAUPUN ONCALL MASIH KOSONG
                            $("#btn-oncall").prop('hidden',false);
                            $("#btn-masuk").prop('disabled',false);
                            $("#btn-pulang").prop('disabled',true);
                            $("#btn-ijin").prop('disabled',false);
                        } else {
                            if (res.show == null && res.oncall != null) { // TERDAPAT ONCALL AKTIF TANPA MASUK SHIFT
                                $("#btn-oncall").prop('hidden',true);
                                $("#btn-masuk").prop('disabled',false);
                                $("#btn-pulang").prop('disabled',false);
                                $("#btn-ijin").prop('disabled',false);
                            } else {

                            }
                        }
                    }
                } else {
                    // INIT DISABLED BUTTON ONCALL
                    $("#hiddenButton2").prop('hidden',true);
                    $("#btn-masuk-oc").prop('disabled',true);
                    $("#btn-pulang-oc").prop('disabled',true);
                    $("#btn-ijin-oc").prop('disabled',true);
                    $("#btn-oncall-mulai").prop('disabled',true);
                    $("#btn-oncall-selesai").prop('disabled',true);
                    $("#btn-pulang-oncall").prop('disabled',true);
                    $("#btn-oncall-pulang").prop('disabled',true);
                    // EXECUTE
                    if (res.jadwal == null) { // JIKA BELUM MENAMBAH JADWAL / BELUM DIVERIFIKASI OLEH KEPEGAWAIAN
                        $("#hiddenButton1").prop('hidden',true);
                        $("#btn-masuk").prop('disabled',true);
                        $("#btn-pulang").prop('disabled',true);
                        $("#btn-ijin").prop('disabled',true);
                        $("#hiddenButton").prop('hidden',false);
                    } else {
                        $("#hiddenButton").prop('hidden',true);
                        $("#hiddenButton1").prop('hidden',true);
                        $("#btn-ijin").prop('disabled',true);
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
        })
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
                    radius: 30
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
                        if (res > 30) {
                        Swal.fire({
                            title: `Anda berada di luar area Rumah Sakit`,
                            text: 'Jarak Anda '+res+'m dari lokasi Absensi!',
                            icon: `error`,
                            showConfirmButton: false,
                            showCancelButton: false,
                            allowOutsideClick: true,
                            allowEscapeKey: false,
                            timer: 3000,
                            timerProgressBar: true,
                            backdrop: `rgba(26,27,41,0.8)`,
                        });
                        } else {
                            Swal.fire({
                                title: `Anda berada di dalam area Rumah Sakit`,
                                text: 'Silakan melakukan absensi! '+res+'m',
                                icon: `success`,
                                showConfirmButton: false,
                                showCancelButton: false,
                                allowOutsideClick: true,
                                allowEscapeKey: false,
                                timer: 3000,
                                timerProgressBar: true,
                                backdrop: `rgba(26,27,41,0.8)`,
                            });
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
