<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Payment - Booking {{ $booking->code }} - Tambora Tracker</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { background: #f8f9fa; }
        .main-container { min-height: 100vh; padding: 60px 0; }
        .payment-card { 
            background: white; 
            border-radius: 15px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.1); 
            padding: 30px; 
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
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('booking.homepage') }}">
                <i class="fas fa-mountain text-primary me-2"></i>Tambora Tracker
            </a>
            <div class="d-flex">
                <a href="{{ route('booking.check-status') }}" class="btn btn-outline-primary me-2">
                    <i class="fas fa-search me-2"></i>Check Status
                </a>
                <a href="{{ route('booking.homepage') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-home me-2"></i>Homepage
                </a>
            </div>
        </div>
    </nav>

    <div class="main-container">
        <div class="container">
            <!-- Page Header -->
            <div class="text-center mb-4">
                <i class="fas fa-credit-card text-primary" style="font-size: 3rem;"></i>
                <h2 class="mt-3">Complete Your Payment</h2>
                <p class="lead">Secure your spot for the Mount Tambora adventure!</p>
            </div>

            <div class="row">
                <!-- Booking Summary -->
                <div class="col-lg-4 col-md-12 mb-4">
                    <div class="payment-card">
                        <h5><i class="fas fa-ticket-alt me-2"></i>Booking Summary</h5>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <td><strong>Booking Code:</strong></td>
                                    <td>{{ $booking->code }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="status-badge status-{{ $booking->status }}">
                                            {{ str_replace('-', ' ', $booking->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Route:</strong></td>
                                    <td>{{ $booking->route->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Trip Date:</strong></td>
                                    <td>{{ $booking->trip_date->format('d M Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Participants:</strong></td>
                                    <td>{{ $booking->participants_count }} people</td>
                                </tr>
                                <tr class="border-top">
                                    <td><strong>Total Amount:</strong></td>
                                    <td>
                                        <h5 class="text-primary mb-0">
                                            {{ $booking->currency }} {{ number_format($booking->amount, 0, ',', '.') }}
                                        </h5>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                        @if($booking->payment_due_at)
                        <div class="alert alert-warning mt-3 mb-0">
                            <small><i class="fas fa-clock me-1"></i> 
                            <strong>Payment Due:</strong><br>
                            {{ $booking->payment_due_at->format('d M Y, H:i') }}</small>
                        </div>
                        @endif
                        
                        <!-- Contact Info -->
                        <div class="mt-4">
                            <h6><i class="fas fa-user me-2"></i>Lead Hiker</h6>
                            <div class="bg-light p-3 rounded">
                                <div><strong>{{ $booking->hiker->name }}</strong></div>
                                <div><i class="fas fa-envelope me-1"></i> {{ $booking->hiker->email }}</div>
                                <div><i class="fas fa-phone me-1"></i> {{ $booking->hiker->phone }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Form -->
                <div class="col-lg-8 col-md-12">
                    <div class="payment-card">
                        @if($booking->status === 'pending-payment')
                        <!-- Payment Methods -->
                        <h5><i class="fas fa-credit-card me-2"></i>Choose Payment Method</h5>
                        <div class="mb-4">
                            @php $firstMethod = true; @endphp
                            @foreach($paymentMethods as $methodKey => $method)
                            <div class="payment-method-item" data-method="{{ $methodKey }}">
                                <div class="d-flex align-items-center">
                                    <input type="radio" name="payment_method" value="{{ $methodKey }}" id="{{ $methodKey }}" @if($firstMethod) checked @endif class="me-3">
                                    <div>
                                        <h6 class="mb-1"><i class="{{ $method['icon'] }} me-2"></i>{{ $method['name'] }}</h6>
                                        <p class="mb-0 text-muted">{{ $method['description'] }}</p>
                                    </div>
                                </div>
                            </div>
                            @php $firstMethod = false; @endphp
                            @endforeach
                        </div>
                        
                        <!-- Payment Details -->
                        @foreach($paymentMethods as $methodKey => $method)
                        <div id="{{ $methodKey }}Details" class="bank-details" style="display: none;">
                            <h6><i class="{{ $method['icon'] }} me-2"></i>{{ $method['name'] }} Details</h6>
                            
                            @if($methodKey === 'bank_transfer')
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Bank:</strong> {{ $method['details']['bank_name'] }}<br>
                                        <strong>Account Number:</strong> {{ $method['details']['account_number'] }}<br>
                                        <strong>Account Name:</strong> {{ $method['details']['account_name'] }}
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Amount:</strong> {{ $booking->currency }} {{ number_format($booking->amount, 0, ',', '.') }}<br>
                                        <strong>Reference:</strong> {{ $booking->code }}
                                    </div>
                                </div>
                            @elseif($methodKey === 'e_wallet')
                                <p>Please transfer to one of these e-wallet accounts:</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        @foreach(array_slice($method['details']['accounts'], 0, 2) as $walletName => $walletNumber)
                                        <strong>{{ $walletName }}:</strong> {{ $walletNumber }}<br>
                                        @endforeach
                                    </div>
                                    <div class="col-md-6">
                                        @foreach(array_slice($method['details']['accounts'], 2) as $walletName => $walletNumber)
                                        <strong>{{ $walletName }}:</strong> {{ $walletNumber }}<br>
                                        @endforeach
                                    </div>
                                </div>
                            @elseif($methodKey === 'cash')
                                <p><strong>Office Address:</strong> {{ $method['details']['office_address'] }}</p>
                                <p><strong>Office Hours:</strong> {{ $method['details']['office_hours'] }}</p>
                                <p><strong>Contact:</strong> {{ $method['details']['contact'] }}</p>
                            @endif
                            
                            <div class="alert alert-info mt-3 mb-0">
                                <small><i class="fas fa-info-circle me-1"></i> 
                                {{ $method['details']['instructions'] }}</small>
                            </div>
                        </div>
                        @endforeach
                        
                        <!-- Upload Proof of Payment -->
                        <div class="mt-4">
                            <h6><i class="fas fa-upload me-2"></i>Upload Proof of Payment</h6>
                            <p class="text-muted">Upload a screenshot or photo of your payment confirmation.</p>
                            
                            <form id="paymentForm" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="payment_method" id="selectedPaymentMethod" value="{{ array_key_first($paymentMethods) }}">
                                
                                <div class="upload-area" id="uploadArea">
                                    <input type="file" id="proofOfPayment" name="proof_of_payment" accept="image/*,application/pdf" style="display: none;" required>
                                    <i class="fas fa-cloud-upload-alt text-muted" style="font-size: 3rem;"></i>
                                    <h6 class="mt-3">Click to upload or drag and drop</h6>
                                    <p class="text-muted mb-0">JPG, PNG, or PDF files up to 5MB</p>
                                </div>
                                
                                <div class="file-info" id="fileInfo">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-file me-2"></i>
                                            <span id="fileName"></span>
                                            <small class="text-muted">(<span id="fileSize"></span>)</small>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-danger" id="removeFile">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="fas fa-check me-2"></i>Submit Payment Proof
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        @elseif($booking->status === 'awaiting-validation')
                        <!-- Awaiting Validation State -->
                        <div class="text-center">
                            <i class="fas fa-clock text-info" style="font-size: 4rem;"></i>
                            <h4 class="mt-3">Payment Proof Received!</h4>
                            <div class="alert alert-info">
                                <h6>Your payment proof has been uploaded successfully.</h6>
                                <p class="mb-0">Our team is validating your payment. You will receive a confirmation email within 24 hours.</p>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('booking.check-status') }}" class="btn btn-outline-primary btn-lg me-2">
                                    <i class="fas fa-search me-2"></i>Check Status Again
                                </a>
                                <a href="{{ route('booking.homepage') }}" class="btn btn-outline-secondary btn-lg">
                                    <i class="fas fa-home me-2"></i>Back to Homepage
                                </a>
                            </div>
                        </div>
                        
                        @else
                        <!-- Other Status -->
                        <div class="text-center">
                            <i class="fas fa-info-circle text-warning" style="font-size: 4rem;"></i>
                            <h4 class="mt-3">Payment Not Required</h4>
                            <div class="alert alert-warning">
                                <p class="mb-0">This booking is not available for payment. Status: <strong>{{ str_replace('-', ' ', $booking->status) }}</strong></p>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('booking.check-status') }}" class="btn btn-outline-primary btn-lg me-2">
                                    <i class="fas fa-search me-2"></i>Check Status
                                </a>
                                <a href="{{ route('booking.homepage') }}" class="btn btn-outline-secondary btn-lg">
                                    <i class="fas fa-home me-2"></i>Back to Homepage
                                </a>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Payment method selection
        document.querySelectorAll('.payment-method-item').forEach(item => {
            item.addEventListener('click', function() {
                const method = this.getAttribute('data-method');
                
                // Update UI
                document.querySelectorAll('.payment-method-item').forEach(i => i.classList.remove('selected'));
                this.classList.add('selected');
                
                // Update radio button
                document.querySelector(`input[name="payment_method"][value="${method}"]`).checked = true;
                document.getElementById('selectedPaymentMethod').value = method;
                
                // Show/hide relevant payment details
                document.querySelectorAll('[id$="Details"]').forEach(detail => detail.style.display = 'none');
                
                // Show the details for the selected method
                const detailsElement = document.getElementById(method + 'Details');
                if (detailsElement) {
                    detailsElement.style.display = 'block';
                }
            });
        });
        
        // File upload handling
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('proofOfPayment');
        const fileInfo = document.getElementById('fileInfo');
        
        uploadArea.addEventListener('click', () => fileInput.click());
        
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('dragover');
        });
        
        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
        });
        
        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                handleFileSelect();
            }
        });
        
        fileInput.addEventListener('change', handleFileSelect);
        
        document.getElementById('removeFile').addEventListener('click', function() {
            fileInput.value = '';
            fileInfo.style.display = 'none';
            uploadArea.style.display = 'block';
        });
        
        function handleFileSelect() {
            const file = fileInput.files[0];
            if (file) {
                // Validate file size (5MB max)
                if (file.size > 5 * 1024 * 1024) {
                    alert('File size must be less than 5MB');
                    fileInput.value = '';
                    return;
                }
                
                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Please upload JPG, PNG, or PDF files only');
                    fileInput.value = '';
                    return;
                }
                
                // Show file info
                document.getElementById('fileName').textContent = file.name;
                document.getElementById('fileSize').textContent = formatFileSize(file.size);
                uploadArea.style.display = 'none';
                fileInfo.style.display = 'block';
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
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Uploading...';
            
            const formData = new FormData(this);
            
            fetch('/api/public/bookings/{{ $booking->code }}/payment', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message and redirect
                    const paymentCard = document.querySelector('.payment-card');
                    paymentCard.innerHTML = `
                        <div class="text-center">
                            <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                            <h4 class="mt-3">Payment Proof Uploaded!</h4>
                            <div class="alert alert-success">
                                <p class="mb-0">${data.message}</p>
                            </div>
                            <div class="mt-4">
                                <button type="button" class="btn btn-primary btn-lg me-2" onclick="location.reload()">
                                    <i class="fas fa-refresh me-2"></i>Refresh Page
                                </button>
                                <a href="{{ route('booking.check-status') }}" class="btn btn-outline-primary btn-lg">
                                    <i class="fas fa-search me-2"></i>Check Status
                                </a>
                            </div>
                        </div>
                    `;
                } else {
                    alert(data.message || 'Failed to upload payment proof.');
                }
            })
            .catch(error => {
                alert('Failed to upload payment proof. Please try again.');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-check me-2"></i>Submit Payment Proof';
            });
        });
        
        // Initialize first payment method as selected
        const firstPaymentMethod = document.querySelector('.payment-method-item');
        if (firstPaymentMethod) {
            firstPaymentMethod.click();
        }
    </script>
</body>
</html>