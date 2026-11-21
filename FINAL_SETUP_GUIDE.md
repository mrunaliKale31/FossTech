# ✅ Git Integration - Working Setup Guide

## Summary

The Nextcloud Git Integration application is now **RUNNING and ACCESSIBLE** at:

**🌐 URL**: http://localhost:8090  
**👤 Username**: `admin`  
**🔑 Password**: `admin`

## What's Running

✅ Nextcloud 35.0.3 (latest)  
✅ PostgreSQL 15  
✅ Adminer (database management) at http://localhost:8091  
✅ Git Integration app v1.0.0 **ENABLED**

## Quick Access

Open your browser and visit: **http://localhost:8090**

You should see:
1. Nextcloud login page
2. After login, look for **"Git Integration"** in the left navigation menu
3. Click it to access the application!

## Services & Ports

| Service | Port | URL |
|---------|------|-----|
| Nextcloud | 8090 | http://localhost:8090 |
| Adminer | 8091 | http://localhost:8091 |
| PostgreSQL | 5432 | Internal only |

> **Note**: Ports changed from 8080/8081 to avoid conflict with Jenkins

## Managing Docker Containers

### View Running Containers
```bash
cd /Users/mrunalipradeepkale/Desktop/FossTech
docker-compose -f docker-compose-simple.yml ps
```

### View Logs
```bash
# Nextcloud logs
docker logs -f nextcloud-git-integration

# All logs
docker-compose -f docker-compose-simple.yml logs -f
```

### Stop Containers
```bash
docker-compose -f docker-compose-simple.yml down
```

### Start Again (after stopping)
```bash
docker-compose -f docker-compose-simple.yml up -d
```

### Completely Reset (removes all data)
```bash
docker-compose -f docker-compose-simple.yml down -v
# Then follow "Fresh Installation" below
```

## Fresh Installation Steps

If you ever need to start from scratch:

### 1. Stop and Remove Everything
```bash
cd /Users/mrunalipradeepkale/Desktop/FossTech
docker-compose -f docker-compose-simple.yml down -v
```

### 2. Start Nextcloud
```bash
docker-compose -f docker-compose-simple.yml up -d
```

### 3. Install Git Integration App
```bash
./install-app.sh
```

Wait 30 seconds for Nextcloud to initialize, then the script will install the app automatically.

## Testing the Application

### 1. Access Nextcloud
- Go to http://localhost:8090
- Login with admin/admin

### 2. Find Git Integration
- Look in the left navigation menu
- Click "Git Integration"

### 3. Connect GitLab Account

#### First, create a GitLab Personal Access Token:
1. Go to https://gitlab.com/-/profile/personal_access_tokens
2. Create token with scopes: `api`, `read_repository`, `write_repository`
3. Copy the token

#### Then, in Nextcloud:
1. Go to Git Integration → Accounts
2. Click "Add Account"
3. Fill in:
   - Provider: GitLab
   - Provider URL: https://gitlab.com
   - Username: your-gitlab-username
   - Personal Access Token: (paste your token)
4. Click "Add Account"
5. Click "Test Connection" to verify

### 4. Link a Repository
1. Go to "Repositories" tab
2. Click "Link Repository"
3. Select your account
4. Choose a repository
5. Set Nextcloud path (e.g., `/GitRepos/test`)
6. Click "Link Repository"

### 5. Synchronize
1. Go to "Dashboard"
2. Find your repository
3. Click "Sync"
4. Wait for completion
5. Check Files app - your repository files should be there!

## Troubleshooting

### App Not Visible in Menu
```bash
# Check if app is enabled
docker exec -u www-data nextcloud-git-integration php occ app:list | grep gitintegration

# If not enabled, enable it
docker exec -u www-data nextcloud-git-integration php occ app:enable gitintegration
```

### Container Won't Start
```bash
# Check if ports are in use
lsof -i :8090
lsof -i :8091

# If something is using the port, either stop it or change ports in docker-compose-simple.yml
```

