@extends('layouts.home2')

@section('content')
{{-- <div class="input-group input-search">
    <span class="input-group-text">
        <a href="javascript:void(0);" class="search-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path d="M20.5605 18.4395L16.7528 14.6318C17.5395 13.446 18 12.0262 18 10.5C18 6.3645 14.6355 3 10.5 3C6.3645 3 3 6.3645 3 10.5C3 14.6355 6.3645 18 10.5 18C12.0262 18 13.446 17.5395 14.6318 16.7528L18.4395 20.5605C19.0245 21.1462 19.9755 21.1462 20.5605 20.5605C21.1462 19.9748 21.1462 19.0252 20.5605 18.4395ZM5.25 10.5C5.25 7.605 7.605 5.25 10.5 5.25C13.395 5.25 15.75 7.605 15.75 10.5C15.75 13.395 13.395 15.75 10.5 15.75C7.605 15.75 5.25 13.395 5.25 10.5Z" fill="#B9B9B9"></path>
            </svg>
        </a>
    </span>
    <input type="text" id="myInput" placeholder="Search job here..." class="form-control bs-0 ps-0">
</div>
<!-- Masseges List -->
<ul class="dz-list message-list">
    <li>
        <a href="javascript:void(0);">
            <div class="rounded-circle me-3">
                <i class="fas fa-check"></i>
            </div>
            <div class="media-content">
                <div>
                    <h6 class="name">Gustauv Semalam</h6>
                    <p class="my-1">
                        <svg  enable-background="new 0 0 460.702 460.702" class="text-primary me-1" width="15" height="15" viewBox="0 0 460.702 460.702">
                            <path d="m316.608 121.805c-8.937-9.037-23.499-9.151-32.576-.254l-170.268 168.282-74.017-76.626c-8.828-9.201-23.443-9.503-32.643-.675-9.201 8.828-9.503 23.443-.675 32.643.04.041.079.082.119.123l90.248 93.526c4.319 4.406 10.222 6.901 16.392 6.926h.254c6.053-.019 11.857-2.415 16.161-6.672l186.797-184.697c9.025-8.95 9.117-23.511.208-32.576z"/>
                            <path d="m235.318 338.824c4.308 4.395 10.192 6.888 16.346 6.926h.254c6.053-.019 11.857-2.415 16.161-6.672l186.798-184.697c8.467-9.534 7.602-24.126-1.931-32.593-8.643-7.676-21.63-7.777-30.391-.237l-170.199 168.282-6.072-6.303c-8.827-9.201-23.442-9.504-32.643-.676-9.201 8.827-9.504 23.442-.676 32.643.04.042.08.083.12.124z"/>
                        </svg>
                        Roger that sir, thankyou
                    </p>
                </div>
                <span class="time">2m ago</span>
            </div>
        </a>
    </li>
    <li>
        <a href="messages-detail.html">
            <div class="media media-45 rounded-circle">
                <img src="assets/images/message/pic2.jpg" alt="image">
            </div>
            <div class="media-content">
                <div>
                    <h6 class="name">Claudia Surrr</h6>
                    <p class="my-1">
                        <svg enable-background="new 0 0 460.702 460.702" class="text-primary me-1" width="15" height="15" viewBox="0 0 460.702 460.702">
                            <path d="m316.608 121.805c-8.937-9.037-23.499-9.151-32.576-.254l-170.268 168.282-74.017-76.626c-8.828-9.201-23.443-9.503-32.643-.675-9.201 8.828-9.503 23.443-.675 32.643.04.041.079.082.119.123l90.248 93.526c4.319 4.406 10.222 6.901 16.392 6.926h.254c6.053-.019 11.857-2.415 16.161-6.672l186.797-184.697c9.025-8.95 9.117-23.511.208-32.576z"/>
                            <path d="m235.318 338.824c4.308 4.395 10.192 6.888 16.346 6.926h.254c6.053-.019 11.857-2.415 16.161-6.672l186.798-184.697c8.467-9.534 7.602-24.126-1.931-32.593-8.643-7.676-21.63-7.777-30.391-.237l-170.199 168.282-6.072-6.303c-8.827-9.201-23.442-9.504-32.643-.676-9.201 8.827-9.504 23.442-.676 32.643.04.042.08.083.12.124z"/>
                        </svg>
                        OK. Lorem ipsum dolor sect...
                    </p>
                </div>
                <span class="time">2m ago</span>
            </div>
        </a>
    </li>
</ul> --}}

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-title">
            </div>
            <div class="card-body">
                <h6 class="text-center mb-3">Riwayat Absensi</h6>
                <div class="table-responsive">
                    <table id="table" class="table table-hover" style="font-size:13px">
                        <tbody id="tampil-tbody">
                            <tr>
                                <td colspan="5">
                                    <center><i class="fa fa-spinner fa-spin fa-fw"></i> Memproses data...</center>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
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
            url: "/api/kepegawaian/riwayat/{{ Auth::user()->id }}",
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                $("#tampil-tbody").empty();
                res.show.forEach(item => {
                    var updet = new Date(item.updated_at).toLocaleDateString("sv-SE");
                    var date = new Date().toLocaleDateString("sv-SE");
                    content = "<tr id='data" + item.id + "' style='font-size:13px'>";
                    content += `<td style='white-space: normal !important;word-wrap: break-word;'>
                                    <div class='d-flex justify-content-start align-items-center'>
                                        <div class='d-flex flex-column'>
                                            <h6 class='mb-0'><a href="javascript:void(0);" class="text-dark"><b data-bs-toggle="tooltip"
                                                data-bs-offset="0,4" data-bs-placement="bottom" data-bs-html="true" title="">Shift ${item.nm_shift}</b> ${item.terlambat == 1?'(<b class="text-danger">Terlambat selama '+item.keterlambatan+'</b>)':'(<b class="text-primary">Tepat Waktu</b>)'}</a>
                                            </h6>
                                            <small class='text-truncate text-muted'>Lokasi Masuk <b>${item.lokasi_in}</b> dan Lokasi Pulang <b>${item.lokasi_out?item.lokasi_out:''}</b></small>
                                            <small class='text-truncate text-muted'>Bekerja dari <b>${item.tgl_in}</b> Sampai <b>${item.tgl_out?item.tgl_out:'-'}</b> (${item.selisih_jam?item.selisih_jam:''})</small>
                                        </div>
                                    </div>
                                </td>`;
                    content += "</tr>";
                    $('#tampil-tbody').append(content);
                })
            }
        })
    }
</script>
@endsection
