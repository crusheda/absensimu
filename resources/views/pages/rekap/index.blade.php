@extends('layouts.index')

@section('content')

@include('inc.topbutton')

@include('inc.footerbar')


<div class="page-content header-clear-medium pb-5">

    <div class="content mb-2 mt-0">
        <div class="row mb-0">
            <div class="col-6 pe-2">
                <div class="card card-style mx-0 mb-3">
                    <div class="d-flex p-3">
                        <div>
                            <h1 class="mb-n2"><a id="totalAbsensi" class="color-dark-dark"><i class="fa fa-sync-alt fa-spin fa-1x font-5 mb-2"></i></a></h1>
                            <span class="font-11">Absensi <b class="color-highlight">Tahun Ini</b></span>
                        </div>
                        <div class="ms-auto align-self-center">
                            <i class="color-green-dark fas fa-user-clock font-18"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 ps-2">
                <div class="card card-style mx-0 mb-3">
                    <div class="d-flex p-3">
                        <div>
                            <h1 class="mb-n2"><a id="bulanIni" class="color-dark-dark"><i class="fa fa-sync-alt fa-spin fa-1x mb-2"></i></a></h1>
                            <span class="font-11">Absensi <b class="color-highlight">Bulan Ini</b></span>
                        </div>
                        <div class="ms-auto align-self-center">
                            <i class="color-blue-dark fas fa-calendar-check font-18"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-style mb-4">
        <div class="content">
            <h2 class="text-center">Grafik Absensi</h2>
            <p class="text-center mt-n2 mb-2 font-11 color-highlight" id="tx_graph"><i class="fa fa-sync-alt fa-spin fa-1x mt-2"></i></p>
            <div class="chart-container" style="width:100%; height:350px;">
                <canvas class="graph" id="grafik" width="512" height="350" style="display: block; box-sizing: border-box; height: 350px; width: 512px;"></canvas>
            </div>
        </div>
    </div>

    <div class="card card-style mt-0 mb-4">
        <div class="content m-0 mb-n3 text-center">
            <h5 class="font-900 text-center p-1 mt-2 mb-n2"><i class="fas fa-filter me-1 color-highlight"></i> Filter Data</h5>
            <div class="input-style input-style-always-active has-borders no-icon p-3" style="margin-bottom: 0px">
                <select id="filter_select" class="border-1" onchange="filter()">
                    {{-- <option value="1" disabled="" selected="">Select Time Frame</option> --}}
                    {{-- <option value="2" selected="">1 Bulan Absensi</option> --}}
                    <option value="1">1 Minggu Terakhir</option>
                    <option value="2">2 Minggu Terakhir</option>
                    <option value="3">21 {{ \Carbon\Carbon::now()->subMonth()->isoFormat('MMMM YYYY') }} - 20 {{ \Carbon\Carbon::now()->isoFormat('MMMM YYYY') }}</option>
                    @if (\Carbon\Carbon::now()->isoFormat('DD') > 20)
                        <option value="4">
                            21 {{ \Carbon\Carbon::now()->isoFormat('MMMM YYYY') }} - Saat ini
                        </option>
                    @endif
                </select>
                <span><i class="fa fa-chevron-down mt-2 me-3"></i></span>
            </div>
            {{-- <p>asdassdas</p> --}}
        </div>
    </div>

    <div class="content mt-0 mb-4">
        <div class="row mb-n3">
            <div class="col-4 pe-2">
                <div class="card card-style mx-0 mb-3">
                    <div class="p-3">
                        <h4 class="font-700 text-uppercase font-12 opacity-50 mt-n2">Tepat Waktu</h4>
                        <h1 class="font-700 font-34 color-blue-dark  mb-0"><b id="tepatWaktu"></b></h1>
                        <i class="fas fa-skiing float-end mt-n3 opacity-20"></i>
                    </div>
                </div>
            </div>
            <div class="col-4 ps-2 pe-2">
                <div class="card card-style mx-0 mb-3">
                    <div class="p-3">
                        <h4 class="font-700 text-uppercase font-12 opacity-50 mt-n2">Terlambat</h4>
                        <h1 class="font-700 font-34 color-red-dark mb-0"><b id="terlambat"></b></h1>
                        <i class="fas fa-hiking float-end mt-n3 opacity-20"></i>
                    </div>
                </div>
            </div>
            <div class="col-4 ps-2">
                <div class="card card-style mx-0 mb-3">
                    <div class="p-3">
                        <h4 class="font-700 text-uppercase font-12 opacity-50 mt-n2">Absen 1x</h4>
                        <h1 class="font-700 font-34 color-green-dark mb-0"><b id="absenOne"></b></h1>
                        <i class="fas fa-people-carry float-end mt-n3 opacity-20"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-style mt-0">
        <div class="content mb-0 mt-0">
            <h5 class="font-14 font-700 p-1 mt-2 mb-n2"><i class="fas fa-sort-amount-down me-1 color-highlight"></i> Data diurutkan dari absensi terakhir</h5>
            <div class="list-group list-custom-large" id="list-absensi"></div>
        </div>
    </div>

</div>

@include('inc.setting');

