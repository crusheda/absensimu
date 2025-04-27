<div class="header header-fixed @if (request()->routeIs('absensi.index')) header-logo-app @else header-logo-center @endif">
    @if (request()->routeIs('rekap.index') || request()->routeIs('rekap.detail'))
        <a href="#" class="header-title font-900">Rekap Absensi</a>
    @else
        @if (request()->routeIs('absensi.index'))
            <a href="#" id="clock" class="header-title font-18 font-900"></a>
        @else
            <a href="#" class="header-title font-900">Jadwal Pegawai</a>
        @endif
    @endif
    <a href="#" data-back-button class="header-icon header-icon-1" style="padding-top: 20px"><i class="fas fa-arrow-left"></i></a>
    @if (request()->routeIs('absensi.index'))
        {{-- <a href="#" onclick="startFrontCamera()" class="header-icon header-icon-4" style="padding-top: 20px"><i class="fas fa-camera fa-1x"></i></a> --}}
        <div id="header-icon-3-group">
            <button type="button" id="header-2" data-bs-toggle="dropdown" class="header-icon header-icon-2" aria-expanded="false">
                <i class="fas fa-camera fa-1x"></i>
            </button>
            <div class="dropdown-menu bg-theme border-0 shadow-l rounded-s me-2 mt-2" aria-labelledby="header-2"
                style="margin: 0px;">
                <p class="font-12 ps-3 pe-3 font-500 mb-0 text-center">Pilihan Kamera</p>
                <div class="divider mb-0"></div>
                <div class="list-group list-custom-small ps-2 pe-3">
                    <a href="#" onclick="startFrontCamera()">
                        <span>Kamera <b class="color-highlight">Depan</b></span>
                        <i class="fa fa-angle-right"></i>
                    </a>
                    <a href="#" onclick="startFrontCameraFlip()">
                        <span>Kamera <b class="color-highlight">Depan</b> (<b class="color-red-dark">Flip</b>)</span>
                        <i class="fa fa-angle-right"></i>
                    </a>
                    <a href="#" onclick="startRearCamera()" class="border-0">
                        <span>Kamera <b class="color-highlight">Belakang</b></span>
                        <i class="fa fa-angle-right"></i>
                    </a>
                </div>
            </div>
        </div>
    @else
        <a href="#" data-toggle-theme class="header-icon header-icon-4" style="padding-top: 20px"><i class="fas fa-lightbulb"></i></a>
    @endif

</div>
