@extends('layouts.app')

@section('title', 'Tambora Tracker - Mount Tambora Hiking & Adventure')

@section('content')
<!-- Slider -->
<header class="header slider-fade">
    <div class="owl-carousel owl-theme">
        <!-- Slide 1 -->
        <div class="item bg-img" data-overlay-dark="3" data-background="{{ asset('demo/html/bycamp/light/img/slider/01.jpg') }}">
            <div class="v-middle caption">
                <div class="container">
                    <div class="row justify-content-start">
                        <div class="col-lg-8 col-md-12 text-left">
                            <h1>Conquer Mount <span>Tambora</span></h1>
                            <p>Experience the Ultimate Hiking Adventure at Indonesia's Legendary Volcano!</p> 
                            <a href="#routes" class="button-1 mb-30 mt-20">Explore Routes <span class="ti-arrow-top-right"></span></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Slide 2 -->
        <div class="item bg-img" data-overlay-dark="3" data-background="{{ asset('demo/html/bycamp/light/img/slider/02.jpg') }}">
            <div class="v-middle caption">
                <div class="container">
                    <div class="row justify-content-start">
                        <div class="col-lg-7 col-md-12 text-left">
                            <h1>Adventure <span>Awaits</span></h1>
                            <p>Professional Guides, Safety Equipment, and Unforgettable Memories!</p> 
                            <a data-bs-toggle="modal" data-bs-target="#bookingModal" href="#0" class="button-1 mb-30 mt-20">Book Now <span class="ti-arrow-top-right"></span></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Slide 3 -->
        <div class="item bg-img" data-overlay-dark="3" data-background="{{ asset('demo/html/bycamp/light/img/slider/03.jpg') }}">
            <div class="v-middle caption">
                <div class="container">
                    <div class="row justify-content-start">
                        <div class="col-lg-7 col-md-12 text-left">
                            <h1>Track Your <span>Journey</span></h1>
                            <p>Real-time GPS tracking and safety monitoring for your peace of mind!</p> 
                            <a href="#about" class="button-1 mb-30 mt-20">Learn More <span class="ti-arrow-top-right"></span></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- WhatsApp Contact -->
    <div class="whatsapp">
        <a href="https://wa.me/6281234567890">
            <div class="icon d-flex justify-content-center align-items-center"> <i class="fa-brands fa-whatsapp"></i> </div>
            <div class="call"><span>+62 812-3456-7890</span>
                <br>WhatsApp Us
            </div>
        </a>
    </div>
</header>

<!-- About -->
<section class="about section-padding" id="about">
    <div class="container">
        <div class="row">
            <div class="col-lg-5 col-md-12 mb-30">
                <div class="section-subtitle">
                    <div class="iconix"><i class="flaticon-023-hiking-1"></i></div> Adventure & Tracking
                </div>
                <div class="section-title">About Tambora Tracker</div>
                <p class="mb-60">Experience the legendary Mount Tambora with professional guides, safety equipment, and real-time GPS tracking. We provide comprehensive hiking packages for adventurers seeking to explore Indonesia's most historic volcano.</p>
                <div class="row">
                    <div class="col col-md-4">
                        <div class="icon"> <span class="flaticon-023-hiking-1"></span>
                            <h5>Professional Guides</h5>
                        </div>
                    </div>
                    <div class="col col-md-4">
                        <div class="icon"> <span class="flaticon-007-tent-1"></span>
                            <h5>Equipment Rental</h5>
                        </div>
                    </div>
                    <div class="col col-md-4">
                        <div class="icon"> <span class="flaticon-005-firewood"></span>
                            <h5>GPS Tracking</h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 offset-lg-1 col-md-12">
                <div class="item"> 
                    <img src="{{ asset('demo/html/bycamp/light/img/about.jpg') }}" class="img-fluid" alt="Mount Tambora">
                    <div class="float-box animation-float d-flex"> <i class="icon flaticon-023-hiking-1"></i>
                        <div class="title"><span>Let's Hike!</span> Adventure awaits</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Route Types -->
