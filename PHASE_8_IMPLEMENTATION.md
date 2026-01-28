# Phase 8: REST API Implementation Summary

## Overview

Phase 8 adds a complete REST API to the Worlds project with token-based authentication, allowing programmatic access to entity data and search functionality.

## Files Created

### Database Schema

1. **database/015_api_tokens.sql**
   - API tokens table with SHA-256 hashed tokens
   - User association (foreign key to users table)
   - Token name, expiration, and usage tracking
   - Indexes for fast token lookup

### Authentication & Authorization

2. **src/Config/ApiAuth.php**
   - Token authentication via Bearer header
   - Token generation with secure random tokens (64 hex characters)
   - SHA-256 token hashing for secure storage
   - Token expiration checking
   - Rate limiting (100 requests/minute per token)
   - Last used timestamp tracking

### Controllers

3. **src/Controllers/ApiController.php**
   - Base controller for all API endpoints
   - `requireApiAuth()` - Authentication middleware
   - `jsonResponse()` - Success response helper
   - `errorResponse()` - Error response helper
   - `successResponse()` - Standardized data/meta response
   - `validateRequired()` - Field validation
   - `getPagination()` - Extract pagination params

4. **src/Controllers/Api/EntitiesApiController.php**
   - `GET /api/v1/entities` - List entities with pagination
   - `GET /api/v1/entities/{id}` - Get single entity
   - `POST /api/v1/entities` - Create entity
   - `PUT /api/v1/entities/{id}` - Update entity
   - `DELETE /api/v1/entities/{id}` - Delete entity
   - Supports filtering by campaign_id and type
   - JSON and form data support

5. **src/Controllers/Api/SearchApiController.php**
   - `GET /api/v1/search` - Full-text search
   - Uses SQLite FTS5 for fast search
   - Filters by campaign_id and type
   - Pagination support

6. **src/Controllers/ApiTokenController.php**
   - `GET /settings/api-tokens` - List user's tokens
   - `POST /settings/api-tokens` - Create new token
   - `DELETE /settings/api-tokens/{id}` - Revoke token
   - Token displayed only once on creation
   - Expiration options: never, 30 days, 90 days, 1 year

### Views

7. **src/Views/settings/api-tokens.php**
   - Token management interface
   - Create new tokens with name and expiration
   - List existing tokens with metadata
   - Revoke tokens
   - Security best practices guide
   - API documentation snippets
   - Shows token ONLY ONCE after creation

### Routes (Updated)

8. **public/index.php** - Added routes:

**Token Management UI:**
```php
GET    /settings/api-tokens       - List tokens
POST   /settings/api-tokens       - Create token
DELETE /settings/api-tokens/{id}  - Revoke token
```

**REST API Endpoints:**
```php
GET    /api/v1/entities           - List entities
GET    /api/v1/entities/{id}      - Get entity
POST   /api/v1/entities           - Create entity
PUT    /api/v1/entities/{id}      - Update entity
DELETE /api/v1/entities/{id}      - Delete entity
GET    /api/v1/search             - Search entities
```

### Database Migration

9. **database/migrate.sql** - Updated to include:
   - API tokens table schema
   - Required indexes
   - Foreign key constraints

### Navigation

10. **src/Views/partials/header.php** - Updated:
    - Added "API Tokens" link to user menu

### Documentation

11. **API.md** - Complete API documentation:
    - Authentication guide
    - Endpoint reference
    - Request/response examples
    - Error codes
    - Rate limiting
    - Security best practices
    - Code examples (Python, JavaScript)

## Features Implemented

### Token-Based Authentication
- Secure token generation using cryptographically secure random bytes
- SHA-256 hashing for token storage (tokens never stored in plain text)
- Bearer token authentication via Authorization header
- Token expiration support (optional)
- Last used timestamp tracking

### Rate Limiting
- 100 requests per minute per token
- SQLite-based rate limit tracking
- Automatic cleanup of old rate limit entries
- Returns 429 Too Many Requests when exceeded

### API Response Format

**Success Response:**
```json
{
  "data": {...},
  "meta": {
    "page": 1,
    "per_page": 50,
    "total": 100,
    "total_pages": 2
  }
}
```

**Error Response:**
```json
{
  "error": {
    "message": "Error description",
    "code": "ERROR_CODE"
  }
}
```

### HTTP Status Codes
- 200 OK - Success
- 201 Created - Resource created
- 400 Bad Request - Invalid request
- 401 Unauthorized - Invalid/missing token
- 403 Forbidden - Insufficient permissions
- 404 Not Found - Resource not found
- 422 Unprocessable Entity - Validation error
- 429 Too Many Requests - Rate limit exceeded
- 500 Internal Server Error - Server error

### Security Features

1. **Token Security**
   - Tokens hashed with SHA-256 before storage
   - Tokens never retrievable after creation
   - Secure random token generation (64 hex chars)

2. **Rate Limiting**
   - Prevents API abuse
   - Per-token request tracking
   - Configurable limits

3. **CSRF Protection**
   - Token management UI uses CSRF tokens
   - API uses Bearer token authentication (no CSRF needed)

4. **Expiration Support**
   - Optional token expiration dates
   - Automatic expiration checking
   - Expired tokens rejected

5. **Usage Tracking**
   - Last used timestamp
   - Helps identify unused/compromised tokens

## Usage Example

### Creating a Token

