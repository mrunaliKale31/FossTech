# Installation Guide - Nextcloud Git Integration

This guide will help you install and configure the Nextcloud Git Integration application.

## Quick Start (Docker)

The fastest way to get started is using Docker:

```bash
# Clone the repository
git clone https://github.com/mrunaliKale31/FossTech.git
cd FossTech

# Run complete setup (installs dependencies, builds, starts Docker)
make setup
```

After about 30 seconds, access:
- **Nextcloud**: http://localhost:8090 (admin/admin)
- **Adminer**: http://localhost:8091 (for database management)

## Manual Installation

### Prerequisites

Ensure you have:
- PHP 8.0 or higher with required extensions
- Composer (PHP dependency manager)
- Node.js 16+ and npm
- A running Nextcloud 25+ instance
- PostgreSQL or MySQL/MariaDB database

### Step 1: Install Dependencies

```bash
# Install PHP dependencies
composer install --no-dev

# Install Node.js dependencies
npm install

# Build frontend assets
npm run build
```

### Step 2: Deploy to Nextcloud

```bash
# Copy the application to Nextcloud custom_apps directory
sudo cp -r . /var/www/nextcloud/custom_apps/gitintegration

# Set proper ownership (adjust user/group as needed)
sudo chown -R www-data:www-data /var/www/nextcloud/custom_apps/gitintegration
```

### Step 3: Enable the Application

```bash
# Enable via command line
sudo -u www-data php /var/www/nextcloud/occ app:enable gitintegration

# Or enable via Nextcloud web interface:
# Settings → Apps → Integration → Git Integration → Enable
```

### Step 4: Verify Installation

1. Log into Nextcloud
2. Look for "Git Integration" in the main navigation menu
3. Click to open the application
4. You should see the dashboard

## Configuration

### Create GitLab Personal Access Token

Before you can connect a GitLab account:

1. Go to GitLab (gitlab.com or your instance)
2. Navigate to **User Settings → Access Tokens**
3. Create a new token with these scopes:
   - `api`
   - `read_repository`
   - `write_repository`