<section class="accommodation-box section-padding bg-gray">
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center">
                <div class="section-subtitle">
                    <div class="iconix"><i class="flaticon-023-hiking-1"></i></div> What We Offer
                </div>
                <div class="section-title">Hiking Packages</div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4 col-md-12">
                <div class="item"> 
                    <span class="icon flaticon-021-cottage-1"></span>
                    <div class="cont">
                        <h5><a href="#routes">Summit Trail</a></h5>
                        <p>Challenge yourself with the complete summit route. Experience breathtaking views and volcanic landscapes.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-12">
                <div class="item"> 
                    <span class="icon flaticon-007-tent-1"></span>
                    <div class="cont">
                        <h5><a href="#routes">Crater Rim Trek</a></h5>
                        <p>Explore the massive caldera rim and witness one of the largest volcanic craters in the world.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-12">
                <div class="item"> 
                    <span class="icon flaticon-048-van"></span>
                    <div class="cont">
                        <h5><a href="#routes">Base Camp Adventure</a></h5>
                        <p>Perfect for beginners. Experience the beauty of Tambora's lower slopes and forest trails.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Available Routes -->
<section class="accommodation section-padding" id="routes">
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center mb-30">
                <div class="section-subtitle">
                    <div class="iconix"><i class="flaticon-023-hiking-1"></i></div> Choose Your Adventure
                </div>
                <div class="section-title">Available Hiking Routes</div>
            </div>
        </div>
        <div class="accommodation-carousel owl-theme owl-carousel">
            @forelse($routes as $route)
            <div class="item">
                <div class="img"> 
                    <img src="{{ asset('demo/html/bycamp/light/img/accommodations/0' . (($loop->index % 5) + 1) . '.jpg') }}" alt="{{ $route->name }}"> 
                </div>
                <div class="con opacity-1">
                    <div class="row">
                        <div class="col-md-7">
                            <div class="title"><a href="#0">{{ $route->name }}</a></div>
                            <div class="details"> 
                                <i class="flaticon-023-hiking-1" title="Hiking Trail"></i> 
                                <i class="flaticon-005-firewood" title="Campfire Included"></i> 
                                <i class="flaticon-030-camping-gas" title="Camping Equipment"></i> 
                                <i class="flaticon-031-pot" title="Meal Included"></i> 
                                <i class="flaticon-010-bedding" title="Sleeping Equipment"></i> 
                            </div>
                            <p class="route-description">{{ Str::limit($route->description ?? 'Experience the adventure of Mount Tambora with professional guidance and safety equipment.', 80) }}</p>
                        </div>
                        <div class="col-md-5">
                            <div class="book">
                                <div><a href="#0" class="btn route-select" data-route-id="{{ $route->id }}" data-route-name="{{ $route->name }}"><span>Select Route</span></a></div>
                                <div><span class="price">IDR 500K</span><span>per person</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="item">
                <div class="col-md-12 text-center">
                    <p>No routes available at the moment. Please check back later.</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Features -->
<section class="section-padding">
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center mb-30">
                <div class="section-subtitle">
                    <div class="iconix"><i class="flaticon-023-hiking-1"></i></div> Why Choose Us
                </div>
                <div class="section-title">Safety & Professional Service</div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="feature-item text-center">
                    <i class="flaticon-023-hiking-1"></i>
                    <h5>Expert Guides</h5>
                    <p>Certified mountain guides with extensive knowledge of Tambora's terrain and weather patterns.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="feature-item text-center">
                    <i class="flaticon-005-firewood"></i>
                    <h5>Safety Equipment</h5>
                    <p>Professional-grade safety equipment including GPS trackers, first aid kits, and communication devices.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="feature-item text-center">
                    <i class="flaticon-007-tent-1"></i>
                    <h5>Quality Gear</h5>
                    <p>High-quality camping and hiking equipment available for rent or included in our packages.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="feature-item text-center">
                    <i class="flaticon-030-camping-gas"></i>
                    <h5>Real-time Tracking</h5>
                    <p>GPS tracking system allows families to monitor your journey in real-time for peace of mind.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact -->
<section class="section-padding bg-gray" id="contact">
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center mb-30">
                <div class="section-subtitle">
                    <div class="iconix"><i class="flaticon-023-hiking-1"></i></div> Get in Touch
                </div>
                <div class="section-title">Contact Information</div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4 col-md-6">
                <div class="contact-item text-center">
                    <i class="ti-location-pin"></i>
                    <h5>Location</h5>
                    <p>Tambora National Park<br>West Nusa Tenggara, Indonesia</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="contact-item text-center">
                    <i class="ti-mobile"></i>
                    <h5>Phone</h5>
                    <p>+62 812-3456-7890<br>Available 24/7</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="contact-item text-center">
                    <i class="ti-email"></i>
                    <h5>Email</h5>
                    <p>info@tamboratracker.com<br>bookings@tamboratracker.com</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('modals')
