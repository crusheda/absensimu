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
                <div class="alert alert-secondary">
                    <h6 class="text-center mb-3">Panduan Absensi</h6>
                    <i class="ti ti-arrow-narrow-right me-1">#</i>
                </div>
                <input type="text" class="form-control" id="lokasi" hidden>
                <div id="map" class="mb-3"></div>
                <div id="hiddenButton1" hidden>
                    <center>
                        <button type="button" class="btn btn-secondary me-2 mb-3" onclick="prosesOnCall()" disabled><i class="ti ti-activity"></i> On Call</button>
                        <button type="button" class="btn btn-secondary me-2 mb-3" onclick="prosesIjin()" disabled><i class="ti ti-stethoscope"></i> Ijin Sakit</button>
                        <button type="button" class="btn btn-danger me-2 mb-3" onclick="prosesPulang()" id="btn-pulang" disabled><i class="ti ti-plane-departure"></i> Absen Pulang</button>
                        <button type="button" class="btn btn-primary mb-3" onclick="prosesMasuk()" id="btn-masuk" disabled><i class="ti ti-plane-arrival"></i> Absen Masuk</button>
                    </center>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var map;
    $(document).ready(function() {
        // Webcam.set({
        //     height: 480,
        //     width: 0,
        //     image_format: 'jpeg',
        //     jpeg_quality: 80
        // });
        // Webcam.attach('.webcam-selfi');

        init();
        refreshMap();
        // validation();
    })

    function init() {
        $.ajax({
            url: "/api/kepegawaian/absensi/{{ Auth::user()->id }}",
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                console.log(res);
                $("#hiddenButton1").prop('hidden',true);
                if (res.show == null) {
                    $("#btn-pulang").prop('disabled',true).removeClass('btn-danger').addClass('btn-secondary');
                    $("#btn-masuk").prop('disabled',false).removeClass('btn-secondary').addClass('btn-primary');
                } else {
                    $("#btn-pulang").prop('disabled',false).removeClass('btn-secondary').addClass('btn-danger');
                    $("#btn-masuk").prop('disabled',true).removeClass('btn-primary').addClass('btn-secondary');
                }
                $("#hiddenButton1").prop('hidden',false);
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
        // VALIDATION
        $.ajax({
            url: "/api/kepegawaian/absensi/validate/jadwal/{{ Auth::user()->id }}",
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.code == 200) { // JIKA SYARAT ABSEN TERPENUHI
                    // INIT
                    var save = new FormData();
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
                        url: "{{route('kepegawaian.absensi.executeAbsensi')}}",
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

    function take_snapshot() {
        // Webcam.snap( function(data_uri) {
        //     $(".image-tag").val(data_uri);
        //     document.getElementById('results').innerHTML = '<img src="'+data_uri+'"/>';
        // } );

        Swal.fire({
            title: `Absensi Berhasil`,
            text: 'Selamat beraktivitas!',
            icon: `success`,
            showConfirmButton: false,
            showCancelButton: false,
            allowOutsideClick: false,
            allowEscapeKey: false,
            timer: 3000,
            timerProgressBar: true,
            backdrop: `rgba(26,27,41,0.8)`,
        });
    }
</script>
@endsection
