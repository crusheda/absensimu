@extends('layouts.home')

@section('content')
<style>
    #map { height: 180px;},
    /*.webcam-selfi,*/
    .webcam-selfi video {
        /*display: inline-block; */
        width: auto;
        height: auto !important;
        border-radius: 15px;
    }
</style>
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
            <div class="dashboard-area container">
                <input type="text" class="form-control mb-4" id="lokasi">
                <!--<div class="features-box mb-4 webcam-selfi"></div>-->
                <div class="row">
                    <div class="col-md-12 mb-4">
                        <div class="webcam-selfi" style=""></div>
                        <br/>
                        <input type="button" class="form-control" value="Take Snapshot" onClick="take_snapshot()">
                        <button class="form-control" onclick="showSwal()">TES SWAL</button>
                        <input type="hidden" name="image" class="image-tag">
                    </div>
                    <div class="col-md-12">
                        <div id="results" style="height:auto;width:auto"></div>
                    </div>
                    <div class="col-md-12 text-center">
                        <br/>
                        <!--<button class="btn btn-success">Submit</button>-->
                    </div>
                </div>
                {{-- <div class="features-box mb-4">
                    <div id="reader" width="300px"></div>
                    <div id="result" hidden></div>
                </div> --}}
                <div id="map"></div>
                {{-- <button class="form-control" onclick="getLocation()">Try It</button> --}}

                <!-- Features -->
                {{-- <div class="features-box">
                    <div class="row m-b20 g-3">
                        <div class="col">
                            <div class="card card-bx card-content bg-primary">
                                <div class="card-body">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="50" height="50" viewBox="0 0 24 24" version="1.1" class="svg-main-icon">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect x="0" y="0" width="24" height="24"/>
                                            <path d="M5.84026576,8 L18.1597342,8 C19.1999115,8 20.0664437,8.79732479 20.1528258,9.83390904 L20.8194924,17.833909 C20.9112219,18.9346631 20.0932459,19.901362 18.9924919,19.9930915 C18.9372479,19.9976952 18.8818364,20 18.8264009,20 L5.1735991,20 C4.0690296,20 3.1735991,19.1045695 3.1735991,18 C3.1735991,17.9445645 3.17590391,17.889153 3.18050758,17.833909 L3.84717425,9.83390904 C3.93355627,8.79732479 4.80008849,8 5.84026576,8 Z M10.5,10 C10.2238576,10 10,10.2238576 10,10.5 L10,11.5 C10,11.7761424 10.2238576,12 10.5,12 L13.5,12 C13.7761424,12 14,11.7761424 14,11.5 L14,10.5 C14,10.2238576 13.7761424,10 13.5,10 L10.5,10 Z" fill="#fff"/>
                                            <path d="M10,8 L8,8 L8,7 C8,5.34314575 9.34314575,4 11,4 L13,4 C14.6568542,4 16,5.34314575 16,7 L16,8 L14,8 L14,7 C14,6.44771525 13.5522847,6 13,6 L11,6 C10.4477153,6 10,6.44771525 10,7 L10,8 Z" fill="#fff" fill-rule="nonzero" opacity="0.3"/>
                                        </g>
                                    </svg>
                                    <div class="card-info">
                                        <h4 class="title">29x</h4>
                                        <p>Tepat Waktu</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card card-bx card-content bg-secondary">
                                <div class="card-body">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="50" height="50" viewBox="0 0 24 24" version=	"1.1" class="svg-main-icon">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect x="0" y="0" width="24" height="24"/>
                                            <path d="M12,2 C13.8385982,2 15.5193947,3.03878936 16.3416408,4.68328157 L19,10 C20.365323,12.730646 19.25851,16.0510849 16.527864,17.4164079 C15.7602901,17.8001948 14.9139019,18 14.0557281,18 L9.94427191,18 C6.8913169,18 4.41640786,15.525091 4.41640786,12.472136 C4.41640786,11.6139622 4.61621302,10.767574 5,10 L7.65835921,4.68328157 C8.48060532,3.03878936 10.1614018,2 12,2 Z M7.55,13.6 C9.00633458,14.6922509 10.4936654,15.25 12,15.25 C13.5063346,15.25 14.9936654,14.6922509 16.45,13.6 L15.55,12.4 C14.3396679,13.3077491 13.1603321,13.75 12,13.75 C10.8396679,13.75 9.66033208,13.3077491 8.45,12.4 L7.55,13.6 Z" fill="#fff"/>
                                            <path d="M6.15999985,21.0604779 L8.15999985,17.5963763 C8.43614222,17.1180837 9.04773263,16.9542085 9.52602525,17.2303509 C10.0043179,17.5064933 10.168193,18.1180837 9.89205065,18.5963763 L7.89205065,22.0604779 C7.61590828,22.5387706 7.00431787,22.7026457 6.52602525,22.4265033 C6.04773263,22.150361 5.88385747,21.5387706 6.15999985,21.0604779 Z M17.8320512,21.0301278 C18.1081936,21.5084204 17.9443184,22.1200108 17.4660258,22.3961532 C16.9877332,22.6722956 16.3761428,22.5084204 16.1000004,22.0301278 L14.1000004,18.5660262 C13.823858,18.0877335 13.9877332,17.4761431 14.4660258,17.2000008 C14.9443184,16.9238584 15.5559088,17.0877335 15.8320512,17.5660262 L17.8320512,21.0301278 Z" fill="#fff" opacity="0.3"/>
                                        </g>
                                    </svg>

                                    <div class="card-info">
                                        <h4 class="title">3x</h4>
                                        <p>Terlambat</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> --}}
                <!-- Features End -->

                <!-- Categorie -->
                <div class="categorie-section">
                    <div class="title-bar">
                        <h5 class="dz-title">Menu Absensi</h5>
                    </div>
                    <ul class="d-flex align-items-center ps-0 text-center">
                        <li>
                            <button class="btn btn-primary me-sm-3 me-1" id="reload-scan" onclick="reloadScan()"><i class="fa fa-sync fa-2x text-dark" width="30"></i>&nbsp;&nbsp;Ulangi Scan</button>
                            {{-- <a href="search.html" class="btn">
                                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.4" d="M19.9925 18.9533H14.2982C13.7426 18.9533 13.2908 19.4123 13.2908 19.9766C13.2908 20.5421 13.7426 21 14.2982 21H19.9925C20.548 21 20.9999 20.5421 20.9999 19.9766C20.9999 19.4123 20.548 18.9533 19.9925 18.9533Z" fill="#130F26"/>
                                    <path d="M10.309 6.90388L15.7049 11.264C15.835 11.3682 15.8573 11.5596 15.7557 11.6929L9.35874 20.0282C8.95662 20.5431 8.36402 20.8345 7.72908 20.8452L4.23696 20.8882C4.05071 20.8904 3.88775 20.7614 3.84542 20.5765L3.05175 17.1258C2.91419 16.4916 3.05175 15.8358 3.45388 15.3306L9.88256 6.95548C9.98627 6.82111 10.1778 6.79746 10.309 6.90388Z" fill="#130F26"/>
                                    <path opacity="0.4" d="M18.1205 8.66544L17.0803 9.96401C16.9755 10.0962 16.7872 10.1177 16.657 10.0124C15.3924 8.98901 12.1543 6.36285 11.2559 5.63509C11.1247 5.52759 11.1067 5.33625 11.2125 5.20295L12.2157 3.95706C13.1257 2.78534 14.7131 2.67784 15.9935 3.69906L17.4644 4.87078C18.0676 5.34377 18.4698 5.96726 18.6073 6.62299C18.7661 7.3443 18.5967 8.0527 18.1205 8.66544Z" fill="#130F26"/>
                                </svg>
                            </a> --}}
                        </li>
                        {{-- <li>
                            <a href="search.html" class="btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" version="1.1" class="svg-main-icon">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <polygon points="0 0 24 0 24 24 0 24"/>
                                        <path d="M10.5857864,12 L5.46446609,6.87867966 C5.0739418,6.48815536 5.0739418,5.85499039 5.46446609,5.46446609 C5.85499039,5.0739418 6.48815536,5.0739418 6.87867966,5.46446609 L12,10.5857864 L18.1923882,4.39339828 C18.5829124,4.00287399 19.2160774,4.00287399 19.6066017,4.39339828 C19.997126,4.78392257 19.997126,5.41708755 19.6066017,5.80761184 L13.4142136,12 L19.6066017,18.1923882 C19.997126,18.5829124 19.997126,19.2160774 19.6066017,19.6066017 C19.2160774,19.997126 18.5829124,19.997126 18.1923882,19.6066017 L12,13.4142136 L6.87867966,18.5355339 C6.48815536,18.9260582 5.85499039,18.9260582 5.46446609,18.5355339 C5.0739418,18.1450096 5.0739418,17.5118446 5.46446609,17.1213203 L10.5857864,12 Z" fill="#000000" opacity="0.3" transform="translate(12.535534, 12.000000) rotate(-360.000000) translate(-12.535534, -12.000000) "/>
                                        <path d="M6,18 L9,18 C9.66666667,18.1143819 10,18.4477153 10,19 C10,19.5522847 9.66666667,19.8856181 9,20 L4,20 L4,15 C4,14.3333333 4.33333333,14 5,14 C5.66666667,14 6,14.3333333 6,15 L6,18 Z M18,18 L18,15 C18.1143819,14.3333333 18.4477153,14 19,14 C19.5522847,14 19.8856181,14.3333333 20,15 L20,20 L15,20 C14.3333333,20 14,19.6666667 14,19 C14,18.3333333 14.3333333,18 15,18 L18,18 Z M18,6 L15,6 C14.3333333,5.88561808 14,5.55228475 14,5 C14,4.44771525 14.3333333,4.11438192 15,4 L20,4 L20,9 C20,9.66666667 19.6666667,10 19,10 C18.3333333,10 18,9.66666667 18,9 L18,6 Z M6,6 L6,9 C5.88561808,9.66666667 5.55228475,10 5,10 C4.44771525,10 4.11438192,9.66666667 4,9 L4,4 L9,4 C9.66666667,4 10,4.33333333 10,5 C10,5.66666667 9.66666667,6 9,6 L6,6 Z" fill="#000000" fill-rule="nonzero"/>
                                    </g>
                                </svg>
                            </a>
                        </li>
                        <li>
                            <a href="search.html" class="btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" version="1.1" class="svg-main-icon">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <polygon points="0 0 24 0 24 24 0 24"/>
                                        <path d="M12,4.25932872 C12.1488635,4.25921584 12.3000368,4.29247316 12.4425657,4.36281539 C12.6397783,4.46014562 12.7994058,4.61977315 12.8967361,4.81698575 L14.9389263,8.95491503 L19.5054023,9.61846284 C20.0519472,9.69788046 20.4306287,10.2053233 20.351211,10.7518682 C20.3195865,10.9695052 20.2170993,11.1706476 20.0596157,11.3241562 L16.7552826,14.545085 L17.5353298,19.0931094 C17.6286908,19.6374458 17.263103,20.1544017 16.7187666,20.2477627 C16.5020089,20.2849396 16.2790408,20.2496249 16.0843804,20.1472858 L12,18 L12,4.25932872 Z" fill="#000000" opacity="0.3"/>
                                        <path d="M12,4.25932872 L12,18 L7.91561963,20.1472858 C7.42677504,20.4042866 6.82214789,20.2163401 6.56514708,19.7274955 C6.46280801,19.5328351 6.42749334,19.309867 6.46467018,19.0931094 L7.24471742,14.545085 L3.94038429,11.3241562 C3.54490071,10.938655 3.5368084,10.3055417 3.92230962,9.91005817 C4.07581822,9.75257453 4.27696063,9.65008735 4.49459766,9.61846284 L9.06107374,8.95491503 L11.1032639,4.81698575 C11.277344,4.464261 11.6315987,4.25960807 12,4.25932872 Z" fill="#000000"/>
                                    </g>
                                </svg>
                            </a>
                        </li>
                        <li>
                            <a href="search.html" class="btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" version="1.1" class="svg-main-icon">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24"/>
                                        <path d="M17.2718029,8.68536757 C16.8932864,8.28319382 16.9124644,7.65031935 17.3146382,7.27180288 C17.7168119,6.89328641 18.3496864,6.91246442 18.7282029,7.31463817 L22.7282029,11.5646382 C23.0906029,11.9496882 23.0906029,12.5503176 22.7282029,12.9353676 L18.7282029,17.1853676 C18.3496864,17.5875413 17.7168119,17.6067193 17.3146382,17.2282029 C16.9124644,16.8496864 16.8932864,16.2168119 17.2718029,15.8146382 L20.6267538,12.2500029 L17.2718029,8.68536757 Z M6.72819712,8.6853647 L3.37324625,12.25 L6.72819712,15.8146353 C7.10671359,16.2168091 7.08753558,16.8496835 6.68536183,17.2282 C6.28318808,17.6067165 5.65031361,17.5875384 5.27179713,17.1853647 L1.27179713,12.9353647 C0.909397125,12.5503147 0.909397125,11.9496853 1.27179713,11.5646353 L5.27179713,7.3146353 C5.65031361,6.91246155 6.28318808,6.89328354 6.68536183,7.27180001 C7.08753558,7.65031648 7.10671359,8.28319095 6.72819712,8.6853647 Z" fill="#000000" fill-rule="nonzero"/>
                                        <rect fill="#40189d" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-345.000000) translate(-12.000000, -12.000000) " x="11" y="4" width="2" height="16" rx="1"/>
                                    </g>
                                </svg>
                            </a>
                        </li>
                        <li>
                            <a href="search.html" class="btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" version="1.1" class="svg-main-icon">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <polygon points="0 0 24 0 24 24 0 24"/>
                                        <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                                        <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero"/>
                                    </g>
                                </svg>
                            </a>
                        </li>
                        <li>
                            <a href="search.html" class="btn">
                                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10.1528 5.55553C10.2037 5.65919 10.2373 5.77021 10.2524 5.88434L10.5308 10.0243L10.669 12.1051C10.6705 12.3191 10.704 12.5317 10.7687 12.736C10.9356 13.1326 11.3372 13.3846 11.7741 13.367L18.4313 12.9315C18.7196 12.9268 18.998 13.0346 19.2052 13.2313C19.3779 13.3952 19.4894 13.6096 19.5246 13.8402L19.5364 13.9802C19.2609 17.7949 16.4592 20.9767 12.6524 21.798C8.84555 22.6193 4.94186 20.8843 3.06071 17.5349C2.51839 16.5618 2.17965 15.4923 2.06438 14.389C2.01623 14.0624 1.99503 13.7325 2.00098 13.4025C1.99503 9.31273 4.90747 5.77696 8.98433 4.92457C9.47501 4.84816 9.95603 5.10792 10.1528 5.55553Z" fill="#130F26"/>
                                    <path opacity="0.4" d="M12.8701 2.00082C17.43 2.11683 21.2624 5.39579 22.0001 9.81229L21.993 9.84488L21.9729 9.89227L21.9757 10.0224C21.9652 10.1947 21.8987 10.3605 21.784 10.4945C21.6646 10.634 21.5014 10.729 21.3217 10.7659L21.2121 10.7809L13.5313 11.2786C13.2758 11.3038 13.0214 11.2214 12.8314 11.052C12.6731 10.9107 12.5719 10.7201 12.5433 10.5147L12.0277 2.84506C12.0188 2.81913 12.0188 2.79102 12.0277 2.76508C12.0348 2.55367 12.1278 2.35384 12.2861 2.21023C12.4444 2.06662 12.6547 1.9912 12.8701 2.00082Z" fill="#130F26"/>
                                </svg>
                            </a>
                        </li>
                        <li>
                            <a href="search.html" class="btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" version="1.1" class="svg-main-icon">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <polygon points="0 0 24 0 24 24 0 24"/>
                                        <path d="M12.9336061,16.072447 L19.36,10.9564761 L19.5181585,10.8312381 C20.1676248,10.3169571 20.2772143,9.3735535 19.7629333,8.72408713 C19.6917232,8.63415859 19.6104327,8.55269514 19.5206557,8.48129411 L12.9336854,3.24257445 C12.3871201,2.80788259 11.6128799,2.80788259 11.0663146,3.24257445 L4.47482784,8.48488609 C3.82645598,9.00054628 3.71887192,9.94418071 4.23453211,10.5925526 C4.30500305,10.6811601 4.38527899,10.7615046 4.47382636,10.8320511 L4.63,10.9564761 L11.0659024,16.0730648 C11.6126744,16.5077525 12.3871218,16.5074963 12.9336061,16.072447 Z" fill="#000000" fill-rule="nonzero"/>
                                        <path d="M11.0563554,18.6706981 L5.33593024,14.122919 C4.94553994,13.8125559 4.37746707,13.8774308 4.06710397,14.2678211 C4.06471678,14.2708238 4.06234874,14.2738418 4.06,14.2768747 L4.06,14.2768747 C3.75257288,14.6738539 3.82516916,15.244888 4.22214834,15.5523151 C4.22358765,15.5534297 4.2250303,15.55454 4.22647627,15.555646 L11.0872776,20.8031356 C11.6250734,21.2144692 12.371757,21.2145375 12.909628,20.8033023 L19.7677785,15.559828 C20.1693192,15.2528257 20.2459576,14.6784381 19.9389553,14.2768974 C19.9376429,14.2751809 19.9363245,14.2734691 19.935,14.2717619 L19.935,14.2717619 C19.6266937,13.8743807 19.0546209,13.8021712 18.6572397,14.1104775 C18.654352,14.112718 18.6514778,14.1149757 18.6486172,14.1172508 L12.9235044,18.6705218 C12.377022,19.1051477 11.6029199,19.1052208 11.0563554,18.6706981 Z" fill="#000000" opacity="0.3"/>
                                    </g>
                                </svg>
                            </a>
                        </li> --}}
                    </ul>
                </div>
                <!-- Categorie End -->

                <!-- Riwayat Absensi -->
                {{-- <div class="title-bar">
                    <h5 class="dz-title">Riwayat Absensi</h5>
                    <a class="btn btn-sm text-primary" href="search.html">Selengkapnya</a>
                </div>
                <div class="list item-list recent-jobs-list">
                    <ul class="ps-0">
                        <li>
                            <div class="item-content">
                                <a href="job-detail.html" class="item-media"><img src="assets/images/logo/company-logo1.png" width="55" alt="logo"></a>
                                <div class="item-inner">
                                    <div class="item-title-row">
                                        <div class="item-subtitle">Highspeed Studios</div>
                                        <h6 class="item-title"><a href="job-detail.html">Software Engineer</a></h6>
                                    </div>
                                    <div class="d-flex align-items-center mb-2">
                                        <svg class="text-primary" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path d="M8.5 23C9.70017 23.0072 10.8898 22.7761 12 22.32C13.109 22.7799 14.2995 23.0112 15.5 23C19.145 23 22 21.055 22 18.571V14.429C22 11.945 19.145 10 15.5 10C15.331 10 15.165 10.008 15 10.017V5.333C15 2.9 12.145 1 8.5 1C4.855 1 2 2.9 2 5.333V18.667C2 21.1 4.855 23 8.5 23ZM20 18.571C20 19.72 18.152 21 15.5 21C12.848 21 11 19.72 11 18.571V17.646C12.3542 18.4696 13.9153 18.8898 15.5 18.857C17.0847 18.8898 18.6458 18.4696 20 17.646V18.571ZM15.5 12C18.152 12 20 13.28 20 14.429C20 15.578 18.152 16.857 15.5 16.857C12.848 16.857 11 15.577 11 14.429C11 13.281 12.848 12 15.5 12ZM8.5 3C11.152 3 13 4.23 13 5.333C13 6.43601 11.152 7.66701 8.5 7.66701C5.848 7.66701 4 6.43701 4 5.333C4 4.229 5.848 3 8.5 3ZM4 8.48201C5.35986 9.28959 6.91876 9.7001 8.5 9.66701C10.0812 9.7001 11.6401 9.28959 13 8.48201V10.33C11.9102 10.6047 10.9107 11.1586 10.1 11.937C9.57422 12.0508 9.03795 12.1091 8.5 12.111C5.848 12.111 4 10.881 4 9.77801V8.48201ZM4 12.927C5.36015 13.7338 6.91891 14.1439 8.5 14.111C8.678 14.111 8.85 14.089 9.025 14.08C9.0101 14.1958 9.00176 14.3123 9 14.429V16.514C8.832 16.524 8.67 16.556 8.5 16.556C5.848 16.556 4 15.326 4 14.222V12.927ZM4 17.371C5.35986 18.1786 6.91876 18.5891 8.5 18.556C8.668 18.556 8.833 18.543 9 18.535V18.571C9.01431 19.4223 9.34144 20.2385 9.919 20.864C9.45111 20.9524 8.97615 20.9979 8.5 21C5.848 21 4 19.77 4 18.667V17.371Z" fill="#40189D"/>
                                        </svg>
                                        <div class="item-price">$500 - $1,000</div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <svg class="text-primary" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path opacity="0.4" d="M0.000244141 9.07849C0.0502441 11.4165 0.190244 15.4155 0.210244 15.8565C0.281244 16.7995 0.642244 17.7525 1.20424 18.4245C1.98624 19.3675 2.94924 19.7885 4.29224 19.7885C6.14824 19.7985 8.19424 19.7985 10.1812 19.7985C12.1762 19.7985 14.1122 19.7985 15.7472 19.7885C17.0712 19.7885 18.0642 19.3565 18.8362 18.4245C19.3982 17.7525 19.7592 16.7895 19.8102 15.8565C19.8302 15.4855 19.9302 11.1445 19.9902 9.07849H0.000244141Z" fill="#130F26"/>
                                            <path d="M9.24548 13.3842V14.6782C9.24548 15.0922 9.58148 15.4282 9.99548 15.4282C10.4095 15.4282 10.7455 15.0922 10.7455 14.6782V13.3842C10.7455 12.9702 10.4095 12.6342 9.99548 12.6342C9.58148 12.6342 9.24548 12.9702 9.24548 13.3842Z" fill="#130F26"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M8.21137 12.5564C8.11137 12.9194 7.76237 13.1514 7.38437 13.1014C4.83337 12.7454 2.39537 11.8404 0.337366 10.4814C0.126366 10.3434 0.000366211 10.1074 0.000366211 9.8554V6.3894C0.000366211 4.2894 1.71237 2.5814 3.81737 2.5814H5.78437C5.97237 1.1294 7.20237 0.000396729 8.70437 0.000396729H11.2864C12.7874 0.000396729 14.0184 1.1294 14.2064 2.5814H16.1834C18.2824 2.5814 19.9904 4.2894 19.9904 6.3894V9.8554C19.9904 10.1074 19.8634 10.3424 19.6544 10.4814C17.5924 11.8464 15.1444 12.7554 12.5764 13.1104C12.5414 13.1154 12.5074 13.1174 12.4734 13.1174C12.1344 13.1174 11.8314 12.8884 11.7464 12.5524C11.5444 11.7564 10.8214 11.1994 9.99037 11.1994C9.14837 11.1994 8.43337 11.7444 8.21137 12.5564ZM11.2864 1.5004H8.70437C8.03137 1.5004 7.46937 1.9604 7.30137 2.5814H12.6884C12.5204 1.9604 11.9584 1.5004 11.2864 1.5004Z" fill="#130F26"/>
                                        </svg>
                                        <div class="item-price">Jakarta, Indonesia</div>
                                    </div>
                                </div>
                            </div>
                            <div class="sortable-handler"></div>
                        </li>
                        <li>
                            <div class="item-content">
                                <a href="job-detail.html" class="item-media"><img src="assets/images/logo/company-logo2.png" width="55" alt="logo"></a>
                                <div class="item-inner">
                                    <div class="item-title-row">
                                        <div class="item-subtitle">Lunar Djaja Corp.</div>
                                        <h6 class="item-title"><a href="job-detail.html">Database Engineer</a></h6>
                                    </div>
                                    <div class="d-flex align-items-center mb-2">
                                        <svg class="text-primary" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path d="M8.5 23C9.70017 23.0072 10.8898 22.7761 12 22.32C13.109 22.7799 14.2995 23.0112 15.5 23C19.145 23 22 21.055 22 18.571V14.429C22 11.945 19.145 10 15.5 10C15.331 10 15.165 10.008 15 10.017V5.333C15 2.9 12.145 1 8.5 1C4.855 1 2 2.9 2 5.333V18.667C2 21.1 4.855 23 8.5 23ZM20 18.571C20 19.72 18.152 21 15.5 21C12.848 21 11 19.72 11 18.571V17.646C12.3542 18.4696 13.9153 18.8898 15.5 18.857C17.0847 18.8898 18.6458 18.4696 20 17.646V18.571ZM15.5 12C18.152 12 20 13.28 20 14.429C20 15.578 18.152 16.857 15.5 16.857C12.848 16.857 11 15.577 11 14.429C11 13.281 12.848 12 15.5 12ZM8.5 3C11.152 3 13 4.23 13 5.333C13 6.43601 11.152 7.66701 8.5 7.66701C5.848 7.66701 4 6.43701 4 5.333C4 4.229 5.848 3 8.5 3ZM4 8.48201C5.35986 9.28959 6.91876 9.7001 8.5 9.66701C10.0812 9.7001 11.6401 9.28959 13 8.48201V10.33C11.9102 10.6047 10.9107 11.1586 10.1 11.937C9.57422 12.0508 9.03795 12.1091 8.5 12.111C5.848 12.111 4 10.881 4 9.77801V8.48201ZM4 12.927C5.36015 13.7338 6.91891 14.1439 8.5 14.111C8.678 14.111 8.85 14.089 9.025 14.08C9.0101 14.1958 9.00176 14.3123 9 14.429V16.514C8.832 16.524 8.67 16.556 8.5 16.556C5.848 16.556 4 15.326 4 14.222V12.927ZM4 17.371C5.35986 18.1786 6.91876 18.5891 8.5 18.556C8.668 18.556 8.833 18.543 9 18.535V18.571C9.01431 19.4223 9.34144 20.2385 9.919 20.864C9.45111 20.9524 8.97615 20.9979 8.5 21C5.848 21 4 19.77 4 18.667V17.371Z" fill="#40189D"/>
                                        </svg>
                                        <div class="item-price">$500 - $1,000</div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <svg class="text-primary" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path opacity="0.4" d="M0.000244141 9.07849C0.0502441 11.4165 0.190244 15.4155 0.210244 15.8565C0.281244 16.7995 0.642244 17.7525 1.20424 18.4245C1.98624 19.3675 2.94924 19.7885 4.29224 19.7885C6.14824 19.7985 8.19424 19.7985 10.1812 19.7985C12.1762 19.7985 14.1122 19.7985 15.7472 19.7885C17.0712 19.7885 18.0642 19.3565 18.8362 18.4245C19.3982 17.7525 19.7592 16.7895 19.8102 15.8565C19.8302 15.4855 19.9302 11.1445 19.9902 9.07849H0.000244141Z" fill="#130F26"/>
                                            <path d="M9.24548 13.3842V14.6782C9.24548 15.0922 9.58148 15.4282 9.99548 15.4282C10.4095 15.4282 10.7455 15.0922 10.7455 14.6782V13.3842C10.7455 12.9702 10.4095 12.6342 9.99548 12.6342C9.58148 12.6342 9.24548 12.9702 9.24548 13.3842Z" fill="#130F26"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M8.21137 12.5564C8.11137 12.9194 7.76237 13.1514 7.38437 13.1014C4.83337 12.7454 2.39537 11.8404 0.337366 10.4814C0.126366 10.3434 0.000366211 10.1074 0.000366211 9.8554V6.3894C0.000366211 4.2894 1.71237 2.5814 3.81737 2.5814H5.78437C5.97237 1.1294 7.20237 0.000396729 8.70437 0.000396729H11.2864C12.7874 0.000396729 14.0184 1.1294 14.2064 2.5814H16.1834C18.2824 2.5814 19.9904 4.2894 19.9904 6.3894V9.8554C19.9904 10.1074 19.8634 10.3424 19.6544 10.4814C17.5924 11.8464 15.1444 12.7554 12.5764 13.1104C12.5414 13.1154 12.5074 13.1174 12.4734 13.1174C12.1344 13.1174 11.8314 12.8884 11.7464 12.5524C11.5444 11.7564 10.8214 11.1994 9.99037 11.1994C9.14837 11.1994 8.43337 11.7444 8.21137 12.5564ZM11.2864 1.5004H8.70437C8.03137 1.5004 7.46937 1.9604 7.30137 2.5814H12.6884C12.5204 1.9604 11.9584 1.5004 11.2864 1.5004Z" fill="#130F26"/>
                                        </svg>
                                        <div class="item-price">London, United Kingdom</div>
                                    </div>
                                </div>
                            </div>
                            <div class="sortable-handler"></div>
                        </li>
                        <li>
                            <div class="item-content">
                                <a href="job-detail.html" class="item-media"><img src="assets/images/logo/company-logo3.png" width="55" alt="logo"></a>
                                <div class="item-inner">
                                    <div class="item-title-row">
                                        <div class="item-subtitle">Highspeed Studios</div>
                                        <h6 class="item-title"><a href="job-detail.html">Software Engineer</a></h6>
                                    </div>
                                    <div class="d-flex align-items-center mb-2">
                                        <svg class="text-primary" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path d="M8.5 23C9.70017 23.0072 10.8898 22.7761 12 22.32C13.109 22.7799 14.2995 23.0112 15.5 23C19.145 23 22 21.055 22 18.571V14.429C22 11.945 19.145 10 15.5 10C15.331 10 15.165 10.008 15 10.017V5.333C15 2.9 12.145 1 8.5 1C4.855 1 2 2.9 2 5.333V18.667C2 21.1 4.855 23 8.5 23ZM20 18.571C20 19.72 18.152 21 15.5 21C12.848 21 11 19.72 11 18.571V17.646C12.3542 18.4696 13.9153 18.8898 15.5 18.857C17.0847 18.8898 18.6458 18.4696 20 17.646V18.571ZM15.5 12C18.152 12 20 13.28 20 14.429C20 15.578 18.152 16.857 15.5 16.857C12.848 16.857 11 15.577 11 14.429C11 13.281 12.848 12 15.5 12ZM8.5 3C11.152 3 13 4.23 13 5.333C13 6.43601 11.152 7.66701 8.5 7.66701C5.848 7.66701 4 6.43701 4 5.333C4 4.229 5.848 3 8.5 3ZM4 8.48201C5.35986 9.28959 6.91876 9.7001 8.5 9.66701C10.0812 9.7001 11.6401 9.28959 13 8.48201V10.33C11.9102 10.6047 10.9107 11.1586 10.1 11.937C9.57422 12.0508 9.03795 12.1091 8.5 12.111C5.848 12.111 4 10.881 4 9.77801V8.48201ZM4 12.927C5.36015 13.7338 6.91891 14.1439 8.5 14.111C8.678 14.111 8.85 14.089 9.025 14.08C9.0101 14.1958 9.00176 14.3123 9 14.429V16.514C8.832 16.524 8.67 16.556 8.5 16.556C5.848 16.556 4 15.326 4 14.222V12.927ZM4 17.371C5.35986 18.1786 6.91876 18.5891 8.5 18.556C8.668 18.556 8.833 18.543 9 18.535V18.571C9.01431 19.4223 9.34144 20.2385 9.919 20.864C9.45111 20.9524 8.97615 20.9979 8.5 21C5.848 21 4 19.77 4 18.667V17.371Z" fill="#40189D"/>
                                        </svg>
                                        <div class="item-price">$500 - $1,000</div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <svg class="text-primary" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path opacity="0.4" d="M0.000244141 9.07849C0.0502441 11.4165 0.190244 15.4155 0.210244 15.8565C0.281244 16.7995 0.642244 17.7525 1.20424 18.4245C1.98624 19.3675 2.94924 19.7885 4.29224 19.7885C6.14824 19.7985 8.19424 19.7985 10.1812 19.7985C12.1762 19.7985 14.1122 19.7985 15.7472 19.7885C17.0712 19.7885 18.0642 19.3565 18.8362 18.4245C19.3982 17.7525 19.7592 16.7895 19.8102 15.8565C19.8302 15.4855 19.9302 11.1445 19.9902 9.07849H0.000244141Z" fill="#130F26"/>
                                            <path d="M9.24548 13.3842V14.6782C9.24548 15.0922 9.58148 15.4282 9.99548 15.4282C10.4095 15.4282 10.7455 15.0922 10.7455 14.6782V13.3842C10.7455 12.9702 10.4095 12.6342 9.99548 12.6342C9.58148 12.6342 9.24548 12.9702 9.24548 13.3842Z" fill="#130F26"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M8.21137 12.5564C8.11137 12.9194 7.76237 13.1514 7.38437 13.1014C4.83337 12.7454 2.39537 11.8404 0.337366 10.4814C0.126366 10.3434 0.000366211 10.1074 0.000366211 9.8554V6.3894C0.000366211 4.2894 1.71237 2.5814 3.81737 2.5814H5.78437C5.97237 1.1294 7.20237 0.000396729 8.70437 0.000396729H11.2864C12.7874 0.000396729 14.0184 1.1294 14.2064 2.5814H16.1834C18.2824 2.5814 19.9904 4.2894 19.9904 6.3894V9.8554C19.9904 10.1074 19.8634 10.3424 19.6544 10.4814C17.5924 11.8464 15.1444 12.7554 12.5764 13.1104C12.5414 13.1154 12.5074 13.1174 12.4734 13.1174C12.1344 13.1174 11.8314 12.8884 11.7464 12.5524C11.5444 11.7564 10.8214 11.1994 9.99037 11.1994C9.14837 11.1994 8.43337 11.7444 8.21137 12.5564ZM11.2864 1.5004H8.70437C8.03137 1.5004 7.46937 1.9604 7.30137 2.5814H12.6884C12.5204 1.9604 11.9584 1.5004 11.2864 1.5004Z" fill="#130F26"/>
                                        </svg>
                                        <div class="item-price">Medan, Indonesia</div>
                                    </div>
                                </div>
                            </div>
                            <div class="sortable-handler"></div>
                        </li>
                    </ul>
                </div> --}}
                <!-- Riwayat Absensi End -->

            </div>
        {{-- @else
        @endif --}}
    </div>
