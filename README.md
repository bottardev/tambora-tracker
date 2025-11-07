# Tambora Tracker Backend

Tambora Tracker is a comprehensive Laravel-powered backend system that manages hiking expeditions on Mount Tambora.  
It provides both public booking capabilities and professional trip management tools.

## Features

### Public Booking System
- **Self-Service Booking**: Public homepage with route browsing and instant booking
- **Real-Time Availability**: Check route quotas and availability without authentication
- **Configurable Pricing**: Database-driven per-participant pricing system
- **Payment Integration**: Multiple payment methods (Bank Transfer, E-Wallet, Cash)
- **Booking Status Tracking**: Real-time status updates and confirmation system

### Admin Management System
- **Route Management**: Full CRUD operations with image uploads and quota management
- **Booking Administration**: Comprehensive booking oversight and confirmation workflows
- **Settings Management**: Database-based configuration system accessible through admin panel
- **Trip Tracking**: Real-time GPS tracking and event logging for active expeditions
- **User Management**: Role-based access control with hiker and admin roles

### Technical Features
- Route and checkpoint metadata seeded with the Pancasila trail
- Hiker profiles linked to authenticated user accounts and role-based access control
- Trip tracking endpoints for ranger and mobile clients, including location and event ingestion
- Image management system for routes with public storage integration
- Caching system for optimal performance

## Getting Started

### Prerequisites
- PHP 8.2+ with required extensions (BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML)
- Composer 2.0+
- Database supported by Laravel (MySQL 8.0+ or MariaDB 10.3+ recommended)
- Node.js 16+ and npm (for asset compilation)

### Installation

1. **Clone the repository and install dependencies:**
```bash
git clone <repository-url>
cd tambora-backend
composer install
npm install
```

2. **Environment Configuration:**
```bash
cp .env.example .env
php artisan key:generate
```

3. **Database Setup:**
```bash
# Configure database settings in .env file
php artisan migrate --seed
```

4. **Storage Configuration:**
```bash
php artisan storage:link
```

5. **Asset Compilation:**
```bash
npm run build
```

6. **Start the Application:**
```bash
php artisan serve
```

### Initial Setup

The seeder creates default configuration and accounts:
- **Admin Account**: `admin@tambora.local` / `password`
- **Default Pricing**: IDR 20,000 per participant
- **Payment Methods**: Bank Transfer, E-Wallet, Cash options
- **Route Data**: Pancasila trail with checkpoints

Access the admin panel at `/admin` and public booking system at `/` after setup.

## System Architecture

### Public Interface
- **Homepage** (`/`): Public route browsing and booking system
- **Booking Status** (`/booking-status`): Check booking status without authentication
- **Payment Processing** (`/payment/{booking_code}`): Secure payment confirmation

### Admin Interface
- **Admin Panel** (`/admin`): Filament-powered administration interface
- **Route Management**: Upload images, manage quotas, configure pricing
- **Booking Management**: Confirm payments, manage reservations
- **Settings Panel**: Database-driven configuration system

### Authentication

**API Endpoints**: All protected API endpoints require a Laravel Sanctum token.  
Use the login endpoint to obtain a `plainTextToken`, then pass it in the `Authorization: Bearer <token>` header.

**Admin Panel**: Session-based authentication with role-based access control.

## API Endpoints

### Public Endpoints (No Authentication Required)
| Method | URI | Description |
| ------ | --- | ----------- |
| GET | `/` | Public homepage with booking system |
| GET | `/booking-status` | Check booking status |
| POST | `/booking-status` | Submit booking code for status check |
| GET | `/payment/{booking_code}` | Payment confirmation page |
| POST | `/public-booking` | Create public booking |
| GET | `/public-booking/availability` | Check route availability |

