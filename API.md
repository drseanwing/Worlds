# Worlds REST API Documentation

## Overview

The Worlds REST API provides programmatic access to your worldbuilding data. All API endpoints require authentication via Bearer token.

**Base URL:** `/api/v1`

## Authentication

### Creating an API Token

1. Log in to your Worlds account
2. Navigate to **Settings → API Tokens** (or visit `/settings/api-tokens`)
3. Click **Generate Token**
4. Provide a name and expiration date
5. Copy the generated token (it will only be shown once!)

### Using Your API Token

Include your API token in the `Authorization` header of every request:

```bash
Authorization: Bearer YOUR_TOKEN_HERE
```

### Example Request

```bash
curl -H "Authorization: Bearer YOUR_TOKEN_HERE" \
     https://your-domain.com/api/v1/entities?campaign_id=1
```

## Rate Limiting

- **Limit:** 100 requests per minute per token
- **Response:** `429 Too Many Requests` when limit exceeded

## Response Format

### Success Response

```json
{
  "data": {
    // Response data
  },
  "meta": {
    "page": 1,
    "per_page": 50,
    "total": 100,
    "total_pages": 2
  }
}
```

### Error Response

```json
{
  "error": {
    "message": "Error description",
    "code": "ERROR_CODE"
  }
}
```

## HTTP Status Codes

| Code | Description |
|------|-------------|
| 200 | Success |
| 201 | Created |
| 400 | Bad Request |
| 401 | Unauthorized |
| 403 | Forbidden |
| 404 | Not Found |
| 422 | Validation Error |
| 429 | Rate Limit Exceeded |
| 500 | Internal Server Error |

## Endpoints

### List Entities

Get a paginated list of entities for a campaign.

**Endpoint:** `GET /api/v1/entities`

**Query Parameters:**
- `campaign_id` (required) - Campaign ID
- `type` (optional) - Filter by entity type (character, location, quest, etc.)
- `page` (optional) - Page number (default: 1)
- `per_page` (optional) - Items per page (default: 50, max: 100)

**Example:**

```bash
curl -H "Authorization: Bearer YOUR_TOKEN" \
     "https://your-domain.com/api/v1/entities?campaign_id=1&type=character&page=1&per_page=25"
```

**Response:**

```json
{
  "data": [
    {
      "id": 1,
      "campaign_id": 1,
      "entity_type": "character",
      "name": "Gandalf",
      "type": "Wizard",
      "entry": "A wise and powerful wizard...",
      "image_path": null,
      "parent_id": null,
      "is_private": 0,
      "data": "{}",
      "created_at": "2024-01-01 12:00:00",
      "updated_at": "2024-01-01 12:00:00"
    }
  ],
  "meta": {
    "page": 1,
    "per_page": 25,
    "total": 100,
    "total_pages": 4
  }
}
```

### Get Single Entity

Retrieve a single entity by ID.

**Endpoint:** `GET /api/v1/entities/{id}`

**Example:**

```bash
curl -H "Authorization: Bearer YOUR_TOKEN" \
     "https://your-domain.com/api/v1/entities/1"
```

**Response:**

```json
{
  "data": {
    "id": 1,
    "campaign_id": 1,
    "entity_type": "character",
    "name": "Gandalf",
    "type": "Wizard",
    "entry": "A wise and powerful wizard...",
    "image_path": null,
    "parent_id": null,
    "is_private": 0,
    "data": "{}",
    "created_at": "2024-01-01 12:00:00",
    "updated_at": "2024-01-01 12:00:00"
  }
}
```

### Create Entity

Create a new entity.

**Endpoint:** `POST /api/v1/entities`

**Content-Type:** `application/json`

**Required Fields:**
- `campaign_id` (integer)
- `entity_type` (string) - character, location, quest, etc.
- `name` (string)

**Optional Fields:**
- `type` (string)
- `entry` (string) - Main content/description
- `image_path` (string)
- `parent_id` (integer)
- `is_private` (integer) - 0 or 1
- `data` (object) - Custom JSON data

**Example:**

```bash
curl -X POST \
     -H "Authorization: Bearer YOUR_TOKEN" \
     -H "Content-Type: application/json" \
     -d '{
       "campaign_id": 1,
       "entity_type": "character",
       "name": "Frodo Baggins",
       "type": "Hobbit",
       "entry": "A young hobbit from the Shire..."
     }' \
     "https://your-domain.com/api/v1/entities"
```

**Response:** `201 Created`

```json
{
  "data": {
    "id": 2,
    "campaign_id": 1,
    "entity_type": "character",
    "name": "Frodo Baggins",
    "type": "Hobbit",
    "entry": "A young hobbit from the Shire...",
    "image_path": null,
    "parent_id": null,
    "is_private": 0,
    "data": "{}",
    "created_at": "2024-01-01 13:00:00",
    "updated_at": "2024-01-01 13:00:00"
  }
}
```

### Update Entity

Update an existing entity.

**Endpoint:** `PUT /api/v1/entities/{id}`

**Content-Type:** `application/json`

**Allowed Fields:**
- `name` (string)
- `type` (string)
- `entry` (string)
- `image_path` (string)
- `parent_id` (integer)
- `is_private` (integer)
- `data` (object)

**Example:**

