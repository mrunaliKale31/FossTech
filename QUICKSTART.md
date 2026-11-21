# Quick Start Guide

Get the Nextcloud Git Integration running in under 5 minutes.

## Prerequisites

- Docker and Docker Compose installed
- Git installed
- A GitLab account (free on GitLab.com)

## Step 1: Clone and Setup (2 minutes)

```bash
# Clone the repository
git clone https://github.com/mrunaliKale31/FossTech.git
cd FossTech

# Run automated setup
make setup
```

This will:
- Install all dependencies (PHP and Node.js)
- Build the frontend
- Start Docker containers (Nextcloud, PostgreSQL, Adminer)
- Enable the Git Integration app

Wait about 30 seconds for Nextcloud to initialize.

## Step 2: Access Nextcloud (30 seconds)

Open your browser and go to: **http://localhost:8090**

Login credentials:
- Username: `admin`
- Password: `admin`

You'll see the Nextcloud dashboard. Look for **Git Integration** in the main navigation.

## Step 3: Create GitLab Personal Access Token (1 minute)

1. Go to https://gitlab.com/-/profile/personal_access_tokens
2. Create a new token:
   - Name: `Nextcloud Git Integration`
   - Scopes: Check `api`, `read_repository`, `write_repository`
   - Expiration: Optional
3. Click **Create personal access token**
4. **Copy the token** - you'll need it in the next step

## Step 4: Connect GitLab Account (1 minute)

In Nextcloud:

1. Click **Git Integration** in the main menu
2. Go to the **Accounts** tab
3. Click **Add Account**
4. Fill in:
   - Provider: `GitLab`
   - Provider URL: `https://gitlab.com`
   - Username: Your GitLab username
   - Personal Access Token: Paste the token from Step 3
5. Click **Add Account**
6. Click **Test Connection** to verify (should show "Connection successful")

## Step 5: Link a Repository (1 minute)

1. Go to the **Repositories** tab
2. Click **Link Repository**
3. Select your GitLab account from the dropdown
4. Wait for repositories to load
5. Click on a repository (choose a small one for testing)
6. Enter Nextcloud path: `/GitRepos/test-repo` (or your preferred path)
7. Click **Link Repository**

## Step 6: Synchronize (1 minute)

1. Go to the **Dashboard** tab
2. Find your linked repository
3. Click **Sync**
4. Wait for synchronization to complete
5. Check the **Files** app in Nextcloud - navigate to the path you specified
6. You should see your repository files!

## Verify Everything Works

- ✅ Dashboard shows repository with sync status
- ✅ Repositories tab lists your linked repo
- ✅ Activity tab shows synchronization events
- ✅ Files appear in Nextcloud at the specified path

## What's Next?

### Try These Features

- Link multiple repositories
- View sync history
- Check activity logs
- Sync repositories again to see updates

### Explore the Code

- Backend: `lib/` directory
- Frontend: `src/` directory
- Database: Check Adminer at http://localhost:8091

### Read Documentation

- [README.md](README.md) - Full documentation
- [INSTALL.md](INSTALL.md) - Detailed installation
- [PROJECT_SUMMARY.md](PROJECT_SUMMARY.md) - Implementation details

## Troubleshooting

### Container won't start
```bash
# Check if ports are in use
lsof -i :8080
lsof -i :8081

# Stop and restart
make docker-down
make docker-up
```

### Can't see Git Integration in menu
```bash
# Enable the app
make enable-app

# Restart containers
docker-compose restart
```

### Connection test fails
- Verify GitLab URL is correct
- Check token has required scopes
- Ensure token hasn't expired

### Sync fails
- Check repository permissions
- Verify Nextcloud path is valid
- Review Activity tab for error details

## Stopping the Environment

```bash
# Stop containers but keep data
make docker-down

# Or manually:
docker-compose down

# To remove everything including data:
docker-compose down -v
```

## Restarting Later

```bash
cd FossTech
make docker-up
```

Your data persists in Docker volumes, so linked repositories and accounts remain.

## Database Access (Optional)

To inspect the database:

1. Go to http://localhost:8091 (Adminer)
2. Login:
   - System: `PostgreSQL`
   - Server: `db`
   - Username: `nextcloud`
   - Password: `nextcloud`
   - Database: `nextcloud`
3. Look for tables starting with `oc_gitintegration_`

## Next Steps

1. **Test Thoroughly**: Try different repositories and operations
2. **Report Issues**: Found a bug? Report it on GitHub
3. **Contribute**: Want to add features? See [README.md](README.md#contributing)
4. **Deploy**: Ready for production? See [INSTALL.md](INSTALL.md#production-deployment)

## Common Commands

```bash
# View logs
make docker-logs

# Rebuild everything
make clean
make setup

# Access container shell
make docker-shell

# Run tests
make test

# Build frontend
npm run build
```

## Success Criteria

You've successfully set up Git Integration if:

✅ Nextcloud is accessible  
✅ Git Integration appears in navigation  
✅ GitLab account is connected  
✅ Test connection succeeds  
✅ Repository is linked  
✅ Sync completes successfully  
✅ Files appear in Nextcloud  
✅ Activity logs show events  

## Get Help

- **Documentation**: Check README.md and INSTALL.md
- **Issues**: https://github.com/mrunaliKale31/FossTech/issues
- **Community**: Nextcloud forums

---

**Time to working app: ~5 minutes**  
**Difficulty: Easy**  
**Requirements: Docker, GitLab account**

Enjoy your Git-integrated Nextcloud! 🚀
