# =============================================================================
# Worlds - Multi-Stage Docker Build
# =============================================================================
# This Dockerfile creates a production-ready single image containing:
# - PHP 8.2 with Apache
# - Pre-built Tailwind CSS assets
# - All application dependencies
# =============================================================================

# -----------------------------------------------------------------------------
# Stage 1: Node.js - Build Frontend Assets (Tailwind CSS)
# -----------------------------------------------------------------------------
FROM node:20-alpine AS frontend-builder

WORKDIR /build

# Copy package files first for better layer caching
COPY package.json package-lock.json* ./

# Install dependencies
RUN npm ci --include=dev

# Copy Tailwind configuration and source files
COPY tailwind.config.js postcss.config.js ./
COPY public/assets/css/input.css ./public/assets/css/
COPY src/Views/ ./src/Views/

# Build production CSS
RUN npm run build:css:prod

# -----------------------------------------------------------------------------
# Stage 2: Composer - Install PHP Dependencies
# -----------------------------------------------------------------------------
FROM composer:2 AS composer-builder

WORKDIR /build

# Copy composer files
COPY composer.json composer.lock* ./

# Install production dependencies only
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader

# -----------------------------------------------------------------------------
# Stage 3: Production Image - PHP 8.2 + Apache
# -----------------------------------------------------------------------------
FROM php:8.2-apache AS production

# Labels for image metadata
LABEL maintainer="Sean Wing"
LABEL description="Worlds - Lightweight worldbuilding and RPG campaign management"
LABEL version="1.0.0"

# Environment variables with defaults
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public \
    DATABASE_PATH=/var/www/html/data/campaign.db \
    DEBUG_MODE=false \
    UPLOAD_DIR=/var/www/html/data/uploads \
    APP_NAME=Worlds \
    APP_URL=http://localhost:8080 \
    AUTO_MIGRATE=true

# Install system dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    libsqlite3-dev \
    curl \
    && rm -rf /var/lib/apt/lists/* \
    && apt-get clean

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_sqlite

# Enable Apache modules
RUN a2enmod rewrite headers

# Configure Apache document root
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Copy custom Apache configuration
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Set working directory
WORKDIR /var/www/html

# Copy application source code
COPY --chown=www-data:www-data . /var/www/html

# Copy built CSS from frontend stage
COPY --from=frontend-builder --chown=www-data:www-data /build/public/assets/css/output.css /var/www/html/public/assets/css/output.css

# Copy vendor directory from composer stage
COPY --from=composer-builder --chown=www-data:www-data /build/vendor /var/www/html/vendor

# Copy entrypoint and healthcheck scripts
COPY --chmod=755 docker/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
COPY --chmod=755 docker/healthcheck.sh /usr/local/bin/healthcheck.sh

# Create data directories and set permissions
RUN mkdir -p /var/www/html/data/uploads \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/data

# Expose HTTP port
EXPOSE 80

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=5s --retries=3 \
    CMD /usr/local/bin/healthcheck.sh

# Entrypoint for initialization
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]

# Start Apache in foreground
CMD ["apache2-foreground"]
