# Docker Setup Guide

Run Worlds using Docker and Docker Compose for consistent development across different machines.

## Prerequisites

- **Docker** - [Install Docker Desktop](https://www.docker.com/products/docker-desktop)
- **Docker Compose** - Usually included with Docker Desktop

Verify installation:

```bash
docker --version
docker-compose --version
```

## Quick Start

### 1. Start the Application

From the project root directory, run:

```bash
docker-compose up
```

The first run will build the Docker image (may take 1-2 minutes). Subsequent runs start much faster.

### 2. Access the Application

Once the containers are running, the application is available at:

```
http://localhost:8080
```

### 3. Stop the Application

Press `Ctrl+C` in your terminal, or in another terminal run:

```bash
docker-compose down
```

## Docker Compose Configuration

The `docker-compose.yml` file defines the application setup:

```yaml
version: '3.8'

services:
  web:
    build:
      context: .
      dockerfile: Dockerfile
    container_name: worlds-web
    ports:
      - "8080:80"
    volumes:
      - .:/var/www/html
      - worlds-data:/var/www/html/data
      - /var/www/html/vendor
      - /var/www/html/node_modules
    environment:
      - DATABASE_PATH=/var/www/html/data/campaign.db
      - DEBUG_MODE=true
      - UPLOAD_DIR=/var/www/html/data/uploads
    restart: unless-stopped

volumes:
  worlds-data:
    driver: local
```

## Key Docker Components

### Container Configuration

- **Container Name**: `worlds-web`
- **Port Mapping**: `8080:80` (external:internal)
- **Web Server**: Apache running inside the container
- **Auto-restart**: Container restarts unless manually stopped

### Volumes (Persistent Storage)

Volumes ensure data persists even when containers are removed:

| Volume | Purpose |
|--------|---------|
| `.:/var/www/html` | Mounts entire project for live development (source code changes instantly visible) |
| `worlds-data:/var/www/html/data` | Persists application data (database, uploads) across container restarts |
| `/var/www/html/vendor` | Excludes Composer dependencies from host sync (uses container version) |
| `/var/www/html/node_modules` | Excludes npm dependencies from host sync (uses container version) |

### Environment Variables

The container runs with these environment variables:

```
DATABASE_PATH=/var/www/html/data/campaign.db
DEBUG_MODE=true
UPLOAD_DIR=/var/www/html/data/uploads
```

These override the `.env` file settings inside the container.

## Common Docker Tasks

### View Container Logs

```bash
# Show recent logs
docker-compose logs

# Follow logs in real-time
docker-compose logs -f

# Show logs from specific service
docker-compose logs web
```

### Access the Container Shell

```bash
docker-compose exec web bash
```

Inside the container shell, you can:

```bash
# Run Composer commands
composer install
composer test

# Run npm commands
npm install
npm run build:css

# Manage PHP/MySQL directly
php -v
```

### Rebuild the Docker Image

If you modify `Dockerfile`:

```bash
docker-compose build --no-cache
```

Then restart:

```bash
docker-compose up
```

### Remove All Docker Data

To completely reset (warning: deletes everything):

```bash
docker-compose down -v
```

The `-v` flag removes all volumes. Next `docker-compose up` will create fresh volumes.

### Run One-Time Commands

Execute a command without starting full services:

```bash
# Run tests
docker-compose run web composer test

# Install dependencies
docker-compose run web composer install

# Build CSS
docker-compose run web npm run build:css
```

## Development Workflow with Docker

### Making Code Changes

1. Edit files in your IDE on your host machine
2. Changes are instantly visible in the running container (due to volume mount)
3. Refresh your browser to see updates

### Database Persistence

The database is stored in the `worlds-data` volume:

```bash
# The database file is located at
data/campaign.db

# On your host machine (for backup)
docker-compose exec web cp data/campaign.db data/campaign.db.backup
```

### Uploading Files

User-uploaded files are stored in:

```
data/uploads/
```

This directory persists across container restarts via the `worlds-data` volume.

## Dockerfile Details

The application uses a multi-stage build:

```dockerfile
# Base image: PHP 8.1 with Apache
FROM php:8.1-apache

# Install dependencies
RUN docker-php-ext-install pdo pdo_sqlite

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set document root
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# Copy project files
COPY . /var/www/html/
WORKDIR /var/www/html

# Install Composer and dependencies
RUN curl -sS https://getcomposer.org/installer | php && \
    php composer.phar install && \
    rm composer.phar

# Install Node.js and build assets
RUN curl -fsSL https://deb.nodesource.com/setup_16.x | bash && \
    apt-get install -y nodejs && \
    npm install && \
    npm run build:css
```

## Troubleshooting

### Port Already in Use

If port 8080 is already in use:

```bash
# Change port in docker-compose.yml
ports:
  - "8081:80"  # Use 8081 instead of 8080

# Then restart
docker-compose up
```

### Container Exits Immediately

Check logs:

```bash
docker-compose logs web
```

Common causes:
- PHP errors in configuration
- Permission issues on mounted volumes
- Missing environment variables

### Volume Sync Not Working

On Mac/Windows with Docker Desktop:

```bash
# Restart Docker
docker-compose down
docker system prune -a
docker-compose up
```

### Build Failures

If the Docker image won't build:

```bash
# Clear build cache and rebuild
docker-compose build --no-cache --pull

# Check for Dockerfile syntax errors
docker build --progress=plain .
```

### Accessing Uploaded Files

Files uploaded through the web interface are in:

```bash
# View files from host
ls -la data/uploads/

# View files from inside container
docker-compose exec web ls -la /var/www/html/data/uploads/
```

## Production Considerations

This Docker setup is optimized for **development**. For production:

1. Set `DEBUG_MODE=false`
2. Use environment-specific configuration
3. Set up proper logging
4. Configure health checks
5. Use a production-grade database (PostgreSQL, MySQL)
6. Implement proper backup strategies
7. Use environment secrets instead of `.env` files
8. Configure HTTPS/SSL
9. Set up monitoring and alerting

See documentation or contact the maintainer for production deployment guidance.

## Useful Docker Commands Reference

```bash
# Start services in background
docker-compose up -d

# Stop services
docker-compose down

# View running containers
docker ps

# View all containers (including stopped)
docker ps -a

# View container resource usage
docker stats

# Clean up unused Docker resources
docker system prune

# Inspect a specific container
docker inspect worlds-web

# Copy file from container to host
docker cp worlds-web:/var/www/html/file.txt ./file.txt

# Copy file from host to container
docker cp ./file.txt worlds-web:/var/www/html/file.txt
```

## Next Steps

- See [INSTALL.md](INSTALL.md) for native installation (without Docker)
- Review [README.md](README.md) for project overview
- Check [CONTRIBUTING.md](CONTRIBUTING.md) for development guidelines
