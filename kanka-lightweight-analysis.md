# Kanka Lightweight Alternative Analysis

## Executive Summary

Kanka is a feature-rich worldbuilding and RPG campaign management tool with 12,000+ commits of development history. While comprehensive, its architecture reflects enterprise-scale complexity that may be overkill for personal or small-team use. This analysis examines whether a lightweight reproduction is feasible.

**Verdict:** Yes, a significantly lighter implementation is achievable by reducing the stack from 8+ services to 2, eliminating ~70% of dependencies, and focusing on core entity management with modern tooling.

---

## Current Kanka Stack Analysis

### Backend Architecture

| Component | Technology | Purpose |
|-----------|------------|---------|
| Framework | Laravel 10 (PHP 8.0+) | Full MVC framework with extensive features |
| Database | MySQL/MariaDB | Relational data storage |
| Cache | Redis | Session/query caching |
| Queue | Redis/Database | Background job processing |
| File Storage | Minio (S3-compatible) | Image/file uploads |
| Search | Meilisearch | Full-text search engine |
| Dev Environment | Laravel Sail (Docker) | 6+ containers for local dev |
| Admin | Voyager | Admin panel package |

### Frontend Architecture

| Component | Technology | Notes |
|-----------|------------|-------|
| JavaScript | Vue.js + jQuery | Mixed paradigms (legacy migration) |
| CSS Framework | Tailwind + Bootstrap | Transitioning from Bootstrap |
| Build Tool | Vite (was Webpack Mix) | Asset compilation |
| Preprocessor | SCSS | Styling |

### Infrastructure Dependencies

```
Docker Containers (Development):
├── PHP/Laravel (Sail)
├── MySQL 8
├── Redis
├── Meilisearch
├── Minio
├── Mailpit (email testing)
└── Selenium (testing)
```

### Entity Types (~20 modules)

1. **Characters** - People/NPCs with traits, relations, organisations
2. **Locations** - Places with nested hierarchy
3. **Families** - Lineages with family trees
4. **Organisations** - Factions, guilds, groups
5. **Items** - Objects, artifacts, equipment
6. **Notes** - Generic text entries
7. **Events** - Historical occurrences
8. **Calendars** - Custom calendar systems with moons, seasons
9. **Races** - Species/ancestries
10. **Quests** - Adventures with objectives
11. **Journals** - Session logs, diaries
12. **Tags** - Cross-entity categorisation
13. **Maps** - Interactive map layers
14. **Timelines** - Visual event sequences
15. **Abilities** - Powers, spells, skills
16. **Conversations** - Dialogue records
17. **Dice Rolls** - Random generators
18. **Attribute Templates** - Reusable field sets

### Core Features (Shared Across Entities)

- **@mentions system** - Wiki-style entity linking
- **Relations** - Bidirectional entity connections
- **Nested entities** - Parent-child hierarchies
- **Posts** - Sub-entries within entities
- **Attributes** - Custom key-value fields
- **Permissions** - Granular visibility control
- **Tags** - Multi-tag support
- **Files/Images** - Attachments per entity
- **Inventory** - Item management on any entity
- **Abilities** - Attachable powers/effects

---

## Complexity Assessment

### What Makes Kanka Heavy

1. **Multi-tenancy at scale** - Built for thousands of campaigns with isolated data
2. **Premium feature gating** - Subscriber-only features add conditional logic everywhere
3. **Legacy code** - 12k commits means accumulated technical debt (jQuery + Vue mix)
4. **Translation system** - 20+ language support with translation management
5. **Marketplace/plugins** - Theme and content pack ecosystem
6. **API** - Full REST API for third-party integrations
7. **Real-time features** - Collaborative editing considerations
8. **Complex permissions** - Role-based access at campaign, entity, and attribute levels

### Lines of Code Estimate

| Category | Approximate |
|----------|-------------|
| PHP (Controllers, Models, Services) | ~150,000 |
| Blade Templates | ~50,000 |
| JavaScript/Vue | ~30,000 |
| Migrations | ~200 files |
| **Total** | ~230,000+ LOC |

---

## Lightweight Alternative Proposal

### Design Philosophy

> Build for a single user or small group, not for SaaS scale. Trade features for simplicity.

### Proposed Stack

| Component | Technology | Rationale |
|-----------|------------|-----------|
| Backend | **SQLite** + **PHP (no framework)** or **Node.js** | Zero-config database, minimal runtime |
| Alternative | **Astro** + **SQLite** | Static-first with dynamic islands |
| Frontend | **Alpine.js** + **Tailwind** | Lightweight reactivity, no build step possible |
| Storage | **Filesystem** | Local images, no S3 needed |
| Search | **SQLite FTS5** | Built-in full-text search |
| Auth | **Single user** or **simple session** | No OAuth complexity |

### Architecture Options

#### Option A: Static-First (Astro + SQLite)
```
Tech Stack:
├── Astro (SSG with islands)
├── SQLite (embedded database)
├── Alpine.js (interactivity)
├── Tailwind CSS (styling)
└── Node.js runtime
```

