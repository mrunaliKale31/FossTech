#!/bin/bash

# Script to install Git Integration app into running Nextcloud container

set -e

echo "Installing Git Integration into Nextcloud container..."

# Build the frontend if needed
if [ ! -d "js" ]; then
    echo "Building frontend..."
    npm run build
fi

# Install PHP dependencies if needed
if [ ! -d "vendor" ]; then
    echo "Installing PHP dependencies..."
    composer install --no-dev
fi

# Create a clean tarball (excluding development files)
echo "Creating tarball..."
tar czf /tmp/gitintegration-clean.tar.gz \
    --exclude='node_modules' \
    --exclude='.git' \
    --exclude='tests' \
    --exclude='.gitignore' \
    --exclude='docker-compose.yml' \
    --exclude='Makefile' \
    --exclude='*.md' \
    --exclude='install-to-nextcloud.sh' \
    --exclude='webpack.config.js' \
    --exclude='package*.json' \
    --exclude='.env.example' \
    appinfo/ lib/ templates/ css/ img/ js/ composer.json composer.lock src/

# Copy to container
echo "Copying to container..."
docker exec nextcloud-git-integration rm -rf /var/www/html/custom_apps/gitintegration 2>/dev/null || true
docker exec nextcloud-git-integration mkdir -p /var/www/html/custom_apps/gitintegration
docker cp /tmp/gitintegration-clean.tar.gz nextcloud-git-integration:/tmp/

# Extract in container
echo "Extracting in container..."
docker exec nextcloud-git-integration bash -c "cd /var/www/html/custom_apps/gitintegration && tar xzf /tmp/gitintegration-clean.tar.gz && chown -R www-data:www-data /var/www/html/custom_apps/gitintegration"

# Install composer dependencies in container
echo "Installing Composer dependencies in container..."
docker exec -u www-data nextcloud-git-integration bash -c "cd /var/www/html/custom_apps/gitintegration && php /var/www/html/occ app:enable gitintegration" || true

# Enable the app
echo "Enabling Git Integration app..."
docker exec -u www-data nextcloud-git-integration php occ app:enable gitintegration

echo "✓ Installation complete!"
echo ""
echo "Access Nextcloud at: http://localhost:8090"
echo "Username: admin"
echo "Password: admin"
echo ""
echo "Git Integration should now appear in the main navigation menu."