</div>

{{-- OFFCANVAS --}}
<div class="offcanvas offcanvas-bottom pwa-offcanvas">
    <div class="container">
        <div class="offcanvas-body small">
            <input type="text" id="ip" value="{{ Request::ip() }}" hidden>
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-light" style="text-align:left;">
                        <div class="col-md-12">
                            <h6>Ketentuan Absen</h6>
                        </div>
                        <div class="col-md-6">
                            <i class="ti ti-arrow-narrow-right me-1"></i> Pastikan HP terkoneksi pada jaringan Wifi Rumah Sakit<br>
                            <i class="ti ti-arrow-narrow-right me-1"></i> Silakan Selfi wajah Anda sebagai Bukti Absensi
                        </div>
                        <div class="col-md-6">

                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                    </div>
                    <div class="form-group">
                        <input type="file" class="form-control mb-4" name="photo" capture="user" accept="image/*">
                    </div>
                </div>
                <div class="col-md-12 mb-4">
                    <center>
                        <div class="btn-group">
                            <button class="btn btn-primary" onclick="absen()">Validasi</button>
                            <button class="btn btn-outline-secondary" onclick="closeScan()">Tutup</button>
                        </div>
                    </center>
                </div>
            </div>
            {{-- <img class="logo" src="assets/images/icon.png" alt="">
            <h5 class="title">Jobie on Your Home Screen</h5>
            <p class="pwa-text">Install Jobie job portal mobile app template to your home screen for easy access, just like any other app</p>
            <a href="javascrpit:void(0);" class="btn btn-sm btn-secondary ms-2" onclick="closeScan()">Sembunyikan</a> --}}
        </div>
    </div>
