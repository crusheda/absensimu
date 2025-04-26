@extends('layouts.index')

@section('content')
<div class="page-content pb-0">
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div data-card-height="cover" class="card" style="height: 911px;">
            <div class="card-top notch-clear">
                <div class="d-flex">
                    {{-- <a href="#" data-back-button="" class="me-auto icon icon-m"><i
                            class="font-14 fa fa-arrow-left color-theme"></i></a> --}}
                    <a href="#" data-toggle-theme="" class="show-on-theme-light ms-auto icon icon-m"><i
                            class="font-12 fa fa-moon color-theme"></i></a>
                    <a href="#" data-toggle-theme="" class="show-on-theme-dark ms-auto icon icon-m"><i
                            class="font-12 fa fa-lightbulb color-yellow-dark"></i></a>
                </div>
            </div>
            <div class="card-center">
                <div class="ps-5 pe-5">
                    <div class="d-flex justify-content-center">
                        <div class="me-3"><img src="{{ asset('images/logo/logo_clear_100kb.png') }}" width="50" alt=""></div>
                        <div>

                            <h1 class="font-800 font-40 mb-1">E-Absensi</h1>
                            <p class="color-highlight font-12">RS PKU Muhammadiyah Sukoharjo</p>
                        </div>
                    </div>

                    <div class="input-style no-borders has-icon validate-field">
                        <i class="fa fa-user"></i>
                        <input type="text" class="form-control validate-name" id="form1a" name="name" value="{{ old('name') }}" placeholder="Masukkan Username" autocomplete="name" required>
                        <label for="form1a" class="color-blue-dark font-10 mt-1">Username</label>
                        <i class="fa fa-times disabled invalid color-red-dark"></i>
                        <i class="fa fa-check disabled valid color-green-dark"></i>
                        <em>(Wajib)</em>
                    </div>

                    <div class="input-style no-borders has-icon validate-field mt-4">
                        <i class="fa fa-lock"></i>
                        <input type="password" class="form-control validate-password" id="form3a" name="password" value="{{ old('password') }}" autocomplete="current-password" placeholder="Masukkan Password" required>
                        <label for="form3a" class="color-blue-dark font-10 mt-1">Password</label>
                        <i class="fa fa-times disabled invalid color-red-dark"></i>
                        <i class="fa fa-check disabled valid color-green-dark"></i>
                        <em>(Wajib)</em>
                    </div>

                    <div class="d-flex mt-4 mb-4">
                        <div class="w-50 font-11 pb-2 text-start"><a href="#">Butuh Bantuan?</a>
                        </div>
                        <div class="w-50 font-11 pb-2 text-end"><a href="https://simrsmu.com/lupapassword">Lupa Password</a>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-fluid btn-full btn-m shadow-large rounded-sm text-uppercase font-700 bg-highlight" style="width: 100%">Masuk <i class="fas fa-sign-in-alt ms-1"></i></button>
                    {{-- <div class="divider mt-4"></div>
                    <a href="#"
                        class="btn btn-icon btn-m btn-full shadow-l rounded-sm bg-facebook text-uppercase font-700 text-start"><i
                            class="fab fa-facebook-f text-center bg-transparent"></i>Sign in with Facebook</a>
                    <a href="#"
                        class="btn btn-icon btn-m btn-full shadow-l rounded-sm bg-twitter text-uppercase font-700 text-start mt-2 "><i
                            class="fab fa-twitter text-center bg-transparent"></i>Sign in with Twitter</a> --}}
                </div>
            </div>
        </div>
    </form>
</div>
<!-- End of Page Content-->
@endsection
