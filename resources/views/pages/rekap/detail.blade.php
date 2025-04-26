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
                    <h3 class="mb-0">Absensi ID#<b class="color-highlight">1</b></h3>
                    <p>
                        Status : <b><u>Masuk SHIFT</u></b>
                    </p>
                </div>
                <div class="align-self-center ms-auto mt-n2 me-2">
                    <span class="badge bg-green-dark color-white p-2 float-end">TEPAT WAKTU</span>
                </div>
            </div>
            <div class="d-flex">
                <div class="pe-3 w-75">
                    <div class="card card-style m-0 bg-18" data-card-height="140" style="height: 140px;">
                        <div class="card-overlay bg-black opacity-30"></div>
                    </div>
                </div>
                <div class="w-100">
                    <h5>Absen Berangkat</h5>
                    <div class="row mb-0 pt-2">
                        <a href="#" class="d-block font-12 color-theme"><i class="fa fa-fw pe-1 fa-clock color-theme opacity-70"></i> Pukul <span class="font-400 opacity-50">07:09:00</span></a>
                        <a href="#" class="d-block font-12 color-theme"><i class="fa fa-fw pe-1 fa-user color-theme opacity-70"></i> <span class="font-400 opacity-50">45 Students</span></a>
                    </div>
                    <h5 class="pt-2 pb-2 font-13 color-blue-dark">Keterlambatan 00:00:00</h5>
                </div>
            </div>
            <div class="divider mt-2 mb-3"></div>
            <div class="d-flex">
                <div class="pe-3 w-75">
                    <div class="card card-style m-0 bg-14" data-card-height="140" style="height: 140px;">
                        <div class="card-overlay bg-black opacity-30"></div>
                    </div>
                </div>
                <div class="w-100">
                    <h5>Absen Pulang</h5>
                    <div class="row mb-0 pt-2">
                        <a href="#" class="d-block font-12 color-theme"><i class="fa fa-fw pe-1 fa-book color-theme opacity-70"></i> Pukul <span class="font-400 opacity-50">21 Lessons</span></a>
                        <a href="#" class="d-block font-12 color-theme"><i class="fa fa-fw pe-1 fa-user color-theme opacity-70"></i> <span class="font-400 opacity-50">45 Students</span></a>
                    </div>
                    <h5 class="pt-2 pb-2 font-13 color-orange-dark">Bekerja selama 07:15:00</h5>
                </div>
            </div>
            <div class="divider mt-2 mb-3"></div>
            <h5>Keterangan</h5>
            <p>asdsadas</p>
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
            <div class="row">
                <div class="col-6">
                    <a href="/new/images/pictures/14.jpg" class="default-link" data-gallery="gallery-1" title="Foto Absen Masuk/Berangkat"><img src="/new/images/pictures/14s.jpg" data-src="/new/images/pictures/14s.jpg" class="preload-img shadow-s img-fluid rounded-s entered loaded" alt="img" data-ll-status="loaded"></a>
                    <center>Berangkat</center>
                </div>
                <div class="col-6">
                    <a href="/new/images/pictures/14.jpg" class="default-link" data-gallery="gallery-1" title="Foto Absen Keluar/Pulang"><img src="/new/images/pictures/14s.jpg" data-src="/new/images/pictures/14s.jpg" class="preload-img shadow-s img-fluid rounded-s entered loaded" alt="img" data-ll-status="loaded"></a>
                    <center>Pulang</center>
                </div>
            </div>
        </div>
    </div>

