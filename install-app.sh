#!/bin/bash

set -e

echo "🚀 Installing Git Integration into Nextcloud..."
echo ""

# Wait for Nextcloud to be ready
echo "⏳ Waiting 30 seconds for Nextcloud to initialize..."
sleep 30

# Create temp directory with just the app files (no Mac metadata)
echo "📦 Preparing app files..."
TEMP_DIR=$(mktemp -d)
cd /Users/mrunalipradeepkale/Desktop/FossTech

# Copy files manually to avoid Mac metadata
mkdir -p "$TEMP_DIR/gitintegration"
cp -R appinfo "$TEMP_DIR/gitintegration/"
cp -R lib "$TEMP_DIR/gitintegration/"
cp -R templates "$TEMP_DIR/gitintegration/"
cp -R css "$TEMP_DIR/gitintegration/"
cp -R img "$TEMP_DIR/gitintegration/"
cp -R js "$TEMP_DIR/gitintegration/"
cp -R src "$TEMP_DIR/gitintegration/"
cp composer.json "$TEMP_DIR/gitintegration/"
cp composer.lock "$TEMP_DIR/gitintegration/"

echo "📤 Copying to container..."
docker cp "$TEMP_DIR/gitintegration" nextcloud-git-integration:/var/www/html/custom_apps/

echo "🔧 Setting permissions..."
docker exec nextcloud-git-integration chown -R www-data:www-data /var/www/html/custom_apps/gitintegration

echo "📥 Installing Composer dependencies in container..."
docker exec -u www-data nextcloud-git-integration bash -c "cd /var/www/html/custom_apps/gitintegration && curl -sS https://getcomposer.org/installer | php && php composer.phar install --no-dev --optimize-autoloader"

echo "✅ Enabling app..."
docker exec -u www-data nextcloud-git-integration php occ app:enable gitintegration

echo ""
echo "✅ ✅ ✅  Installation Complete! ✅ ✅ ✅"
echo ""
echo "🌐 Access Nextcloud at: http://localhost:8090"
echo "👤 Username: admin"
echo "🔑 Password: admin"
echo ""
echo "📁 Git Integration should now appear in the main navigation menu!"
echo ""

# Cleanup
rm -rf "$TEMP_DIR"