</div>
<div class="offcanvas-backdrop fade pwa-backdrop"></div>
<script src="{{ asset('js/html5-qrcode.js') }}"></script>
<script>
    var map;
    $(document).ready(function() {
        Webcam.set({
            height: 480,
            width: 0,
            image_format: 'jpeg',
            jpeg_quality: 80
        });
        Webcam.attach('.webcam-selfi');

        const x = document.getElementById("lokasi");
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(position => {
                const { latitude, longitude } = position.coords;
                $("#lokasi").val("Latitude: " + position.coords.latitude + "----->  Longitude: " + position.coords.longitude);
                // Show a map centered at latitude / longitude.
                map = L.map('map').setView([position.coords.latitude, position.coords.longitude], 16);
                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                }).addTo(map);

                // Titik Lokasi GPS
                var marker = new L.Marker([position.coords.latitude, position.coords.longitude]);
                marker.addTo(map);
                
                // Radius
                var circle = L.circle([-7.6378845, 110.868032], {
                    color: 'red',
                    fillColor: '#f03',
                    fillOpacity: 0.5,
                    radius: 50
                }).addTo(map);
            });
        } else {
            alert("Geolocation is not supported by this browser.");
        }
    })


    function take_snapshot() {
        Webcam.snap( function(data_uri) {
            $(".image-tag").val(data_uri);
            document.getElementById('results').innerHTML = '<img src="'+data_uri+'"/>';
        } );
    }

    function showSwal() {
        Swal.fire({
            title: `Absensi Berhasil`,
            text: 'Selamat beraktivitas!',
            icon: `success`,
            showConfirmButton: false,
            showCancelButton: false,
            allowOutsideClick: false,
            allowEscapeKey: false,
            timer: 3000,
            timerProgressBar: true,
            backdrop: `rgba(26,27,41,0.8)`,
        });
    }

    function showPosition(position) {
        x.innerHTML = "Latitude: " + position.coords.latitude +
        "----->  Longitude: " + position.coords.longitude;
    }

    function successCallback(position) {
        $("#lokasi").val(position.coords.latitude);
    }

    function errorCallback() {

    }
</script>
@endsection
