<div class="header-bar header-fixed header-app header-bar-detached">
    <a data-back-button href="#"><i class="bi bi-caret-left-fill font-11 color-theme ps-2"></i></a>
    <a href="#" class="header-title color-theme font-13">Kembali</a>
    {{-- <a data-bs-toggle="offcanvas" data-bs-target="#menu-color" href="#"><i class="bi bi-palette-fill font-13 color-highlight"></i></a> --}}
    <a href="javascript:void(0);" id="clock" class="me-3 no-click"></a>
    {{-- <a href="#" class="show-on-theme-light" data-toggle-theme><i class="bi bi-moon-fill font-13"></i></a>
    <a href="#" class="show-on-theme-dark" data-toggle-theme ><i class="bi bi-lightbulb-fill color-yellow-dark font-13"></i></a> --}}
</div>
<script>
    window.onload = displayClock();
    function displayClock() {
        var display = new Date().toLocaleTimeString();
        $('#clock').html('<i class="bi bi-clock me-2"></i>'+display);
        setTimeout(displayClock, 1000);
    }
</script>
