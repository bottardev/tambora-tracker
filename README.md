<h1>Tambora Tracker Backend</h1>

Tambora Tracker is a Laravel-powered backend that manages hiking expeditions on Mount Tambora.  
It provides:

- Route and checkpoint metadata seeded with the Pancasila trail.
- Hiker profiles linked to authenticated user accounts and role-based access control.
- Trip tracking endpoints for ranger and mobile clients, including location and event ingestion.

## Getting Started

- PHP 8.2+, Composer, and a database supported by Laravel (MySQL or MariaDB recommended).
- Clone the repository and install dependencies:

```bash
composer install
```

- Copy `.env.example` to `.env` and adjust database / Sanctum settings.
- Generate the application key:

```bash
php artisan key:generate
```

- Run migrations and seeders (routes, roles, default admin account):

```bash
php artisan migrate --seed
```

- Serve the API locally:

```bash
php artisan serve
```

## Authentication

All protected endpoints require a Laravel Sanctum token.  
Use the login endpoint to obtain a `plainTextToken`, then pass it in the `Authorization: Bearer <token>` header.

## API Endpoints

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

### Booking Rules

- Bookings must be made at least **30 days** before the hike date (`config/booking.php`).
- Holidays can be configured globally or per route; closed dates reject bookings and block trip starts.
- Daily quotas can be managed per route/date from the admin panel. Confirmed bookings reserve capacity automatically.
- Trips can only be started on the booked date, by the booking’s hiker, after payment is confirmed.

## Seeding Reference Accounts

The `RolesSeeder` creates an administrator account:

- Email: `admin@tambora.local`
- Password: `password`

Assign the `hiker` role to newly created users to enable mobile access.

## Testing

Run the test suite with:

```bash
php artisan test
```

Ensure background jobs and queues are configured if you enable the `MapMatchAndGeofenceJob`.
