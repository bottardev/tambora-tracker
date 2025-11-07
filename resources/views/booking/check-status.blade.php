@extends('layouts.app')

@section('title', 'Check Booking Status - Tambora Tracker')

@section('styles')
<style>
.booking-check-section {
    min-height: 70vh;
    display: flex;
    align-items: center;
}

.booking-check-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    padding: 40px;
    margin: 40px 0;
}

.booking-details-card {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 30px;
    margin-top: 30px;
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

.participant-item {
    background: white;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 10px;
    border-left: 4px solid #007bff;
}

.booking-action-buttons {
    margin-top: 30px;
    text-align: center;
}

.booking-action-buttons .btn {
    margin: 5px 10px;
    min-width: 150px;
}
</style>
@endsection

@section('content')
<section class="booking-check-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="booking-check-card text-center">
                    <div class="section-subtitle">
                        <div class="iconix"><i class="flaticon-023-hiking-1"></i></div> Booking Status
                    </div>
                    <div class="section-title mb-4">Check Your Booking</div>
                    <p class="mb-4">Enter your booking code to view details, payment status, and make payments.</p>
                    
                    <!-- Booking Code Form -->
                    <div id="bookingCodeForm">
                        <form id="checkBookingForm" class="form1">
                            @csrf
                            <div class="row justify-content-center">
                                <div class="col-lg-6 col-md-8">
                                    <div class="input-group mb-3">
                                        <input type="text" 
                                               name="booking_code" 
                                               class="form-control text-center" 
                                               placeholder="Enter Booking Code (e.g., BOOK-ABC12)" 
                                               style="font-size: 1.1em; padding: 15px;"
                                               required>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="button-1 mb-30">
                                <i class="fa-solid fa-search mr-2"></i> Check Status
                            </button>
                        </form>
                    </div>
                    
                    <!-- Loading State -->
                    <div id="loadingState" style="display: none;">
                        <i class="fa-solid fa-spinner fa-spin" style="font-size: 2rem;"></i>
                        <p class="mt-3">Checking your booking...</p>
                    </div>
                    
                    <!-- Error Message -->
                    <div id="errorMessage" class="alert alert-danger" style="display: none;">
                        <i class="fa-solid fa-exclamation-triangle mr-2"></i>
                        <span id="errorText">Booking not found. Please check your booking code.</span>
                    </div>
                </div>
                
                <!-- Booking Details -->
                <div id="bookingDetails" class="booking-details-card" style="display: none;">
                    <div class="row">
                        <div class="col-12 text-center mb-4">
                            <h3><i class="fa-solid fa-mountain mr-2"></i> Booking Details</h3>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Booking Info -->
                        <div class="col-lg-6 col-md-12 mb-4">
                            <div class="booking-info">
                                <h5><i class="fa-solid fa-ticket mr-2"></i> Booking Information</h5>
                                <div class="info-item">
                                    <strong>Booking Code:</strong> <span id="bookingCode"></span>
                                </div>
                                <div class="info-item">
                                    <strong>Status:</strong> <span id="bookingStatus" class="status-badge"></span>
                                </div>
                                <div class="info-item">
                                    <strong>Route:</strong> <span id="routeName"></span>
                                </div>
                                <div class="info-item">
                                    <strong>Trip Date:</strong> <span id="tripDate"></span>
                                </div>
                                <div class="info-item">
                                    <strong>Participants:</strong> <span id="participantCount"></span> people
                                </div>
                                <div class="info-item">
                                    <strong>Total Amount:</strong> <span id="totalAmount"></span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Contact Info -->
                        <div class="col-lg-6 col-md-12 mb-4">
                            <div class="contact-info">
                                <h5><i class="fa-solid fa-user mr-2"></i> Contact Information</h5>
                                <div class="info-item">
                                    <strong>Name:</strong> <span id="hikerName"></span>
                                </div>
                                <div class="info-item">
                                    <strong>Email:</strong> <span id="hikerEmail"></span>
                                </div>
                                <div class="info-item">
                                    <strong>Phone:</strong> <span id="hikerPhone"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Payment Information -->
                    <div id="paymentInfo" class="row mt-4" style="display: none;">
                        <div class="col-12">
                            <h5><i class="fa-solid fa-credit-card mr-2"></i> Payment Information</h5>
                            <div id="paymentDue" class="alert alert-warning">
                                <strong>Payment Due:</strong> <span id="paymentDueDate"></span>
                            </div>
                            <div id="paymentConfirmed" class="alert alert-success" style="display: none;">
                                <strong>Payment Confirmed:</strong> <span id="paidDate"></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Participants List -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5><i class="fa-solid fa-users mr-2"></i> Participants</h5>
                            <div id="participantsList"></div>
                        </div>
                    </div>
                    
                    <!-- Additional Notes -->
                    <div id="notesSection" class="row mt-4" style="display: none;">
                        <div class="col-12">
                            <h5><i class="fa-solid fa-sticky-note mr-2"></i> Additional Notes</h5>
                            <div class="alert alert-info">
                                <span id="bookingNotes"></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="booking-action-buttons">
                        <button type="button" id="makePaymentBtn" class="btn btn-success" style="display: none;">
                            <i class="fa-solid fa-credit-card mr-2"></i> Make Payment
                        </button>
                        <button type="button" id="checkAnotherBtn" class="btn btn-outline-primary">
                            <i class="fa-solid fa-search mr-2"></i> Check Another Booking
                        </button>
                        <a href="{{ route('booking.homepage') }}" class="btn btn-outline-secondary">
                            <i class="fa-solid fa-home mr-2"></i> Back to Homepage
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Check booking form submission
    $('#checkBookingForm').on('submit', function(e) {
        e.preventDefault();
        
        const bookingCode = $('input[name="booking_code"]').val().trim();
        if (!bookingCode) {
            showError('Please enter a booking code.');
            return;
        }
        
        checkBookingStatus(bookingCode);
    });
    
    // Check another booking button
    $('#checkAnotherBtn').on('click', function() {
        resetForm();
    });
    
    // Make payment button
    $('#makePaymentBtn').on('click', function() {
        const bookingCode = $('#bookingCode').text();
        window.location.href = `/booking/${bookingCode}/payment`;
    });
    
    function checkBookingStatus(bookingCode) {
        // Show loading state
        $('#bookingCodeForm').hide();
        $('#errorMessage').hide();
        $('#bookingDetails').hide();
        $('#loadingState').show();
        
        $.ajax({
            url: '{{ route("api.booking.status") }}',
            method: 'POST',
            data: {
                booking_code: bookingCode
            },
            success: function(response) {
                if (response.success) {
                    displayBookingDetails(response.booking);
                } else {
                    showError(response.message || 'Booking not found.');
                }
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Error checking booking status. Please try again.';
                showError(message);
            },
            complete: function() {
                $('#loadingState').hide();
            }
        });
    }
    
    function displayBookingDetails(booking) {
        // Basic booking information
        $('#bookingCode').text(booking.code);
        $('#routeName').text(booking.route_name);
        $('#tripDate').text(new Date(booking.trip_date).toLocaleDateString());
        $('#participantCount').text(booking.participants_count);
        $('#totalAmount').text(`${booking.currency} ${new Intl.NumberFormat().format(booking.amount)}`);
        
        // Contact information
        $('#hikerName').text(booking.hiker.name);
        $('#hikerEmail').text(booking.hiker.email);
        $('#hikerPhone').text(booking.hiker.phone);
        
        // Status with appropriate styling
        const statusBadge = $('#bookingStatus');
        statusBadge.text(booking.status.replace(/-/g, ' '));
        statusBadge.removeClass().addClass('status-badge status-' + booking.status);
        
        // Payment information
        if (booking.status === 'pending-payment' && booking.payment_due_at) {
            $('#paymentDueDate').text(new Date(booking.payment_due_at).toLocaleString());
            $('#paymentInfo').show();
            $('#paymentDue').show();
            $('#paymentConfirmed').hide();
            $('#makePaymentBtn').show();
        } else if (booking.paid_at) {
            $('#paidDate').text(new Date(booking.paid_at).toLocaleString());
            $('#paymentInfo').show();
            $('#paymentDue').hide();
            $('#paymentConfirmed').show();
            $('#makePaymentBtn').hide();
        } else {
            $('#paymentInfo').hide();
            $('#makePaymentBtn').hide();
        }
        
        // Participants list
        const participantsList = $('#participantsList');
        participantsList.empty();
        
        booking.participants.forEach((participant, index) => {
            const participantHtml = `
                <div class="participant-item">
                    <div class="row">
                        <div class="col-md-4">
                            <strong>${participant.name}</strong>
                            ${index === 0 ? '<span class="badge bg-primary ms-2">Lead Hiker</span>' : ''}
                        </div>
                        <div class="col-md-4">
                            ${participant.phone ? `<i class="fa-solid fa-phone mr-1"></i> ${participant.phone}` : '<span class="text-muted">No phone</span>'}
                        </div>
                        <div class="col-md-4">
                            ${participant.emergency_contact ? `<i class="fa-solid fa-user-shield mr-1"></i> ${participant.emergency_contact}` : '<span class="text-muted">No emergency contact</span>'}
                        </div>
                    </div>
                </div>
            `;
            participantsList.append(participantHtml);
        });
        
        // Additional notes
        if (booking.notes) {
            $('#bookingNotes').text(booking.notes);
            $('#notesSection').show();
        } else {
            $('#notesSection').hide();
        }
        
        // Show booking details
        $('#bookingDetails').show();
    }
    
    function showError(message) {
        $('#errorText').text(message);
        $('#errorMessage').show();
        $('#bookingCodeForm').show();
        $('#bookingDetails').hide();
    }
    
    function resetForm() {
        $('#bookingCodeForm').show();
        $('#errorMessage').hide();
        $('#bookingDetails').hide();
        $('input[name="booking_code"]').val('').focus();
    }
    
    // Auto-uppercase booking code input
    $('input[name="booking_code"]').on('input', function() {
        this.value = this.value.toUpperCase();
    });
});
</script>
@endsection