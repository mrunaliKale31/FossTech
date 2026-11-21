# Port Configuration Change

## Changes Made

The default ports have been changed to avoid conflicts with Jenkins:

| Service | Old Port | New Port |
|---------|----------|----------|
| Nextcloud | 8080 | **8090** |
| Adminer | 8081 | **8091** |

## Quick Start (Updated)

### 1. Start Docker Desktop

Make sure Docker Desktop is running on your Mac.

### 2. Start the Environment

```bash
cd /Users/mrunalipradeepkale/Desktop/FossTech
make docker-up
```

Or manually:

```bash
docker-compose up -d
```

### 3. Access the Application

**Nextcloud**: http://localhost:8090  
**Adminer**: http://localhost:8091

Login credentials:
- Username: `admin`
- Password: `admin`

### 4. Enable the Git Integration App

Wait about 30 seconds for Nextcloud to initialize, then:

```bash
make enable-app
```

Or manually:

```bash
docker exec -u www-data nextcloud-git-integration php occ app:enable gitintegration
```

## Custom Ports

If you need different ports, edit `docker-compose.yml`:

```yaml
services:
  nextcloud:
    ports:
      - "YOUR_PORT:80"  # Change YOUR_PORT
  
  adminer:
    ports:
      - "YOUR_PORT:8080"  # Change YOUR_PORT
```

Then restart:

```bash
docker-compose down
docker-compose up -d
```

## Verify Ports are Free

Before starting, check if ports are available:

```bash
# Check if port 8090 is free
lsof -i :8090

# Check if port 8091 is free
lsof -i :8091
```

If a port is in use, you'll see output. If it's free, there will be no output.

## Troubleshooting

### Docker Daemon Not Running

Error: "Cannot connect to the Docker daemon"

**Solution**: Start Docker Desktop application on your Mac.

### Port Already in Use

Error: "bind: address already in use"

**Solution**: 
1. Find what's using the port:
   ```bash
   lsof -i :8090
   ```
2. Either stop that service or change the port in docker-compose.yml

### Container Won't Start

```bash
# View logs
docker-compose logs nextcloud

# Restart
docker-compose restart

# Full reset
docker-compose down
docker-compose up -d
```

## Current Status

All documentation has been updated with the new ports:
- ✅ docker-compose.yml
- ✅ README.md
- ✅ INSTALL.md
- ✅ QUICKSTART.md
- ✅ PROJECT_SUMMARY.md

## Git Status

Changes have been committed and pushed to GitHub:
```
Commit: eeaf93e - fix: Change default ports to avoid conflicts with Jenkins
```

---

**Updated Access URLs:**
- Nextcloud: http://localhost:8090
- Adminer: http://localhost:8091
