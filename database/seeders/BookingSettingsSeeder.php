<?php

namespace Database\Seeders;

use App\Models\BookingSetting;
use Illuminate\Database\Seeder;

class BookingSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Pricing Settings
            [
                'key' => 'price_per_participant',
                'value' => '20000',
                'type' => 'integer',
                'group' => 'pricing',
                'label' => 'Price Per Participant',
                'description' => 'Price per participant in IDR'
            ],
            [
                'key' => 'currency',
                'value' => 'IDR',
                'type' => 'string',
                'group' => 'pricing',
                'label' => 'Currency',
                'description' => 'Currency code'
            ],
            [
                'key' => 'currency_symbol',
                'value' => 'Rp',
                'type' => 'string',
                'group' => 'pricing',
                'label' => 'Currency Symbol',
                'description' => 'Currency display symbol'
            ],

            // Payment Method Settings
            [
                'key' => 'payment_bank_transfer_enabled',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'payment',
                'label' => 'Enable Bank Transfer',
                'description' => 'Enable bank transfer payment method'
            ],
            [
                'key' => 'payment_e_wallet_enabled',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'payment',
                'label' => 'Enable E-Wallet',
                'description' => 'Enable e-wallet payment method'
            ],
            [
                'key' => 'payment_cash_enabled',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'payment',
                'label' => 'Enable Cash Payment',
                'description' => 'Enable cash payment method'
            ],

            // Bank Transfer Details
            [
                'key' => 'bank_name',
                'value' => 'Bank Mandiri',
                'type' => 'string',
                'group' => 'payment',
                'label' => 'Bank Name',
                'description' => 'Primary bank name for transfers'
            ],
            [
                'key' => 'bank_account_number',
                'value' => '1234-567890-123',
                'type' => 'string',
                'group' => 'payment',
                'label' => 'Bank Account Number',
                'description' => 'Bank account number for transfers'
            ],
            [
                'key' => 'bank_account_name',
                'value' => 'PT Tambora Adventure',
                'type' => 'string',
                'group' => 'payment',
                'label' => 'Bank Account Name',
                'description' => 'Bank account holder name'
            ],

            // E-Wallet Details
            [
                'key' => 'ewallet_accounts',
                'value' => json_encode([
                    'GoPay' => '0812-3456-7890',
                    'OVO' => '0812-3456-7890',
                    'DANA' => '0812-3456-7890',
                    'ShopeePay' => '0812-3456-7890'
                ]),
                'type' => 'json',
                'group' => 'payment',
                'label' => 'E-Wallet Accounts',
                'description' => 'E-wallet account numbers'
            ],

            // Contact Information
            [
                'key' => 'office_address',
                'value' => 'Jl. Raya Tambora No. 123, Dompu, West Nusa Tenggara',
                'type' => 'string',
                'group' => 'contact',
                'label' => 'Office Address',
                'description' => 'Physical office address'
            ],
            [
                'key' => 'office_hours',
                'value' => 'Monday - Sunday, 08:00 - 17:00',
                'type' => 'string',
                'group' => 'contact',
                'label' => 'Office Hours',
                'description' => 'Office operating hours'
            ],
            [
                'key' => 'contact_phone',
                'value' => '+62 812-3456-7890',
                'type' => 'string',
                'group' => 'contact',
                'label' => 'Contact Phone',
                'description' => 'Primary contact phone number'
            ],
            [
                'key' => 'contact_email',
                'value' => 'bookings@tamboratracker.com',
                'type' => 'string',
                'group' => 'contact',
                'label' => 'Contact Email',
                'description' => 'Primary contact email address'
            ],
            [
                'key' => 'contact_whatsapp',
                'value' => '+62 812-3456-7890',
                'type' => 'string',
                'group' => 'contact',
                'label' => 'WhatsApp Number',
                'description' => 'WhatsApp contact number'
            ],

            // Booking Settings
            [
                'key' => 'payment_due_hours',
                'value' => '24',
                'type' => 'integer',
                'group' => 'general',
                'label' => 'Payment Due Hours',
                'description' => 'Hours to complete payment after booking'
            ],
            [
                'key' => 'max_participants_per_booking',
                'value' => '10',
                'type' => 'integer',
                'group' => 'general',
                'label' => 'Max Participants Per Booking',
                'description' => 'Maximum number of participants per booking'
            ],
            [
                'key' => 'booking_code_prefix',
                'value' => 'BOOK-',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Booking Code Prefix',
                'description' => 'Prefix for booking codes'
            ],
            [
                'key' => 'booking_code_length',
                'value' => '5',
                'type' => 'integer',
                'group' => 'general',
                'label' => 'Booking Code Length',
                'description' => 'Length of booking code (excluding prefix)'
            ]
        ];

        foreach ($settings as $setting) {
            BookingSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
