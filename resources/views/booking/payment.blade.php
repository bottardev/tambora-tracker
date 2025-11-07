@extends('layouts.app')

@section('title', 'Payment - Booking ' . $booking->code . ' - Tambora Tracker')

@section('styles')
<style>
.payment-section {
    min-height: 70vh;
    padding: 60px 0;
}

.payment-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    padding: 40px;
    margin: 20px 0;
}

.booking-summary {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 25px;
    margin-bottom: 30px;
    border-left: 5px solid #007bff;
}

.payment-methods {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 25px;
    margin-bottom: 30px;
}

.payment-method-item {
    background: white;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 15px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.payment-method-item:hover,
.payment-method-item.selected {
    border-color: #007bff;
    background: #f8f9ff;
}

.payment-method-item input[type="radio"] {
    margin-right: 10px;
}

.upload-area {
    border: 2px dashed #dee2e6;
    border-radius: 10px;
    padding: 40px;
    text-align: center;
    background: #f8f9fa;
    transition: all 0.3s ease;
    cursor: pointer;
}

.upload-area:hover {
    border-color: #007bff;
    background: #f8f9ff;
}

.upload-area.dragover {
    border-color: #007bff;
    background: #e3f2fd;
}

.file-info {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 15px;
    margin-top: 15px;
    display: none;
}

.bank-details {
    background: #e8f5e8;
    border: 1px solid #4caf50;
    border-radius: 8px;
    padding: 20px;
    margin: 20px 0;
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
</style>
@endsection

@section('content')
<section class="payment-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-md-12">
                <!-- Page Header -->
                <div class="text-center mb-5">
                    <div class="section-subtitle">
                        <div class="iconix"><i class="flaticon-023-hiking-1"></i></div> Payment
                    </div>
                    <div class="section-title">Complete Your Booking</div>
                    <p class="lead">Secure your spot for the Mount Tambora adventure!</p>
                </div>
                
                <div class="row">
                    <!-- Booking Summary -->
                    <div class="col-lg-4 col-md-12">
                        <div class="payment-card">
                            <h5><i class="fa-solid fa-ticket mr-2"></i> Booking Summary</h5>
                            <div class="booking-summary">
                                <div class="row mb-3">
                                    <div class="col-5"><strong>Booking Code:</strong></div>
                                    <div class="col-7">{{ $booking->code }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-5"><strong>Status:</strong></div>
                                    <div class="col-7">
                                        <span class="status-badge status-{{ $booking->status }}">
                                            {{ str_replace('-', ' ', $booking->status) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-5"><strong>Route:</strong></div>
                                    <div class="col-7">{{ $booking->route->name }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-5"><strong>Trip Date:</strong></div>
                                    <div class="col-7">{{ $booking->trip_date->format('d M Y') }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-5"><strong>Participants:</strong></div>
                                    <div class="col-7">{{ $booking->participants_count }} people</div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-5"><strong>Total Amount:</strong></div>
                                    <div class="col-7">
                                        <h5 class="text-primary mb-0">
                                            {{ $booking->currency }} {{ number_format($booking->amount, 0, ',', '.') }}
                                        </h5>
                                    </div>
                                </div>
                                @if($booking->payment_due_at)
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="alert alert-warning mb-0">
                                            <small><i class="fa-solid fa-clock mr-1"></i> 
                                            <strong>Payment Due:</strong><br>
                                            {{ $booking->payment_due_at->format('d M Y, H:i') }}</small>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                            
                            <!-- Contact Info -->
                            <div class="mt-4">
                                <h6><i class="fa-solid fa-user mr-2"></i> Lead Hiker</h6>
                                <div class="booking-summary">
                                    <div><strong>{{ $booking->hiker->name }}</strong></div>
                                    <div><i class="fa-solid fa-envelope mr-1"></i> {{ $booking->hiker->email }}</div>
                                    <div><i class="fa-solid fa-phone mr-1"></i> {{ $booking->hiker->phone }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Payment Form -->
                    <div class="col-lg-8 col-md-12">
                        <div class="payment-card">
                            @if($booking->status === 'pending-payment')
                            <!-- Payment Methods -->
                            <h5><i class="fa-solid fa-credit-card mr-2"></i> Choose Payment Method</h5>
                            <div class="payment-methods">
                                <div class="payment-method-item" data-method="bank_transfer">
                                    <div class="d-flex align-items-center">
                                        <input type="radio" name="payment_method" value="bank_transfer" id="bank_transfer" checked>
                                        <div>
                                            <h6 class="mb-1"><i class="fa-solid fa-university mr-2"></i> Bank Transfer</h6>
                                            <p class="mb-0 text-muted">Transfer to our bank account (Most common method)</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="payment-method-item" data-method="e_wallet">
                                    <div class="d-flex align-items-center">
                                        <input type="radio" name="payment_method" value="e_wallet" id="e_wallet">
                                        <div>
                                            <h6 class="mb-1"><i class="fa-solid fa-mobile-alt mr-2"></i> E-Wallet</h6>
                                            <p class="mb-0 text-muted">GoPay, OVO, DANA, ShopeePay</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="payment-method-item" data-method="cash">
                                    <div class="d-flex align-items-center">
                                        <input type="radio" name="payment_method" value="cash" id="cash">
                                        <div>
                                            <h6 class="mb-1"><i class="fa-solid fa-money-bill mr-2"></i> Cash</h6>
                                            <p class="mb-0 text-muted">Pay at our office or during pickup</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Bank Details (shown for bank transfer) -->
                            <div id="bankDetails" class="bank-details">
                                <h6><i class="fa-solid fa-university mr-2"></i> Bank Transfer Details</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Bank:</strong> Bank Mandiri<br>
                                        <strong>Account Number:</strong> 1234-567890-123<br>
                                        <strong>Account Name:</strong> PT Tambora Adventure
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Amount:</strong> {{ $booking->currency }} {{ number_format($booking->amount, 0, ',', '.') }}<br>
                                        <strong>Reference:</strong> {{ $booking->code }}
                                    </div>
                                </div>
                                <div class="alert alert-info mt-3 mb-0">
                                    <small><i class="fa-solid fa-info-circle mr-1"></i> 
                                    Please include your booking code ({{ $booking->code }}) in the transfer description.</small>
                                </div>
                            </div>
                            
                            <!-- E-Wallet Details (hidden by default) -->
                            <div id="ewalletDetails" class="bank-details" style="display: none;">
                                <h6><i class="fa-solid fa-mobile-alt mr-2"></i> E-Wallet Payment Details</h6>
                                <p>Please transfer to one of these e-wallet accounts:</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>GoPay:</strong> 0812-3456-7890<br>
                                        <strong>OVO:</strong> 0812-3456-7890
                                    </div>
                                    <div class="col-md-6">
                                        <strong>DANA:</strong> 0812-3456-7890<br>
                                        <strong>ShopeePay:</strong> 0812-3456-7890
                                    </div>
                                </div>
                                <div class="alert alert-info mt-3 mb-0">
                                    <small><i class="fa-solid fa-info-circle mr-1"></i> 
                                    Please include your booking code ({{ $booking->code }}) in the transfer note.</small>
                                </div>
                            </div>
                            
                            <!-- Cash Details (hidden by default) -->
                            <div id="cashDetails" class="bank-details" style="display: none;">
                                <h6><i class="fa-solid fa-money-bill mr-2"></i> Cash Payment Details</h6>
                                <p><strong>Office Address:</strong> Jl. Raya Tambora No. 123, Dompu, West Nusa Tenggara</p>
                                <p><strong>Office Hours:</strong> Monday - Sunday, 08:00 - 17:00</p>
                                <p><strong>Pickup Points:</strong> We can arrange pickup in Dompu city center</p>
                                <div class="alert alert-info mt-3 mb-0">
                                    <small><i class="fa-solid fa-info-circle mr-1"></i> 
                                    Please contact us via WhatsApp (+62 812-3456-7890) to arrange cash payment.</small>
                                </div>
                            </div>
                            
                            <!-- Upload Proof of Payment -->
                            <div class="mt-4">
                                <h6><i class="fa-solid fa-upload mr-2"></i> Upload Proof of Payment</h6>
                                <p class="text-muted">Upload a screenshot or photo of your payment confirmation.</p>
                                
                                <form id="paymentForm" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="payment_method" id="selectedPaymentMethod" value="bank_transfer">
                                    
                                    <div class="upload-area" id="uploadArea">
                                        <input type="file" id="proofOfPayment" name="proof_of_payment" accept="image/*,application/pdf" style="display: none;" required>
                                        <i class="fa-solid fa-cloud-upload-alt" style="font-size: 3rem; color: #6c757d;"></i>
                                        <h6 class="mt-3">Click to upload or drag and drop</h6>
                                        <p class="text-muted mb-0">JPG, PNG, or PDF files up to 5MB</p>
                                    </div>
                                    
                                    <div class="file-info" id="fileInfo">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="fa-solid fa-file mr-2"></i>
                                                <span id="fileName"></span>
                                                <small class="text-muted">(<span id="fileSize"></span>)</small>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-danger" id="removeFile">
                                                <i class="fa-solid fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="text-center mt-4">
                                        <button type="submit" class="btn btn-success btn-lg">
                                            <i class="fa-solid fa-check mr-2"></i> Submit Payment Proof
                                        </button>
                                    </div>
                                </form>
                            </div>
                            
                            @elseif($booking->status === 'awaiting-validation')
                            <!-- Awaiting Validation State -->
                            <div class="text-center">
                                <i class="fa-solid fa-clock text-info" style="font-size: 4rem;"></i>
                                <h4 class="mt-3">Payment Proof Received!</h4>
                                <div class="alert alert-info">
                                    <h6>Your payment proof has been uploaded successfully.</h6>
                                    <p class="mb-0">Our team is validating your payment. You will receive a confirmation email within 24 hours.</p>
                                </div>
                                <div class="mt-4">
                                    <a href="{{ route('booking.check-status') }}" class="btn btn-outline-primary">
                                        <i class="fa-solid fa-search mr-2"></i> Check Status Again
                                    </a>
                                    <a href="{{ route('booking.homepage') }}" class="btn btn-outline-secondary">
                                        <i class="fa-solid fa-home mr-2"></i> Back to Homepage
                                    </a>
                                </div>
                            </div>
                            
                            @else
                            <!-- Other Status -->
                            <div class="text-center">
                                <i class="fa-solid fa-info-circle text-warning" style="font-size: 4rem;"></i>
                                <h4 class="mt-3">Payment Not Required</h4>
                                <div class="alert alert-warning">
                                    <p class="mb-0">This booking is not available for payment. Status: <strong>{{ str_replace('-', ' ', $booking->status) }}</strong></p>
                                </div>
                                <div class="mt-4">
                                    <a href="{{ route('booking.check-status') }}" class="btn btn-outline-primary">
                                        <i class="fa-solid fa-search mr-2"></i> Check Status
                                    </a>
                                    <a href="{{ route('booking.homepage') }}" class="btn btn-outline-secondary">
                                        <i class="fa-solid fa-home mr-2"></i> Back to Homepage
                                    </a>
                                </div>
                            </div>
                            @endif
                        </div>
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
    // Payment method selection
    $('.payment-method-item').on('click', function() {
        const method = $(this).data('method');
        
        // Update UI
        $('.payment-method-item').removeClass('selected');
        $(this).addClass('selected');
        $(`input[name="payment_method"][value="${method}"]`).prop('checked', true);
        $('#selectedPaymentMethod').val(method);
        
        // Show/hide relevant payment details
        $('.bank-details').hide();
        if (method === 'bank_transfer') {
            $('#bankDetails').show();
        } else if (method === 'e_wallet') {
            $('#ewalletDetails').show();
        } else if (method === 'cash') {
            $('#cashDetails').show();
        }
    });
    
    // File upload handling
    const uploadArea = $('#uploadArea');
    const fileInput = $('#proofOfPayment');
    const fileInfo = $('#fileInfo');
    
    uploadArea.on('click', function() {
        fileInput.click();
    });
    
    uploadArea.on('dragover', function(e) {
        e.preventDefault();
        $(this).addClass('dragover');
    });
    
    uploadArea.on('dragleave', function(e) {
        e.preventDefault();
        $(this).removeClass('dragover');
    });
    
    uploadArea.on('drop', function(e) {
        e.preventDefault();
        $(this).removeClass('dragover');
        
        const files = e.originalEvent.dataTransfer.files;
        if (files.length > 0) {
            fileInput[0].files = files;
            handleFileSelect();
        }
    });
    
    fileInput.on('change', handleFileSelect);
    
    $('#removeFile').on('click', function() {
        fileInput.val('');
        fileInfo.hide();
        uploadArea.show();
    });
    
    function handleFileSelect() {
        const file = fileInput[0].files[0];
        if (file) {
            // Validate file size (5MB max)
            if (file.size > 5 * 1024 * 1024) {
                alert('File size must be less than 5MB');
                fileInput.val('');
                return;
            }
            
            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
            if (!allowedTypes.includes(file.type)) {
                alert('Please upload JPG, PNG, or PDF files only');
                fileInput.val('');
                return;
            }
            
            // Show file info
            $('#fileName').text(file.name);
            $('#fileSize').text(formatFileSize(file.size));
            uploadArea.hide();
            fileInfo.show();
        }
    }
    
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    // Form submission
    $('#paymentForm').on('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-2"></i> Uploading...');
        
        const formData = new FormData(this);
        
        $.ajax({
            url: '{{ route("api.booking.payment", $booking->code) }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    // Show success message and redirect
                    const successHtml = `
                        <div class="text-center">
                            <i class="fa-solid fa-check-circle text-success" style="font-size: 4rem;"></i>
                            <h4 class="mt-3">Payment Proof Uploaded!</h4>
                            <div class="alert alert-success">
                                <p class="mb-0">${response.message}</p>
                            </div>
                            <div class="mt-4">
                                <button type="button" class="btn btn-primary" onclick="location.reload()">
                                    <i class="fa-solid fa-refresh mr-2"></i> Refresh Page
                                </button>
                                <a href="{{ route('booking.check-status') }}" class="btn btn-outline-primary">
                                    <i class="fa-solid fa-search mr-2"></i> Check Status
                                </a>
                            </div>
                        </div>
                    `;
                    
                    $('.payment-card').html(successHtml);
                } else {
                    alert(response.message || 'Failed to upload payment proof.');
                }
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Failed to upload payment proof. Please try again.';
                alert(message);
            },
            complete: function() {
                submitBtn.prop('disabled', false).html('<i class="fa-solid fa-check mr-2"></i> Submit Payment Proof');
            }
        });
    });
});
</script>
@endsection