**Pros:** Fast, SEO-friendly, minimal JS, easy deployment  
**Cons:** Requires rebuild for data changes (or API routes)

#### Option B: SPA with Lightweight Backend
```
Tech Stack:
├── React/Preact or Vue 3 (frontend)
├── Hono/Fastify (API server)
├── SQLite/better-sqlite3 (database)
├── Tailwind CSS (styling)
└── Vite (build tool)
```

**Pros:** Rich interactivity, real-time capable  
**Cons:** More JavaScript, slightly heavier

#### Option C: PHP Minimal (Recommended for your stack)
```
Tech Stack:
├── PHP 8+ (no framework, or Slim/Lumen)
├── SQLite (database)
├── Alpine.js (frontend interactivity)
├── Tailwind CSS (styling)
└── Optional: HTMX for dynamic updates
```

**Pros:** Familiar stack, minimal dependencies, easy VPS deployment  
**Cons:** Less "modern" but highly practical

### Recommended: Option C (PHP Minimal)

Given your existing PHP/WordPress experience and VPS deployment preference, this option maximises familiarity while dramatically reducing complexity.

---

## Simplified Data Model

### Core Schema (SQLite)

```sql
-- Campaigns (world containers)
CREATE TABLE campaigns (
    id INTEGER PRIMARY KEY,
    name TEXT NOT NULL,
    description TEXT,
    settings JSON,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME
);

-- Unified entity table (polymorphic approach)
CREATE TABLE entities (
    id INTEGER PRIMARY KEY,
    campaign_id INTEGER NOT NULL,
    entity_type TEXT NOT NULL, -- 'character', 'location', 'item', etc.
    name TEXT NOT NULL,
    type TEXT,                 -- User-defined subtype (e.g., "NPC", "City")
    entry TEXT,                -- Main description (markdown)
    image_path TEXT,
    parent_id INTEGER,         -- Self-referential for nesting
    is_private INTEGER DEFAULT 0,
    data JSON,                 -- Type-specific fields
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME,
    FOREIGN KEY (campaign_id) REFERENCES campaigns(id),
    FOREIGN KEY (parent_id) REFERENCES entities(id)
);

-- Full-text search
CREATE VIRTUAL TABLE entities_fts USING fts5(
    name, entry, content='entities', content_rowid='id'
);

-- Relations between entities
CREATE TABLE relations (
    id INTEGER PRIMARY KEY,
    source_id INTEGER NOT NULL,
    target_id INTEGER NOT NULL,
    relation TEXT,             -- "ally", "enemy", "parent", etc.
    mirror_relation TEXT,      -- Reverse label
    description TEXT,
    FOREIGN KEY (source_id) REFERENCES entities(id),
    FOREIGN KEY (target_id) REFERENCES entities(id)
);

-- Tags
CREATE TABLE tags (
    id INTEGER PRIMARY KEY,
    campaign_id INTEGER NOT NULL,
    name TEXT NOT NULL,
    colour TEXT,
    FOREIGN KEY (campaign_id) REFERENCES campaigns(id)
);

CREATE TABLE entity_tags (
    entity_id INTEGER,
    tag_id INTEGER,
    PRIMARY KEY (entity_id, tag_id)
);

-- Custom attributes
CREATE TABLE attributes (
    id INTEGER PRIMARY KEY,
    entity_id INTEGER NOT NULL,
    name TEXT NOT NULL,
    value TEXT,
    is_private INTEGER DEFAULT 0,
    FOREIGN KEY (entity_id) REFERENCES entities(id)
);

-- Posts (sub-entries)
CREATE TABLE posts (
    id INTEGER PRIMARY KEY,
    entity_id INTEGER NOT NULL,
    name TEXT,
    entry TEXT,
    is_private INTEGER DEFAULT 0,
    position INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (entity_id) REFERENCES entities(id)
);
```

### JSON Data Field Examples

```json
// Character
{
    "age": "34",
    "gender": "Female",
    "pronouns": "she/her",
    "title": "Captain",
    "location_id": 5,
    "family_ids": [2, 7],
    "organisation_ids": [3],
    "race_ids": [1],
    "is_dead": false,
    "traits": {
        "personality": ["Bold", "Cunning"],
        "appearance": ["Scarred", "Tall"]
    }
}

// Location
{
    "population": "15,000",
    "location_type": "City",
    "map_id": 12
}

// Calendar (more complex)
{
    "months": [...],
    "weekdays": [...],
    "moons": [...],
    "current_date": {...}
}
```

---

## Feature Comparison Matrix

