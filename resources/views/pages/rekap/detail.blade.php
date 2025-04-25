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

@endsection
