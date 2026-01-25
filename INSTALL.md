# Installation Guide

This guide walks you through setting up the Worlds application for local development.

## Prerequisites

Before installing Worlds, ensure you have the following installed on your system:

- **PHP 8.0 or higher** - The application requires PHP 8.0 or later
- **Composer** - PHP dependency manager ([install from composer.org](https://getcomposer.org))
- **Node.js 16+** and **npm** - For building frontend assets ([install from nodejs.org](https://nodejs.org))
- **Git** - For cloning the repository

## Installation Steps

### 1. Clone the Repository

```bash
git clone https://github.com/drseanwing/worlds.git
cd Worlds
```

### 2. Install PHP Dependencies

Use Composer to install all PHP dependencies:

```bash
composer install
```

This will create a `vendor/` directory with all required PHP packages.

### 3. Install Node.js Dependencies

Use npm to install frontend dependencies:

```bash
npm install
```

This will create a `node_modules/` directory with Tailwind CSS, Alpine.js, and other frontend packages.

### 4. Set Up Environment Configuration

Copy the example environment file to create your local configuration:

```bash
cp .env.example .env
```

Edit the `.env` file to configure your application:

```
# Database Configuration
DATABASE_PATH=data/campaign.db

# Debug Mode
DEBUG_MODE=false

# Upload Directory
UPLOAD_DIR=data/uploads

# Application Settings
APP_NAME=Worlds
APP_URL=http://localhost:8080
```

**For development**, you may want to set `DEBUG_MODE=true` to see detailed error messages.

### 5. Build Frontend Assets

Compile Tailwind CSS for the application UI:

```bash
npm run build:css
```

For development with file watching, use:

```bash
npm run watch:css
```

This will automatically recompile CSS whenever you modify files.

### 6. Create Data Directory and Set Permissions

The application needs write access to the data directory for database and uploads:

```bash
mkdir -p data/uploads
chmod 755 data
chmod 755 data/uploads
```

The database file will be created automatically at `data/campaign.db` when you first run the application.

### 7. Run Tests (Optional)

Verify the installation by running the test suite:

```bash
composer run test
```

## Starting Development

### Using Built-in PHP Server

Start a local development server:

```bash
php -S localhost:8080 -t public/
```

The application will be accessible at `http://localhost:8080`

### Using Docker (Alternative)

If you prefer Docker, see [DOCKER.md](DOCKER.md) for setup instructions.

## Project Structure

After installation, your project structure should look like:

```
Worlds/
├── src/                    # PHP source code
│   ├── Controllers/        # HTTP request handlers
│   ├── Models/             # Data models and business logic
│   ├── Repositories/       # Database access layer
│   ├── Views/              # Template files (HTML)
│   └── Config/             # Configuration classes and schemas
├── public/                 # Web-accessible files
│   ├── assets/
│   │   ├── css/           # Compiled CSS output
│   │   └── js/            # JavaScript files (Alpine.js)
│   └── index.php          # Front controller
├── data/                   # Application data (not in Git)
│   ├── uploads/           # User-uploaded files
│   └── campaign.db        # SQLite database
├── vendor/                # PHP dependencies (Composer)
├── node_modules/          # JavaScript dependencies (npm)
├── database/              # SQL migration files
├── tests/                 # Test files
├── documentation/         # Project documentation
├── .env                   # Local environment configuration
├── composer.json          # PHP dependencies manifest
└── package.json           # JavaScript dependencies manifest
```

## Troubleshooting

### Composer Issues

If you encounter Composer dependency issues:

```bash
# Clear Composer cache
composer clear-cache

# Reinstall dependencies
composer install --no-cache
```

### npm Issues

If npm installation fails:

```bash
# Clear npm cache
npm cache clean --force

# Reinstall dependencies
npm install
```

### CSS Not Building

If `npm run build:css` fails:

```bash
# Reinstall Tailwind CSS
npm install -D tailwindcss

# Try building again
npm run build:css
```

### Database Permission Errors

If you get permission errors when accessing the database:

```bash
# Fix data directory permissions
chmod 755 data data/uploads

# Remove and recreate database
rm -f data/campaign.db
```

### PHP Version Issues

Verify your PHP version:

```bash
php --version
```

Must be PHP 8.0 or higher. If you have multiple PHP versions installed, ensure you're using the correct one by specifying the full path, e.g., `/usr/bin/php8.1 --version`.

## Next Steps

- Read the [README.md](README.md) for project overview
- Check [CONTRIBUTING.md](CONTRIBUTING.md) for development guidelines
- Review entity schemas in [documentation/entity-schemas.md](documentation/entity-schemas.md)
- See the [task list](documentation/kanka-task-list.md) for development progress

## Support

For issues or questions:

1. Check existing [GitHub issues](https://github.com/drseanwing/worlds/issues)
2. Review [CONTRIBUTING.md](CONTRIBUTING.md) for development setup
3. Consult [DOCKER.md](DOCKER.md) if using Docker
