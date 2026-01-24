# Data Directory

This directory contains the SQLite database and uploaded files.

## Structure

- **uploads/** - User-uploaded files (images, attachments)
- **campaign.db** - SQLite database (created at runtime)

## Directory Permissions

### Required Permissions

This directory and its subdirectories must be **writable** by the web server user to function correctly.

#### Data Directory
- **Path:** `data/`
- **Required permissions:** Read, Write, Execute (755 or 775)
- **Purpose:** Allows creation of the SQLite database file

#### Uploads Directory
- **Path:** `data/uploads/`
- **Required permissions:** Read, Write, Execute (755 or 775)
- **Purpose:** Allows storage of user-uploaded files (images, attachments)

### Setting Permissions

#### Using chmod (Linux/Mac)
```bash
# From project root
chmod -R 755 data/
```

#### Using Docker
```bash
# If running in Docker, ensure the container user has write access
# Add to docker-compose.yml or Dockerfile:
# volumes:
#   - ./data:/var/www/html/data:rw
```

#### Verification
To verify permissions are correct:
```bash
# Check current permissions
ls -la data/
ls -la data/uploads/

# Test write access
touch data/test.txt && rm data/test.txt
touch data/uploads/test.txt && rm data/uploads/test.txt
```

### Security Notes

1. **Never make data directory world-writable (777)** - This is a security risk
2. **Database file should not be web-accessible** - Ensure `.htaccess` or nginx config blocks direct access
3. **Uploads should be validated** - Always validate file types and sizes before accepting uploads
