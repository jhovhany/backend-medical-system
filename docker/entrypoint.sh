#!/bin/bash
set -e

# Copy .env if not exists
if [ ! -f /var/www/html/.env ]; then
    echo "Creating .env from .env.example..."
    cp /var/www/html/.env.example /var/www/html/.env
fi

# Install dependencies if vendor is missing (common with bind mounts in local dev)
if [ ! -f /var/www/html/vendor/autoload.php ]; then
    echo "Installing Composer dependencies..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
fi

# Ensure Laravel writable directories exist and are writable by php-fpm user
mkdir -p \
    /var/www/html/storage/framework/cache/data \
    /var/www/html/storage/framework/sessions \
    /var/www/html/storage/framework/views \
    /var/www/html/storage/logs \
    /var/www/html/bootstrap/cache

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R ug+rwx /var/www/html/storage /var/www/html/bootstrap/cache

# Generate app key if missing
if grep -q "APP_KEY=$" /var/www/html/.env; then
    echo "Generating application key..."
    php artisan key:generate --no-interaction
fi

# Generate JWT secret if missing
if grep -q "JWT_SECRET=$" /var/www/html/.env; then
    echo "Generating JWT secret..."
    php artisan jwt:secret --no-interaction --force
fi

# Wait for database
echo "Waiting for database connection..."
until php -r "new PDO('pgsql:host=${DB_HOST:-postgres};port=${DB_PORT:-5432};dbname=${DB_DATABASE:-medical_system}', '${DB_USERNAME:-medical_user}', '${DB_PASSWORD:-medical_password}');" 2>/dev/null; do
    echo "Database not ready yet. Retrying in 3 seconds..."
    sleep 3
done
echo "Database connection established."

# Run migrations
echo "Running migrations..."
php artisan migrate --force --no-interaction

# Run seeders (only if roles table is empty)
ROLE_COUNT=$(php -r "
require 'vendor/autoload.php';
\$app = require 'bootstrap/app.php';
\$kernel = \$app->make(Illuminate\Contracts\Console\Kernel::class);
\$kernel->bootstrap();
echo \Spatie\Permission\Models\Role::count();
" 2>/dev/null || echo "0")

if [ "$ROLE_COUNT" = "0" ]; then
    echo "Seeding database..."
    php artisan db:seed --no-interaction
fi

# Create storage link
php artisan storage:link --force 2>/dev/null || true

# Clear and cache config
php artisan optimize:clear
php artisan config:cache
php artisan route:cache

echo "Application bootstrap complete."

exec "$@"