| Feature | Kanka | Lightweight | Notes |
|---------|-------|-------------|-------|
| Entity CRUD | ✅ | ✅ | Core functionality |
| @mentions | ✅ | ✅ | Regex replacement on render |
| Relations | ✅ | ✅ | Simple join table |
| Nested entities | ✅ | ✅ | Parent_id field |
| Tags | ✅ | ✅ | Many-to-many |
| Custom attributes | ✅ | ✅ | Key-value table |
| Posts | ✅ | ✅ | Child entries |
| Full-text search | ✅ | ✅ | SQLite FTS5 |
| Image uploads | ✅ | ✅ | Filesystem storage |
| Markdown editor | ✅ | ✅ | Client-side library |
| Multi-campaign | ✅ | ✅ | Simple foreign key |
| Calendars | ✅ | ⚠️ Simplified | JSON-based, less UI |
| Interactive maps | ✅ | ⚠️ Basic | Leaflet.js integration |
| Family trees | ✅ | ❌ | Premium feature, complex |
| Relation explorer | ✅ | ⚠️ Basic | D3.js/vis.js graph |
| Timelines | ✅ | ⚠️ Basic | Simple vertical list |
| Multi-user permissions | ✅ | ❌ | Single-user focus |
| Premium tiers | ✅ | ❌ | Not needed |
| API | ✅ | ⚠️ Optional | Simple REST if needed |
| Plugins/marketplace | ✅ | ❌ | Direct customisation |
| 20+ languages | ✅ | ❌ | English only |

---

## Simplified Entity Types

### Core (Must Have)
1. **Characters** - The heart of any campaign
2. **Locations** - Where things happen
3. **Items** - Objects of significance
4. **Notes** - Catch-all for lore

### Extended (High Value)
5. **Organisations** - Groups and factions
6. **Families** - Lineages (without tree visualisation)
7. **Quests** - Adventure tracking
8. **Journals** - Session logs

### Optional (Can Be Notes)
- Events → Notes with "event" tag
- Races → Notes or character attribute
- Abilities → Notes or item attribute
- Conversations → Journal entries

### Recommendation: Start with 4-6 entity types

The JSON `data` field approach allows adding type-specific fields without schema changes.

---

## Implementation Roadmap

### Phase 1: Foundation (MVP)
- [ ] SQLite database setup with schema
- [ ] Basic PHP routing (or Slim framework)
- [ ] Entity CRUD operations
- [ ] Simple listing and detail views
- [ ] Markdown rendering with @mention support
- [ ] Image upload to filesystem
- [ ] Tailwind CSS styling

### Phase 2: Core Features
- [ ] Full-text search (FTS5)
- [ ] Relations between entities
- [ ] Tags system
- [ ] Nested entity views
- [ ] Custom attributes
- [ ] Posts (sub-entries)

### Phase 3: Enhanced UI
- [ ] Alpine.js interactivity
- [ ] Quick entity creation modal
- [ ] Inline editing
- [ ] Dashboard/recent activity

### Phase 4: Extended Features (Optional)
- [ ] Basic calendar with events
- [ ] Simple map markers (Leaflet)
- [ ] Basic relation graph
- [ ] Export/backup functionality
- [ ] Import from Kanka JSON

---

## Deployment Considerations

### Single-File Deployment Option
```
project/
├── index.php          # Router + controllers
├── data/
│   ├── campaign.db    # SQLite database
│   └── uploads/       # Images
├── templates/         # Blade-like or plain PHP
└── public/
    └── assets/        # CSS, JS
```

### Docker Deployment (Simple)
```dockerfile
FROM php:8.2-apache
COPY . /var/www/html
RUN apt-get update && apt-get install -y sqlite3
```

**One container, one file database, zero external services.**

---

## Estimated Development Time

| Phase | Complexity | Time Estimate |
|-------|------------|---------------|
| Phase 1 (MVP) | Low | 2-3 weeks |
| Phase 2 (Core) | Medium | 2-3 weeks |
| Phase 3 (UI) | Medium | 1-2 weeks |
| Phase 4 (Extended) | Variable | 2-4 weeks |
| **Total** | | **7-12 weeks** |

*Assumes part-time development, single developer*

---

## Risks and Mitigations

| Risk | Mitigation |
|------|------------|
| Feature creep | Strict MVP scope, defer to "Notes" |
| Data migration from Kanka | Build JSON import early |
| Calendar complexity | Start with simple JSON, iterate |
| Map complexity | Use Leaflet with image overlay only |
| Scope expansion | Each entity type is same codebase pattern |

---

## Conclusion

A lightweight Kanka alternative is absolutely feasible. The key insights:

1. **80/20 rule applies** - 20% of features serve 80% of use cases
2. **SQLite is sufficient** - No need for MySQL/Redis/Minio for single-user
3. **Polymorphic entities** - One table pattern handles all types
4. **JSON flexibility** - Type-specific fields without migrations
5. **Modern CSS/JS** - Tailwind + Alpine achieves Kanka's UI with minimal code

The result would be ~5,000-10,000 lines of code vs Kanka's ~230,000, deployable as a single Docker container or even a shared hosting PHP app.

---

## Next Steps

1. **Confirm scope** - Which entity types are essential for your use case?
2. **Choose stack** - PHP minimal vs Node.js vs hybrid?
3. **Design UI mockups** - What does the interface need to look like?
4. **Define MVP** - What's the minimum to be useful?

Would you like me to proceed with detailed architecture documentation or begin scaffolding the codebase?
