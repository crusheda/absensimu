@extends('layouts.dashboard.index')

@section('content')
<style>
    #map { height: 150px;}
</style>
<!-- Your Page Content Goes Here-->
<div class="page-content header-clear-medium">
    {{-- <div class="card card-style">
        <div class="content mx-3 mt-3">
            <div class="d-flex">
                <div>
                    <h6 class="font-700 mb-n1 color-highlight">Web, Mobile, Apps</h6>
                    <h3>Duo Mobile</h3>
                </div>
                <div class="ms-auto">
                    <a href="#" data-toast="snack-bookmark" class="icon icon-xs gradient-red shadow-bg shadow-bg-xs rounded-s me-2"><i class="bi bi-heart-fill color-white"></i></a>
                    <a href="#" data-bs-toggle="offcanvas" data-bs-target="#menu-share" class="icon icon-xs gradient-blue shadow-bg shadow-bg-xs rounded-s"><i class="bi bi-share-fill color-white"></i></a>
                </div>
            </div>
            <img src="images/pictures/23w.jpg" class="img-fluid rounded-m mb-2 mt-2" alt="img">
            <p class="mb-3">
                The ultimate Progressive Web App with a modern design and layout.
            </p>
            <a href="#" class="btn btn-full btn-s gradient-highlight shadow-bg shadow-bg-xs">View Project</a>
        </div>
    </div> --}}
    <div class="card card-style">
        <div class="card-top p-3">
            <a href="#" class="btn btn-xs bg-theme color-theme font-700 font-9 float-end">Lihat Grafik</a>
        </div>
        <div class="content">
            <h4 class="mb-3">Rekapitulasi Data <b class="text-primary">Absensi</b></h4>
            <p class="mb-3">
                Halaman ini menampilkan absensi Anda dari tanggal 20 Januari sampai dengan tgl 20 Februari 2025. Ketuk baris di bawah untuk melihat Detail Absensi..
            </p>
            <div class="list-group list-custom list-group-m rounded-xs" id="list-absensi"></div>
        </div>
    </div>
    {{-- <a data-bs-toggle="offcanvas" data-bs-target="#menu-gallery-2"  href="#"><img src="images/pictures/5s.jpg" class="rounded-m img-fluid d-block"></a>
    <span class="font-700 color-theme font-14 text-center d-block mb-3">Album Sheet 2</span> --}}

	<div class="offcanvas offcanvas-bottom rounded-m offcanvas-detached" id="detail">
		<div class="content mb-0">
            <div class="d-flex">
                <div>
                    <h6 class="font-700 mb-n1 color-highlight" id="id-detail"></h6>
                    <h3 id="judul-detail"></h3>
                </div>
                <div class="ms-auto" id="btn-map"></div>
            </div>
            <div id="map" class=""></div>
            <p class="mb-3">
                Keakuratan GPS pada masing-masing Device sangatlah berpengaruh pada penentuan titik lokasi Map Anda.
            </p>
			{{-- <h5 class="mb-n1 font-12 color-highlight font-700 text-uppercase pt-1">2 x 2 Image Grid</h5> --}}
            {{-- <h4>Classic</h4>
            <p class="mb-3">
                Lorem ipsum dolor sit amet consectetur adipisicing elit.
            </p> --}}
            <div class="table-responsive mb-3" id="table-detail"></div>
			<div class="row text-center mb-4 pb-5" id="img-detail"></div>

		</div>
	</div>