</div>
<script>
    function detail(id) {
        console.log(id);

        $.ajax({
            url: "/api/kepegawaian/rekap/{{ Auth::user()->id }}/detail/"+id,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                // INIT JUDUL
                $('#id-detail').text('ID : '+res.show.id);
                if (res.show.jenis == 1) {
                    $('#judul-detail').text('Shift '+res.show.nm_shift);
                } else {
                    if (res.show.jenis == 3) {
                        $('#judul-detail').text('Ijin');
                    } else {
                        $('#judul-detail').text('OnCall');
                    }
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
                // INIT TABLE
                var content2 = ``;
                content2 += `<table class="table mb-2">
                                <thead>
                                    <tr>
                                        <th class="border-fade-blue" scope="col">Jam Masuk</th>
                                        <td>
                                            <a data-gallery="gallery-1" href="javascript:;" title="">${res.show.tgl_in}</a>&nbsp;&nbsp;<a href="/storage/${res.show.path_in.substring(7,1000)}">(<u><b class="text-dark">Lihat Foto</b></u>)</a>&nbsp;&nbsp;<a href="#" onclick="tampilMap('${res.show.lokasi_in}')">(<u><b class="text-dark">Lihat Peta</b></u>)</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="border-fade-blue" scope="col">Jam Pulang</th>
                                        <td>
                                            <a data-gallery="gallery-1" href="javascript:;" title="">${res.show.tgl_out?res.show.tgl_out:'-'}</a>&nbsp;&nbsp;${res.show.tgl_out?'<a href="/storage/'+res.show.path_out.substring(7,1000)+'">(<u><b class="text-dark">Lihat Foto</b></u>)</a>':''}&nbsp;&nbsp;${res.show.tgl_out?`<a href="#" onclick="tampilMap('${res.show.lokasi_out}')">(<u><b class="text-dark">Lihat Peta</b></u>)</a>`:''}</td>
                                    </tr>
                                    <tr>
                                    <th class="border-fade-blue" scope="col">Keterlambatan</th>
                                        <td>${res.show.keterlambatan?res.show.keterlambatan:'-'}</td>
                                    </tr>
                                    <tr>
                                        <th class="border-fade-blue" scope="col">Waktu Bekerja</th>
                                        <td>${res.show.selisih_jam?res.show.selisih_jam:'-'}</td>
                                    </tr>
                                    <tr>
                                        <th class="border-fade-blue" scope="col">Keterangan :</th>
                                        <td>${res.show.keterangan?res.show.keterangan:'-'}</td>
                                    </tr>
                                </thead>
                            </table>`; // <i class="bi bi-check-circle-fill color-green-dark"></i>
                $('#table-detail').empty().append(content2);

                // INIT IMAGE SELFI
                var content3 = ``;
                if (res.show.jenis == 1) { // JAGA SHIFT
                    content3 += `<a class="col" data-gallery="gallery-2" href="/storage/${res.show.path_in.substring(7,1000)}" title="${res.show.foto_in}">
                                    <img src="/storage/${res.show.path_in.substring(7,1000)}" data-src="/storage/${res.show.path_in.substring(7,1000)}" class="preload-img img-fluid rounded-xs" alt="img" style="height:150px;width:100px">
                                    <p class="font-600 color-theme font-12 pb-3">Masuk</p>
                                </a>`;
                    if (res.show.path_out) {
                        content3 += `<a class="col" data-gallery="gallery-2" href="/storage/${res.show.path_out.substring(7,1000)}" title="${res.show.foto_out}">
                                        <img src="/storage/${res.show.path_in.substring(7,1000)}" data-src="/storage/${res.show.path_out.substring(7,1000)}" class="preload-img img-fluid rounded-xs" alt="img" style="height:150px;width:100px">
                                        <p class="font-600 color-theme font-12 pb-3">Pulang</p>
                                    </a>`;
                    }
                } else {
                    if (res.show.jenis == 4) { // ONCALL

                    } else { // IJIN
                        content3 += `<a class="col-md-12" data-gallery="gallery-2" href="/storage/${res.show.path_in.substring(7,1000)}" title="${res.show.foto_in}">
                                        <img src="/storage/${res.show.path_in.substring(7,1000)}" data-src="/storage/${res.show.path_in.substring(7,1000)}" class="preload-img img-fluid rounded-xs" alt="img" style="height:150px;width:100px">
                                        <p class="font-600 color-theme font-12 pb-3">Lampiran Surat</p>
                                    </a>`;
                    }
                }
                $('#img-detail').empty().append(content3);
                $('#img').empty().append(`src="/storage/${res.show.path_in.substring(7,1000)}"`);
                // SHOWING OFFCANVAS
                var myOffcanvas = document.getElementById('detail');
                event.stopPropagation();
                var bsOffcanvas = new bootstrap.Offcanvas(myOffcanvas);
                bsOffcanvas.show();

                tampilMap(res.show.lokasi_in);
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

    function tampilMap(lokasi) {
        // Tampil MAP
        if (map) {
            map.remove();
        }
        var lat,long;// Creating a promise out of the function
        var arr = lokasi.split(", ");
        console.log(arr);
        lat = arr[0];
        long = arr[1];
        map = L.map('map',{
            keyboard: false,
            zoomControl: true,
            boxZoom: false,
            doubleClickZoom: true,
            tap: false,
            touchZoom: false,
            enableHighAccuracy: true,
            scrollWheelZoom: true,
            dragging: true,
            doubleClickZoom: false,
        }).setView([lat, long], 18);

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxNativeZoom:19,
            minZoom:7,
            maxZoom:22
            // attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        var marker = new L.Marker([lat, long]);
        marker.addTo(map).bindPopup("<center>Titik Lokasi Anda<br><b class='text-danger'>"+lokasi+"</b></center>").openPopup();
    }
</script>
@endsection
