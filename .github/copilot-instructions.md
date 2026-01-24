# GitHub Copilot Instructions for Worlds

## Overview

This repository is a standalone fantasy worldbuilding project focused on creating, organizing, and managing creative writing and worldbuilding data. These instructions provide GitHub Copilot with context, coding standards, and project-specific guidance for all development work.

---

## Global Development Standards

### Code Quality Principles

Apply these standards across all aspects of this project:

1. **Production-Ready Code**: Never use placeholders, TODOs without implementation, or incomplete logic. All code should be functional and complete.

2. **Comprehensive Documentation**: Include purpose comments for functions/classes, inline comments for complex logic, and docstrings for all public interfaces. Documentation is critical for creative content and worldbuilding systems.

3. **Graceful Error Handling**: Implement try-catch blocks with specific error types, meaningful error messages, and fallback behaviors. Never allow silent failures that could corrupt worldbuilding data.

4. **Modular Architecture**: Separate concerns into distinct modules/classes. Each function should do one thing well. Keep worldbuilding entities, narrative content, and system logic separated.

### Naming Conventions

| Context | Convention | Example |
|---------|------------|---------|
| Classes | PascalCase | `CharacterEntity`, `LocationRegistry` |
| Functions/Methods | camelCase | `createCharacter()`, `linkEntities()` |
| Constants | SCREAMING_SNAKE | `MAX_RELATIONS`, `DEFAULT_CAMPAIGN` |
| Variables | camelCase | `characterName`, `locationData` |
| Database Tables | snake_case (plural) | `characters`, `locations`, `campaigns` |
| Database Columns | snake_case | `created_at`, `parent_id`, `entity_type` |
| File Names | kebab-case | `character-templates.json`, `world-map-data.json` |
| Entity IDs | UUID or prefixed | `char_123abc`, `loc_456def`, `item_789ghi` |

### Data Structure Standards

#### Entity-Based Architecture

All worldbuilding content should use a consistent entity-based structure:

```json
{
    "id": "unique_identifier",
    "entity_type": "character|location|item|faction|event|concept",
    "name": "Entity Name",
    "summary": "One-sentence description",
    "description": "Full markdown description",
    "tags": ["tag1", "tag2"],
    "relations": [
        {
            "target_id": "other_entity_id",
            "relation_type": "parent|child|ally|enemy|member|owns",
            "description": "Context about this relationship"
        }
    ],
    "metadata": {
        "created_at": "ISO8601 timestamp",
        "updated_at": "ISO8601 timestamp",
        "author": "creator name",
        "status": "draft|in-progress|complete|published"
    },
    "custom_fields": {}
}
```

#### Markdown Standards for Creative Content

All narrative and descriptive content should use consistent markdown:

```markdown
# Entity Name

## Summary
One-sentence hook or tagline.

## Description
Main narrative description using markdown formatting:
- **Bold** for emphasis on key terms
- *Italics* for thoughts, titles, or foreign words
- `code` for system terms or game mechanics
- > Blockquotes for in-world quotes or flavor text

## Appearance
Physical description (for characters/locations).

## History
Background and timeline events.

## Relationships
- [[Entity Name]]: Description of relationship
- [[Another Entity]]: Another relationship

## Notes
Out-of-world notes, design decisions, or TODO items clearly marked.

## Metadata
- **Created**: YYYY-MM-DD
- **Status**: Draft/In Progress/Complete
- **Tags**: tag1, tag2, tag3
```

---

## Project-Specific Instructions: Fantasy Worldbuilding

### Repository Context

This is a standalone repository for managing fantasy worldbuilding data and creative writing. It serves as a central knowledge base for fictional worlds, characters, locations, items, factions, events, and narrative content.

### Core Principles

1. **Interconnected Entities**: Everything is connected. Characters live in locations, belong to factions, own items, participate in events.

2. **Narrative-First**: Worldbuilding serves the story. Technical data exists to support narrative, not vice versa.

3. **Iterative Development**: Worlds grow organically. Start with broad strokes, add detail progressively.

4. **Canon Flexibility**: Track multiple versions or timelines. Use tags or version fields to manage canonical vs. alternative content.

