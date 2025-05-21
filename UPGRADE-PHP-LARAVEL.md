# Upgrade Guide: PHP 8.2 and Laravel 11

## Step 1: Update composer.json

Update the `require` section in `composer.json`:

```json
"require": {
    "php": "^8.2",
    "guzzlehttp/guzzle": "^7.5",
    "laravel/framework": "^11.0",
    "laravel/sanctum": "^3.0",
    "laravel/tinker": "^2.7"
}
```

## Step 2: Update Dependencies

Run the following command in the `airabackendd` directory:

```bash
composer update
```

This will update Laravel and other dependencies to compatible versions.

## Step 3: Review Laravel 11 Upgrade Guide

Check the official Laravel 11 upgrade guide for breaking changes and new features:

https://laravel.com/docs/11.x/upgrade

Make necessary code changes as per the guide.

## Step 4: Update PHP Version on Server

Ensure your server or local environment uses PHP 8.2.

## Step 5: Test Application

- Run automated tests: `php artisan test`
- Manually test critical features
- Check logs for errors

## Step 6: Additional Notes

- Some third-party packages may need updates or replacements if incompatible with Laravel 11.
- Backup your project before upgrading.
- Consider upgrading in a separate branch or staging environment.

---

This guide helps you upgrade your Laravel project from PHP 8.1 and Laravel 9 to PHP 8.2 and Laravel 11.
