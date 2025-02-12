@extends('layouts.dashboard.index')

@section('content')
<!-- Your Page Content Goes Here-->
<div class="page-content">

    <div class="card mb-n5" data-card-height="500" style="background-image: url(/images/cover.jpg)">
        <div class="card-overlay bg-gradient opacity-90"></div>
    </div>

    <div class="card card-style over-card">
        <div class="content">
            <div class="hstack gap-3">
                <div class="d-flex">
                    <div><img src="{{ asset('/images/user2.png') }}" width="50" class="rounded-xl"></div>
                    <div>
                        <h5 class="mx-2">Yussuf Faisal</h5>
                        <p class="mb-0 mt-n2 font-11 mx-2">Reguler</p>
                        <p class="mb-0 mt-n2 font-10 mx-2">07.00 - 14.00 WIB</p>
                    </div>
                </div>
                <div class="ms-auto"></div>
                <div class="vr"></div>
                <div class="align-self-center"><span class="opacity-50 font-10"><i class="bi bi-clock pe-2"></i>{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</span><h1 id="clock"></h1></div>
            </div>
        </div>
    </div>

    <div class="card card-style bg-18">
        <div class="card-body my-3">
            <h5 class="badge gradient-highlight shadow-bg shadow-bg-s color-white rounded-xs p-2 font-11 mb-3">LIVE FEED</h5>
            <h1 class="color-white">
                Event
                <br>Coverage
            </h1>
            <p class="color-white opacity-60 mb-0">
                We are here to show off the brand new Apple Watch Release. Join us and watch it live!
            </p>
            <a href="#" class="btn btn-s rounded-sm gradient-highlight shadow-bg shadow-bg-m color-white mt-3 text-uppercase font-800"><i class="bi bi-play-circle pe-2"></i> WATCH LIVE</a>
        </div>
        <div class="card-overlay bg-black opacity-50"></div>
        <div class="card-overlay bg-gradient-fade"></div>
    </div>


    {{-- <div class="card card-style">
        <div class="content">
            <div class="d-flex">
                <div><img src="images/pictures/1s.jpg" width="40" class="rounded-xl"></div>
                <div><h5 class="mx-2">John Doe</h5><p class="mb-0 mt-n2 font-10 mx-2">Posted 25 Minutes Ag</p></div>
            </div>
            <p class="mb-2">Absolutely brilliant widgets. This is a chat widget, right? I can use multiple comments? How about replies? Can we add those too?</p>
            <div class="d-flex">
                <div class="me-auto"><a href="#" class="color-red-dark font-11"><i class="bi bi-heart-fill color-red-dark"></i><span class="px-1"></span>Love</a></div>
                <div class="m-auto"><a href="#" class="color-theme font-11"><i class="bi bi-reply-fill font-13 color-blue-dark"></i><span class="px-1"></span>Reply</a></div>
                <div class="ms-auto"><a href="#" class="color-brown-dark font-11"><i class="bi bi-flag-fill color-brown-dark"></i><span class="px-1"></span>Report</a></div>
            </div>
        </div>
        <div class="content ps-4">
            <div class="d-flex">
                <div><img src="images/pictures/2s.jpg" width="40" class="rounded-xl"></div>
                <div><h5 class="mx-2">Jack Son</h5><p class="mb-0 mt-n2 font-10 mx-2">Posted 15 Minutes Ag</p></div>
            </div>
            <p class="mb-2">Yeap! Leave replies too! It's super simple!</p>
            <div class="d-flex">
                <div class="me-auto"><a href="#" class="color-red-dark font-11"><i class="bi bi-heart-fill color-red-dark"></i><span class="px-1"></span>Love</a></div>
                <div class="m-auto"><a href="#" class="color-theme font-11"><i class="bi bi-reply-fill font-13 color-blue-dark"></i><span class="px-1"></span>Reply</a></div>
                <div class="ms-auto"><a href="#" class="color-brown-dark font-11"><i class="bi bi-flag-fill color-brown-dark"></i><span class="px-1"></span>Report</a></div>
            </div>
        </div>
        <div class="content">
            <div class="d-flex">
                <div><img src="images/pictures/3s.jpg" width="40" class="rounded-xl"></div>
                <div><h5 class="mx-2">Sir John</h5><p class="mb-0 mt-n2 font-10 mx-2">Posted 15 Minutes Ag</p></div>
            </div>
            <p class="mb-2">This is awesome. How about if I just write a lot of text here, it will look good right? The typography seems amazing!</p>
            <div class="d-flex">
                <div class="me-auto"><a href="#" class="color-red-dark font-11"><i class="bi bi-heart-fill color-red-dark"></i><span class="px-1"></span>Love</a></div>
                <div class="m-auto"><a href="#" class="color-theme font-11"><i class="bi bi-reply-fill font-13 color-blue-dark"></i><span class="px-1"></span>Reply</a></div>
                <div class="ms-auto"><a href="#" class="color-brown-dark font-11"><i class="bi bi-flag-fill color-brown-dark"></i><span class="px-1"></span>Report</a></div>
            </div>
        </div>
        <div class="divider divider-margins mb-0"></div>
        <div class="content">
            <h4>Leave a reply.</h4>
            <p class="font-12">
                Please keep in mind of our <a href="page-terms.html">Terms and Conditions</a>
            </p>
            <div class="form-custom form-label form-icon mb-3">
                <i class="bi bi-person-circle font-14"></i>
                <input type="text" class="form-control rounded-xs" id="c1" placeholder="John Doe" pattern="[A-Za-z ]{1,32}" required />
                <label for="c1" class="color-theme">Your Name</label>
                <div class="valid-feedback">Excellent!<!-- text for field valid--></div>
                <div class="invalid-feedback">Name is Missing or Invalid</div>
                <span>(required)</span>
            </div>
            <div class="form-custom form-label form-icon mb-3">
                <i class="bi bi-at font-16"></i>
                <input type="email" class="form-control rounded-xs" id="c2" placeholder="name@example.com" pattern="[A-Za-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" required />
                <label for="c2" class="color-theme">Your Email</label>
                <div class="valid-feedback">Email address looks good!<!-- text for field valid--></div>
                <div class="invalid-feedback">Email is missing or is invalid.</div>
                <span>(required)</span>
            </div>
            <div class="form-custom form-label form-icon mb-3">
                <i class="bi bi-pencil-fill font-12"></i>
                <textarea class="form-control rounded-xs" placeholder="Leave a comment here" id="c7"></textarea>
                <label for="c7" class="color-theme">Your Message</label>
                <div class="valid-feedback">HTML5 does not offer Dates Field Validation!<!-- text for field valid--></div>
            </div>
        </div>
    </div> --}}

    {{-- <div class="card card-style">
        <div class="content mb-3">
            <h3>You may like</h3>
            <p>
                Other posts based on your current reading habbits.
            </p>
            <a href="#">
                <div class="d-flex mb-3">
                    <div class="align-self-center me-auto">
                        <img src="images/pictures/18l.jpg" class="rounded-m me-3" width="90">
                    </div>
                    <div class="align-self-center w-100">
                        <span class="badge text-uppercase px-2 py-1 gradient-blue shadow-bg shadow-bg-xs d-block me-3 mt-n3 rounded-xs">ECONOMICS</span>
                        <h5 class="font-15 pt-2">Global warming increases development speed of electric cars.</h5>
                        <span class="color-theme font-11 opacity-50">25 Minutes Ago</span>
                    </div>
                </div>
            </a>
            <div class="divider mb-3"></div>
            <a href="#">
                <div class="d-flex mb-3">
                    <div class="align-self-center me-auto">
                        <img src="images/pictures/1l.jpg" class="rounded-m me-3" width="90">
                    </div>
                    <div class="align-self-center w-100">
                        <span class="badge text-uppercase px-2 py-1 gradient-green shadow-bg shadow-bg-xs d-block me-3 mt-n3 rounded-xs">SOCIAL</span>
                        <h5 class="font-15 pt-2">Hosting companies now charge influencers more that double. Here's why.</h5>
                        <span class="color-theme font-11 opacity-50">25 Minutes Ago</span>
                    </div>
                </div>
            </a>
            <div class="divider mb-3"></div>
            <a href="#">
                <div class="d-flex mb-3">
                    <div class="align-self-center me-auto">
                        <img src="images/pictures/2l.jpg" class="rounded-m me-3" width="90">
                    </div>
                    <div class="align-self-center w-100">
                        <span class="badge text-uppercase px-2 py-1 gradient-red shadow-bg shadow-bg-xs d-block me-3 mt-n3 rounded-xs">TECHNOLOGY</span>
                        <h5 class="font-15 pt-2">Apple's sales increased tenfold after new Macbook M1 Realease.</h5>
                        <span class="color-theme font-11 opacity-50">25 Minutes Ago</span>
                    </div>
                </div>
            </a>
        </div>
    </div> --}}

    {{-- <div class="card card-style py-3">
        <div class="content px-2 text-center">
            <h5 class="mb-n1 font-12 color-highlight font-700 text-uppercase">Time to Go Mobile</h5>
            <h2>Get Duo Mobile Today</h2>
            <p class="mb-3">
                Start your next project with Duo and enjoy the power of a Progressive Web App.
            </p>
            <a href="https://1.envato.market/2ryjKA" target="_blank" class="default-link btn btn-m rounded-s gradient-highlight shadow-bg shadow-bg-s px-5 mb-0 mt-2">Get Duo Now</a>
        </div>
    </div> --}}

</div>
<!-- End of Page Content-->
<script>
    window.onload = displayClock();
    function displayClock() {
        var display = new Date().toLocaleTimeString();
        $('#clock').text(display);
        setTimeout(displayClock, 1000);
    }
</script>
@endsection
