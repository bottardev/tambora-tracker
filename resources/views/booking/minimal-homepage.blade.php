<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tambora Tracker - Mount Tambora Hiking & Adventure</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { font-family: 'Arial', sans-serif; }
        .hero-section { 
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('https://images.unsplash.com/photo-1506905925346-21bda4d32df4?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');
            background-size: cover;
            background-position: center;
            height: 100vh;
            color: white;
        }
        .route-card { 
            border: none; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.1); 
            transition: transform 0.3s ease;
        }
        .route-card:hover { transform: translateY(-5px); }
        .btn-primary { background: #007bff; border: none; }
        .navbar { background: rgba(255,255,255,0.95) !important; }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('booking.homepage') }}">
                <i class="fas fa-mountain text-primary me-2"></i>Tambora Tracker
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#routes">Routes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('booking.check-status') }}">Check Booking</a>
                    </li>
                </ul>
                <button class="btn btn-primary ms-3" data-bs-toggle="modal" data-bs-target="#bookingModal">
                    <i class="fas fa-calendar me-2"></i>Book Now
                </button>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section d-flex align-items-center">
        <div class="container text-center">
            <h1 class="display-4 fw-bold mb-4">Conquer Mount Tambora</h1>
            <p class="lead mb-5">Experience the Ultimate Hiking Adventure at Indonesia's Legendary Volcano!</p>
            <button class="btn btn-primary btn-lg me-3" data-bs-toggle="modal" data-bs-target="#bookingModal">
                <i class="fas fa-calendar me-2"></i>Book Your Adventure
            </button>
            <a href="#routes" class="btn btn-outline-light btn-lg">
                <i class="fas fa-arrow-down me-2"></i>Explore Routes
            </a>
        </div>
    </section>

    <!-- About Section -->
    <section class="py-5" id="about">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <h2 class="mb-4">About Tambora Tracker</h2>
                    <p class="lead">Experience the legendary Mount Tambora with professional guides, safety equipment, and real-time GPS tracking.</p>
                    <p>We provide comprehensive hiking packages for adventurers seeking to explore Indonesia's most historic volcano.</p>
                    
                    <div class="row mt-4">
                        <div class="col-md-4 text-center mb-3">
                            <i class="fas fa-user-tie fa-3x text-primary mb-3"></i>
                            <h5>Expert Guides</h5>
                        </div>
                        <div class="col-md-4 text-center mb-3">
                            <i class="fas fa-shield-alt fa-3x text-primary mb-3"></i>
                            <h5>Safety First</h5>
                        </div>
                        <div class="col-md-4 text-center mb-3">
                            <i class="fas fa-satellite fa-3x text-primary mb-3"></i>
                            <h5>GPS Tracking</h5>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <img src="https://images.unsplash.com/photo-1551632811-561732d1e306?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" class="img-fluid rounded" alt="Mount Tambora">
                </div>
            </div>
        </div>
    </section>

    <!-- Routes Section -->
    <section class="py-5 bg-light" id="routes">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="mb-4">Available Hiking Routes</h2>
                <p class="lead">Choose your perfect Tambora adventure</p>
            </div>
            
            <div class="row">
                @forelse($routes as $route)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card route-card h-100">
                        <img src="https://images.unsplash.com/photo-1464822759844-d150baec93c5?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&h=300&q=80" class="card-img-top" alt="{{ $route->name }}">
                        <div class="card-body">
                            <h5 class="card-title">{{ $route->name }}</h5>
                            <p class="card-text">{{ Str::limit($route->description ?? 'Experience the adventure of Mount Tambora with professional guidance and safety equipment.', 100) }}</p>
                            @if($route->total_distance_km)
                            <p class="text-muted"><i class="fas fa-route me-2"></i>{{ $route->total_distance_km }} km</p>
                            @endif
                        </div>
                        <div class="card-footer bg-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="h5 text-primary">{{ $currencySymbol }} {{ number_format($pricePerParticipant, 0, ',', '.') }}/person</span>
                                    <small class="text-muted">/person</small>
                                </div>
                                <button class="btn btn-primary route-select" data-route-id="{{ $route->id }}" data-route-name="{{ $route->name }}">
                                    Select Route
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center">
                    <div class="alert alert-info">
                        <h5>No routes available at the moment</h5>
                        <p>We're working on adding exciting hiking routes. Please check back soon!</p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2>Contact Information</h2>
            </div>
            <div class="row">
                <div class="col-md-4 text-center mb-4">
                    <i class="fas fa-map-marker-alt fa-3x text-primary mb-3"></i>
                    <h5>Location</h5>
                    <p>Tambora National Park<br>West Nusa Tenggara, Indonesia</p>
                </div>
                <div class="col-md-4 text-center mb-4">
                    <i class="fas fa-phone fa-3x text-primary mb-3"></i>
                    <h5>Phone</h5>
                    <p>+62 812-3456-7890<br>Available 24/7</p>
                </div>
                <div class="col-md-4 text-center mb-4">
                    <i class="fas fa-envelope fa-3x text-primary mb-3"></i>
                    <h5>Email</h5>
                    <p>info@tamboratracker.com<br>bookings@tamboratracker.com</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Booking Modal -->
    <div class="modal fade" id="bookingModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Book Your Tambora Adventure</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="bookingForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Full Name *</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email Address *</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">WhatsApp Number *</label>
                                <input type="text" class="form-control" name="phone" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Select Route *</label>
                                <select class="form-control" name="route_id" required>
                                    <option value="">Choose a route</option>
                                    @foreach($routes as $route)
                                    <option value="{{ $route->id }}">{{ $route->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Trip Date *</label>
                                <input type="date" class="form-control" name="trip_date" min="{{ date('Y-m-d', strtotime('+3 days')) }}" max="{{ date('Y-m-d', strtotime('+30 days')) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Number of Participants *</label>
                                <select class="form-control" name="participants_count" required>
                                    <option value="">Select participants</option>
                                    @for($i = 1; $i <= 10; $i++)
                                    <option value="{{ $i }}">{{ $i }} {{ $i == 1 ? 'Person' : 'People' }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        
                        <div id="participantsContainer"></div>
                        
                        <div class="mb-3">
                            <label class="form-label">Additional Notes</label>
                            <textarea class="form-control" name="notes" rows="3" placeholder="Special requests, dietary requirements, medical conditions, etc."></textarea>
                        </div>
                        
                        <div id="availabilityResult"></div>
                        
                        <div class="text-end">
                            <button type="button" id="checkAvailabilityBtn" class="btn btn-outline-primary me-2">
                                <i class="fas fa-search me-2"></i>Check Availability
                            </button>
                            <button type="submit" id="bookNowBtn" class="btn btn-success" style="display: none;">
                                <i class="fas fa-calendar-check me-2"></i>Book Now
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Setup CSRF token for AJAX requests
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Route selection from cards
        document.querySelectorAll('.route-select').forEach(button => {
            button.addEventListener('click', function() {
                const routeId = this.getAttribute('data-route-id');
                const routeName = this.getAttribute('data-route-name');
                
                // Set the route in the modal
                document.querySelector('select[name="route_id"]').value = routeId;
                
                // Open the booking modal
                new bootstrap.Modal(document.getElementById('bookingModal')).show();
            });
        });
        
        // Participants count change handler
        document.querySelector('select[name="participants_count"]').addEventListener('change', function() {
            const count = parseInt(this.value) || 0;
            generateParticipantFields(count);
        });
        
        function generateParticipantFields(count) {
            const container = document.getElementById('participantsContainer');
            container.innerHTML = '';
            
            if (count === 0) return;
            
            container.innerHTML = '<h6 class="mt-4 mb-3">Participants Information</h6>';
            
            for (let i = 0; i < count; i++) {
                const participantHtml = `
                    <div class="border p-3 mb-3 rounded">
                        <h6 class="mb-3">Participant ${i + 1} ${i === 0 ? '(Lead Hiker)' : ''}</h6>
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <input type="text" class="form-control" name="participants[${i}][name]" placeholder="Full Name *" required>
                            </div>
                            <div class="col-md-4 mb-2">
                                <input type="text" class="form-control" name="participants[${i}][phone]" placeholder="Phone Number">
                            </div>
                            <div class="col-md-4 mb-2">
                                <input type="text" class="form-control" name="participants[${i}][emergency_contact]" placeholder="Emergency Contact">
                            </div>
                        </div>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', participantHtml);
            }
        }
        
        // Check availability
        document.getElementById('checkAvailabilityBtn').addEventListener('click', function() {
            const routeId = document.querySelector('select[name="route_id"]').value;
            const tripDate = document.querySelector('input[name="trip_date"]').value;
            
            if (!routeId || !tripDate) {
                showAlert('Please select a route and trip date first.', 'warning');
                return;
            }
            
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Checking...';
            
            fetch('/api/public/bookings/check-availability', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    route_id: routeId,
                    trip_date: tripDate
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.available) {
                    showAlert(`Great! ${data.remaining} spots available for this date.`, 'success');
                    document.getElementById('bookNowBtn').style.display = 'inline-block';
                } else {
                    showAlert(`Sorry, this date is not available. ${data.message || ''}`, 'danger');
                    document.getElementById('bookNowBtn').style.display = 'none';
                }
            })
            .catch(error => {
                showAlert('Error checking availability.', 'danger');
                document.getElementById('bookNowBtn').style.display = 'none';
            })
            .finally(() => {
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-search me-2"></i>Check Availability';
            });
        });
        
        // Submit booking form
        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('bookNowBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating Booking...';
            
            const formData = new FormData(this);
            
            fetch('/api/public/bookings', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message with booking code
                    const modalBody = document.querySelector('#bookingModal .modal-body');
                    modalBody.innerHTML = `
                        <div class="text-center">
                            <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                            <h4 class="mt-3 mb-3">Booking Created Successfully!</h4>
                            <div class="alert alert-info">
                                <h5><strong>Your Booking Code: ${data.booking_code}</strong></h5>
                                <p>Please save this code. You'll need it to check your booking status and make payment.</p>
                                <p><strong>Payment Due:</strong> ${new Date(data.payment_due_at).toLocaleString()}</p>
                                <p><strong>Amount:</strong> ${data.currency} ${new Intl.NumberFormat().format(data.amount)}</p>
                            </div>
                            <div class="mt-4">
                                <a href="/check-booking" class="btn btn-primary me-2">
                                    <i class="fas fa-search me-2"></i>Check Booking Status
                                </a>
                                <a href="/booking/${data.booking_code}/payment" class="btn btn-success">
                                    <i class="fas fa-credit-card me-2"></i>Make Payment
                                </a>
                            </div>
                        </div>
                    `;
                } else {
                    showAlert(data.message || 'Failed to create booking.', 'danger');
                }
            })
            .catch(error => {
                showAlert('Failed to create booking. Please try again.', 'danger');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-calendar-check me-2"></i>Book Now';
            });
        });
        
        function showAlert(message, type) {
            const alertHtml = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>`;
            
            const container = document.getElementById('availabilityResult');
            container.innerHTML = alertHtml;
            
            // Auto-hide after 5 seconds
            setTimeout(() => {
                const alert = container.querySelector('.alert');
                if (alert) {
                    new bootstrap.Alert(alert).close();
                }
            }, 5000);
        }
    </script>
</body>
</html>