<!-- Booking Modal -->
<div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bookingModalLabel">Book Your Tambora Adventure</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="booking-inner clearfix">
                    <form id="bookingForm" class="form1 clearfix">
                        @csrf
                        <div class="row">
                            <!-- Personal Information -->
                            <div class="col-12 mb-3">
                                <h6 class="mb-3"><i class="fa-solid fa-user mr-2"></i> Personal Information</h6>
                            </div>
                            
                            <div class="col-lg-6 col-md-12">
                                <input name="name" type="text" placeholder="Full Name *" required>
                            </div>
                            <div class="col-lg-6 col-md-12">
                                <input name="email" type="email" placeholder="Email Address *" required>
                            </div>
                            <div class="col-lg-6 col-md-12">
                                <input name="phone" type="text" placeholder="WhatsApp Number *" required>
                            </div>
                            
                            <!-- Route Selection -->
                            <div class="col-lg-6 col-md-12">
                                <div class="select1_wrapper">
                                    <label>Select Hiking Route *</label>
                                    <div class="select1_inner">
                                        <select name="route_id" class="select2 select" style="width: 100%" required>
                                            <option value="">Choose a route</option>
                                            @foreach($routes as $route)
                                            <option value="{{ $route->id }}">{{ $route->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Trip Details -->
                            <div class="col-12 mt-4 mb-3">
                                <h6 class="mb-3"><i class="fa-solid fa-calendar mr-2"></i> Trip Details</h6>
                            </div>
                            
                            <div class="col-lg-6 col-md-12">
                                <div class="input1_wrapper">
                                    <label>Trip Date *</label>
                                    <div class="input1_inner">
                                        <input type="text" name="trip_date" class="form-control input datepicker" placeholder="Select trip date" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12">
                                <div class="input1_wrapper">
                                    <label>Number of Participants *</label>
                                    <div class="input1_inner">
                                        <select name="participants_count" class="form-control" required>
                                            <option value="">Select participants</option>
                                            @for($i = 1; $i <= 10; $i++)
                                            <option value="{{ $i }}">{{ $i }} {{ $i == 1 ? 'Person' : 'People' }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Participants Information -->
                            <div class="col-12 mt-4 mb-3">
                                <h6 class="mb-3"><i class="fa-solid fa-users mr-2"></i> Participants Information</h6>
                            </div>
                            
                            <div id="participantsContainer" class="col-12">
                                <!-- Participants will be dynamically added here -->
                            </div>
                            
                            <!-- Additional Information -->
                            <div class="col-12 mt-4 mb-3">
                                <h6 class="mb-3"><i class="fa-solid fa-info-circle mr-2"></i> Additional Information</h6>
                            </div>
                            
                            <div class="col-lg-12 col-md-12 form-group">
                                <textarea name="notes" id="notes" cols="30" rows="4" placeholder="Special requests, dietary requirements, medical conditions, etc."></textarea>
                            </div>
                            
                            <!-- Availability Check -->
                            <div class="col-12">
                                <div id="availabilityResult" class="alert" style="display: none;"></div>
                            </div>
                            
                            <!-- Submit -->
                            <div class="col-lg-7 col-md-12">
                                <p style="line-height: 1.3em;"><small><i class="fa-solid fa-circle-info"></i> We are committed to protecting your privacy. Your information will only be used for booking purposes.</small></p>
                                <p style="line-height: 1.3em;"><small><i class="fa-solid fa-money-bill"></i> <strong>Price:</strong> IDR 500,000 per person. Payment can be made via bank transfer or e-wallet.</small></p>
                            </div>
                            <div class="col-lg-5 col-md-12 text-end">
                                <button type="button" id="checkAvailabilityBtn" class="btn btn-outline mb-2"><i class="fa-light fa-search mr-5"></i> Check Availability</button>
                                <button type="submit" id="bookNowBtn" style="display: none;"><i class="fa-light fa-calendar-check mr-5"></i> Book Your Adventure</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    let selectedRouteId = '';
    
    // Route selection from carousel
    $('.route-select').on('click', function(e) {
        e.preventDefault();
        selectedRouteId = $(this).data('route-id');
        const routeName = $(this).data('route-name');
        
        // Set the route in the modal
        $('select[name="route_id"]').val(selectedRouteId).trigger('change');
        
        // Open the booking modal
        $('#bookingModal').modal('show');
    });
    
    // Participants count change handler
    $('select[name="participants_count"]').on('change', function() {
        const count = parseInt($(this).val()) || 0;
        generateParticipantFields(count);
    });
    
    function generateParticipantFields(count) {
        const container = $('#participantsContainer');
        container.empty();
        
        for (let i = 0; i < count; i++) {
            const participantHtml = `
                <div class="participant-group border p-3 mb-3 rounded">
                    <h6 class="mb-3">Participant ${i + 1} ${i === 0 ? '(Lead Hiker)' : ''}</h6>
                    <div class="row">
                        <div class="col-lg-4 col-md-6">
                            <input type="text" name="participants[${i}][name]" placeholder="Full Name *" required>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <input type="text" name="participants[${i}][phone]" placeholder="Phone Number">
                        </div>
                        <div class="col-lg-4 col-md-12">
                            <input type="text" name="participants[${i}][emergency_contact]" placeholder="Emergency Contact">
                        </div>
                    </div>
                </div>
            `;
            container.append(participantHtml);
        }
    }
    
    // Check availability
    $('#checkAvailabilityBtn').on('click', function() {
        const routeId = $('select[name="route_id"]').val();
        const tripDate = $('input[name="trip_date"]').val();
        
        if (!routeId || !tripDate) {
            showAlert('Please select a route and trip date first.', 'warning');
            return;
        }
        
        $(this).prop('disabled', true).html('<i class="fa-light fa-spinner fa-spin mr-5"></i> Checking...');
        
        $.ajax({
            url: '{{ route("api.booking.availability") }}',
            method: 'POST',
            data: {
                route_id: routeId,
                trip_date: tripDate
            },
            success: function(response) {
                if (response.available) {
                    showAlert(`Great! ${response.remaining} spots available for this date.`, 'success');
                    $('#bookNowBtn').show();
                } else {
                    showAlert(`Sorry, this date is not available. ${response.message || ''}`, 'danger');
                    $('#bookNowBtn').hide();
                }
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Error checking availability.';
                showAlert(message, 'danger');
                $('#bookNowBtn').hide();
            },
            complete: function() {
                $('#checkAvailabilityBtn').prop('disabled', false).html('<i class="fa-light fa-search mr-5"></i> Check Availability');
            }
        });
    });
    
    // Submit booking form
    $('#bookingForm').on('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = $('#bookNowBtn');
        submitBtn.prop('disabled', true).html('<i class="fa-light fa-spinner fa-spin mr-5"></i> Creating Booking...');
        
        const formData = new FormData(this);
        
        $.ajax({
            url: '{{ route("api.booking.store") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    // Show success message with booking code
                    const successHtml = `
                        <div class="text-center">
                            <i class="fa-solid fa-check-circle text-success" style="font-size: 3rem;"></i>
                            <h4 class="mt-3 mb-3">Booking Created Successfully!</h4>
                            <div class="alert alert-info">
                                <h5><strong>Your Booking Code: ${response.booking_code}</strong></h5>
                                <p>Please save this code. You'll need it to check your booking status and make payment.</p>
                                <p><strong>Payment Due:</strong> ${new Date(response.payment_due_at).toLocaleString()}</p>
                                <p><strong>Amount:</strong> ${response.currency} ${new Intl.NumberFormat().format(response.amount)}</p>
                            </div>
                            <div class="mt-4">
                                <button type="button" class="btn btn-primary me-2" onclick="window.location.href='{{ route('booking.check-status') }}'">
                                    <i class="fa-solid fa-search mr-2"></i>Check Booking Status
                                </button>
                                <button type="button" class="btn btn-success" onclick="window.location.href='/booking/${response.booking_code}/payment'">
                                    <i class="fa-solid fa-credit-card mr-2"></i>Make Payment
                                </button>
                            </div>
                        </div>
                    `;
                    
                    $('#bookingModal .modal-body').html(successHtml);
                } else {
                    showAlert(response.message || 'Failed to create booking.', 'danger');
                }
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Failed to create booking. Please try again.';
                showAlert(message, 'danger');
            },
            complete: function() {
                submitBtn.prop('disabled', false).html('<i class="fa-light fa-calendar-check mr-5"></i> Book Your Adventure');
            }
        });
    });
    
    function showAlert(message, type) {
        const alertHtml = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>`;
        
        $('#availabilityResult').html(alertHtml).show();
        
        // Auto-hide after 5 seconds
        setTimeout(function() {
            $('#availabilityResult').fadeOut();
        }, 5000);
    }
    
    // Initialize datepicker with minimum date
    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd',
        startDate: '+3d', // 3 days from today
        endDate: '+30d', // 30 days from today
        autoclose: true,
        todayHighlight: true
    });
});
</script>
@endsection