4. Give it a name like "Nextcloud Git Integration"
5. Set an expiration date (or leave blank for no expiration)
6. Click **Create personal access token**
7. **Copy the token immediately** (it won't be shown again)

### Connect GitLab Account

1. Open Git Integration in Nextcloud
2. Go to the **Accounts** tab
3. Click **Add Account**
4. Fill in the form:
   - **Provider**: GitLab
   - **Provider URL**: `https://gitlab.com` (or your self-hosted URL)
   - **Username**: Your GitLab username
   - **Personal Access Token**: Paste the token you created
5. Click **Add Account**
6. Click **Test Connection** to verify

### Link Your First Repository

1. Go to the **Repositories** tab
2. Click **Link Repository**
3. Select your connected GitLab account
4. Browse the list of repositories
5. Select a repository
6. Specify the Nextcloud folder path (e.g., `/GitRepos/my-project`)
7. Click **Link Repository**

### Synchronize Repository

1. From the **Dashboard** or **Repositories** tab
2. Find the linked repository
3. Click **Sync**
4. Wait for synchronization to complete
5. Check the specified Nextcloud folder - files should now be present

## Docker Development Environment

### Start Development Environment

```bash
# Start all containers
make docker-up

# Or manually:
docker-compose up -d
```

### Access Services

- **Nextcloud**: http://localhost:8090
- **Adminer** (database management): http://localhost:8091
  - System: PostgreSQL
  - Server: db
  - Username: nextcloud
  - Password: nextcloud
  - Database: nextcloud

### View Logs

```bash
# Follow Nextcloud logs
make docker-logs

# Or manually:
docker-compose logs -f nextcloud
```

### Stop Environment

```bash
# Stop containers
make docker-down

# Or manually:
docker-compose down
```

### Reset Environment

```bash
# Stop and remove everything (including data)
docker-compose down -v

# Start fresh
make setup
```

## Development Workflow

### Frontend Development

```bash
# Watch for changes and rebuild automatically
npm run watch

# Or manually rebuild
npm run build
```

### Backend Development

After making PHP changes:

```bash
# Restart Nextcloud container to reload PHP
docker-compose restart nextcloud

# Or if running without Docker, restart your web server
```

### Database Changes

After creating new migrations:

```bash
# Run migrations via Nextcloud
docker exec -u www-data nextcloud-git-integration php occ migrations:execute gitintegration
```

### Running Tests

```bash
# Run all tests
make test

# Or manually:
vendor/bin/phpunit
```

## Troubleshooting

### Application Not Appearing

1. Check if enabled:
   ```bash
   docker exec -u www-data nextcloud-git-integration php occ app:list
   ```

2. Enable it:
   ```bash
   make enable-app
   ```

3. Check Nextcloud logs:
   ```bash
   docker exec nextcloud-git-integration cat /var/www/html/data/nextcloud.log
   ```

### Connection Test Fails

- Verify the GitLab URL is correct and accessible
- Check that your Personal Access Token has the required scopes
- Ensure your Nextcloud instance can reach the GitLab server
- Check for firewall or network issues

### Sync Fails

1. Check activity log in the application for error details
2. Verify you have permission to access the repository
3. Ensure the Nextcloud folder path is valid
4. Check available disk space
5. Review Nextcloud logs for detailed errors

### Build Fails

```bash
# Clear caches and rebuild
rm -rf node_modules vendor
npm install
composer install
npm run build
```

### Docker Issues

```bash
# Restart containers
docker-compose restart

# Rebuild and restart
docker-compose up -d --build

# View detailed logs
docker-compose logs nextcloud
```

### Permission Issues

```bash
# Fix file ownership (Docker)
docker exec nextcloud-git-integration chown -R www-data:www-data /var/www/html/custom_apps/gitintegration

# Fix permissions (local)
sudo chown -R www-data:www-data /path/to/nextcloud/custom_apps/gitintegration
sudo chmod -R 755 /path/to/nextcloud/custom_apps/gitintegration
```

## Upgrading

### From Source

```bash
cd /path/to/FossTech
git pull origin main
composer install --no-dev
npm install
npm run build

# Copy updated files to Nextcloud
sudo cp -r . /var/www/nextcloud/custom_apps/gitintegration

# Run migrations
sudo -u www-data php /var/www/nextcloud/occ migrations:execute gitintegration
```

### Docker Environment

```bash
cd /path/to/FossTech
git pull origin main
make clean
make setup
```

## Uninstallation

### Via Nextcloud

1. Settings → Apps → Integration
2. Find "Git Integration"
3. Click **Disable**
4. Click **Remove**

### Manual

```bash
# Disable the app
sudo -u www-data php /var/www/nextcloud/occ app:disable gitintegration

# Remove files
sudo rm -rf /var/www/nextcloud/custom_apps/gitintegration

# Optionally remove database tables
# (This will delete all your Git Integration data!)
sudo -u www-data php /var/www/nextcloud/occ migrations:rollback gitintegration
```

## Production Deployment

### Security Checklist

- [ ] Use HTTPS for all connections
- [ ] Secure your GitLab Personal Access Tokens
- [ ] Regularly rotate access tokens
- [ ] Enable Nextcloud 2FA
- [ ] Keep Nextcloud and this app updated
- [ ] Review access logs regularly
- [ ] Backup your database

### Performance Optimization

- Enable PHP OPcache
- Use Redis or Memcached for caching
- Configure appropriate PHP memory limits
- Enable Nextcloud background jobs via cron
- Consider using a reverse proxy (nginx/Apache) with proper caching

### Backup

Backup these items regularly:

1. Nextcloud database
2. Nextcloud data directory
3. Application configuration

```bash
# Backup database
pg_dump nextcloud > nextcloud_backup.sql

# Backup data
tar -czf nextcloud_data_backup.tar.gz /path/to/nextcloud/data/

# Backup app configuration
cp -r /var/www/nextcloud/custom_apps/gitintegration /backup/location/
```

## Getting Help

- **Documentation**: See README.md
- **Issues**: https://github.com/mrunaliKale31/FossTech/issues
- **Nextcloud Community**: https://help.nextcloud.com

## Next Steps

After installation:

1. Connect your GitLab account
2. Link your first repository
3. Perform initial synchronization
4. Explore the activity history
5. Set up additional repositories as needed

For advanced features and usage, see the main [README.md](README.md).