1. Navigate to Settings → API Tokens
2. Enter token name (e.g., "Mobile App")
3. Select expiration (Never, 30d, 90d, 1y)
4. Click "Generate Token"
5. Copy the displayed token (shown only once!)

### Using the API

```bash
# List entities
curl -H "Authorization: Bearer YOUR_TOKEN" \
     "https://your-domain.com/api/v1/entities?campaign_id=1&type=character"

# Create entity
curl -X POST \
     -H "Authorization: Bearer YOUR_TOKEN" \
     -H "Content-Type: application/json" \
     -d '{
       "campaign_id": 1,
       "entity_type": "character",
       "name": "Gandalf",
       "type": "Wizard"
     }' \
     "https://your-domain.com/api/v1/entities"

# Search
curl -H "Authorization: Bearer YOUR_TOKEN" \
     "https://your-domain.com/api/v1/search?q=wizard&campaign_id=1"
```

## Testing the Implementation

### Manual Testing Steps

1. **Create a user account**
   ```
   Navigate to /register and create an account
   ```

2. **Create an API token**
   ```
   Login → User Menu → API Tokens → Generate Token
   Copy the token immediately
   ```

3. **Test API endpoints**
   ```bash
   # Replace YOUR_TOKEN with the actual token

   # List entities (requires campaign_id)
   curl -H "Authorization: Bearer YOUR_TOKEN" \
        "http://localhost/api/v1/entities?campaign_id=1"

   # Create entity
   curl -X POST \
        -H "Authorization: Bearer YOUR_TOKEN" \
        -H "Content-Type: application/json" \
        -d '{"campaign_id":1,"entity_type":"character","name":"Test"}' \
        "http://localhost/api/v1/entities"

   # Search
   curl -H "Authorization: Bearer YOUR_TOKEN" \
        "http://localhost/api/v1/search?q=Test&campaign_id=1"
   ```

4. **Test error cases**
   ```bash
   # No token (should return 401)
   curl "http://localhost/api/v1/entities?campaign_id=1"

   # Invalid token (should return 401)
   curl -H "Authorization: Bearer invalid_token" \
        "http://localhost/api/v1/entities?campaign_id=1"

   # Missing required field (should return 400)
   curl -H "Authorization: Bearer YOUR_TOKEN" \
        "http://localhost/api/v1/entities"
   ```

5. **Test token management**
   ```
   - View tokens at /settings/api-tokens
   - Create multiple tokens with different names
   - Revoke a token and verify it no longer works
   - Check "Last Used" timestamps
   ```

## Architecture Decisions

### Why SHA-256 for Tokens?
- Fast hashing suitable for token lookups
- One-way hash prevents token recovery
- Industry standard for API token hashing

### Why Rate Limiting in SQLite?
- Simple implementation without Redis/Memcached
- Good enough for small-to-medium deployments
- Can be upgraded to Redis later if needed

### Why Base ApiController?
- DRY principle - shared authentication/response logic
- Consistent API response format
- Easy to add global API features

### Why Separate Api\ Namespace?
- Clear separation of API vs web controllers
- Easy to version (Api\V2\ later)
- Better organization

## Performance Considerations

- **Token Lookup:** Indexed on token column (fast)
- **Rate Limiting:** Automatic cleanup of old entries
- **Search:** Uses FTS5 full-text index (fast)
- **Pagination:** Supports per_page up to 100

## Future Enhancements

Possible improvements for future phases:

1. **API Versioning** - /api/v2 for breaking changes
2. **Webhooks** - Event notifications
3. **OAuth2** - Third-party app authorization
4. **API Key Scopes** - Limit permissions per token
5. **GraphQL** - Alternative query interface
6. **Bulk Operations** - Create/update multiple entities
7. **Batch Requests** - Multiple requests in one HTTP call
8. **Redis Rate Limiting** - Better performance at scale
9. **API Usage Analytics** - Track endpoint usage
10. **IP Whitelisting** - Restrict tokens to specific IPs

## Compatibility

- **PHP:** 8.0+
- **SQLite:** 3.8.3+ (for FTS5)
- **JSON:** Native PHP JSON extension required

## Files Modified

- public/index.php - Added API routes
- database/migrate.sql - Added API tokens table
- src/Views/partials/header.php - Added API Tokens menu link

## Files Created

- database/015_api_tokens.sql
- src/Config/ApiAuth.php
- src/Controllers/ApiController.php
- src/Controllers/Api/EntitiesApiController.php
- src/Controllers/Api/SearchApiController.php
- src/Controllers/ApiTokenController.php
- src/Views/settings/api-tokens.php
- API.md
- PHASE_8_IMPLEMENTATION.md (this file)

## Migration Notes

To apply this phase:

1. **Run migrations**
   - Migrations auto-run in debug mode
   - Or manually: `sqlite3 database.db < database/015_api_tokens.sql`

2. **No breaking changes**
   - All existing functionality preserved
   - API is additive feature only

3. **No dependencies added**
   - Uses existing PHP/SQLite stack
   - No composer updates needed

## Conclusion

Phase 8 successfully implements a complete REST API with:
- ✅ Token-based authentication
- ✅ Rate limiting
- ✅ Full CRUD operations on entities
- ✅ Full-text search
- ✅ Pagination
- ✅ Proper HTTP status codes
- ✅ Comprehensive error handling
- ✅ Security best practices
- ✅ Complete documentation
- ✅ User-friendly token management UI

The API is production-ready and follows industry standards for REST API design.
