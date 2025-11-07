    /*
    |--------------------------------------------------------------------------
    | Pricing Configuration
    |--------------------------------------------------------------------------
    |
    | Configurable pricing per participant and route-specific pricing.
    |
    */
    'pricing' => [
        'price_per_participant' => env('BOOKING_PRICE_PER_PARTICIPANT', 20000), // IDR per person
        'currency' => 'IDR',
        'currency_symbol' => 'Rp',
        'currency_format' => 'Rp %s', // %s will be replaced with formatted number
        // Route-specific pricing (override default price for specific routes)
        'route_specific_pricing' => [
            // 'route-slug' => ['price_per_participant' => 25000],
            // 'premium-route' => ['price_per_participant' => 50000],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for payment processing and deadlines.
    |
    */
    'payment_due_hours' => env('BOOKING_PAYMENT_DUE_HOURS', 24),
    'max_participants_per_booking' => env('BOOKING_MAX_PARTICIPANTS', 10),
    'booking_code_prefix' => env('BOOKING_CODE_PREFIX', 'BOOK-'),
    'booking_code_length' => env('BOOKING_CODE_LENGTH', 5),

    /*
    |--------------------------------------------------------------------------
    | Payment Methods Configuration
    |--------------------------------------------------------------------------
    |
    | Available payment methods with their details and settings.
    |
    */
    'payment_methods' => [
        'bank_transfer' => [
            'name' => 'Bank Transfer',
            'description' => 'Transfer to our bank account (Most common method)',
            'enabled' => env('PAYMENT_BANK_TRANSFER_ENABLED', true),
            'icon' => 'fas fa-university',
            'details' => [
                'bank_name' => env('BANK_NAME', 'Bank Mandiri'),
                'account_number' => env('BANK_ACCOUNT_NUMBER', '1234-567890-123'),
                'account_name' => env('BANK_ACCOUNT_NAME', 'PT Tambora Adventure'),
                'instructions' => 'Please include your booking code in the transfer description.',
            ]
        ],
        'e_wallet' => [
            'name' => 'E-Wallet',
            'description' => 'GoPay, OVO, DANA, ShopeePay',
            'enabled' => env('PAYMENT_E_WALLET_ENABLED', true),
            'icon' => 'fas fa-mobile-alt',
            'details' => [
                'accounts' => [
                    'GoPay' => env('EWALLET_GOPAY', '0812-3456-7890'),
                    'OVO' => env('EWALLET_OVO', '0812-3456-7890'),
                    'DANA' => env('EWALLET_DANA', '0812-3456-7890'),
                    'ShopeePay' => env('EWALLET_SHOPEEPAY', '0812-3456-7890'),
                ],
                'instructions' => 'Please include your booking code in the transfer note.',
            ]
        ],
        'cash' => [
            'name' => 'Cash Payment',
            'description' => 'Pay at our office or during pickup',
            'enabled' => env('PAYMENT_CASH_ENABLED', true),
            'icon' => 'fas fa-money-bill',
            'details' => [
                'office_address' => env('OFFICE_ADDRESS', 'Jl. Raya Tambora No. 123, Dompu, West Nusa Tenggara'),
                'office_hours' => env('OFFICE_HOURS', 'Monday - Sunday, 08:00 - 17:00'),
                'contact' => env('CONTACT_PHONE', '+62 812-3456-7890'),
                'instructions' => 'Please contact us via WhatsApp to arrange cash payment.',
            ]
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | File Upload Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for proof of payment file uploads.
    |
    */
    'max_file_size' => env('BOOKING_MAX_FILE_SIZE', 5120), // 5MB in KB
    'allowed_file_types' => ['jpg', 'jpeg', 'png', 'pdf'],

    /*
    |--------------------------------------------------------------------------
    | Contact Information
    |--------------------------------------------------------------------------
    |
    | Business contact details for customer communications.
    |
    */
    'contact' => [
        'phone' => env('BOOKING_CONTACT_PHONE', '+62 812-3456-7890'),
        'email' => env('BOOKING_CONTACT_EMAIL', 'bookings@tamboratracker.com'),
        'whatsapp' => env('BOOKING_CONTACT_WHATSAPP', '+62 812-3456-7890'),
        'address' => env('BOOKING_CONTACT_ADDRESS', 'Jl. Raya Tambora No. 123, Dompu, West Nusa Tenggara'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Legacy Settings (Backward Compatibility)
    |--------------------------------------------------------------------------
    |
    | These are kept for backward compatibility with existing code.
    |
    */
    'base_price_per_person' => env('BOOKING_BASE_PRICE_PER_PERSON', 20000), // Alias for price_per_participant
];
