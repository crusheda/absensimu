<div id="footer-bar" class="footer-bar-1">
    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active-nav' : '' }}"><i class="fa fa-home"></i><span>Dashboard</span></a>
    <a href="{{ route('absensi.index') }}" class="{{ request()->routeIs('absensi.index') ? 'active-nav' : '' }}"><i class="fa fa-map-marker-alt"></i><span>Absensi</span></a>
    <a href="{{ route('rekap.index') }}" class="{{ request()->routeIs('rekap.index') ? 'active-nav' : '' }}"><i class="fa fa-calendar-check"></i><span>Rekap</span></a>
    <a href="#" data-menu="menu-settings" class="color-mint-dark"><i class="fa fa-cog fa-spin"></i><span>Settings</span></a>
</div>