### Protected API Endpoints
| Method | URI | Description | Auth |
| ------ | --- | ----------- | ---- |
| POST | `/api/auth/login` | Issue Sanctum token for a user account (`email`, `password`). | – |
| GET | `/api/routes` | List available hiking routes. | ✔ |
| GET | `/api/routes/{route}` | Show route details including checkpoints. | ✔ |
| POST | `/api/bookings` | Create a hiking booking with participants and payment method. | ✔ |
| GET | `/api/bookings` | List bookings for the authenticated hiker. | ✔ |
| GET | `/api/bookings/availability` | Check day-by-day quota status for a route. | ✔ |
| POST | `/api/trips` | Start a trip for a **confirmed** booking (`booking_code`). | ✔ |
| GET | `/api/trips/{trip}` | Retrieve trip details with related hiker and route. | ✔ |
| PATCH | `/api/trips/{trip}` | Update trip status or end time. | ✔ |
| POST | `/api/trips/{trip}/locations` | Submit batched GPS points for a trip. | ✔ |
| GET | `/api/trips/{trip}/last` | Fetch the last recorded location for a trip. | ✔ |
| POST | `/api/trips/{trip}/events` | Record notable trip events (e.g., emergency, checkpoint reached). | ✔ |

> Tip: The Filament admin panel can be used to manage hikers, routes, and trips through a browser UI.

## Configuration Management

### Database-Based Settings
The system uses a flexible database-driven configuration system accessible through the admin panel:

- **Pricing Configuration**: Set per-participant pricing (default: IDR 20,000)
- **Payment Methods**: Configure available payment options
- **Contact Information**: Manage business contact details
- **Booking Rules**: Adjust advance booking requirements and restrictions

### Key Configuration Options
- `price_per_participant`: Base price per person (automatically calculates total for multiple participants)
- `advance_booking_days`: Minimum days required for advance booking (default: 30)
- `payment_methods`: Available payment options (Bank Transfer, E-Wallet, Cash)
- `contact_phone`: Business contact number
- `contact_email`: Business email address

### Booking Rules

- **Advance Booking**: Bookings must be made at least **30 days** before the hike date (configurable in admin settings)
- **Holiday Management**: Holidays can be configured globally or per route; closed dates reject bookings and block trip starts
- **Quota Management**: Daily quotas can be managed per route/date from the admin panel. Confirmed bookings reserve capacity automatically
- **Trip Authorization**: Trips can only be started on the booked date, by the booking's hiker, after payment is confirmed
- **Pricing Calculation**: Total amount = price_per_participant × number_of_participants

### Route Management
- **Image Upload**: Routes support image uploads with automatic public URL generation
- **Quota Control**: Set daily participant limits per route
- **Checkpoint Management**: Define and manage trail checkpoints
- **Availability Tracking**: Real-time quota and booking availability

## Default Accounts and Data

### Administrator Account
The `RolesSeeder` creates a default administrator account:
- **Email**: `admin@tambora.local`
- **Password**: `password`
- **Access**: Full admin panel access and system configuration

### Default Configuration
The `BookingSettingsSeeder` populates initial system settings:
- **Base Price**: IDR 20,000 per participant
- **Payment Methods**: Bank Transfer, E-Wallet, Cash
- **Booking Rules**: 30-day advance booking requirement
- **Contact Information**: Default business contact details

### User Role Management
- Assign the `hiker` role to newly created users to enable mobile API access
- Admin users have full access to the Filament admin panel
- Public booking system requires no authentication

## Development and Testing

### Running Tests
```bash
php artisan test
```

### Cache Management
```bash
# Clear all caches during development
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Queue Configuration
Ensure background jobs and queues are configured if you enable the `MapMatchAndGeofenceJob`:
```bash
php artisan queue:work
```

### File Permissions
Ensure proper permissions for storage and bootstrap/cache directories:
```bash
chmod -R 775 storage bootstrap/cache
```

## Troubleshooting

### Common Issues
1. **Storage Link Not Working**: Run `php artisan storage:link`
2. **Images Not Displaying**: Check file permissions and storage configuration
3. **Cache Issues**: Clear all caches with `php artisan cache:clear`
4. **Database Connection**: Verify `.env` database configuration
5. **Admin Panel Access**: Ensure user has admin role assigned

### Performance Optimization
- Enable Redis for caching in production
- Configure proper queue drivers for background jobs
- Use CDN for static assets and uploaded images
- Enable PHP OPcache for better performance

## License

This project is licensed under the MIT License.
