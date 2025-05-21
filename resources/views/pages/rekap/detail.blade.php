@extends('layouts.index')

@section('content')

@include('inc.topbutton')

@include('inc.footerbar')

<div class="page-content header-clear-medium">

    {{-- <div class="content">
        <div class="row">
            <div class="col-6">
                <a href="#" class="btn btn-full btn-m btn-border rounded-sm bg-theme shadow-l color-highlight text-uppercase font-800"><i class="fa fa-heart pe-3"></i>Save</a>
            </div>
            <div class="col-6">
                <a href="#" class="btn btn-full btn-m btn-border rounded-sm bg-theme shadow-l color-blue-dark text-uppercase font-800"><i class="fa fa-cloud pe-3"></i>Download</a>
            </div>
        </div>
    </div> --}}

    <div class="card card-style">
        <div class="content">
            <div class="d-flex mb-3">
                <div class="align-self-center ">
                    <h3 class="mb-0">Absensi ID#<b class="color-highlight">{{ $list['id'] }}</b></h3>
                    <p>
                        Status : <b><u class="text-uppercase" id="shift"></u></b>
                    </p>
                </div>
                <div class="align-self-center ms-auto mt-n2 me-2" id="status"></div>
            </div>
            <div class="d-flex">
                <div class="pe-3 w-75">
                    <div class="card card-style m-0" data-card-height="140" style="height: 140px;" id="map_in">
                        <div class="card-overlay bg-black opacity-30"></div>
                    </div>
                </div>
                <div class="w-100">
                    <h5>Absen Berangkat</h5>
                    <div class="row mb-0 pt-2">
                        <a href="#" class="d-block font-13 font-400 color-theme mb-1"><i class="fa fa-fw pe-1 fa-calendar-alt color-theme opacity-70"></i><span id="tgl_masuk"></span></a>
                        <a href="#" class="d-block font-13 font-400 color-theme"><i class="fa fa-fw pe-1 fa-clock color-theme opacity-70"></i><span id="jam_masuk"></span></a>
                    </div>
                    <h5 class="pt-2 pb-2 font-13 color-blue-dark">Keterlambatan : <b class="color-dark-dark" id="keterlambatan"></b></h5>
                </div>
            </div>
            <div class="divider mt-2 mb-3"></div>
            <div class="d-flex">
                <div class="pe-3 w-75">
                    <div class="card card-style m-0" data-card-height="140" style="height: 140px;" id="map_out">
                        <div class="card-overlay bg-black opacity-30"></div>
                    </div>
                </div>
                <div class="w-100">
                    <h5>Absen Pulang</h5>
                    <div class="row mb-0 pt-2">
                        <a href="#" class="d-block font-13 font-400 color-theme mb-1"><i class="fa fa-fw pe-1 fa-calendar-alt color-theme opacity-70"></i><span id="tgl_keluar"></span></a>
                        <a href="#" class="d-block font-13 font-400 color-theme"><i class="fa fa-fw pe-1 fa-clock color-theme opacity-70"></i><span id="jam_keluar"></span></a>
                    </div>
                    <h5 class="pt-2 font-13 color-orange-dark">Bekerja selama : <b class="color-dark-dark" id="selisih_jam"></b></h5>
                    <h5 class="pb-2 font-13 color-pink-dark">Lembur selama : <b class="color-dark-dark" id="lembur"></b></h5>
                </div>
            </div>
            <div class="divider mt-2 mb-3"></div>
            <h5>Keterangan</h5>
            <p id="keterangan" style="text-align: justify;"></p>
        </div>
    </div>

    <div class="card card-style">
        <div class="content">
            <div id="chapter-5" class="d-flex">
                <div class="align-self-center">
                    <i class="fa fa-camera fa-3x me-2"></i>
                </div>
                <div class="align-self-center">
                    <h4 class="mb-n1 font-15">Bukti Foto Absensi</h4>
                    <p class="font-11 opacity-50">Lampiran</p>
                </div>
                {{-- <div class="align-self-center ms-auto">
                    <i class="fa fa-play-circle fa-2x color-highlight"></i>
                </div> --}}
            </div>
        </div>
        <div class="content mb-0 mt-0">
            <div class="row" id="foto"></div>
        </div>
    </div>

</div>

@include('inc.setting');

