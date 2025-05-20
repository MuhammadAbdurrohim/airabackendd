#!/bin/bash

echo "Starting Stisla Migration..."

# Create backup of current files
echo "Creating backup..."
timestamp=$(date +%Y%m%d_%H%M%S)
backup_dir="backup_adminlte_${timestamp}"
mkdir -p "${backup_dir}"

# Backup current layout files
cp -r resources/views/admin/layouts/app.blade.php "${backup_dir}/app.blade.php"
cp -r resources/views/admin/login.blade.php "${backup_dir}/login.blade.php"
cp -r public/assets/adminlte "${backup_dir}/adminlte"

# Download and setup Stisla
echo "Downloading Stisla..."
mkdir -p public/assets/stisla
cd public/assets
curl -L https://github.com/stisla/stisla/archive/refs/heads/master.zip -o stisla.zip
unzip stisla.zip
cp -r stisla-master/dist/* stisla/
rm -rf stisla-master stisla.zip
cd ../..

# Rename existing files
echo "Renaming existing files..."
mv resources/views/admin/layouts/app.blade.php resources/views/admin/layouts/app-adminlte.blade.php
mv resources/views/admin/login.blade.php resources/views/admin/auth/login-adminlte.blade.php

# Create directories if they don't exist
mkdir -p resources/views/admin/auth
mkdir -p resources/views/admin/layouts

# Copy new Stisla files
echo "Installing Stisla templates..."
cp STISLA-MIGRATION.md docs/
cp resources/views/admin/layouts/stisla.blade.php resources/views/admin/layouts/app.blade.php
cp resources/views/admin/auth/login-stisla.blade.php resources/views/admin/auth/login.blade.php

# Update file permissions
chmod -R 775 public/assets/stisla
chmod -R 775 resources/views/admin

# Clear cache
php artisan view:clear
php artisan cache:clear

echo "Migration completed!"
echo "Please review the migration guide at docs/STISLA-MIGRATION.md"
echo "Test the new interface and keep the backup at ${backup_dir} until everything is confirmed working."
