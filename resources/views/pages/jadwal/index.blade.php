@extends('layouts.index')

@section('content')

@include('inc.topbutton')

@include('inc.footerbar')

<div class="page-content pb-4 mt-5">

    <div class="card card-style mt-3 mb-3">
        <div class="content text-center">
            <h5 class="font-900 text-center mt-n2 mb-n1"><i class="fas fa-filter me-1 color-highlight"></i> Filter Bulan & Tahun</h5>
            <div class="row mb-0">
                <div class="col">
                    <div class="input-style input-style-always-active has-borders no-icon mt-3 mb-3">
                        <select id="filter_bulan" class="border-1"></select>
                        <span><i class="fa fa-chevron-down mt-2 me-3"></i></span>
                    </div>
                </div>
                <div class="col">
                    <div class="input-style input-style-always-active has-borders no-icon mt-3 mb-3">
                        <select id="filter_tahun" class="border-1"></select>
                        <span><i class="fa fa-chevron-down mt-2 me-3"></i></span>
                    </div>
                </div>
            </div>
            <button onclick="refresh()" style="width: 100%" class="btn btn-m rounded-sm text-uppercase font-700 bg-blue-dark">Tampilkan Jadwal</button>
        </div>
    </div>

    <div class="card card-style mt-3" id="card-jadwal" hidden>
        <div class="content">
            <div id="tampil-jadwal">

            </div>
        </div>
    </div>

</div>

@include('inc.setting');

