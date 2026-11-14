<!doctype html>
<html lang="en">

<head>
    @include('partials.favicon')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="@yield('page_description', '')">
    <meta name="author" content="">

    <title>@yield('page_title', new \Illuminate\Support\HtmlString('Film Fest-26 | IAC Film & TV Society'))</title>

    <!-- CSS FILES -->
    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100;200;400;700&display=swap" rel="stylesheet">

    <link href="{{asset('frontend/css/bootstrap.min.css')}}" rel="stylesheet">

    <link href="{{asset('frontend/css/bootstrap-icons.css')}}" rel="stylesheet">

    <link href="{{asset('frontend/css/templatemo-festava-live.css')}}" rel="stylesheet">
    <link href="{{ asset('frontend/css/brand.css') }}?v={{ filemtime(public_path('frontend/css/brand.css')) }}" rel="stylesheet">
    <link href="{{ asset('frontend/css/motion.css') }}?v={{ md5_file(public_path('frontend/css/motion.css')) }}" rel="stylesheet">
    <script src="{{ asset('frontend/js/motion.js') }}?v={{ filemtime(public_path('frontend/js/motion.js')) }}" defer></script>

    <!--

TemplateMo 583 Festava Live

https://templatemo.com/tm-583-festava-live

-->
</head>

<body>

    <main>
        <nav class="navbar navbar-expand-lg festival-nav" aria-label="Main navigation">
            <div class="container">
                <a class="navbar-brand" href="{{route('index')}}">
                   <span>Film Fest-26</span>
                </a>

                @if(config('app.show_navbar_buy_ticket'))
                    <a href="{{route('form')}}" class="btn custom-btn navbar-ticket-btn d-lg-none ms-auto">Buy Ticket</a>
                @endif

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav align-items-lg-center ms-auto me-lg-3">
                        <li class="nav-item">
                            <a class="nav-link click-scroll" href="{{ route('index') }}#home">Home</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link click-scroll" href="{{ route('index') }}#about">About</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link click-scroll" href="{{ route('index') }}#leaders">Leaders</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link click-scroll" href="{{ route('index') }}#schedule">Schedule</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('submission') ? 'active' : '' }}" href="{{ route('submission') }}" @if(request()->routeIs('submission')) aria-current="page" @endif>Submission</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link click-scroll" href="{{ route('index') }}#pricing">Pricing</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link click-scroll" href="{{ route('index') }}#venue"> Venue</a>
                        </li>

                    </ul>

                    @if(config('app.show_navbar_buy_ticket'))
                        <a href="{{route('form')}}" class="btn custom-btn navbar-ticket-btn d-lg-block d-none">Buy Ticket</a>
                    @endif
                </div>
            </div>
        </nav>

        @yield('content')
    </main>


    <footer class="site-footer">
        @include('partials.footer-venue')
        <div class="site-footer-top">
            <div class="container">
                <div class="row">

                    <div class="col-lg-6 col-12">
                        <h2 class="text-white mb-lg-0 mb-3">Film Fest-26 </h2>
                        
                        <div class="footer-logos d-flex flex-row align-items-center justify-content-center justify-content-lg-start gap-3 gap-md-4 mt-4">
                            <img src="{{asset('frontend/images/leaders/uni-logo.png')}}" alt="University Logo" class="footer-logo img-fluid" style="max-height: 80px;">
                            <img src="{{asset('frontend/images/leaders/ftv-society-logo.png')}}" alt="FTV Society — Film & TV Society, IAC" class="footer-logo img-fluid" style="max-height: 80px; max-width: 160px; width: auto; height: auto;" loading="lazy">
                            <span class="festival-logo festival-logo-footer"><img src="{{ asset('frontend/images/filmfestlogo.png') }}" alt="IAC Film Festival logo" loading="lazy"></span>
                        </div>
                    </div>

                    <div class="col-lg-6 col-12 d-flex justify-content-center justify-content-lg-end align-items-end align-items-lg-center mt-4 mt-lg-0">
                        <ul class="social-icon d-flex justify-content-end">
                            {{-- <li class="social-icon-item">
                                <a href="#" class="social-icon-link">
                                    <span class="bi-twitter"></span>
                                </a>
                            </li>

                            <li class="social-icon-item">
                                <a href="#" class="social-icon-link">
                                    <span class="bi-apple"></span>
                                </a>
                            </li> --}}

                            <li class="social-icon-item">
                                <a href="https://www.instagram.com/iac_ftvsociety/" target="_blank" class="social-icon-link">
                                    <span class="bi-instagram"></span>
                                </a>
                            </li>

                            {{-- <li class="social-icon-item">
                                <a href="#" class="social-icon-link">
                                    <span class="bi-youtube"></span>
                                </a>
                            </li> --}}

                            {{-- <li class="social-icon-item">
                                <a href="#" class="social-icon-link">
                                    <span class="bi-pinterest"></span>
                                </a>
                            </li> --}}
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- <div class="container">
            <div class="row">

                <div class="col-lg-6 col-12 mb-4 pb-2">
                    <h5 class="site-footer-title mb-3">Links</h5>

                    <ul class="site-footer-links">
                        <li class="site-footer-link-item">
                            <a href="#" class="site-footer-link">Home</a>
                        </li>

                        <li class="site-footer-link-item">
                            <a href="#" class="site-footer-link">About</a>
                        </li>

                        <li class="site-footer-link-item">
                            <a href="#" class="site-footer-link">Artists</a>
                        </li>

                        <li class="site-footer-link-item">
                            <a href="#" class="site-footer-link">Schedule</a>
                        </li>

                        <li class="site-footer-link-item">
                            <a href="#" class="site-footer-link">Pricing</a>
                        </li>

                        <li class="site-footer-link-item">
                            <a href="#" class="site-footer-link">Contact</a>
                        </li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6 col-12 mb-4 mb-lg-0">
                    <h5 class="site-footer-title mb-3">Have a question?</h5>

                    <p class="text-white d-flex mb-1">
                        <a href="tel: 090-080-0760" class="site-footer-link">
                            090-080-0760
                        </a>
                    </p>

                    <p class="text-white d-flex">
                        <a href="mailto:hello@company.com" class="site-footer-link">
                            hello@company.com
                        </a>
                    </p>
                </div>

                <div class="col-lg-3 col-md-6 col-11 mb-4 mb-lg-0 mb-md-0">
                    <h5 class="site-footer-title mb-3">Location</h5>

                    <p class="text-white d-flex mt-3 mb-2">
                        Silang Junction South, Tagaytay, Cavite, Philippines</p>

                    <a class="link-fx-1 color-contrast-higher mt-3" href="#">
                        <span>Our Maps</span>
                        <svg class="icon" viewBox="0 0 32 32" aria-hidden="true">
                            <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="16" cy="16" r="15.5"></circle>
                                <line x1="10" y1="18" x2="16" y2="12"></line>
                                <line x1="16" y1="12" x2="22" y2="18"></line>
                            </g>
                        </svg>
                    </a>
                </div>
            </div>
        </div> --}}

       
    </footer>

    <!--

T e m p l a t e M o

-->

    <!-- JAVASCRIPT FILES -->
    <script src="{{asset('frontend/js/jquery.min.js')}}"></script>
    <script src="{{asset('frontend/js/bootstrap.min.js')}}"></script>
    <script src="{{ asset('frontend/js/click-scroll.js') }}?v={{ filemtime(public_path('frontend/js/click-scroll.js')) }}"></script>
    <script src="{{ asset('frontend/js/custom.js') }}?v={{ filemtime(public_path('frontend/js/custom.js')) }}"></script>

</body>

</html>
