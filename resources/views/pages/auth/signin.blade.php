@extends('layouts.auth')

@section('content')
    <!-- Banner -->
    <div class="banner-wrapper shape-1">
        <div class="container inner-wrapper">
            <h2 class="dz-title">E-Absensi</h2>
            <p class="mb-0">Silakan masuk menggunakan akun Simrsmu</p>
        </div>
    </div>
    <!-- Banner End -->

    <div class="container">
        <div class="account-area">
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="input-group">
                    <input type="text" placeholder="Masukkan Username" id="name" name="name" class="form-control" value="{{ old('name') }}" autocomplete="name" required>
                </div>
                <div class="input-group">
                    <input type="password" placeholder="Masukkan Password" id="dz-password" name="password" class="form-control be-0" value="{{ old('password') }}" autocomplete="current-password" required>
                    <span class="input-group-text show-pass">
                        <i class="fa fa-eye-slash"></i>
                        <i class="fa fa-eye"></i>
                    </span>
                </div>
                <a href="javascript: void(0);" class="btn-link d-block text-center">Lupa Password?</a>
                <div class="input-group">
                    <button type="submit" class="btn mt-2 btn-primary w-100 btn-rounded">Masuk</button>
                </div>
            </form>
            {{-- <div class="text-center p-tb20">
                <span class="saprate">Or sign in with</span>
            </div>
            <div class="social-btn-group text-center">
                <a href="https://www.google.com/" target="_blank" class="social-btn"><img
                        src="{{ asset('assets/images/social/google.png') }}" alt="socila-image"></a>
                <a href="https://www.facebook.com/" target="_blank" class="social-btn ms-3"><img
                        src="{{ asset('assets/images/social/facebook.png') }}" alt="social-image"></a>
            </div> --}}
        </div>
    </div>
@endsection
