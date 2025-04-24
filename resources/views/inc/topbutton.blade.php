<div class="header header-fixed header-logo-center">
    <a href="#" id="clock" class="header-title font-20"></a>
    <a href="#" data-back-button class="header-icon header-icon-1" style="padding-top: 20px"><i class="fas fa-arrow-left"></i></a>
    <a href="#" data-toggle-theme class="header-icon header-icon-4" style="padding-top: 20px"><i class="fas fa-lightbulb"></i></a>
</div>

<script>
    window.onload = displayClock();
    function displayClock() {
        var display = new Date().toLocaleTimeString();
        $('#clock').text(display);
        setTimeout(displayClock, 1000);
    }
</script>
