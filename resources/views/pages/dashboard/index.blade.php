@extends('layouts.home')

@section('content')
<div class="content-inner pt-0">
    <div class="container fb">
        <!-- Search -->
        <form class="m-b30">
            <div class="input-group">
                <span class="input-group-text">
                    <a href="javascript:void(0);" class="search-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M20.5605 18.4395L16.7528 14.6318C17.5395 13.446 18 12.0262 18 10.5C18 6.3645 14.6355 3 10.5 3C6.3645 3 3 6.3645 3 10.5C3 14.6355 6.3645 18 10.5 18C12.0262 18 13.446 17.5395 14.6318 16.7528L18.4395 20.5605C19.0245 21.1462 19.9755 21.1462 20.5605 20.5605C21.1462 19.9748 21.1462 19.0252 20.5605 18.4395ZM5.25 10.5C5.25 7.605 7.605 5.25 10.5 5.25C13.395 5.25 15.75 7.605 15.75 10.5C15.75 13.395 13.395 15.75 10.5 15.75C7.605 15.75 5.25 13.395 5.25 10.5Z" fill="#B9B9B9"/>
                        </svg>
                    </a>
                </span>
                <input type="text" placeholder="Cari sesuatu..." class="form-control ps-0 bs-0" style="border-top-right-radius:50px;border-bottom-right-radius:50px">
            </div>
        </form>

        {{-- @if ($list['agent']->isMobile()) --}}
            <!-- Dashboard Area -->
            <div class="dashboard-area">

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
@endsection
