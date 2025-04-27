<div id="menu-settings" class="menu menu-box-bottom menu-box-detached" style="display: block;">
    <div class="menu-title mt-0 pt-0">
        <h1>Pengaturan</h1>
        {{-- <p class="color-highlight"></p><a href="#" class="close-menu"><i
                class="fa fa-times"></i></a> --}}
    </div>
    <div class="divider divider-margins mb-n2"></div>
    <div class="content">
        <div class="list-group list-custom-small">
            <a href="#" data-toggle-theme="" data-trigger-switch="switch-dark-mode" class="pb-2 ms-n1">
                <i class="fa font-12 fa-moon rounded-s bg-dark color-white me-3"></i>
                <span>Mode Gelap</span>
                <div class="custom-control scale-switch ios-switch">
                    <input data-toggle-theme="" type="checkbox" class="ios-input" id="switch-dark-mode">
                    <label class="custom-control-label" for="switch-dark-mode"></label>
                </div>
                <i class="fa fa-angle-right"></i>
            </a>
        </div>
        <div class="list-group list-custom-large">
            {{-- <a data-menu="menu-highlights" href="#">
                <i class="fa font-14 fa-tint bg-green-dark rounded-s"></i>
                <span>Warna Dasar</span>
                <strong>Terdapat 16 Pilihan Warna</strong>
                <span class="badge bg-highlight color-white">Baru</span>
                <i class="fa fa-angle-right"></i>
            </a> --}}
            <a href="{{ route('jadwal.index') }}">
                <i class="fa font-14 fa-calendar-alt bg-mint-dark rounded-s"></i>
                <span>Jadwal Dinas</span>
                <strong>Lihat Seluruh Jadwal Dinas Anda</strong>
                <span class="badge bg-highlight color-white">Baru</span>
                <i class="fa fa-angle-right"></i>
            </a>
            <a data-menu="menu-backgrounds" href="#">
                <i class="fa font-14 fa-cog bg-blue-dark rounded-s"></i>
                <span>Warna Latar Belakang</span>
                <strong>Terdapat 10 Pilihan Warna</strong>
                <span class="badge bg-highlight color-white">Baru</span>
                <i class="fa fa-angle-right"></i>
            </a>
            <a href="#" class="border-0" onclick="event.preventDefault(); document.getElementById('logoutform').submit();">
                <i class="fa font-14 fa-lock bg-red-dark rounded-s"></i>
                <span>Logout</span>
                <strong>Keluar dari Akun Absensi</strong>
                <i class="fa fa-angle-right"></i>
            </a>
        </div>
    </div>
