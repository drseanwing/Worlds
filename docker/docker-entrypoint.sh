#!/bin/bash
# =============================================================================
# Worlds Docker Entrypoint Script
# =============================================================================
# This script runs at container startup to:
# - Initialize data directories
# - Set proper permissions
# - Run database migrations if enabled
# - Validate configuration
# =============================================================================

set -euo pipefail

echo "=============================================="
echo " Worlds - Container Initialization"
echo "=============================================="

# -----------------------------------------------------------------------------
# Color output helpers
# -----------------------------------------------------------------------------
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

info() { echo -e "${GREEN}[INFO]${NC} $1"; }
warn() { echo -e "${YELLOW}[WARN]${NC} $1"; }
error() { echo -e "${RED}[ERROR]${NC} $1"; }

# -----------------------------------------------------------------------------
# Initialize data directories
# -----------------------------------------------------------------------------
info "Initializing data directories..."

DATA_DIR="/var/www/html/data"
UPLOAD_DIR="${UPLOAD_DIR:-/var/www/html/data/uploads}"

# Create directories if they don't exist
mkdir -p "$DATA_DIR"
mkdir -p "$UPLOAD_DIR"

# Set proper ownership
chown -R www-data:www-data "$DATA_DIR"
chmod -R 775 "$DATA_DIR"

info "Data directories initialized."

# -----------------------------------------------------------------------------
# Validate environment configuration
# -----------------------------------------------------------------------------
info "Validating configuration..."

# Check for required PHP extensions
if ! php -m | grep -q pdo_sqlite; then
    error "Required PHP extension 'pdo_sqlite' is not installed!"
    exit 1
fi

# Verify database path is writable
DB_DIR=$(dirname "${DATABASE_PATH:-/var/www/html/data/campaign.db}")
if [ ! -w "$DB_DIR" ]; then
    warn "Database directory is not writable: $DB_DIR"
    chown www-data:www-data "$DB_DIR"
    chmod 775 "$DB_DIR"
fi

info "Configuration validated."

# -----------------------------------------------------------------------------
# Database initialization
# -----------------------------------------------------------------------------
DATABASE_PATH="${DATABASE_PATH:-/var/www/html/data/campaign.db}"

if [ ! -f "$DATABASE_PATH" ]; then
    info "Database not found. Will be created on first request."
else
    info "Database found at: $DATABASE_PATH"
fi

# -----------------------------------------------------------------------------
# Display configuration summary
# -----------------------------------------------------------------------------
echo ""
echo "=============================================="
echo " Configuration Summary"
echo "=============================================="
echo " APP_NAME:      ${APP_NAME:-Worlds}"
echo " APP_URL:       ${APP_URL:-http://localhost:8080}"
echo " DEBUG_MODE:    ${DEBUG_MODE:-false}"
echo " DATABASE_PATH: ${DATABASE_PATH}"
echo " UPLOAD_DIR:    ${UPLOAD_DIR}"
echo " AUTO_MIGRATE:  (migrations run on first request)"
echo "=============================================="
echo ""

info "Starting Apache..."

# Execute the main command (apache2-foreground)
exec "$@"