5. **Human-Readable**: All data should be readable and editable by humans in plain text formats (Markdown, JSON, YAML).

### Entity Types and Their Purposes

| Entity Type | Purpose | Key Fields |
|-------------|---------|------------|
| **Character** | People, creatures, NPCs | name, pronouns, age, appearance, personality, backstory, status (alive/dead) |
| **Location** | Places, regions, buildings | name, location_type (city/region/building/poi), parent_location, population, geography |
| **Item** | Objects, artifacts, equipment | name, item_type, properties, owner, location, magical (yes/no) |
| **Faction** | Groups, organizations, nations | name, faction_type (nation/guild/religion/family), government, members, goals |
| **Event** | Historical events, plot points | name, date, location, participants, outcomes, significance |
| **Concept** | Magic systems, religions, customs | name, category, rules, cultural_context |

### Data Organization Patterns

#### Directory Structure
```
/
├── campaigns/               # Campaign-specific worlds
│   ├── campaign-name/
│   │   ├── characters/
│   │   ├── locations/
│   │   ├── items/
│   │   ├── factions/
│   │   ├── events/
│   │   ├── concepts/
│   │   └── narrative/       # Story content, session notes
├── shared/                  # Reusable content across campaigns
│   ├── templates/
│   ├── generators/
│   └── reference/
├── data/                    # Structured data files
│   ├── schema/
│   └── exports/
└── docs/                    # Documentation
    ├── guides/
    └── worldbuilding-bible.md
```

#### File Naming Convention
- One file per entity: `entity-name.md` or `entity-name.json`
- Use kebab-case for file names
- Use descriptive names: `lord-valeron-stormheart.md` not `character1.md`
- Include entity type prefix if needed: `char-valeron.md`, `loc-stormhold.md`

### JSON Schema for Entity Data

When implementing data structures, use this JSON schema:

```json
{
  "$schema": "http://json-schema.org/draft-07/schema#",
  "type": "object",
  "required": ["id", "entity_type", "name"],
  "properties": {
    "id": {
      "type": "string",
      "pattern": "^[a-z]+_[a-zA-Z0-9]+$"
    },
    "entity_type": {
      "type": "string",
      "enum": ["character", "location", "item", "faction", "event", "concept"]
    },
    "name": {
      "type": "string",
      "minLength": 1,
      "maxLength": 255
    },
    "summary": {
      "type": "string",
      "maxLength": 500
    },
    "description": {
      "type": "string",
      "description": "Markdown-formatted description"
    },
    "tags": {
      "type": "array",
      "items": {
        "type": "string"
      }
    },
    "relations": {
      "type": "array",
      "items": {
        "type": "object",
        "required": ["target_id", "relation_type"],
        "properties": {
          "target_id": {"type": "string"},
          "relation_type": {"type": "string"},
          "description": {"type": "string"}
        }
      }
    },
    "metadata": {
      "type": "object",
      "properties": {
        "created_at": {"type": "string", "format": "date-time"},
        "updated_at": {"type": "string", "format": "date-time"},
        "author": {"type": "string"},
        "status": {
          "type": "string",
          "enum": ["draft", "in-progress", "complete", "published", "archived"]
        },
        "version": {"type": "number"}
      }
    },
    "custom_fields": {
      "type": "object",
      "description": "Entity-specific fields as key-value pairs"
    }
  }
}
```

### Character Entity Pattern

```json
{
    "id": "char_valeron",
    "entity_type": "character",
    "name": "Lord Valeron Stormheart",
    "summary": "The exiled Duke of Stormhold, seeking redemption after his family's fall from grace.",
    "description": "**Lord Valeron Stormheart** is an imposing figure...",
    "tags": ["nobility", "warrior", "exile", "major-npc"],
    "relations": [
        {
            "target_id": "loc_stormhold",
            "relation_type": "former_ruler",
            "description": "Was Duke of Stormhold before exile"
        },
        {
            "target_id": "faction_stormguard",
            "relation_type": "founder",
            "description": "Founded the Stormguard military order"
        }
    ],
    "metadata": {
        "created_at": "2026-01-24T00:00:00Z",
        "updated_at": "2026-01-24T00:00:00Z",
        "author": "Sean",
        "status": "complete"
    },
    "custom_fields": {
        "age": 42,
        "pronouns": "he/him",
        "status": "alive",
        "character_class": "Fighter",
        "level": 8,
        "alignment": "Lawful Good"
    }
}
```