<script>
    var map;
    $(document).ready(function() {
        $.ajax({
            url: "/api/kepegawaian/rekap/{{ Auth::user()->id }}",
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                $("#tx_graph").text('3 Bulan Terakhir');
                $("#totalAbsensi").text(res.total+'x');
                $("#bulanIni").text(res.thisMonth+'x');

                // Menyiapkan dataset untuk Chart.js
                var tepatWaktuData = [];
                var terlambatData = [];
                var absenData = [];
                var ijinData = [];

                // Mengisi array data dari response untuk tiap bulan
                res.dataPerMonth.forEach(function(item) {
                    tepatWaktuData.push(item.data1); // Data Tepat Waktu
                    terlambatData.push(item.data2);  // Data Terlambat
                    absenData.push(item.data3);      // Data Absen 1x
                    ijinData.push(item.data4);       // Data Ijin
                });

                // Menyiapkan label bulan
                var labels = res.labels;

                var verticalChart = document.querySelectorAll('#grafik')[0];
                var verticalDemoChart = new Chart(verticalChart, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: "Tepat Waktu",
                                backgroundColor: twitterColor,
                                data: tepatWaktuData // Data Tepat Waktu
                            },
                            {
                                label: "Terlambat",
                                backgroundColor: redFull,
                                data: terlambatData // Data Terlambat
                            },
                            {
                                label: "Absen 1x",
                                backgroundColor: greenFade2,
                                data: absenData // Data Absen 1x
                            },
                            {
                                label: "Ijin",
                                backgroundColor: yellowFull,
                                data: ijinData // Data Ijin
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom',
                                labels: {
                                    fontSize: 13,
                                    padding: 15,
                                    boxWidth: 12
                                },
                            },
                        },
                        title: {
                            display: false
                        }
                    }
                });

            }
        })

        filter();
    })

    function filter() {
        $("#list-absensi").empty().append(`
            <center><i style="font-size: 12px" class="fa fa-sync-alt fa-spin align-middle me-1"></i> Memuat data...</center>
        `);
        $("#tepatWaktu").empty().append(`<i style="font-size: 24px" class="fa fa-sync-alt fa-spin fa-1x mt-2"></i>`);
        $("#terlambat").empty().append(`<i style="font-size: 24px" class="fa fa-sync-alt fa-spin fa-1x mt-2"></i>`);
        $("#absenOne").empty().append(`<i style="font-size: 24px" class="fa fa-sync-alt fa-spin fa-1x mt-2"></i>`);
        params = $('#filter_select').val();
        $.ajax({
            url: "/api/kepegawaian/rekap/{{ Auth::user()->id }}/"+params,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                $("#list-absensi").empty();
                $("#tepatWaktu").empty().text(res.tepatWaktu+'x');
                $("#terlambat").empty().text(res.terlambat+'x');
                $("#absenOne").empty().text(res.absenOne+'x');
                if (res.show) {
                    res.show.forEach(item => {
                        content = ``;
                        // JENIS
                        if (item.jenis == 1) {
                            if (item.tgl_out != null) {
                                if (item.terlambat == 1) {
                                    ico = 'fas fa-times bg-red-dark';
                                    jenis = 'Shift <b class="color-highlight">'+item.nm_shift+'</b> (<b class="color-red-dark">Terlambat</b>)';
                                } else {
                                    ico = 'fas fa-check bg-mint-dark';
                                    jenis = 'Shift <b class="color-highlight">'+item.nm_shift+'</b> (<b class="color-mint-dark">Tepat Waktu</b>)';
                                }
                            } else {
                                ico = 'fas fa-question bg-pink-dark';
                                jenis = 'Shift <b class="color-highlight">'+item.nm_shift+'</b> (<b class="color-yellow-dark">Absen 1x</b>)';
                            }
                        } else {
                            if (item.jenis == 3) {
                                ico = 'fas fa-stethoscope bg-yellow-dark';
                                jenis = 'Ijin/Tidak Masuk';
                            } else {
                                ico = 'fas fa-phone-volume bg-blue-dark';
                                jenis = 'Jaga OnCall';
                            }
                        }
                        content += `<a class="external-link" href="/rekap/${item.id}">
                                        <i class="${ico} rounded-sm"></i>
                                        <span>${jenis}</span>
                                        <strong class="font-12">${formatDate(item.tgl_in)}</strong>
                                        <i class="fas fa-chevron-right"></i>
                                    </a>`;
                        $('#list-absensi').append(content);
                    })
                } else {
                    $('#list-absensi').append(`<center><i style="font-size: 12px" class="fas fa-calendar-times align-middle me-1 color-red-dark"></i> Data Absensi Tidak Ditemukan</center>`);
                }
            }
        })
    }

    function formatDate(datetime) {
        // Parse datetime string menjadi objek Date
        var date = new Date(datetime);

        // Daftar hari dalam bahasa Indonesia
        var days = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];

        // Daftar bulan dalam bahasa Indonesia
        var months = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        // Format hari, tanggal, bulan, tahun, jam dan menit
        var day = days[date.getDay()]; // Mendapatkan nama hari
        var dayOfMonth = date.getDate(); // Mendapatkan tanggal
        var month = months[date.getMonth()]; // Mendapatkan nama bulan
        var year = date.getFullYear(); // Mendapatkan tahun
        var hours = date.getHours(); // Mendapatkan jam
        var minutes = date.getMinutes(); // Mendapatkan menit

        // Tambahkan leading zero untuk jam dan menit jika kurang dari 10
        hours = hours < 10 ? '0' + hours : hours;
        // minutes = minutes < 10 ? '0' + minutes : minutes;

        // Tambahkan leading zero untuk menit agar selalu dua digit
        minutes = minutes < 10 ? '0' + minutes : minutes;

        // Gabungkan menjadi format yang diinginkan
        // var formattedDate = day + ', ' + dayOfMonth + ' ' + month + ' ' + year + ' ' + hours + ':' + minutes + ' WIB';
        var formattedDate = day + ', ' + dayOfMonth + ' ' + month + ' ' + year + ' ' + hours + ':' + minutes + ' WIB';

        return formattedDate;
    }
</script>
@endsection
