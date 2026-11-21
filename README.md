# Nextcloud Git Integration

A comprehensive Nextcloud application that integrates Git repositories (GitLab, Gitea, Forgejo) with Nextcloud, providing seamless synchronization, documentation management, and release note generation.

## Features

### Current Implementation (GitLab MVP)

- ✅ **Git Provider Management**
  - Connect GitLab accounts (GitLab.com and self-hosted)
  - Secure credential storage with encryption
  - Connection testing and status monitoring
  
- ✅ **Repository Management**
  - Browse GitLab repositories
  - Link repositories to Nextcloud folders
  - Repository details and metadata
  - Branch management
  
- ✅ **Bidirectional Synchronization**
  - Initial repository sync to Nextcloud
  - Manual sync on demand
  - Sync history tracking
  - Status monitoring
  - File addition, modification tracking
  
- ✅ **Activity Tracking**
  - Comprehensive activity logging
  - User-specific activity history
  - Repository-specific activity
  - Success/failure tracking

- ✅ **Modern UI**
  - Clean, Nextcloud-compatible interface
  - Dashboard with repository overview
  - Account management interface
  - Repository linking wizard
  - Activity history viewer

### Planned Features

- 🔄 **Advanced Synchronization**
  - Automatic sync scheduling
  - Conflict detection and resolution
  - Background job processing
  - Incremental sync optimization
  
- 📝 **Documentation Features**
  - Markdown/AsciiDoc rendering
  - Documentation suggestion workflow
  - Review and approval system
  - Direct commit to GitLab
  
- 📋 **Release Management**
  - Commit history viewer
  - Automated release note generation
  - Tag and release management
  - Changelog creation
  
- 🔧 **Additional Providers**
  - Gitea support
  - Forgejo support

## Technology Stack

### Backend
- **PHP 8.0+**: Server-side logic
- **Nextcloud App Framework**: Application structure and APIs
- **Guzzle HTTP**: GitLab API communication
- **PostgreSQL**: Database (via Nextcloud abstraction)
- **Composer**: Dependency management

### Frontend
- **Vue.js 2.7**: Reactive UI framework
- **Nextcloud Vue Components**: UI consistency
- **Axios**: HTTP client
- **Webpack**: Asset bundling
- **Marked/Asciidoctor**: Documentation rendering (planned)

### Development
- **Docker & Docker Compose**: Local development environment
- **Make**: Build automation
- **PHPUnit**: Testing framework

## Prerequisites

- **Docker** and **Docker Compose**: For containerized development
- **PHP 8.0+**: For local development
- **Composer**: PHP dependency management
- **Node.js 16+** and **npm**: Frontend build tools
- **Git**: Version control

## Installation

### Option 1: Docker Development Setup (Recommended)

1. **Clone the repository**
   ```bash
   git clone https://github.com/mrunaliKale31/FossTech.git
   cd FossTech
   ```

2. **Complete setup** (installs dependencies, builds assets, starts Docker)
   ```bash
   make setup
   ```

   This command will:
   - Install PHP dependencies via Composer
   - Install Node dependencies via npm
   - Build frontend assets
   - Start Docker containers
   - Enable the Git Integration app

3. **Access Nextcloud**
   - Nextcloud: http://localhost:8090
   - Admin login: `admin` / `admin`
   - Adminer (database): http://localhost:8091

### Option 2: Manual Installation

1. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

2. **Build frontend assets**
   ```bash
   npm run build
   ```

3. **Copy to Nextcloud**
   ```bash
   cp -r . /path/to/nextcloud/custom_apps/gitintegration
   ```

4. **Enable the app**
   ```bash
   cd /path/to/nextcloud
   sudo -u www-data php occ app:enable gitintegration
   ```

## Configuration

### Connect GitLab Account

1. Navigate to **Git Integration** in Nextcloud
2. Go to the **Accounts** tab
3. Click **Add Account**
4. Fill in the form:
   - **Provider**: GitLab
   - **Provider URL**: `https://gitlab.com` (or your self-hosted URL)
   - **Username**: Your GitLab username
   - **Personal Access Token**: Your GitLab PAT

#### Creating a GitLab Personal Access Token

1. Go to GitLab → Settings → Access Tokens
2. Create a token with these scopes:
   - `api`
   - `read_repository`
   - `write_repository`
3. Copy the token and paste it into the Git Integration app

### Link a Repository

1. Go to the **Repositories** tab
2. Click **Link Repository**
3. Select your connected account
4. Choose a repository from the list
5. Specify the Nextcloud folder path (e.g., `/GitRepos/my-project`)
6. Click **Link Repository**

### Synchronize Repository

1. From the **Dashboard** or **Repositories** tab
2. Click the **Sync** button on a repository card
3. Wait for synchronization to complete
4. Files will be available in the specified Nextcloud folder

## Development

### Available Make Commands

```bash
make help           # Show all available commands
make install        # Install dependencies
make build          # Build production assets
make dev            # Build development assets
make watch          # Watch and rebuild on changes
make clean          # Remove generated files
make docker-up      # Start Docker containers
make docker-down    # Stop Docker containers
make docker-logs    # View container logs
make docker-shell   # Open shell in Nextcloud container
make enable-app     # Enable the app in Nextcloud
make test           # Run tests
make lint           # Run linter
```

### Development Workflow

1. **Start development environment**
   ```bash
   make docker-up
   ```

2. **Watch for frontend changes**
   ```bash
   make watch
   ```

