<div class="header header-fixed header-logo-center">
    @if (request()->routeIs('rekap.index') || request()->routeIs('rekap.detail'))
        <a href="#" class="header-title font-20">Rekap Absensi</a>
    @else
        <a href="#" id="clock" class="header-title font-20"></a>
    @endif
    <a href="#" data-back-button class="header-icon header-icon-1" style="padding-top: 20px"><i class="fas fa-arrow-left"></i></a>
    @if (request()->routeIs('absensi.index'))
        <a href="#" onclick="startFrontCamera()" class="header-icon header-icon-4" style="padding-top: 20px"><i class="fas fa-camera fa-1x"></i></a>
    @else
        <a href="#" data-toggle-theme class="header-icon header-icon-4" style="padding-top: 20px"><i class="fas fa-lightbulb"></i></a>
    @endif

</div>

<script>
    window.onload = displayClock();
    function displayClock() {
        var display = new Date().toLocaleTimeString();
        $('#clock').text(display);
        setTimeout(displayClock, 1000);
    }
</script>
