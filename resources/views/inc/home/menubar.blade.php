<!-- Menubar -->
<div class="menubar-area">
    <div class="toolbar-inner menubar-nav">
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="ti ti-home" style="font-size: 30px"></i>
        </a>
        <a href="{{ route('absensi.index') }}" class="nav-link {{ request()->routeIs('absensi.index') ? 'active' : '' }}">
            <i class="ti ti-map-2" style="font-size: 30px"></i>
        </a>
        <a href="{{ route('absensi.index') }}" class="nav-link {{ request()->routeIs('absensi.index') ? 'active' : '' }}">
            <i class="ti ti-list-check" style="font-size: 30px"></i>
        </a>
        <a href="javascript:void(0);" class="menu-toggler">
            <i class="ti ti-apps" style="font-size: 30px"></i>
        </a>
    </div>
</div>
<!-- Menubar -->