```bash
curl -X PUT \
     -H "Authorization: Bearer YOUR_TOKEN" \
     -H "Content-Type: application/json" \
     -d '{
       "name": "Frodo Baggins (Updated)",
       "entry": "The Ring-bearer who saved Middle-earth..."
     }' \
     "https://your-domain.com/api/v1/entities/2"
```

**Response:** `200 OK`

```json
{
  "data": {
    "id": 2,
    "campaign_id": 1,
    "entity_type": "character",
    "name": "Frodo Baggins (Updated)",
    "type": "Hobbit",
    "entry": "The Ring-bearer who saved Middle-earth...",
    "image_path": null,
    "parent_id": null,
    "is_private": 0,
    "data": "{}",
    "created_at": "2024-01-01 13:00:00",
    "updated_at": "2024-01-01 14:00:00"
  }
}
```

### Delete Entity

Delete an entity permanently.

**Endpoint:** `DELETE /api/v1/entities/{id}`

**Example:**

```bash
curl -X DELETE \
     -H "Authorization: Bearer YOUR_TOKEN" \
     "https://your-domain.com/api/v1/entities/2"
```

**Response:** `200 OK`

```json
{
  "data": {
    "message": "Entity deleted successfully",
    "id": 2
  }
}
```

### Search Entities

Search entities using full-text search.

**Endpoint:** `GET /api/v1/search`

**Query Parameters:**
- `q` (required) - Search query
- `campaign_id` (optional) - Filter by campaign
- `type` (optional) - Filter by entity type
- `page` (optional) - Page number (default: 1)
- `per_page` (optional) - Items per page (default: 50, max: 100)

**Example:**

```bash
curl -H "Authorization: Bearer YOUR_TOKEN" \
     "https://your-domain.com/api/v1/search?q=wizard&campaign_id=1"
```

**Response:**

```json
{
  "data": [
    {
      "id": 1,
      "campaign_id": 1,
      "entity_type": "character",
      "name": "Gandalf",
      "type": "Wizard",
      "entry": "A wise and powerful wizard...",
      "image_path": null,
      "parent_id": null,
      "is_private": 0,
      "data": "{}",
      "created_at": "2024-01-01 12:00:00",
      "updated_at": "2024-01-01 12:00:00"
    }
  ],
  "meta": {
    "page": 1,
    "per_page": 50,
    "total": 1,
    "total_pages": 1,
    "query": "wizard"
  }
}
```

## Error Codes

| Code | Description |
|------|-------------|
| `VALIDATION_ERROR` | Request validation failed |
| `MISSING_CAMPAIGN_ID` | Required campaign_id parameter missing |
| `MISSING_QUERY` | Required search query parameter missing |
| `INVALID_TYPE` | Invalid entity_type specified |
| `NOT_FOUND` | Requested resource not found |
| `CREATE_FAILED` | Failed to create entity |
| `UPDATE_FAILED` | Failed to update entity |
| `DELETE_FAILED` | Failed to delete entity |
| `SEARCH_FAILED` | Search operation failed |
| `RATE_LIMIT_EXCEEDED` | Too many requests |

## Security Best Practices

1. **Never share your API tokens** - Treat them like passwords
2. **Store tokens securely** - Use environment variables or secure vaults
3. **Use HTTPS** - Always use encrypted connections
4. **Set expiration dates** - Use short-lived tokens when possible
5. **Use different tokens** - Create separate tokens for different applications
6. **Revoke compromised tokens** - Immediately revoke tokens if compromised
7. **Monitor usage** - Check the "Last Used" timestamp regularly

## Examples

### Python

```python
import requests

API_TOKEN = "your_token_here"
BASE_URL = "https://your-domain.com/api/v1"

headers = {
    "Authorization": f"Bearer {API_TOKEN}",
    "Content-Type": "application/json"
}

# List entities
response = requests.get(
    f"{BASE_URL}/entities",
    headers=headers,
    params={"campaign_id": 1, "type": "character"}
)

entities = response.json()
print(entities)

# Create entity
new_entity = {
    "campaign_id": 1,
    "entity_type": "character",
    "name": "Aragorn",
    "type": "Ranger"
}

response = requests.post(
    f"{BASE_URL}/entities",
    headers=headers,
    json=new_entity
)

created = response.json()
print(created)
```

### JavaScript (Node.js)

```javascript
const axios = require('axios');

const API_TOKEN = 'your_token_here';
const BASE_URL = 'https://your-domain.com/api/v1';

const headers = {
  'Authorization': `Bearer ${API_TOKEN}`,
  'Content-Type': 'application/json'
};

// List entities
axios.get(`${BASE_URL}/entities`, {
  headers,
  params: {
    campaign_id: 1,
    type: 'character'
  }
})
.then(response => {
  console.log(response.data);
})
.catch(error => {
  console.error(error.response.data);
});

// Create entity
const newEntity = {
  campaign_id: 1,
  entity_type: 'character',
  name: 'Aragorn',
  type: 'Ranger'
};

axios.post(`${BASE_URL}/entities`, newEntity, { headers })
.then(response => {
  console.log(response.data);
})
.catch(error => {
  console.error(error.response.data);
});
```

## Support

For issues or questions about the API, please contact support or file an issue on GitHub.