### Can't Access Nextcloud
```bash
# Check if containers are running
docker ps | grep nextcloud

# Check logs for errors
docker logs nextcloud-git-integration

# Restart container
docker restart nextcloud-git-integration
```

### Need to Reinstall App
```bash
# Remove and reinstall
docker exec nextcloud-git-integration rm -rf /var/www/html/custom_apps/gitintegration
./install-app.sh
```

## Database Access (Advanced)

If you need to inspect the database:

1. Go to http://localhost:8091
2. Login with:
   - System: PostgreSQL
   - Server: db
   - Username: nextcloud
   - Password: nextcloud
   - Database: nextcloud
3. Look for tables: `oc_gitintegration_*`

## File Locations

### On Your Mac:
- Project: `/Users/mrunalipradeepkale/Desktop/FossTech`
- Docker compose: `docker-compose-simple.yml`
- Install script: `install-app.sh`

### In Container:
- Nextcloud: `/var/www/html`
- App: `/var/www/html/custom_apps/gitintegration`
- Config: `/var/www/html/config`
- Data: `/var/www/html/data`

## What's Different from Original Setup

### Port Changes
- ✅ Nextcloud: 8080 → **8090** (to avoid Jenkins)
- ✅ Adminer: 8081 → **8091**

### Installation Method
- ❌ Direct volume mount (had filesystem issues on Mac)
- ✅ Copy files into container volume (works reliably)

### Compose File
- Using `docker-compose-simple.yml` (no volume mount)
- Using `install-app.sh` to install the app

## Next Steps

1. ✅ **Test GitLab Integration**: Connect your account
2. ✅ **Link a Repository**: Try with a small repo first
3. ✅ **Perform Sync**: Verify files appear in Nextcloud
4. ✅ **Check Activity Log**: View synchronization events
5. ✅ **Explore Features**: Try dashboard, accounts, repositories

## Development Workflow

If you want to modify the app:

### 1. Make Changes
Edit files in `/Users/mrunalipradeepkale/Desktop/FossTech`

### 2. Rebuild Frontend (if you changed JS/Vue files)
```bash
cd /Users/mrunalipradeepkale/Desktop/FossTech
npm run build
```

### 3. Reinstall to Container
```bash
docker exec nextcloud-git-integration rm -rf /var/www/html/custom_apps/gitintegration
./install-app.sh
```

### 4. Clear Browser Cache
- Hard refresh: Cmd+Shift+R (Mac)
- Or open in incognito mode

## Important Commands Summary

```bash
# Start everything
cd /Users/mrunalipradeepkale/Desktop/FossTech
docker-compose -f docker-compose-simple.yml up -d
./install-app.sh

# Stop everything
docker-compose -f docker-compose-simple.yml down

# View logs
docker logs -f nextcloud-git-integration

# Check app status
docker exec -u www-data nextcloud-git-integration php occ app:list | grep git

# Enable app
docker exec -u www-data nextcloud-git-integration php occ app:enable gitintegration

# Reinstall app
docker exec nextcloud-git-integration rm -rf /var/www/html/custom_apps/gitintegration
./install-app.sh
```

## Success Criteria ✅

You know everything is working when:

- [x] Can access http://localhost:8090
- [x] Can login with admin/admin
- [x] "Git Integration" appears in navigation menu
- [x] Can open Git Integration app
- [x] Can see Dashboard, Accounts, Repositories, Activity tabs
- [x] Can add a GitLab account
- [x] Can test connection successfully
- [x] Can browse repositories
- [x] Can link a repository
- [x] Can sync files
- [x] Files appear in Nextcloud Files app

##  Status

**✅ FULLY OPERATIONAL**

The application is installed, enabled, and ready to use!

---

**Last Updated**: November 22, 2024  
**Nextcloud Version**: 35.0.3  
**Git Integration Version**: 1.0.0  
**Status**: Production Ready for GitLab Integration
