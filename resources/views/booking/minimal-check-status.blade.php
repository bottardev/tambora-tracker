<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Check Booking Status - Tambora Tracker</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { background: #f8f9fa; }
        .main-container { min-height: 100vh; padding: 60px 0; }
        .check-card { 
            background: white; 
            border-radius: 15px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.1); 
            padding: 40px; 
        }
        .status-badge {
            padding: 8px 16px;
            border-radius: 25px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85em;
        }
        .status-pending-payment {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        .status-awaiting-validation {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        .status-confirmed {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .status-expired, .status-cancelled {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f1b0b7;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('booking.homepage') }}">
                <i class="fas fa-mountain text-primary me-2"></i>Tambora Tracker
            </a>
            <div class="d-flex">
                <a href="{{ route('booking.homepage') }}" class="btn btn-outline-primary">
                    <i class="fas fa-home me-2"></i>Back to Homepage
                </a>
            </div>
        </div>
    </nav>

    <div class="main-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="check-card text-center">
                        <div class="mb-4">
                            <i class="fas fa-search text-primary" style="font-size: 3rem;"></i>
                            <h2 class="mt-3 mb-3">Check Your Booking</h2>
                            <p class="text-muted">Enter your booking code to view details, payment status, and make payments.</p>
                        </div>
                        
                        <!-- Booking Code Form -->
                        <div id="bookingCodeForm">
                            <form id="checkBookingForm">
                                @csrf
                                <div class="row justify-content-center">
                                    <div class="col-md-6">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text"><i class="fas fa-ticket-alt"></i></span>
                                            <input type="text" 
                                                   name="booking_code" 
                                                   class="form-control form-control-lg text-center" 
                                                   placeholder="Enter Booking Code" 
                                                   style="text-transform: uppercase;"
                                                   required>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-search me-2"></i>Check Status
                                </button>
                            </form>
                        </div>
                        
                        <!-- Loading State -->
                        <div id="loadingState" style="display: none;">
                            <i class="fas fa-spinner fa-spin text-primary" style="font-size: 2rem;"></i>
                            <p class="mt-3">Checking your booking...</p>
                        </div>
                        
                        <!-- Error Message -->
                        <div id="errorMessage" class="alert alert-danger" style="display: none;">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <span id="errorText">Booking not found. Please check your booking code.</span>
                        </div>
                    </div>
                    
                    <!-- Booking Details -->
                    <div id="bookingDetails" class="check-card mt-4" style="display: none;">
                        <div class="text-center mb-4">
                            <h3><i class="fas fa-mountain me-2"></i>Booking Details</h3>
                        </div>
                        
                        <div class="row">
                            <!-- Booking Info -->
                            <div class="col-lg-6 mb-4">
                                <h5><i class="fas fa-ticket-alt me-2"></i>Booking Information</h5>
                                <div class="table-responsive">
                                    <table class="table table-borderless">
                                        <tr><td><strong>Booking Code:</strong></td><td id="bookingCode"></td></tr>
                                        <tr><td><strong>Status:</strong></td><td><span id="bookingStatus" class="status-badge"></span></td></tr>
                                        <tr><td><strong>Route:</strong></td><td id="routeName"></td></tr>
                                        <tr><td><strong>Trip Date:</strong></td><td id="tripDate"></td></tr>
                                        <tr><td><strong>Participants:</strong></td><td id="participantCount"></td></tr>
                                        <tr><td><strong>Total Amount:</strong></td><td id="totalAmount" class="text-primary fw-bold"></td></tr>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Contact Info -->
                            <div class="col-lg-6 mb-4">
                                <h5><i class="fas fa-user me-2"></i>Contact Information</h5>
                                <div class="table-responsive">
                                    <table class="table table-borderless">
                                        <tr><td><strong>Name:</strong></td><td id="hikerName"></td></tr>
                                        <tr><td><strong>Email:</strong></td><td id="hikerEmail"></td></tr>
                                        <tr><td><strong>Phone:</strong></td><td id="hikerPhone"></td></tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Payment Information -->
                        <div id="paymentInfo" class="mt-4" style="display: none;">
                            <h5><i class="fas fa-credit-card me-2"></i>Payment Information</h5>
                            <div id="paymentDue" class="alert alert-warning">
                                <strong>Payment Due:</strong> <span id="paymentDueDate"></span>
                            </div>
                            <div id="paymentConfirmed" class="alert alert-success" style="display: none;">
                                <strong>Payment Confirmed:</strong> <span id="paidDate"></span>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="text-center mt-4">
                            <button type="button" id="makePaymentBtn" class="btn btn-success btn-lg me-2" style="display: none;">
                                <i class="fas fa-credit-card me-2"></i>Make Payment
                            </button>
                            <button type="button" id="checkAnotherBtn" class="btn btn-outline-primary btn-lg me-2">
                                <i class="fas fa-search me-2"></i>Check Another Booking
                            </button>
                            <a href="{{ route('booking.homepage') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-home me-2"></i>Back to Homepage
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Check booking form submission
        document.getElementById('checkBookingForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const bookingCode = document.querySelector('input[name="booking_code"]').value.trim();
            if (!bookingCode) {
                showError('Please enter a booking code.');
                return;
            }
            
            checkBookingStatus(bookingCode);
        });
        
        // Check another booking button
        document.getElementById('checkAnotherBtn').addEventListener('click', function() {
            resetForm();
        });
        
        // Make payment button
        document.getElementById('makePaymentBtn').addEventListener('click', function() {
            const bookingCode = document.getElementById('bookingCode').textContent;
            window.location.href = `/booking/${bookingCode}/payment`;
        });
        
        function checkBookingStatus(bookingCode) {
            // Show loading state
            document.getElementById('bookingCodeForm').style.display = 'none';
            document.getElementById('errorMessage').style.display = 'none';
            document.getElementById('bookingDetails').style.display = 'none';
            document.getElementById('loadingState').style.display = 'block';
            
            fetch('/api/public/bookings/status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    booking_code: bookingCode
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayBookingDetails(data.booking);
                } else {
                    showError(data.message || 'Booking not found.');
                }
            })
            .catch(error => {
                showError('Error checking booking status. Please try again.');
            })
            .finally(() => {
                document.getElementById('loadingState').style.display = 'none';
            });
        }
        
        function displayBookingDetails(booking) {
            // Basic booking information
            document.getElementById('bookingCode').textContent = booking.code;
            document.getElementById('routeName').textContent = booking.route_name;
            document.getElementById('tripDate').textContent = new Date(booking.trip_date).toLocaleDateString();
            document.getElementById('participantCount').textContent = booking.participants_count + ' people';
            document.getElementById('totalAmount').textContent = `${booking.currency} ${new Intl.NumberFormat().format(booking.amount)}`;
            
            // Contact information
            document.getElementById('hikerName').textContent = booking.hiker.name;
            document.getElementById('hikerEmail').textContent = booking.hiker.email;
            document.getElementById('hikerPhone').textContent = booking.hiker.phone;
            
            // Status with appropriate styling
            const statusBadge = document.getElementById('bookingStatus');
            statusBadge.textContent = booking.status.replace(/-/g, ' ');
            statusBadge.className = 'status-badge status-' + booking.status;
            
            // Payment information
            if (booking.status === 'pending-payment' && booking.payment_due_at) {
                document.getElementById('paymentDueDate').textContent = new Date(booking.payment_due_at).toLocaleString();
                document.getElementById('paymentInfo').style.display = 'block';
                document.getElementById('paymentDue').style.display = 'block';
                document.getElementById('paymentConfirmed').style.display = 'none';
                document.getElementById('makePaymentBtn').style.display = 'inline-block';
            } else if (booking.paid_at) {
                document.getElementById('paidDate').textContent = new Date(booking.paid_at).toLocaleString();
                document.getElementById('paymentInfo').style.display = 'block';
                document.getElementById('paymentDue').style.display = 'none';
                document.getElementById('paymentConfirmed').style.display = 'block';
                document.getElementById('makePaymentBtn').style.display = 'none';
            } else {
                document.getElementById('paymentInfo').style.display = 'none';
                document.getElementById('makePaymentBtn').style.display = 'none';
            }
            
            // Show booking details
            document.getElementById('bookingDetails').style.display = 'block';
        }
        
        function showError(message) {
            document.getElementById('errorText').textContent = message;
            document.getElementById('errorMessage').style.display = 'block';
            document.getElementById('bookingCodeForm').style.display = 'block';
            document.getElementById('bookingDetails').style.display = 'none';
        }
        
        function resetForm() {
            document.getElementById('bookingCodeForm').style.display = 'block';
            document.getElementById('errorMessage').style.display = 'none';
            document.getElementById('bookingDetails').style.display = 'none';
            const input = document.querySelector('input[name="booking_code"]');
            input.value = '';
            input.focus();
        }
        
        // Auto-uppercase booking code input
        document.querySelector('input[name="booking_code"]').addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    </script>
</body>
</html>