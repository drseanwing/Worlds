# Docker Deployment Guide

Complete guide for deploying Worlds using Docker, covering both development and production environments.

## Prerequisites

- **Docker** (v20.10+) - [Install Docker](https://docs.docker.com/get-docker/)
- **Docker Compose** (v2.0+) - Usually included with Docker Desktop

Verify installation:

```bash
docker --version
docker compose version
```

## Quick Start (Development)

### 1. Clone and Build

```bash
git clone <repository-url>
cd Worlds
```

### 2. Start the Application

```bash
docker compose up
```

First build takes 2-3 minutes (downloads base images, compiles CSS). Subsequent starts are fast.

### 3. Access the Application

Open your browser to:

```
http://localhost:8080
```

### 4. Stop the Application

Press `Ctrl+C` or run:

```bash
docker compose down
```

---

## Production Deployment

### Option 1: Using Docker Compose (Recommended)

#### Step 1: Configure Environment

Copy the environment template:

```bash
cp .env.docker .env
```

Edit `.env` with your production settings:

```bash
# .env
HOST_PORT=8080
APP_NAME=Worlds
APP_URL=https://your-domain.com
DEBUG_MODE=false
```

#### Step 2: Build and Deploy

```bash
docker compose -f docker-compose.prod.yml up -d --build
```

#### Step 3: Verify Deployment

Check container health:

```bash
docker compose -f docker-compose.prod.yml ps
docker compose -f docker-compose.prod.yml logs -f
```

### Option 2: Standalone Docker Image

#### Build the Image

```bash
docker build -t worlds:latest .
```

#### Run the Container

```bash
docker run -d \
  --name worlds-app \
  -p 8080:80 \
  -v worlds-data:/var/www/html/data \
  -e APP_NAME=Worlds \
  -e APP_URL=https://your-domain.com \
  -e DEBUG_MODE=false \
  --restart unless-stopped \
  worlds:latest
```

---

## Architecture Overview

### Multi-Stage Build

The Dockerfile uses a three-stage build process:

```
┌─────────────────────────────────────────────────────────────┐
│ Stage 1: frontend-builder (Node.js 20 Alpine)               │
│ - Installs npm dependencies                                  │
│ - Compiles Tailwind CSS (minified for production)           │
└─────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────┐
│ Stage 2: composer-builder (Composer 2)                      │
│ - Installs PHP dependencies                                  │
│ - Optimizes autoloader                                       │
└─────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────┐
│ Stage 3: production (PHP 8.2 + Apache)                      │
│ - Copies compiled assets from Stage 1                        │
│ - Copies vendor directory from Stage 2                       │
│ - Configures Apache with security headers                    │
│ - Sets up healthcheck and entrypoint scripts                 │
└─────────────────────────────────────────────────────────────┘
```

### Container Components

| Component | Description |
|-----------|-------------|
| **Apache 2.4** | Web server with mod_rewrite enabled |
| **PHP 8.2** | With PDO SQLite extension |
| **SQLite** | Embedded database (no external DB required) |
| **Tailwind CSS** | Pre-compiled during build |
| **Alpine.js** | Frontend interactivity |

### Volume Mounts

| Volume | Purpose | Persistence |
|--------|---------|-------------|
| `worlds-data` | Database and uploads | Named volume (persistent) |

---

## Environment Variables

| Variable | Default | Description |
|----------|---------|-------------|
| `HOST_PORT` | `8080` | Port exposed on host machine |
| `APP_NAME` | `Worlds` | Application name in UI |
| `APP_URL` | `http://localhost:8080` | Public URL for links |
| `DEBUG_MODE` | `false` | Enable detailed errors (dev only) |
| `DATABASE_PATH` | `/var/www/html/data/campaign.db` | SQLite database path |
| `UPLOAD_DIR` | `/var/www/html/data/uploads` | File upload directory |

---

## Common Operations

### View Logs

```bash
# Development
docker compose logs -f

# Production
docker compose -f docker-compose.prod.yml logs -f
```

### Access Container Shell

```bash
# Development
docker compose exec web bash

# Production
docker compose -f docker-compose.prod.yml exec worlds bash
```

### Rebuild After Changes

```bash
# Development
docker compose up --build

# Production
docker compose -f docker-compose.prod.yml up -d --build
```

### Backup Database

```bash
# Copy database from container
docker compose exec web cat /var/www/html/data/campaign.db > backup.db

# Or with production compose
docker compose -f docker-compose.prod.yml exec worlds \
  cat /var/www/html/data/campaign.db > backup-$(date +%Y%m%d).db
```

### Restore Database

```bash
# Copy database to container
docker cp backup.db worlds-app:/var/www/html/data/campaign.db
docker compose restart
```

### Check Health Status

```bash
docker inspect --format='{{.State.Health.Status}}' worlds-app
```

---

## Security Considerations

### Production Checklist

- [ ] Set `DEBUG_MODE=false`
- [ ] Use HTTPS with reverse proxy (nginx, Traefik, Caddy)
- [ ] Configure firewall to only expose necessary ports
- [ ] Set up regular database backups
- [ ] Monitor container logs for errors
- [ ] Keep Docker and images updated

### Reverse Proxy Example (nginx)

```nginx
server {
    listen 443 ssl http2;
    server_name your-domain.com;

    ssl_certificate /path/to/cert.pem;
    ssl_certificate_key /path/to/key.pem;

    location / {
        proxy_pass http://localhost:8080;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}

server {
    listen 80;
    server_name your-domain.com;
    return 301 https://$server_name$request_uri;
}
```

---

## Troubleshooting

### Container Won't Start

```bash
# Check logs for errors
docker compose logs web

# Common causes:
# - Port 8080 already in use (change HOST_PORT)
# - Permission issues on data volume
# - Invalid environment variables
```

### Port Already in Use

```bash
# Use a different port
HOST_PORT=9000 docker compose up
```

### Permission Denied Errors

```bash
# Reset volume permissions
docker compose down -v
docker compose up
```

### CSS Not Loading

The CSS is pre-compiled during Docker build. If styles are missing:

```bash
# Rebuild without cache
docker compose build --no-cache
docker compose up
```

### Database Errors

```bash
# Check database file exists
docker compose exec web ls -la /var/www/html/data/

# Check file permissions
docker compose exec web stat /var/www/html/data/campaign.db
```

### Health Check Failing

```bash
# Test health check manually
docker compose exec web /usr/local/bin/healthcheck.sh

# Check Apache is running
docker compose exec web pgrep -x apache2
```

---

## Complete Reset

Remove all containers, volumes, and images:

```bash
docker compose down -v --rmi all
docker compose up --build
```

---

## Docker Commands Reference

```bash
# Start (foreground)
docker compose up

# Start (background)
docker compose up -d

# Stop
docker compose down

# Stop and remove volumes
docker compose down -v

# Rebuild
docker compose up --build

# View running containers
docker ps

# View logs
docker compose logs -f

# Execute command in container
docker compose exec web <command>

# Copy file from container
docker cp container:/path/file ./local-file

# View resource usage
docker stats
```

---

## Files Reference

| File | Purpose |
|------|---------|
| `Dockerfile` | Multi-stage build definition |
| `docker-compose.yml` | Development orchestration |
| `docker-compose.prod.yml` | Production orchestration |
| `.dockerignore` | Build context exclusions |
| `.env.docker` | Environment template |
| `docker/apache.conf` | Apache virtual host config |
| `docker/docker-entrypoint.sh` | Container initialization |
| `docker/healthcheck.sh` | Health check script |

---

## Next Steps

- See [README.md](README.md) for project overview
- See [INSTALL.md](INSTALL.md) for native installation
- See [CONTRIBUTING.md](CONTRIBUTING.md) for development guidelines
