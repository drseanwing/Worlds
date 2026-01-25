# Worlds

A lightweight worldbuilding and RPG campaign management tool, inspired by Kanka.

## Tech Stack

- **Backend:** PHP 8 + SQLite
- **Frontend:** Alpine.js + Tailwind CSS + HTMX
- **Architecture:** Polymorphic entity model with JSON data fields

## Project Structure

```
Worlds/
├── src/                    # PHP source code
│   ├── Controllers/        # HTTP request handlers
│   ├── Models/            # Data models and business logic
│   ├── Repositories/      # Database access layer
│   ├── Views/             # Template files
│   └── Config/            # Configuration classes
├── public/                # Web-accessible files
│   ├── assets/
│   │   ├── css/          # Compiled CSS files
│   │   └── js/           # JavaScript files
│   └── index.php         # Front controller (to be created)
├── data/                  # Application data (excluded from Git)
│   ├── uploads/          # User-uploaded files
│   └── campaign.db       # SQLite database (created at runtime)
├── database/             # SQL migration files
├── tests/                # Test files
└── documentation/        # Project documentation
    ├── kanka-task-list.md           # Development task list
    └── kanka-lightweight-analysis.md # Architecture analysis
```

## Requirements

- PHP 8.0 or higher
- Composer
- Node.js & npm (for building CSS)
- Web server (Apache, Nginx, or PHP built-in server)

## Installation

1. **Clone the repository:**
   ```bash
   git clone <repository-url>
   cd Worlds
   ```

2. **Install PHP dependencies:**
   ```bash
   composer install
   ```

3. **Install frontend dependencies and build assets:**
   ```bash
   npm install
   npm run build
   ```

4. **Configure environment (optional):**
   Create a `.env` file in the project root:
   ```
   APP_NAME=Worlds
   DEBUG=false
   AUTO_MIGRATE=true
   ```

5. **Set up data directory permissions:**
   ```bash
   mkdir -p data/uploads
   chmod 755 data data/uploads
   ```

## Development

Start the PHP built-in server:
```bash
php -S localhost:8000 -t public
```

For CSS development with hot reload:
```bash
npm run dev
```

Run tests:
```bash
composer test
```

## Deployment

### Apache

1. Point your virtual host document root to the `public/` directory.

2. Ensure `mod_rewrite` is enabled and `.htaccess` files are allowed:
   ```apache
   <VirtualHost *:80>
       ServerName worlds.example.com
       DocumentRoot /path/to/Worlds/public

       <Directory /path/to/Worlds/public>
           AllowOverride All
           Require all granted
       </Directory>
   </VirtualHost>
   ```

3. Create `public/.htaccess`:
   ```apache
   RewriteEngine On
   RewriteCond %{REQUEST_FILENAME} !-f
   RewriteCond %{REQUEST_FILENAME} !-d
   RewriteRule ^ index.php [L]
   ```

### Nginx

```nginx
server {
    listen 80;
    server_name worlds.example.com;
    root /path/to/Worlds/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(ht|git) {
        deny all;
    }
}
```

### Production Checklist

- [ ] Set `DEBUG=false` in `.env`
- [ ] Run `composer install --no-dev --optimize-autoloader`
- [ ] Run `npm run build` for minified CSS
- [ ] Ensure `data/` directory is writable by the web server
- [ ] Set appropriate file permissions (755 for directories, 644 for files)
- [ ] Configure your web server to deny access to sensitive files (`.env`, `composer.json`, etc.)
- [ ] Set up HTTPS with a valid SSL certificate

### Database

The SQLite database is automatically created at `data/campaign.db` on first run. Migrations run automatically when `AUTO_MIGRATE=true` or in debug mode.

For manual migration:
```bash
php -r "require 'vendor/autoload.php'; \Worlds\Config\Database::runMigrations();"
```

## Getting Started

See the [task list](documentation/kanka-task-list.md) for development progress.

## License

TBD