### Location Entity Pattern

```json
{
    "id": "loc_stormhold",
    "entity_type": "location",
    "name": "Stormhold",
    "summary": "A fortress city built into coastal cliffs, known for its naval power.",
    "description": "**Stormhold** stands as a testament to military architecture...",
    "tags": ["city", "fortress", "coastal", "major-location"],
    "relations": [
        {
            "target_id": "loc_northern_kingdoms",
            "relation_type": "parent",
            "description": "Located in the Northern Kingdoms region"
        },
        {
            "target_id": "faction_royal_navy",
            "relation_type": "headquarters",
            "description": "Home port of the Royal Navy"
        }
    ],
    "metadata": {
        "created_at": "2026-01-24T00:00:00Z",
        "updated_at": "2026-01-24T00:00:00Z",
        "author": "Sean",
        "status": "in-progress"
    },
    "custom_fields": {
        "location_type": "city",
        "population": 45000,
        "government": "Duchy",
        "coordinates": {"x": 120, "y": 85},
        "climate": "Temperate coastal"
    }
}
```

### Relationship Types

Use consistent relationship types across entities:

| Relationship | Inverse | Usage |
|--------------|---------|-------|
| parent | child | Hierarchical locations, organizations |
| member | organization | Character belongs to faction |
| ally | ally | Friendly relations between entities |
| enemy | enemy | Hostile relations |
| owns | owned_by | Ownership of items |
| rules | ruled_by | Governance relationships |
| created | creator | Creation/authorship |
| participant | event | Event participation |
| located_in | contains | Physical location |

### Narrative Content Standards

#### Session Notes Template

```markdown
# Session [Number]: [Title]

**Date**: YYYY-MM-DD
**Campaign**: Campaign Name
**Players**: Player1 (Character1), Player2 (Character2)
**DM**: DM Name

## Summary
One-paragraph summary of the session.

## Events
- Event 1: Description with [[Entity Links]]
- Event 2: Another event
- Event 3: Third event

## Combat Encounters
### Encounter 1: [Name]
- **Location**: [[Location Name]]
- **Enemies**: Enemy types
- **Outcome**: Result
- **Loot**: Items acquired

## Character Moments
### [Character Name]
Notable character development or roleplay moments.

## Plot Threads
- [ ] Unresolved thread 1
- [x] Resolved thread 2
- [ ] New thread introduced

## Next Session Prep
- Prepare: Things to prepare
- NPCs needed: List
- Locations needed: List

## DM Notes
Private notes, behind-the-scenes info, future plot hooks.
```

#### Quest/Plot Template

```markdown
# Quest: [Quest Name]

## Type
Main Quest / Side Quest / Character Quest

## Quest Giver
[[NPC Name]] - Brief context

## Objective
Clear statement of what needs to be accomplished.

## Stages

### Stage 1: [Stage Name]
- **Objective**: What to do
- **Location**: [[Location]]
- **NPCs**: [[NPC1]], [[NPC2]]
- **Challenges**: Obstacles to overcome
- **Rewards**: Experience, items, information

### Stage 2: [Stage Name]
[Continue pattern]

## Outcomes

### Success
What happens if quest succeeds.

### Failure
What happens if quest fails.

### Partial Success
Alternative outcomes.

## Rewards
- **Experience**: XP amount
- **Gold**: Amount
- **Items**: [[Item1]], [[Item2]]
- **Reputation**: Faction changes
- **Story**: Plot implications

## Connections
- **Related Quests**: [[Other Quests]]
- **Affected Factions**: [[Factions]]
- **Plot Threads**: Which storylines this affects
```

### Creative Writing Guidelines

When generating creative content for this project:

1. **Voice Consistency**: Maintain consistent tone and voice within each world/campaign
2. **Show, Don't Tell**: Use sensory details and action rather than exposition dumps
3. **Character-Driven**: Focus on how worldbuilding serves character development
4. **Avoid Clichés**: Subvert fantasy tropes when possible
5. **Cultural Depth**: Build cultures with internal logic and contradictions
6. **Conflict Layers**: Include personal, local, and world-scale conflicts

### Data Validation Rules

When working with worldbuilding data:

1. **Required Fields**: All entities must have id, entity_type, name
2. **Unique IDs**: Entity IDs must be globally unique within the project
3. **Valid Relations**: All relation target_ids must reference existing entities
4. **ISO 8601 Dates**: All timestamps use ISO 8601 format
5. **Tag Consistency**: Use lowercase, hyphenated tags
6. **Markdown Sanitization**: Strip potentially harmful HTML from markdown
7. **Circular References**: Detect and warn about circular relationship chains

### Version Control Practices

1. **Commit Messages**: Use format: `[entity_type] action: entity name`
   - Example: `[character] add: Lord Valeron Stormheart`
   - Example: `[location] update: Stormhold - added district details`

2. **Branch Strategy**: 
   - `main`: Stable, canonical worldbuilding content
   - `develop`: Active development
   - `feature/[feature-name]`: New campaigns or major additions
   - `experiment/[idea]`: Non-canonical explorations

3. **File Changes**: Treat worldbuilding as code
   - Review changes before committing
   - Avoid mass deletions without backup
   - Document major lore changes in commit messages

### When Generating Code for This Project

**If implementing a web interface**:
- Use static site generators (Jekyll, Hugo, Gatsby) for simple viewing
- Implement search and filtering by entity type, tags, relations
- Create interactive relationship graphs
- Support both read and edit modes

**If implementing data management tools**:
- Use JSON Schema validation for all entity data
- Implement bidirectional relationship management (adding A→B also adds B→A)
- Support markdown preview for descriptions
- Include data import/export utilities

**If implementing generators**:
- Provide random name generators by culture
- Create procedural content with customizable parameters
- Ensure generated content follows established patterns
- Allow manual editing of generated content

**If implementing search/query tools**:
- Full-text search across all entity descriptions
- Filter by entity type, tags, status
- Find entities by relationship (e.g., "all characters in location X")
- Support graph queries (e.g., "path between entity A and entity B")

### Technology Recommendations

| Purpose | Recommended Tools |
|---------|------------------|
| Data Format | JSON (structured), Markdown (narrative) |
| Version Control | Git with clear branching strategy |
| Static Site | Jekyll, Hugo, MkDocs, Obsidian Publish |
| Database (if needed) | SQLite (local), PostgreSQL (multi-user) |
| Visualization | Mermaid diagrams, D3.js for graphs |
| Editing | Obsidian, Notion, VS Code with markdown plugins |

### Accessibility and Usability

1. **Human-Readable First**: All data should be readable in plain text editors
2. **Export Options**: Support exporting to PDF, HTML, EPUB for sharing
3. **Cross-Reference Links**: Use wiki-style [[links]] that work across formats
4. **Mobile-Friendly**: If building web views, ensure mobile responsiveness
5. **Offline Capable**: Worldbuilding should work without internet access

---

## Summary Checklist for GitHub Copilot

When working on this project, Copilot should:

- [ ] Use consistent entity-based data structures
- [ ] Follow naming conventions (camelCase, snake_case, kebab-case as appropriate)
- [ ] Validate all entity IDs and relationships
- [ ] Generate markdown content following the project templates
- [ ] Implement proper error handling for data operations
- [ ] Document all code and creative content thoroughly
- [ ] Keep data human-readable and version-control friendly
- [ ] Support both structured (JSON) and narrative (Markdown) content
- [ ] Maintain referential integrity in entity relationships
- [ ] Use ISO 8601 for all timestamps
- [ ] Tag content appropriately for searchability
- [ ] Write commit messages in the specified format

---

## Questions or Improvements

If anything is unclear or improvements are needed to these instructions, document questions or suggestions in `/docs/copilot-instructions-feedback.md`.