</div>
<script>
    var map;
    $(document).ready(function() {
        refresh();
    })

    function refresh() {
        $.ajax({
            url: "/api/kepegawaian/rekap/{{ Auth::user()->id }}",
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                $("#list-absensi").empty();
                res.show.forEach(item => {
                    var updet = new Date(item.updated_at).toLocaleDateString("sv-SE");
                    var date = new Date().toLocaleDateString("sv-SE");
                    // JENIS
                    if (item.jenis == 1) {
                        ico = 'bi color-blue-dark bi-calendar-check';
                        jenis = 'Jaga Shift '+item.nm_shift;
                    } else {
                        if (item.jenis == 3) {
                            ico = 'bi color-yellow-dark bi-calendar-x';
                            jenis = 'Ijin/Tidak Masuk';
                        } else {
                            ico = 'bi color-red-dark bi-calendar-event';
                            jenis = 'Jaga OnCall';
                        }
                    }
                    content = `<a href="#" class="list-group-item" onclick="detail(${item.id})">
                                    <i class="${ico} font-22 me-2"></i>
                                    <div><strong>${jenis}</strong><span>${item.tgl_in}</span></div>
                                    <i class="bi bi-chevron-right"></i>
                                </a>`; //  data-bs-toggle="offcanvas" data-bs-target="#detail"

                    // content += `<td>
                    //     <a href="javascript:void(0);" class="btn btn-link-secondary" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false"><i class="ti ti-dots-vertical f-18"></i></a>
                    //     <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                    //         <li>
                    //             <a class="dropdown-item" href="javascript:void(0);" onclick="tambah()">Tambah Jadwal Dinas</a>
                    //             <a class="dropdown-item" href="javascript:void(0);" onclick="showRiwayat()">Segarkan Tabel</a>
                    //             <div class="divider pb-1"></div>
                    //         </li>
                    //     </ul></td>`;
                    $('#list-absensi').append(content);
                })
            }
        })
    }

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
                content1 += `<a href="#" class="btn btn-xs gradient-blue color-white shadow-bg shadow-bg-xs rounded me-2" onclick="tampilMap('${res.show.lokasi_in}')">Berangkat</a>`;
                if (res.show.lokasi_out) {
                    content1 += `<a href="#" class="btn btn-xs gradient-red shadow-bg shadow-bg-xs rounded" onclick="tampilMap('${res.show.lokasi_out}')">Pulang</a>`;
                } else {
                    content1 += `<a href="#" class="btn btn-xs color-theme no-click shadow-bg shadow-bg-xs rounded">Pulang</a>`;
                }
                content1 += `<a href="#" data-bs-dismiss="offcanvas" class="btn btn-xs bg-dark rounded-m ms-3"><i class="bi bi-x-lg"></i></a>`;
                $('#btn-map').empty().append(content1);
                // INIT TABLE
                var content2 = ``;
                content2 += `<table class="table mb-2">
                                <thead>
                                    <tr>
                                        <th class="border-fade-blue" scope="col">Jam Masuk</th>
                                        <td>${res.show.tgl_in}</td>
                                    </tr>
                                    <tr>
                                        <th class="border-fade-blue" scope="col">Jam Pulang</th>
                                        <td>${res.show.tgl_out?res.show.tgl_out:'-'}</td>
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
                                    <img src="/storage/${res.show.path_in.substring(7,1000)}" data-src="/storage/${res.show.path_in.substring(7,1000)}" class="preload-img img-fluid rounded-xs" alt="img" style="height:150px;">
                                    <p class="font-600 color-theme font-12 pb-3">Masuk</p>
                                </a>`;
                    if (res.show.path_out) {
                        content3 += `<a class="col" data-gallery="gallery-2" href="/storage/${res.show.path_out.substring(7,1000)}" title="${res.show.foto_out}">
                                        <img src="/storage/${res.show.path_in.substring(7,1000)}" data-src="/storage/${res.show.path_out.substring(7,1000)}" class="preload-img img-fluid rounded-xs" alt="img" style="height:150px;">
                                        <p class="font-600 color-theme font-12 pb-3">Pulang</p>
                                    </a>`;
                    }
                } else {
                    if (res.show.jenis == 4) { // ONCALL

                    } else { // IJIN
                        content3 += `<a class="col-md-12" data-gallery="gallery-2" href="/storage/${res.show.path_in.substring(7,1000)}" title="${res.show.foto_in}">
                                        <img src="/storage/${res.show.path_in.substring(7,1000)}" data-src="/storage/${res.show.path_in.substring(7,1000)}" class="preload-img img-fluid rounded-xs" alt="img" style="height:150px;">
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