</div>
<!-- Menu Settings Highlights-->
<div id="menu-highlights" class="menu menu-box-bottom menu-box-detached" style="display: block;">
    <div class="menu-title">
        <h1>Warna Dasar</h1>
        <p class="color-highlight">Kustomisasi Warna Dasar Halaman Anda</p><a href="#" class="close-menu"><i class="fa fa-times"></i></a>
    </div>
    <div class="divider divider-margins mb-n2"></div>
    <div class="content">
        <div class="highlight-changer">
            <a href="#" data-change-highlight="blue"><i class="fa fa-circle color-blue-dark"></i><span
                    class="color-blue-light">Default</span></a>
            <a href="#" data-change-highlight="red"><i class="fa fa-circle color-red-dark"></i><span
                    class="color-red-light">Red</span></a>
            <a href="#" data-change-highlight="orange"><i class="fa fa-circle color-orange-dark"></i><span
                    class="color-orange-light">Orange</span></a>
            <a href="#" data-change-highlight="pink2"><i class="fa fa-circle color-pink2-dark"></i><span
                    class="color-pink-dark">Pink</span></a>
            <a href="#" data-change-highlight="magenta"><i class="fa fa-circle color-magenta-dark"></i><span
                    class="color-magenta-light">Purple</span></a>
            <a href="#" data-change-highlight="aqua"><i class="fa fa-circle color-aqua-dark"></i><span
                    class="color-aqua-light">Aqua</span></a>
            <a href="#" data-change-highlight="teal"><i class="fa fa-circle color-teal-dark"></i><span
                    class="color-teal-light">Teal</span></a>
            <a href="#" data-change-highlight="mint"><i class="fa fa-circle color-mint-dark"></i><span
                    class="color-mint-light">Mint</span></a>
            <a href="#" data-change-highlight="green"><i class="fa fa-circle color-green-light"></i><span
                    class="color-green-light">Green</span></a>
            <a href="#" data-change-highlight="grass"><i class="fa fa-circle color-green-dark"></i><span
                    class="color-green-dark">Grass</span></a>
            <a href="#" data-change-highlight="sunny"><i class="fa fa-circle color-yellow-light"></i><span
                    class="color-yellow-light">Sunny</span></a>
            <a href="#" data-change-highlight="yellow"><i class="fa fa-circle color-yellow-dark"></i><span
                    class="color-yellow-light">Goldish</span></a>
            <a href="#" data-change-highlight="brown"><i class="fa fa-circle color-brown-dark"></i><span
                    class="color-brown-light">Wood</span></a>
            <a href="#" data-change-highlight="night"><i class="fa fa-circle color-dark-dark"></i><span
                    class="color-dark-light">Night</span></a>
            <a href="#" data-change-highlight="dark"><i class="fa fa-circle color-dark-light"></i><span
                    class="color-dark-light">Dark</span></a>
            <div class="clearfix"></div>
        </div>
        <a href="#" data-menu="menu-settings" class="mb-3 btn btn-full btn-m rounded-sm bg-highlight shadow-xl text-uppercase font-900 mt-4">Kembali ke Pengaturan</a>
    </div>
</div>
<!-- Menu Settings Backgrounds-->
<div id="menu-backgrounds" class="menu menu-box-bottom menu-box-detached" style="display: block;">
    <div class="menu-title">
        <h1>Warna Latar Belakang</h1>
        <p class="color-highlight">Kustomisasi Warna Background Halaman Anda</p><a href="#" class="close-menu"><i
                class="fa fa-times"></i></a>
    </div>
    <div class="divider divider-margins mb-n2"></div>
    <div class="content">
        <div class="background-changer">
            <a href="#" data-change-background="default"><i class="bg-theme"></i><span
                    class="color-dark-dark">Default</span></a>
            <a href="#" data-change-background="plum"><i class="body-plum"></i><span
                    class="color-plum-dark">Plum</span></a>
            <a href="#" data-change-background="magenta"><i class="body-magenta"></i><span
                    class="color-dark-dark">Magenta</span></a>
            <a href="#" data-change-background="dark"><i class="body-dark"></i><span
                    class="color-dark-dark">Dark</span></a>
            <a href="#" data-change-background="violet"><i class="body-violet"></i><span
                    class="color-violet-dark">Violet</span></a>
            <a href="#" data-change-background="red"><i class="body-red"></i><span
                    class="color-red-dark">Red</span></a>
            <a href="#" data-change-background="green"><i class="body-green"></i><span
                    class="color-green-dark">Green</span></a>
            <a href="#" data-change-background="sky"><i class="body-sky"></i><span
                    class="color-sky-dark">Sky</span></a>
            <a href="#" data-change-background="orange"><i class="body-orange"></i><span
                    class="color-orange-dark">Orange</span></a>
            <a href="#" data-change-background="yellow"><i class="body-yellow"></i><span
                    class="color-yellow-dark">Yellow</span></a>
            <div class="clearfix"></div>
        </div>
        <a href="#" data-menu="menu-settings" class="mb-3 btn btn-full btn-m rounded-sm bg-highlight shadow-xl text-uppercase font-900 mt-4">Kembali ke Pengaturan</a>
    </div>
</div>
