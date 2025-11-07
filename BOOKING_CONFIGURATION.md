# Booking System Configuration Guide

## Overview
The booking system now supports configurable pricing per participant and customizable payment methods. You can easily adjust prices and settings without touching any code.

## Quick Setup

### 1. Basic Pricing Configuration

To change the price per participant (currently set to IDR 20,000):

```bash
# In your .env file, add or update:
BOOKING_PRICE_PER_PARTICIPANT=25000  # Change to your desired price
```

### 2. Payment Methods Configuration

Enable or disable payment methods:

```bash
# Enable/disable payment methods (true/false)
PAYMENT_BANK_TRANSFER_ENABLED=true
PAYMENT_E_WALLET_ENABLED=true
PAYMENT_CASH_ENABLED=false  # Disable cash payments
```

### 3. Bank Account Details

Update your bank account information:

```bash
BANK_NAME="Your Bank Name"
BANK_ACCOUNT_NUMBER="Your Account Number"
BANK_ACCOUNT_NAME="Your Account Name"
```

### 4. E-Wallet Accounts

Configure your e-wallet accounts:

```bash
EWALLET_GOPAY="Your GoPay Number"
EWALLET_OVO="Your OVO Number"
EWALLET_DANA="Your DANA Number"
EWALLET_SHOPEEPAY="Your ShopeePay Number"
```

## Advanced Configuration

### Route-Specific Pricing (Future Feature)

You can set different prices for specific routes by editing `config/booking.php`:

```php
'route_specific_pricing' => [
    'premium-route' => ['price_per_participant' => 50000],
    'basic-route' => ['price_per_participant' => 15000],
],
```

### Custom Payment Methods

To add new payment methods, edit `config/booking.php`:

```php
'payment_methods' => [
    'credit_card' => [
        'name' => 'Credit Card',
        'description' => 'Pay with your credit card',
        'enabled' => env('PAYMENT_CREDIT_CARD_ENABLED', false),
        'icon' => 'fas fa-credit-card',
        'details' => [
            'instructions' => 'Use our secure payment gateway.',
        ]
    ],
    // ... existing methods
],
```

## How Pricing Works

### Current System:
- **Price per participant**: IDR 20,000 (configurable)
- **Calculation**: Total Amount = Price × Number of Participants
- **Example**: 3 participants = 20,000 × 3 = IDR 60,000

### Examples:

```bash
# For IDR 15,000 per person
BOOKING_PRICE_PER_PARTICIPANT=15000
# 2 participants = IDR 30,000
# 5 participants = IDR 75,000

# For IDR 35,000 per person  
BOOKING_PRICE_PER_PARTICIPANT=35000
# 2 participants = IDR 70,000
# 5 participants = IDR 175,000
```

## Testing Your Changes

1. Update your `.env` file with new values
2. Clear Laravel cache:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```
3. Test the booking flow on your website
4. Check pricing displays correctly on homepage and payment pages

## Configuration Files

- **Main Config**: `config/booking.php` - Advanced settings
- **Environment**: `.env` - Simple price/contact changes
- **Example**: `.env.booking.example` - All available options

## Support

If you need help configuring the system:

1. Check the example configuration in `.env.booking.example`
2. Test changes in a development environment first
3. Always backup your `.env` file before making changes
4. Clear cache after configuration changes