3. **View logs**
   ```bash
   make docker-logs
   ```

4. **Access Nextcloud container**
   ```bash
   make docker-shell
   ```

### Project Structure

```
gitintegration/
├── appinfo/                  # App metadata and routing
│   ├── info.xml             # App information
│   ├── routes.php           # API routes
│   └── app.php              # Bootstrap
├── lib/                      # Backend PHP code
│   ├── AppInfo/             # Application class
│   ├── Controller/          # API controllers
│   ├── Service/             # Business logic
│   ├── Db/                  # Database entities and mappers
│   ├── Git/                 # Git provider implementations
│   ├── Migration/           # Database migrations
│   └── Settings/            # Settings pages
├── src/                      # Frontend JavaScript/Vue code
│   ├── views/               # Vue page components
│   ├── components/          # Reusable Vue components
│   ├── api/                 # API client
│   └── main.js              # Entry point
├── templates/                # PHP templates
├── css/                      # Stylesheets
├── img/                      # Images and icons
├── tests/                    # Unit and integration tests
├── docker-compose.yml        # Docker configuration
├── Makefile                  # Build automation
├── composer.json             # PHP dependencies
├── package.json              # Node dependencies
├── webpack.config.js         # Frontend build configuration
└── README.md                 # This file
```

## API Endpoints

### Accounts
- `GET /api/accounts` - List all accounts
- `POST /api/accounts` - Create account
- `GET /api/accounts/{id}` - Get account details
- `PUT /api/accounts/{id}` - Update account
- `DELETE /api/accounts/{id}` - Delete account
- `POST /api/accounts/{id}/test` - Test connection

### Repositories
- `GET /api/repositories` - List linked repositories
- `POST /api/repositories/fetch` - Fetch remote repositories
- `POST /api/repositories/link` - Link repository
- `POST /api/repositories/{id}/unlink` - Unlink repository
- `GET /api/repositories/{id}/details` - Get repository details

### Synchronization
- `POST /api/repositories/{id}/sync` - Trigger sync
- `GET /api/repositories/{id}/sync/status` - Get sync status
- `GET /api/repositories/{id}/sync/history` - Get sync history
- `GET /api/repositories/{id}/sync/conflicts` - Get conflicts

### Activity
- `GET /api/activity` - Get user activity
- `GET /api/activity/repository/{id}` - Get repository activity

## Testing

### Running Tests

```bash
make test
```

### Manual Testing Checklist

- [ ] Connect GitLab account
- [ ] Test connection
- [ ] Fetch repositories
- [ ] Link repository to Nextcloud folder
- [ ] Perform initial sync
- [ ] Verify files in Nextcloud
- [ ] Perform subsequent sync
- [ ] View activity history
- [ ] Unlink repository
- [ ] Delete account

## Security Considerations

- **Access Tokens**: Encrypted using Nextcloud's crypto service
- **API Authorization**: All endpoints require authentication
- **CSRF Protection**: Implemented via Nextcloud framework
- **Input Validation**: All user inputs are validated
- **Output Escaping**: Prevents XSS attacks
- **Secure HTTP**: Uses HTTPS for Git provider communication

## Troubleshooting

### Sync Fails

1. Check GitLab token permissions
2. Verify network connectivity
3. Check Nextcloud logs: `docker-compose logs nextcloud`
4. Review activity history for error details

### Cannot Connect Account

1. Verify GitLab URL is correct
2. Check token has required scopes
3. Test connection manually via curl
4. Review browser console for errors

### Build Fails

1. Ensure Node.js and npm are installed
2. Clear node_modules: `rm -rf node_modules && npm install`
3. Check webpack.config.js for errors

### Docker Issues

1. Restart containers: `make docker-down && make docker-up`
2. Check container status: `docker-compose ps`
3. View logs: `make docker-logs`
4. Rebuild: `docker-compose up -d --build`

## Known Limitations

- **GitLab Only**: Currently only GitLab is fully implemented
- **Manual Sync**: Automatic sync scheduling not yet implemented
- **Conflict Resolution**: Basic conflict detection, advanced resolution UI pending
- **Large Repositories**: Very large repositories may timeout during initial sync
- **Binary Files**: Binary files are synced but not optimized

## Roadmap

### Phase 1: Core Features (Current)
- ✅ GitLab integration
- ✅ Repository sync
- ✅ Activity tracking
- ✅ Account management

### Phase 2: Advanced Sync
- ⏳ Conflict resolution UI
- ⏳ Background jobs
- ⏳ Automatic scheduling
- ⏳ Incremental sync

### Phase 3: Documentation
- ⏳ Markdown/AsciiDoc viewer
- ⏳ Documentation suggestions
- ⏳ Review workflow
- ⏳ Direct GitLab commits

### Phase 4: Release Management
- ⏳ Commit history viewer
- ⏳ Release note generation
- ⏳ Tag management
- ⏳ Changelog creation

### Phase 5: Additional Providers
- ⏳ Gitea support
- ⏳ Forgejo support

## Contributing

Contributions are welcome! Please:

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests
5. Submit a pull request

## License

AGPL-3.0-or-later

## Support

- **Issues**: https://github.com/mrunaliKale31/FossTech/issues
- **Documentation**: This README
- **Nextcloud Forum**: https://help.nextcloud.com

## Authors

FossTech Team

## Acknowledgments

- Nextcloud community
- GitLab API documentation
- Vue.js framework
- All contributors
