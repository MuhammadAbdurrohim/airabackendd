# AiraBackendD - Laravel Backend

Backend service for E-Commerce Live Shopping Multi-Store app with admin panel and API endpoints.

## Requirements

- PHP ^8.1
- Composer ^2.0
- MySQL ^8.0
- Node.js ^18.0 (for asset compilation)
- NPM ^9.0

## Core Dependencies

### PHP Packages
- Laravel Framework ^10.0
- Laravel Sanctum ^3.2 (API Authentication)
- Laravel Tinker ^2.8
- GuzzleHTTP ^7.0.1 (HTTP Client)

### Frontend Assets
- AdminLTE ^3.0 (Admin Panel Template)
- Bootstrap ^5.0
- Font Awesome ^6.0

### Development Dependencies
- Laravel Pint ^1.0 (Code Style)
- Laravel Sail ^1.18 (Docker Development)
- PHPUnit ^10.1 (Testing)
- Faker ^1.9.1 (Test Data Generation)
- Laravel Ignition ^2.0 (Error Page)

## Project Structure

- app/
  - Http/
    - Controllers/
      - Admin/ (Admin Panel Controllers)
      - Api/ (API Endpoints)
    - Middleware/
  - Models/
  - Services/
- database/
  - migrations/
- resources/
  - views/
    - admin/
- routes/
  - web.php
  - admin.php
  - api.php

## Installation

1. Clone the repository
```bash
git clone <repository-url>
cd airabackendd
```

2. Install PHP dependencies
```bash
composer install
```

3. Configure environment
```bash
cp .env.example .env
php artisan key:generate
```

4. Configure database in .env
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=airabackendd
DB_USERNAME=root
DB_PASSWORD=
```

5. Run migrations
```bash
php artisan migrate
```

6. Create storage link
```bash
php artisan storage:link
```

7. Install Node.js dependencies and compile assets
```bash
npm install
npm run dev
```

## Development Server

Start the development server:
```bash
php artisan serve
```

The admin panel will be available at `http://localhost:8000/admin`

## Features

- Admin Authentication
- Dashboard with Statistics
- Live Streaming with Zegocloud Integration
- Product Management
- Order Processing
- Payment Verification
- User Management
- Shipping Management
- Payment Settings

## API Documentation

API endpoints are protected by Laravel Sanctum authentication. Documentation available at `/api/documentation`.

## Notes

- AdminLTE template is used for admin panel UI
- Live streaming integration uses Zegocloud
- Supports manual payment verification workflow
- File uploads are stored in `storage/app/public`
- API uses token-based authentication
