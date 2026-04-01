#!/bin/sh

# Exit on error
set -e

echo "🚀 Starting deployment optimizations..."

# Ensure we're in the right directory
cd /var/www/html

# Run database migrations
# Use --force because we are in production
echo "📂 Running migrations..."
php artisan migrate --force

# Cache configuration, routes, and views for performance
echo "⚡️ Caching configuration..."
php artisan config:cache

echo "⚡️ Caching routes..."
php artisan route:cache

echo "⚡️ Caching views..."
php artisan view:cache

echo "✅ Optimization complete!"
