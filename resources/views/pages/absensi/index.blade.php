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
                <h6>Lokasi Anda Saat Ini</h6>
                <input type="text" class="form-control" id="lokasi" hidden>
                <div id="map" class="mb-3"></div>
                <button type="button" class="btn btn-primary right-content" onclick="validation()"><i class="ti ti-map-pin me-1"></i> Absen Masuk</button>
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
        refreshMap();
        // validation();
    })

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
                var circle = L.circle([-7.733248, 110.559232], { // RSPKUSKH COORD : -7.6378845, 110.868032
                    color: 'red',
                    fillColor: '#f03',
                    fillOpacity: 0.5,
                    radius: 50
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
                        if (res > 100) {
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

    // function validation() {
    //     // initialize
    //     var save = new FormData();
    //     save.append('lokasi',$("#lokasi").val());
    //     console.log($("#lokasi").val());
    //     $.ajax({
    //         headers: {
    //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //         },
    //         url: "{{route('kepegawaian.absensi.getDistance')}}",
    //         method: 'POST',
    //         data: save,
    //         cache: false,
    //         contentType: false,
    //         processData: false,
    //         dataType: 'json',
    //         success: function(res) {
    //             if (res > 100) {
    //             Swal.fire({
    //                 title: `Anda berada di luar area Rumah Sakit`,
    //                 text: 'Mendekatlah ke titik lokasi terdekat Absensi!',
    //                 icon: `error`,
    //                 showConfirmButton: false,
    //                 showCancelButton: false,
    //                 allowOutsideClick: true,
    //                 allowEscapeKey: false,
    //                 timer: 3000,
    //                 timerProgressBar: true,
    //                 backdrop: `rgba(26,27,41,0.8)`,
    //             });
    //             } else {
    //                 Swal.fire({
    //                     title: `Anda berada di dalam area Rumah Sakit`,
    //                     text: 'Silakan melakukan absensi!',
    //                     icon: `success`,
    //                     showConfirmButton: false,
    //                     showCancelButton: false,
    //                     allowOutsideClick: true,
    //                     allowEscapeKey: false,
    //                     timer: 3000,
    //                     timerProgressBar: true,
    //                     backdrop: `rgba(26,27,41,0.8)`,
    //                 });
    //             }
    //         }
    //     })
    // }

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