<script>
    var map_in;
    var map_out;
    $(document).ready(function() {
        var lightbox = GLightbox({
            selector: '.glightbox'
        });
        refresh();
    })

    function refresh() {
        var id = "{{ $list['id'] }}";
        $.ajax({
            url: "/api/kepegawaian/rekap/{{ Auth::user()->id }}/detail/"+id,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                // INIT HEADER
                if (res.show.jenis == 1) {
                    $('#shift').text('Shift '+res.show.nm_shift);
                } else {
                    if (res.show.jenis == 3) {
                        $('#shift').text('Ijin / Tidak Masuk');
                    } else {
                        $('#shift').text('OnCall');
                    }
                }
                if (res.show.terlambat == 0) {
                    $('#status').append('<span class="badge bg-green-dark color-white p-2 float-end">TEPAT WAKTU</span>');
                } else {
                    $('#status').append('<span class="badge bg-red-dark color-white p-2 float-end">TERLAMBAT</span>');
                }
                // INIT BUTTON MAP
                var content1 = ``;
                content1 += `<a href="#" class="btn btn-xs gradient-blue color-white shadow-bg shadow-bg-xs rounded me-2" onclick="tampilMap('${res.show.lokasi_in}')">GPS Berangkat</a>`;
                if (res.show.lokasi_out) {
                    content1 += `<a href="#" class="btn btn-xs gradient-red shadow-bg shadow-bg-xs rounded" onclick="tampilMap('${res.show.lokasi_out}')">GPS Pulang</a>`;
                } else {
                    content1 += `<a href="#" class="btn btn-xs color-theme no-click shadow-bg shadow-bg-xs rounded">Pulang</a>`;
                }
                content1 += `<a href="#" data-bs-dismiss="offcanvas" class="btn btn-xs bg-dark rounded">Tutup</a>`;
                $('#btn-map').empty().append(content1);
                // INIT CONTENT
                var parts_in = res.show.tgl_in.split(' '); // pisah berdasarkan spasi
                date_in = parts_in[0]; // "2025-04-04"
                time_in = parts_in[1]; // "20:00:00"
                if (res.show.tgl_out) {
                    var parts_out = res.show.tgl_out.split(' ');
                    date_out = parts_out[0]; // "2025-04-04"
                    time_out = parts_out[1]; // "20:00:00"
                } else {
                    date_out = '-';
                    time_out = '-';
                }
                $('#tgl_masuk').text(date_in);
                $('#tgl_keluar').text(date_out);
                $('#jam_masuk').text('Pukul '+time_in);
                $('#jam_keluar').text('Pukul '+time_out);
                $('#keterlambatan').text(`${res.show.keterlambatan?res.show.keterlambatan:'-'}`);
                $('#selisih_jam').text(`${res.show.selisih_jam?res.show.selisih_jam:'-'}`);
                $('#lembur').text(`${res.show.lembur?res.show.lembur:'-'}`);
                $('#keterangan').text(`${res.show.keterangan?res.show.keterangan:'Tidak ada.'}`);
                foto = ``;
                if (res.show.path_out) {
                    foto += `<div class="col-6">
                                <a href="/storage/${res.show.path_in.substring(7,1000)}" class="default-link glightbox" data-gallery="gallery-1" title="Foto Absen Masuk/Berangkat">
                                    <img src="/storage/${res.show.path_in.substring(7,1000)}" data-src="/storage/${res.show.path_in.substring(7,1000)}" class="preload-img shadow-s img-fluid rounded-s entered loaded" alt="img" data-ll-status="loaded" style="width:100%; aspect-ratio: 2/3; object-fit: cover;">
                                </a>
                                <center>Berangkat</center>
                            </div>
                            <div class="col-6">
                                <a href="/storage/${res.show.path_out.substring(7,1000)}" class="default-link glightbox" data-gallery="gallery-1" title="Foto Absen Masuk/Berangkat">
                                    <img src="/storage/${res.show.path_out.substring(7,1000)}" data-src="/storage/${res.show.path_out.substring(7,1000)}" class="preload-img shadow-s img-fluid rounded-s entered loaded" alt="img" data-ll-status="loaded" style="width:100%; aspect-ratio: 2/3; object-fit: cover;">
                                </a>
                                <center>Pulang</center>
                            </div>`;
                } else {
                    foto += `<div class="col-2"></div>
                            <div class="col-8 text-center">
                                <a href="/storage/${res.show.path_in.substring(7,1000)}" class="default-link glightbox" data-gallery="gallery-1" title="Foto Absen Masuk/Berangkat">
                                    <img src="/storage/${res.show.path_in.substring(7,1000)}" data-src="/storage/${res.show.path_in.substring(7,1000)}" class="preload-img shadow-s img-fluid rounded-s entered loaded" alt="img" data-ll-status="loaded" style="width:100%; aspect-ratio: 2/3; object-fit: cover;">
                                </a>
                                <center>Berangkat</center>
                            </div>
                            <div class="col-2"></div>`;
                }
                $('#foto').empty().append(foto);
                lightbox.reload();
                // INIT MAP
                tampilMapIn(res.show.lokasi_in);
                if (res.show.lokasi_out) {
                    tampilMapOut(res.show.lokasi_out);
                }
            },
            error: function(res) {
                Swal.fire({
                    title: `Pesan Galat`,
                    html: res,
                    icon: `error`,
                    showConfirmButton: false,
                    showCancelButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    timer: 5000,
                    timerProgressBar: true,
                    backdrop: `rgba(26,27,41,0.8)`,
                });
            }
        })
    }

    function tampilMapIn(lokasi) {
        // Tampil MAP
        if (map_in) {
            map_in.remove();
        }
        var lat,long;// Creating a promise out of the function
        var arr = lokasi.split(", ");
        console.log(arr);
        lat = arr[0];
        long = arr[1];
        map_in = L.map('map_in',{
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
        }).setView([lat, long], 18);

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxNativeZoom:16,
            minZoom:16,
            maxZoom:16
            // attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map_in);

        var marker = new L.Marker([lat, long]);
        // marker.addTo(map_in).bindPopup("<center>Titik Lokasi Anda<br><b class='text-danger'>"+lokasi+"</b></center>").openPopup();
        marker.addTo(map_in).openPopup();
    }

    function tampilMapOut(lokasi) {
        if (map_out) {
            map_out.remove(); // beda instance!
        }
        var arr = lokasi.split(", ");
        var lat = arr[0];
        var long = arr[1];

        map_out = L.map('map_out', {
            keyboard: false,
            zoomControl: false,
            boxZoom: false,
            doubleClickZoom: false,
            tap: false,
            touchZoom: false,
            enableHighAccuracy: true,
            scrollWheelZoom: false,
            dragging: false
        }).setView([lat, long], 18);

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxNativeZoom:16,
            minZoom:16,
            maxZoom:16
        }).addTo(map_out);

        var marker = L.marker([lat, long]);
        marker.addTo(map_out).openPopup();
    }
</script>
@endsection
