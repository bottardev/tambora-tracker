<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tambora Tracker - Mount Tambora Hiking & Adventure')</title>
    <link rel="shortcut icon" href="{{ asset('demo/html/bycamp/light/img/favicon.png') }}" />
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/otomanopee-one" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('demo/html/bycamp/light/css/plugins.css') }}" />
    <link rel="stylesheet" href="{{ asset('demo/html/bycamp/light/css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/booking-enhancements.css') }}" />
    @yield('styles')
</head>
<body>
    <!-- Preloader -->
    <div class="preloader-bg"></div>
    <div id="preloader">
        <div id="preloader-status">
            <div class="preloader-position loader"> <span></span> </div>
        </div>
    </div>
    
    <!-- Progress scroll totop -->
    <div class="progress-wrap cursor-pointer">
        <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
            <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" />
        </svg>
    </div>
    
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <!-- Logo -->
            <div class="logo-wrapper">
                <a class="logo" href="{{ route('booking.homepage') }}"> 
                    <img src="{{ asset('demo/html/bycamp/light/img/logo.png') }}" class="logo-img" alt="Tambora Tracker"> 
                </a>
            </div>
            <!-- Button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation"> 
                <span class="navbar-toggler-icon"><i class="fa-light fa-bars"></i></span> 
            </button>
            <!-- Menu -->
            <div class="collapse navbar-collapse" id="navbar">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('booking.homepage') ? 'active' : '' }}" href="{{ route('booking.homepage') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#routes">Hiking Routes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                </ul>
                <div class="navbar-right">
                    <div class="button">
                        <a data-bs-toggle="modal" data-bs-target="#bookingModal" href="#0">
                            <i class="ti-calendar mr-5"></i>Book Now
                        </a>
                    </div>
                    <div class="button ml-10">
                        <a href="{{ route('booking.check-status') }}" class="btn-outline">
                            <i class="ti-search mr-5"></i>Check Booking
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    @yield('content')

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-top">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 col-md-12">
                        <div class="item">
                            <div class="logo-footer">
                                <img src="{{ asset('demo/html/bycamp/light/img/logo.png') }}" alt="Tambora Tracker">
                            </div>
                            <p>Experience the adventure of Mount Tambora with professional guides and safety equipment.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-12">
                        <div class="item">
                            <h6>Quick Links</h6>
                            <ul>
                                <li><a href="#routes">Hiking Routes</a></li>
                                <li><a href="{{ route('booking.check-status') }}">Check Booking</a></li>
                                <li><a href="#contact">Contact Us</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-12">
                        <div class="item">
                            <h6>Contact Info</h6>
                            <div class="info">
                                <div><i class="ti-location-pin"></i> Tambora National Park, West Nusa Tenggara</div>
                                <div><i class="ti-mobile"></i> +62 812-3456-7890</div>
                                <div><i class="ti-email"></i> info@tamboratracker.com</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <p>&copy; {{ date('Y') }} Tambora Tracker. All Rights Reserved.</p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    @yield('modals')

    <!-- jQuery -->
    <script src="{{ asset('demo/html/bycamp/light/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('demo/html/bycamp/light/js/jquery-migrate-3.5.0.min.js') }}"></script>
    <script src="{{ asset('demo/html/bycamp/light/js/modernizr-2.6.2.min.js') }}"></script>
    <script src="{{ asset('demo/html/bycamp/light/js/imagesloaded.pkgd.min.js') }}"></script>
    <script src="{{ asset('demo/html/bycamp/light/js/jquery.isotope.v3.0.2.js') }}"></script>
    <script src="{{ asset('demo/html/bycamp/light/js/popper.min.js') }}"></script>
    <script src="{{ asset('demo/html/bycamp/light/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('demo/html/bycamp/light/js/scrollIt.min.js') }}"></script>
    <script src="{{ asset('demo/html/bycamp/light/js/jquery.waypoints.min.js') }}"></script>
    <script src="{{ asset('demo/html/bycamp/light/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('demo/html/bycamp/light/js/jquery.stellar.min.js') }}"></script>
    <script src="{{ asset('demo/html/bycamp/light/js/jquery.magnific-popup.js') }}"></script>
    <script src="{{ asset('demo/html/bycamp/light/js/YouTubePopUp.js') }}"></script>
    <script src="{{ asset('demo/html/bycamp/light/js/select2.js') }}"></script>
    <script src="{{ asset('demo/html/bycamp/light/js/datepicker.js') }}"></script>
    <script src="{{ asset('demo/html/bycamp/light/js/smooth-scroll.min.js') }}"></script>
    <script src="{{ asset('demo/html/bycamp/light/js/custom.js') }}"></script>
    
    <script>
        // Setup CSRF token for AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    
    @yield('scripts')
</body>
</html>