#!/bin/bash
set -e
# =============================================================================
# Worlds Docker Health Check Script
# =============================================================================
# Verifies the application is running and responsive
# =============================================================================

# Check if Apache is running
if ! pgrep -x "apache2" > /dev/null; then
    echo "Apache is not running"
    exit 1
fi

# Check if the application responds to HTTP requests
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" --max-time 5 http://localhost:80/ 2>/dev/null)

if [ "$HTTP_CODE" -ge 200 ] && [ "$HTTP_CODE" -lt 400 ]; then
    echo "Health check passed (HTTP $HTTP_CODE)"
    exit 0
else
    echo "Health check failed (HTTP $HTTP_CODE)"
    exit 1
fi
