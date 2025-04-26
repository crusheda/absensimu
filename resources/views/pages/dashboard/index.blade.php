@extends('layouts.index')

@section('content')

@include('inc.footerbar')

<div class="page-content header-clear-small">

    <div class="content mt-0">
        <div class="d-flex">
            <div class="align-self-center">
                <p class="mb-n1 font-12">Selamat datang kembali,</p>
                <h1 class="font-25">{{ Auth::user()->nama }}</h1>
            </div>
            <div class="align-self-center ms-auto">
                <a href="#" data-menu="menu-settings" class="icon icon-m color-white rounded-m shadow-l rounded-m ms-2">
                    <img src="{{ asset('images/logo/logo_clear_100kb.png') }}" width="40" alt="">
                </a>
            </div>
        </div>
    </div>

    <div class="card shadow-xl card-style bg-31" data-card-height="300" style="height: 300px;">
        {{-- <div class="card-top p-3 color-white opacity-80 text-center font-25"></div> --}}
        <div class="card-center p-3 text-center">
            <h1 class="color-white font-25">Jadwal Hari Ini<br>{!! $list['shift']==null?'<b class="color-red-dark">Tidak Ditemukan</b>':'<b class="color-highlight">'.$list['nama_shift'].'</b>' !!}</h1>
            <p class="color-white opacity-80 font-15">{{ $list['shift']==null?'Hubungi Admin Jadwal':$list['shift'] }}</p>
        </div>
        <div class="card-top p-3">
            {{-- <span class="badge bg-blue-dark color-white p-2 float-end">s</span> --}}
        </div>
        <div class="card-bottom m-3">
            <h5 class="color-white float-start">{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</h5>
            <h5 class="color-white float-end"><a id="clock"></a></h5>
            <div class="clearfix"></div>
            {{-- <div class="progress mt-2" style="height:2px;">
                <div class="progress-bar border-0 bg-green-dark text-start ps-2" role="progressbar" style="width: 80%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100">
                </div>
            </div> --}}
        </div>
        <div class="card-overlay bg-black opacity-80"></div>
    </div>

    <div class="content">
        <h6 class="mt-n2 mb-3 color-dark-dark px-2"><i class="fas fa-history me-1 color-highlight"></i> Bulan {{ \Carbon\Carbon::now()->translatedFormat('M y') }} (Bulan Ini)</h6>
        <div class="d-flex text-center px-2">
            <div class="me-auto">
                <a href="#" data-menu="menu-add-funds" class="icon icon-xxl bg-theme color-blue-dark shadow-l rounded-m">
                    <i class="fa">
                        @if ($list['hadir'])
                            {{ $list['hadir'] }}x
                        @else
                            0x
                        @endif
                    </i>
                </a>
                <span class="font-10 font-500 color-theme d-block">Tepat Waktu</span>
            </div>
            <div class="m-auto">
                <a href="#" data-menu="menu-transaction-request" class="icon icon-xxl bg-theme color-yellow-dark shadow-l rounded-m">
                    <i class="fa">
                        @if ($list['absenOne'])
                            {{ $list['absenOne'] }}x
                        @else
                            0x
                        @endif
                    </i>
                </a>
                <span class="font-10 font-500 color-theme d-block">Absen 1x/hr</span>
            </div>
            <div class="m-auto">
                <a href="#" data-menu="menu-transaction-transfer" class="icon icon-xxl bg-theme color-red-dark shadow-l rounded-m">
                    <i class="fa">
                        @if ($list['terlambat'])
                            {{ $list['terlambat'] }}x
                        @else
                            0x
                        @endif
                    </i>
                </a>
                <span class="font-10 font-500 color-theme d-block">Terlambat</span>
            </div>
            <div class="ms-auto">
                <a href="#" data-menu="menu-transaction-1" class="icon icon-xxl bg-theme color-green-dark shadow-l rounded-m">
                    <i class="fa">
                        @if ($list['ijin'])
                            {{ $list['ijin'] }}x
                        @else
                            0x
                        @endif
                    </i>
                </a>
                <span class="font-10 font-500 color-theme d-block">Ijin/Tidak Masuk</span>
            </div>
        </div>
    </div>

    <div class="card card-style mb-3">
        <div class="content mb-0 mt-2 pt-1">
            <div class="d-flex pb-2 mb-1">
                <div class="align-self-center">
                    <h4 class="font-700 text-uppercase font-12 color-highlight opacity-70 mb-0">Riwayat Absensi</h4>
                </div>
                <div class="align-self-center ms-auto">
                    <a href="{{ route('rekap.index') }}"
                        class="border-0 font-11 opacity-30 color-theme font-800 text-center d-block">Lihat Selengkapnya
                        <i class="fa fa-arrow-right font-10 ps-2"></i></a>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-style">
        <div class="content mb-0 mt-2 pt-1">
            <div class="d-flex pb-2 mb-1">
                <div class="align-self-center">
                    <h4 class="font-700 text-uppercase font-12 color-mint-dark opacity-70 mb-0">Tata Cara Absensi</h4>
                </div>
                <div class="align-self-center ms-auto">
                    <a href="#" data-menu="menu-unavailable" class="border-0 font-11 opacity-30 color-theme font-800 text-center d-block">Lihat Selengkapnya
                        <i class="fa fa-arrow-right font-10 ps-2"></i></a>
                </div>
            </div>
        </div>
    </div>

</div>
<!-- End of Page Content-->
<div id="menu-unavailable" class="menu menu-box-bottom menu-box-detached rounded-m" data-menu-height="230" style="display: block; height: 230px;">
    <div class="menu-title">
        <h1>Oops!</h1>
        <a href="#" class="close-menu mt-4"><i class="fa fa-times font-16"></i></a>
    </div>
    <div class="divider divider-margins mt-3"></div>
    <div class="content mt-n2 mb-n4">
        <p class="mb-3 font-500">
            Fitur ini masih dalam tahap pengembangan. Mohon tunggu update selanjutnya. Terima Kasih. 😊
        </p>
        <a href="#" class="close-menu btn btn-m text-uppercase font-700 btn-full bg-dark-dark rounded-sm mt-4 mb-4">Tutup</a>
    </div>
</div>

@include('inc.setting');

@endsection