<script>
    $(document).ready(function() {
        // Array nama bulan
        var bulanNama = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        // Isi select bulan
        $.each(bulanNama, function(index, value) {
            let bulanValue = String(index + 1).padStart(2, '0'); // Bikin 2 digit
            $('#filter_bulan').append('<option value="'+ bulanValue +'">'+ value +'</option>');
        });

        // Isi select tahun (2 tahun terakhir)
        var thisYear = new Date().getFullYear();
        for (var i = 0; i < 2; i++) {
            $('#filter_tahun').append('<option value="'+ (thisYear - i) +'">'+ (thisYear - i) +'</option>');
        }

        // Set default ke bulan dan tahun sekarang
        var bulanSekarang = String(new Date().getMonth() + 1).padStart(2, '0'); // Bulan sekarang 2 digit
        $('#filter_bulan').val(bulanSekarang);
        $('#filter_tahun').val(thisYear);

        // refresh();
    })

    function refresh() {
        $('#card-jadwal').prop('hidden',false);
        user = "{{ $list['user_id'] }}";
        bulan = $('#filter_bulan').val();
        tahun = $('#filter_tahun').val();
        $("#tampil-jadwal").empty().append(`<center><i class="fa fa-spinner fa-spin fa-fw"></i> Memproses Data Jadwal...</center>`);
        $.ajax({
            url: "/api/kepegawaian/jadwal/"+user+"/"+bulan+"/"+tahun,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.code == 200) {
                    if (res.detail.length == 0) {
                        $("#tampil-jadwal").empty().append(`<center>Isian Data Jadwal Dinas tidak ditemukan, silakan menghubungi Admin dan melengkapi jadwal unit masing-masing terlebih dahulu sesuai Bulan dan Tahun terpilih</center>`);
                    } else {
                        // $("#showUser").text(res.jadwal.nama_pegawai)
                        // INIT
                        var n = 1;
                        // PROCESS
                        content = ``;
                        content += `<h4 class="text-center mb-2">Jadwal Dinas Unit <b class="text-primary">${res.staf.unit?res.staf.unit:'<s>Tidak Valid</s>'}</b></h4><h5 class="text-center mb-2">Bulan <b class="text-primary">${res.bulan}</b> Tahun <b class="text-primary">${res.jadwal.tahun}</b></h5>`;
                        content += `<div class="row mt-3"><div class="col-md-12"><div class="table-responsive p-10 pb-0">
                                    <table id="dttable" class="table table-bordered" style="width: 100%;table-layout: auto">
                                        <thead>
                                        <tr>
                                            <th class="text-center" rowspan="2">NO</th>
                                            <th class="text-center" rowspan="2">NAMA</th>
                                            <th class="text-center" colspan="${res.totalDay}">TANGGAL</th>
                                        </tr>
                                        <tr>`;
                                        for (let i = 1; i <= res.totalDay; i++) {
                                            content += `<th class="p-2 text-center tgl${i}">${i < 10?'0'+i:i}</th>`;
                                        }
                        content += `    </tr>
                                    </thead>
                                    <tbody>`;
                            for (let t = 0; t < res.detail.length; t++) {
                                content += `<tr class="text-center color-dark-dark" style="background-color: ${res.detail[t].color}">`;
                                    content += `<td>${n++}</td>`;
                                    content += `<td class="text-start">
                                                    <div class='d-flex justify-content-start align-items-center'>
                                                        <div class='d-flex flex-column'>
                                                            <h6 class='mb-0'>${res.detail[t].pegawai_nama}</h6>
                                                            <small class='text-truncate text-muted'>${res.detail[t].jabatan?res.detail[t].jabatan:''}</small>
                                                        </div>
                                                    </div>
                                                </td>`;
                                    content += `<td class="p-2 tgl1">${res.detail[t].tgl1}</td>`;
                                    content += `<td class="p-2 tgl2">${res.detail[t].tgl2}</td>`;
                                    content += `<td class="p-2 tgl3">${res.detail[t].tgl3}</td>`;
                                    content += `<td class="p-2 tgl4">${res.detail[t].tgl4}</td>`;
                                    content += `<td class="p-2 tgl5">${res.detail[t].tgl5}</td>`;
                                    content += `<td class="p-2 tgl6">${res.detail[t].tgl6}</td>`;
                                    content += `<td class="p-2 tgl7">${res.detail[t].tgl7}</td>`;
                                    content += `<td class="p-2 tgl8">${res.detail[t].tgl8}</td>`;
                                    content += `<td class="p-2 tgl9">${res.detail[t].tgl9}</td>`;
                                    content += `<td class="p-2 tgl10">${res.detail[t].tgl10}</td>`;
                                    content += `<td class="p-2 tgl11">${res.detail[t].tgl11}</td>`;
                                    content += `<td class="p-2 tgl12">${res.detail[t].tgl12}</td>`;
                                    content += `<td class="p-2 tgl13">${res.detail[t].tgl13}</td>`;
                                    content += `<td class="p-2 tgl14">${res.detail[t].tgl14}</td>`;
                                    content += `<td class="p-2 tgl15">${res.detail[t].tgl15}</td>`;
                                    content += `<td class="p-2 tgl16">${res.detail[t].tgl16}</td>`;
                                    content += `<td class="p-2 tgl17">${res.detail[t].tgl17}</td>`;
                                    content += `<td class="p-2 tgl18">${res.detail[t].tgl18}</td>`;
                                    content += `<td class="p-2 tgl19">${res.detail[t].tgl19}</td>`;
                                    content += `<td class="p-2 tgl20">${res.detail[t].tgl20}</td>`;
                                    content += `<td class="p-2 tgl21">${res.detail[t].tgl21}</td>`;
                                    content += `<td class="p-2 tgl22">${res.detail[t].tgl22}</td>`;
                                    content += `<td class="p-2 tgl23">${res.detail[t].tgl23}</td>`;
                                    content += `<td class="p-2 tgl24">${res.detail[t].tgl24}</td>`;
                                    content += `<td class="p-2 tgl25">${res.detail[t].tgl25}</td>`;
                                    content += `<td class="p-2 tgl26">${res.detail[t].tgl26}</td>`;
                                    if (res.totalDay >= 27) {
                                        content += `<td class="p-2 tgl27">${res.detail[t].tgl27}</td>`;
                                        if (res.totalDay >= 28) {
                                            content += `<td class="p-2 tgl28">${res.detail[t].tgl28}</td>`;
                                            if (res.totalDay >= 29) {
                                                content += `<td class="p-2 tgl29">${res.detail[t].tgl29}</td>`;
                                                if (res.totalDay >= 30) {
                                                    content += `<td class="p-2 tgl30">${res.detail[t].tgl30}</td>`;
                                                    if (res.totalDay >= 31) {
                                                        content += `<td class="p-2 tgl31">${res.detail[t].tgl31}</td>`;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                content += `</tr>`;
                            }
                        content += `</tbody></table></div></div>`;

                        // KETERANGAN
                        content += `<div class="col-md-6"><div class="p-10">
                                        <h5>Shift Jaga :</h5>
                                        <div class="list-group">
                                            <label class="list-group-item border-0 p-2">
                                                <ul class="color-dark-dark">`;
                                    res.shift.forEach(item => {
                                        content += `<li><b class="me-1">${item.singkat}</b>(<u>${item.shift}</u>) : ${item.berangkat.substring(0,5)} - ${item.pulang.substring(0,5)} WIB</li>`;
                                    });
                                        content += `<li><b class="me-1">L</b>(<u>LIBUR</u>)</li>
                                                    <li><b class="me-1">C</b>(<u>CUTI TAHUNAN</u>)</li>
                                                    <li><b class="me-1">CM</b>(<u>CUTI MELAHIRKAN</u>)</li>
                                                    <li><b class="me-1">CU</b>(<u>CUTI UMROH</u>)</li>
                                                    <li><b class="me-1">CH</b>(<u>CUTI HAJI</u>)</li>
                                                    <li><b class="me-1">CD</b>(<u>CUTI DILUAR TANGGUNGAN</u>)</li>
                                                </ul>
                                            </label>
                                        </div>
                                    </div></div>`;
                        content += `<div class="col-md-6"><div class="p-10">
                                        <h5>Keterangan :</h5>
                                        <div class="list-group">
                                            <label class="list-group-item border-0 p-2 color-dark-dark">
                                                <a class="btn btn-light me-2" style="background-color: #fed8b9" href="javascript:void(0);"></a>
                                                Hari Minggu
                                            </label>
                                        </div>
                                    </div></div></div>`;
                        $('#tampil-jadwal').empty().append(content);
                        for (let i = 0; i < res.totalDay; i++) {
                            if (res.dataArray[i] == 'Minggu') {
                                $('.tgl'+(i+1)).css('background-color','#fed8b9');
                                // console.log(i+1);
                            }
                        }
                    }
                } else {
                    $("#tampil-jadwal").empty().append(`<center>${res.message}</center>`);
                }
            },
            error: function(res) {

            }
        })
    }
</script>
@endsection
