@extends('layouts.home')

@section('content')
<div class="content-inner pt-0">
    <div class="container fb">
        <!-- Search -->
        <form class="">
            <div class="card card-bx card-content" style="border-radius: 30px; overflow: hidden;">
                <div class="card-body">
                    <div class="hstack gap-3">
                        <div class="item-list recent-jobs-list">
                            <div class="item-content">
                                <a href="javascript:void(0);" class="item-media me-2"><img src="{{ asset('/images/user2.png') }}" width="50" alt="logo"></a>
                                <div class="item-inner">
                                    <div class="item-title-row">
                                        <h6 class="item-title"><a href="javascript:void(0);">Reguler</a></h6>
                                        <div class="item-subtitle">07.00 - 14.00 WIB</div>
                                    </div>
                                </div>
                            </div>
                            {{-- <h5>Reguler</h5><code>07:00 - 14:00</code> --}}
                        </div>
                        <div class="ms-auto"></div>
                        <div class="vr"></div>
                        <div class=""><blockquote>{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</blockquote><h4 id="clock"></h4></div>
                    </div>
                    <div class="divider inner-divider transparent mb-0" style="border-radius:30px"><span class="text-primary" style="border-radius:30px">Bulan {{ \Carbon\Carbon::now()->isoFormat('MMMM') }}</span></div>
                    <div class="row mt-1 mb-0">
                        <div class="hstack gap-3 text-center">
                            <div class="col mt-2">
                                <h4 class="title">
                                    @if ($list['hadir'])
                                        @foreach ($list['hadir'] as $item)
                                            {{ $item }}x
                                        @endforeach
                                    @else
                                        0x
                                    @endif
                                </h4>
                                <p>Hadir</p>
                            </div>
                            <div class="vr"></div>
                            <div class="col mt-2">
                                <h4 class="title">
                                    @if ($list['terlambat'])
                                        @foreach ($list['terlambat'] as $item)
                                            {{ $item }}x
                                        @endforeach
                                    @else
                                        0x
                                    @endif
                                </h4>
                                <p>Terlambat</p>
                            </div>
                            <div class="vr"></div>
                            <div class="col mt-2">
                                <h4 class="title">
                                    @if ($list['ijin'])
                                        @foreach ($list['ijin'] as $item)
                                            {{ $item }}x
                                        @endforeach
                                    @else
                                        0x
                                    @endif
                                </h4>
                                <p>Ijin</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- <div class="input-group">
                <span class="input-group-text">
                    <a href="javascript:void(0);" class="search-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M20.5605 18.4395L16.7528 14.6318C17.5395 13.446 18 12.0262 18 10.5C18 6.3645 14.6355 3 10.5 3C6.3645 3 3 6.3645 3 10.5C3 14.6355 6.3645 18 10.5 18C12.0262 18 13.446 17.5395 14.6318 16.7528L18.4395 20.5605C19.0245 21.1462 19.9755 21.1462 20.5605 20.5605C21.1462 19.9748 21.1462 19.0252 20.5605 18.4395ZM5.25 10.5C5.25 7.605 7.605 5.25 10.5 5.25C13.395 5.25 15.75 7.605 15.75 10.5C15.75 13.395 13.395 15.75 10.5 15.75C7.605 15.75 5.25 13.395 5.25 10.5Z" fill="#B9B9B9"/>
                        </svg>
                    </a>
                </span>
                <input type="text" placeholder="Cari sesuatu..." class="form-control ps-0 bs-0" style="border-top-right-radius:50px;border-bottom-right-radius:50px">
            </div> --}}
        </form>

        {{-- @if ($list['agent']->isMobile()) --}}
            <!-- Dashboard Area -->
            <div class="dashboard-area">

                {{-- <div class="m-b10">
                    <div class="title-bar">
                        <h5 class="dz-title">Recomended Jobs</h5>
                        <div class="swiper-defult-pagination pagination-dots style-1 p-0"></div>
                    </div>
                    <div class="swiper-btn-center-lr">
                        <div class="swiper-container tag-group mt-4 dz-swiper recomand-swiper">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide">
                                    <div class="card job-post">
                                        <div class="card-body">
                                            <div class="media media-80">
                                                <img src="assets/images/logo/logo.png" alt="/">
                                            </div>
                                            <div class="card-info">
                                                <h6 class="title"><a href="javascript:void(0);">Software Engineer</a></h6>
                                                <span class="location">Jakarta, Indonesia</span>
                                                <div class="d-flex align-items-center">
                                                    <svg class="text-primary" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                        <path d="M8.5 23C9.70017 23.0072 10.8898 22.7761 12 22.32C13.109 22.7799 14.2995 23.0112 15.5 23C19.145 23 22 21.055 22 18.571V14.429C22 11.945 19.145 10 15.5 10C15.331 10 15.165 10.008 15 10.017V5.333C15 2.9 12.145 1 8.5 1C4.855 1 2 2.9 2 5.333V18.667C2 21.1 4.855 23 8.5 23ZM20 18.571C20 19.72 18.152 21 15.5 21C12.848 21 11 19.72 11 18.571V17.646C12.3542 18.4696 13.9153 18.8898 15.5 18.857C17.0847 18.8898 18.6458 18.4696 20 17.646V18.571ZM15.5 12C18.152 12 20 13.28 20 14.429C20 15.578 18.152 16.857 15.5 16.857C12.848 16.857 11 15.577 11 14.429C11 13.281 12.848 12 15.5 12ZM8.5 3C11.152 3 13 4.23 13 5.333C13 6.43601 11.152 7.66701 8.5 7.66701C5.848 7.66701 4 6.43701 4 5.333C4 4.229 5.848 3 8.5 3ZM4 8.48201C5.35986 9.28959 6.91876 9.7001 8.5 9.66701C10.0812 9.7001 11.6401 9.28959 13 8.48201V10.33C11.9102 10.6047 10.9107 11.1586 10.1 11.937C9.57422 12.0508 9.03795 12.1091 8.5 12.111C5.848 12.111 4 10.881 4 9.77801V8.48201ZM4 12.927C5.36015 13.7338 6.91891 14.1439 8.5 14.111C8.678 14.111 8.85 14.089 9.025 14.08C9.0101 14.1958 9.00176 14.3123 9 14.429V16.514C8.832 16.524 8.67 16.556 8.5 16.556C5.848 16.556 4 15.326 4 14.222V12.927ZM4 17.371C5.35986 18.1786 6.91876 18.5891 8.5 18.556C8.668 18.556 8.833 18.543 9 18.535V18.571C9.01431 19.4223 9.34144 20.2385 9.919 20.864C9.45111 20.9524 8.97615 20.9979 8.5 21C5.848 21 4 19.77 4 18.667V17.371Z" fill="#40189D"/>
                                                    </svg>
                                                    <span class="ms-2 price-item">$500 - $1,000</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <div class="card job-post">
                                        <div class="card-body">
                                            <div class="media media-80">
                                                <img src="assets/images/logo/logo.png" alt="/">
                                            </div>
                                            <div class="card-info">
                                                <h6 class="title"><a href="javascript:void(0);">Software Engineer</a></h6>
                                                <span class="location">Jakarta, Indonesia</span>
                                                <div class="d-flex align-items-center">
                                                    <svg class="text-primary" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                        <path d="M8.5 23C9.70017 23.0072 10.8898 22.7761 12 22.32C13.109 22.7799 14.2995 23.0112 15.5 23C19.145 23 22 21.055 22 18.571V14.429C22 11.945 19.145 10 15.5 10C15.331 10 15.165 10.008 15 10.017V5.333C15 2.9 12.145 1 8.5 1C4.855 1 2 2.9 2 5.333V18.667C2 21.1 4.855 23 8.5 23ZM20 18.571C20 19.72 18.152 21 15.5 21C12.848 21 11 19.72 11 18.571V17.646C12.3542 18.4696 13.9153 18.8898 15.5 18.857C17.0847 18.8898 18.6458 18.4696 20 17.646V18.571ZM15.5 12C18.152 12 20 13.28 20 14.429C20 15.578 18.152 16.857 15.5 16.857C12.848 16.857 11 15.577 11 14.429C11 13.281 12.848 12 15.5 12ZM8.5 3C11.152 3 13 4.23 13 5.333C13 6.43601 11.152 7.66701 8.5 7.66701C5.848 7.66701 4 6.43701 4 5.333C4 4.229 5.848 3 8.5 3ZM4 8.48201C5.35986 9.28959 6.91876 9.7001 8.5 9.66701C10.0812 9.7001 11.6401 9.28959 13 8.48201V10.33C11.9102 10.6047 10.9107 11.1586 10.1 11.937C9.57422 12.0508 9.03795 12.1091 8.5 12.111C5.848 12.111 4 10.881 4 9.77801V8.48201ZM4 12.927C5.36015 13.7338 6.91891 14.1439 8.5 14.111C8.678 14.111 8.85 14.089 9.025 14.08C9.0101 14.1958 9.00176 14.3123 9 14.429V16.514C8.832 16.524 8.67 16.556 8.5 16.556C5.848 16.556 4 15.326 4 14.222V12.927ZM4 17.371C5.35986 18.1786 6.91876 18.5891 8.5 18.556C8.668 18.556 8.833 18.543 9 18.535V18.571C9.01431 19.4223 9.34144 20.2385 9.919 20.864C9.45111 20.9524 8.97615 20.9979 8.5 21C5.848 21 4 19.77 4 18.667V17.371Z" fill="#40189D"/>
                                                    </svg>
                                                    <span class="ms-2 price-item">$500 - $1,000</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <div class="card job-post">
                                        <div class="card-body">
                                            <div class="media media-80">
                                                <img src="assets/images/logo/logo.png" alt="/">
                                            </div>
                                            <div class="card-info">
                                                <h6 class="title"><a href="javascript:void(0);">Software Engineer</a></h6>
                                                <span class="location">Jakarta, Indonesia</span>
                                                <div class="d-flex align-items-center">
                                                    <svg class="text-primary" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                        <path d="M8.5 23C9.70017 23.0072 10.8898 22.7761 12 22.32C13.109 22.7799 14.2995 23.0112 15.5 23C19.145 23 22 21.055 22 18.571V14.429C22 11.945 19.145 10 15.5 10C15.331 10 15.165 10.008 15 10.017V5.333C15 2.9 12.145 1 8.5 1C4.855 1 2 2.9 2 5.333V18.667C2 21.1 4.855 23 8.5 23ZM20 18.571C20 19.72 18.152 21 15.5 21C12.848 21 11 19.72 11 18.571V17.646C12.3542 18.4696 13.9153 18.8898 15.5 18.857C17.0847 18.8898 18.6458 18.4696 20 17.646V18.571ZM15.5 12C18.152 12 20 13.28 20 14.429C20 15.578 18.152 16.857 15.5 16.857C12.848 16.857 11 15.577 11 14.429C11 13.281 12.848 12 15.5 12ZM8.5 3C11.152 3 13 4.23 13 5.333C13 6.43601 11.152 7.66701 8.5 7.66701C5.848 7.66701 4 6.43701 4 5.333C4 4.229 5.848 3 8.5 3ZM4 8.48201C5.35986 9.28959 6.91876 9.7001 8.5 9.66701C10.0812 9.7001 11.6401 9.28959 13 8.48201V10.33C11.9102 10.6047 10.9107 11.1586 10.1 11.937C9.57422 12.0508 9.03795 12.1091 8.5 12.111C5.848 12.111 4 10.881 4 9.77801V8.48201ZM4 12.927C5.36015 13.7338 6.91891 14.1439 8.5 14.111C8.678 14.111 8.85 14.089 9.025 14.08C9.0101 14.1958 9.00176 14.3123 9 14.429V16.514C8.832 16.524 8.67 16.556 8.5 16.556C5.848 16.556 4 15.326 4 14.222V12.927ZM4 17.371C5.35986 18.1786 6.91876 18.5891 8.5 18.556C8.668 18.556 8.833 18.543 9 18.535V18.571C9.01431 19.4223 9.34144 20.2385 9.919 20.864C9.45111 20.9524 8.97615 20.9979 8.5 21C5.848 21 4 19.77 4 18.667V17.371Z" fill="#40189D"/>
                                                    </svg>
                                                    <span class="ms-2 price-item">$500 - $1,000</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> --}}

                <div class="title-bar">
                    <h5 class="dz-title">Berita Terbaru</h5>
                    <a class="btn btn-sm text-dark" href="search.html">Selengkapnya</a>
                </div>
                <div class="list item-list recent-jobs-list">
                    <ul style="padding-left:0rem">
                        <li>
                            <div class="item-content">
                                <a href="javascript:void(0);" class="item-media"><img src="{{ asset('/images/admin.png') }}" width="55" alt="logo"></a>
                                <div class="item-inner">
                                    <div class="item-title-row">
                                        <div class="item-subtitle">Keluaran Baru</div>
                                        <h6 class="item-title"><a href="javascript:void(0);">E-Absensi</a></h6>
                                    </div>
                                    <div class="d-flex align-items-center mb-2">
                                        <svg class="text-primary" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path d="M8.5 23C9.70017 23.0072 10.8898 22.7761 12 22.32C13.109 22.7799 14.2995 23.0112 15.5 23C19.145 23 22 21.055 22 18.571V14.429C22 11.945 19.145 10 15.5 10C15.331 10 15.165 10.008 15 10.017V5.333C15 2.9 12.145 1 8.5 1C4.855 1 2 2.9 2 5.333V18.667C2 21.1 4.855 23 8.5 23ZM20 18.571C20 19.72 18.152 21 15.5 21C12.848 21 11 19.72 11 18.571V17.646C12.3542 18.4696 13.9153 18.8898 15.5 18.857C17.0847 18.8898 18.6458 18.4696 20 17.646V18.571ZM15.5 12C18.152 12 20 13.28 20 14.429C20 15.578 18.152 16.857 15.5 16.857C12.848 16.857 11 15.577 11 14.429C11 13.281 12.848 12 15.5 12ZM8.5 3C11.152 3 13 4.23 13 5.333C13 6.43601 11.152 7.66701 8.5 7.66701C5.848 7.66701 4 6.43701 4 5.333C4 4.229 5.848 3 8.5 3ZM4 8.48201C5.35986 9.28959 6.91876 9.7001 8.5 9.66701C10.0812 9.7001 11.6401 9.28959 13 8.48201V10.33C11.9102 10.6047 10.9107 11.1586 10.1 11.937C9.57422 12.0508 9.03795 12.1091 8.5 12.111C5.848 12.111 4 10.881 4 9.77801V8.48201ZM4 12.927C5.36015 13.7338 6.91891 14.1439 8.5 14.111C8.678 14.111 8.85 14.089 9.025 14.08C9.0101 14.1958 9.00176 14.3123 9 14.429V16.514C8.832 16.524 8.67 16.556 8.5 16.556C5.848 16.556 4 15.326 4 14.222V12.927ZM4 17.371C5.35986 18.1786 6.91876 18.5891 8.5 18.556C8.668 18.556 8.833 18.543 9 18.535V18.571C9.01431 19.4223 9.34144 20.2385 9.919 20.864C9.45111 20.9524 8.97615 20.9979 8.5 21C5.848 21 4 19.77 4 18.667V17.371Z" fill="#40189D"/>
                                        </svg>
                                        <div class="item-price">Lebih praktis dan efisien absensi pakai E-Absensi</div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <svg class="text-primary" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path opacity="0.4" d="M0.000244141 9.07849C0.0502441 11.4165 0.190244 15.4155 0.210244 15.8565C0.281244 16.7995 0.642244 17.7525 1.20424 18.4245C1.98624 19.3675 2.94924 19.7885 4.29224 19.7885C6.14824 19.7985 8.19424 19.7985 10.1812 19.7985C12.1762 19.7985 14.1122 19.7985 15.7472 19.7885C17.0712 19.7885 18.0642 19.3565 18.8362 18.4245C19.3982 17.7525 19.7592 16.7895 19.8102 15.8565C19.8302 15.4855 19.9302 11.1445 19.9902 9.07849H0.000244141Z" fill="#130F26"/>
                                            <path d="M9.24548 13.3842V14.6782C9.24548 15.0922 9.58148 15.4282 9.99548 15.4282C10.4095 15.4282 10.7455 15.0922 10.7455 14.6782V13.3842C10.7455 12.9702 10.4095 12.6342 9.99548 12.6342C9.58148 12.6342 9.24548 12.9702 9.24548 13.3842Z" fill="#130F26"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M8.21137 12.5564C8.11137 12.9194 7.76237 13.1514 7.38437 13.1014C4.83337 12.7454 2.39537 11.8404 0.337366 10.4814C0.126366 10.3434 0.000366211 10.1074 0.000366211 9.8554V6.3894C0.000366211 4.2894 1.71237 2.5814 3.81737 2.5814H5.78437C5.97237 1.1294 7.20237 0.000396729 8.70437 0.000396729H11.2864C12.7874 0.000396729 14.0184 1.1294 14.2064 2.5814H16.1834C18.2824 2.5814 19.9904 4.2894 19.9904 6.3894V9.8554C19.9904 10.1074 19.8634 10.3424 19.6544 10.4814C17.5924 11.8464 15.1444 12.7554 12.5764 13.1104C12.5414 13.1154 12.5074 13.1174 12.4734 13.1174C12.1344 13.1174 11.8314 12.8884 11.7464 12.5524C11.5444 11.7564 10.8214 11.1994 9.99037 11.1994C9.14837 11.1994 8.43337 11.7444 8.21137 12.5564ZM11.2864 1.5004H8.70437C8.03137 1.5004 7.46937 1.9604 7.30137 2.5814H12.6884C12.5204 1.9604 11.9584 1.5004 11.2864 1.5004Z" fill="#130F26"/>
                                        </svg>
                                        <div class="item-price">RS PKU Muhammadiyah Sukoharjo</div>
                                    </div>
                                </div>
                            </div>
                            <div class="sortable-handler"></div>
                        </li>
                    </ul>
                </div>

            </div>
        {{-- @else
        @endif --}}
    </div>
</div>

<div class="offcanvas-backdrop fade pwa-backdrop"></div>
<script>
    $(document).ready(function() {

    })
</script>
<script>
    window.onload = displayClock();
    function displayClock() {
        var display = new Date().toLocaleTimeString();
        $('#clock').text(display);
        setTimeout(displayClock, 1000);
    }
</script>
@endsection
