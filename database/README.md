# Database Migrations

This directory contains SQL migration files for the Worlds database schema.

## Migration Files

Migration files are numbered sequentially and should be run in order:

| File | Description |
|------|-------------|
| `001_campaigns.sql` | Campaigns table (world containers) |
| `002_entities.sql` | Entities table (polymorphic entity storage) |
| `003_relations.sql` | Relations table (entity relationships) |
| `004_tags.sql` | Tags table (categorization) |
| `005_entity_tags.sql` | Entity-Tags junction table |
| `006_attributes.sql` | Attributes table (custom key-value fields) |
| `007_posts.sql` | Posts table (entity sub-entries) |
| `008_files.sql` | Files table (file attachments) |
| `009_users.sql` | Users table (authentication) |
| `010_fts.sql` | Full-Text Search (FTS5 virtual table) |
| `011_indexes.sql` | Database indexes for performance |
| `migrate.sql` | Master migration script (all tables combined) |

## Quick Start

To initialize the database, run the master migration script:

```bash
sqlite3 data/campaign.db < database/migrate.sql
```

## Schema Overview

The database uses a polymorphic entity model where all entity types (characters, locations, items, etc.) are stored in a single `entities` table with type-specific data in a JSON `data` column.

### Core Tables

- **campaigns**: World/campaign containers
- **entities**: Unified entity storage with polymorphic design
- **relations**: Bidirectional relationships between entities
- **tags**: Campaign-scoped tags for categorization
- **entity_tags**: Many-to-many junction for entity-tag associations
- **attributes**: Custom key-value fields per entity
- **posts**: Sub-entries/sections within entities
- **files**: File attachment metadata
- **users**: Simple authentication

### Full-Text Search

The `entities_fts` virtual table provides FTS5 full-text search across entity names and descriptions. Triggers automatically keep the FTS index in sync with the entities table.
