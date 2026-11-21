# Changelog

All notable changes to the Nextcloud Git Integration project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2024-01-01

### Added
- Initial release of Nextcloud Git Integration
- GitLab provider integration
  - Connect GitLab.com and self-hosted instances
  - Secure credential storage with encryption
  - Connection testing functionality
- Account management
  - Add, edit, and remove GitLab accounts
  - Multiple account support
  - Account status monitoring
- Repository management
  - Browse accessible GitLab repositories
  - Link repositories to Nextcloud folders
  - Repository details and metadata display
  - Unlink repository functionality
- Repository synchronization
  - Initial sync from GitLab to Nextcloud
  - Manual sync trigger
  - Sync progress and status tracking
  - File addition and modification tracking
  - Sync history with detailed statistics
- Activity logging
  - Comprehensive activity tracking
  - User activity history
  - Repository-specific activity logs
  - Success and failure tracking with error messages
- User interface
  - Modern, Nextcloud-compatible design
  - Dashboard with repository overview and statistics
  - Account management interface
  - Repository browser and linking wizard
  - Activity history viewer
  - Responsive layout
- Database schema
  - Accounts table with encrypted credentials
  - Repositories table with sync metadata
  - Sync records table
  - Activity logs table
  - Suggestions table (for future use)
- Docker development environment
  - Nextcloud container
  - PostgreSQL database
  - Adminer for database management
  - Persistent volumes
- Build system
  - Makefile with common tasks
  - Webpack frontend build
  - Composer PHP dependency management
  - npm package management
- Documentation
  - Comprehensive README
  - Installation instructions
  - Configuration guide
  - Development workflow
  - API documentation
  - Troubleshooting guide

### Security
- Encrypted storage of Personal Access Tokens using Nextcloud crypto service
- CSRF protection via Nextcloud framework
- Input validation on all API endpoints
- Secure HTTP communication with Git providers
- Access control for all API operations

## [Unreleased]

### Planned Features
- Automatic sync scheduling with configurable intervals
- Advanced conflict detection and resolution UI
- Background job processing for long-running syncs
- Markdown and AsciiDoc documentation viewer
- Documentation suggestion and approval workflow
- Commit to GitLab functionality
- Commit history browser
- Automated release note generation from commits
- Tag and release management
- Branch switching and creation
- Gitea provider implementation
- Forgejo provider implementation
- Incremental sync optimization
- Large file handling improvements
- Notification system for sync events
- Settings page with configuration options
- Multi-language support
- Unit and integration tests
- Performance optimizations

### Known Issues
- Very large repositories may timeout during initial sync
- Binary files not optimized for sync
- Conflict resolution requires manual intervention
- Sync is manual only (no automatic scheduling yet)
