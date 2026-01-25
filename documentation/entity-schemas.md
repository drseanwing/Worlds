# Entity Type Schemas

This document describes all 15 entity types and their schema fields in the Worlds application. Each entity type extends the base entity model with type-specific fields stored as JSON.

## Overview

Worlds uses a polymorphic entity model where all data is stored in an `entities` table with a `type` column. Type-specific fields are stored in a `data` JSON field, validated against schema files in `src/Config/schemas/`.

All entity types inherit these common fields:
- `id` - Unique identifier
- `type` - Entity type (character, location, family, etc.)
- `name` - Entity name (required)
- `description` - Long-form description
- `image` - Image URL or path
- `data` - JSON field containing type-specific fields
- `created_at` - Creation timestamp
- `updated_at` - Last update timestamp
- `created_by` - User ID of creator
- `updated_by` - User ID of last updater

---

## 1. Character

Characters represent people in your world - heroes, villains, NPCs, companions, etc.

**Schema file**: `src/Config/schemas/character.json`

### Fields

| Field | Type | Description |
|-------|------|-------------|
| `age` | string, integer, or null | Age of the character (can be numeric or descriptive like "Middle-aged") |
| `pronouns` | string or null | Character's pronouns (e.g., "he/him", "she/her", "they/them") |
| `is_dead` | boolean | Whether the character is deceased (default: false) |
| `personality_traits` | array of strings | List of personality traits (e.g., ["brave", "stubborn", "loyal"]) |
| `appearance_traits` | array of strings | Physical appearance characteristics (e.g., ["tall", "scarred", "blue eyes"]) |
| `title` | string or null | Character's title or rank (e.g., "Lord", "Mage", "Knight") |
| `gender` | string or null | Character's gender or identity |
| `race_id` | integer or null | Reference to a Race entity |
| `location_id` | integer or null | Reference to current Location entity |
| `family_ids` | array of integers | References to Family entities the character belongs to |
| `organisation_ids` | array of integers | References to Organisation entities the character is part of |

### Example

```json
{
  "age": 45,
  "pronouns": "he/him",
  "is_dead": false,
  "personality_traits": ["honorable", "cautious", "witty"],
  "appearance_traits": ["tall", "grey beard", "scar on left cheek"],
  "title": "Lord Commander",
  "gender": "male",
  "race_id": 3,
  "location_id": 7,
  "family_ids": [2, 5],
  "organisation_ids": [1, 4]
}
```

---

## 2. Location

Locations represent places in your world - cities, villages, regions, buildings, dungeons, etc.

**Schema file**: `src/Config/schemas/location.json`

### Fields

| Field | Type | Description |
|-------|------|-------------|
| `location_type` | string or null | Type of location (e.g., "City", "Village", "Region", "Building", "POI") |
| `population` | string, integer, or null | Population size (numeric or descriptive) |
| `map_id` | integer or null | Reference to Map entity |
| `geography` | string or null | Geographic features and terrain description |
| `climate` | string or null | Climate or weather pattern information |
| `government` | string or null | Type of government or ruling structure |
| `coordinates` | object or null | Map coordinates {x: number, y: number} |

### Example

```json
{
  "location_type": "City",
  "population": 50000,
  "map_id": 1,
  "geography": "Built on high plateau surrounded by mountains",
  "climate": "Temperate, cold winters",
  "government": "Constitutional monarchy",
  "coordinates": {
    "x": 125.5,
    "y": 204.3
  }
}
```

---

## 3. Family

Families represent groups of related characters - royal houses, noble families, clans, etc.

**Schema file**: `src/Config/schemas/family.json`

### Fields

| Field | Type | Description |
|-------|------|-------------|
| `seat_location_id` | integer or null | Reference to Location entity (family seat/headquarters) |
| `motto` | string or null | Family motto or house words |
| `coat_of_arms` | string or null | Description or image reference for coat of arms |
| `founding_date` | string or null | When the family was founded |
| `status` | string or null | Current status (e.g., "Noble", "Extinct", "Common", "Royal") |

### Example

```json
{
  "seat_location_id": 12,
  "motto": "Fire and Blood",
  "coat_of_arms": "Three-headed dragon on black field",
  "founding_date": "Year 1",
  "status": "Royal"
}
```

---

## 4. Organisation

Organisations represent groups of people unified by purpose - guilds, religions, military orders, governments, etc.

**Schema file**: `src/Config/schemas/organisation.json`

### Fields

| Field | Type | Description |
|-------|------|-------------|
| `organisation_type` | string or null | Type of organisation (e.g., "Guild", "Religion", "Military", "Government") |
| `headquarters_id` | integer or null | Reference to Location entity (main headquarters) |
| `goals` | string or null | Primary goals or objectives |
| `founding_date` | string or null | When founded |
| `leader_id` | integer or null | Reference to Character entity (current leader) |
| `member_count` | integer or null | Approximate number of members |
| `ranks` | array of strings | List of ranks or positions (e.g., ["Apprentice", "Journeyman", "Master"]) |

### Example

```json
{
  "organisation_type": "Guild",
  "headquarters_id": 8,
  "goals": "Protect craftsmen and maintain quality standards",
  "founding_date": "Year 150",
  "leader_id": 45,
  "member_count": 2500,
  "ranks": ["Apprentice", "Journeyman", "Master", "Grand Master"]
}
```

---

## 5. Item

Items represent objects and artifacts - weapons, armor, magical items, tools, etc.

**Schema file**: `src/Config/schemas/item.json`

### Fields

| Field | Type | Description |
|-------|------|-------------|
| `item_type` | string or null | Type of item (e.g., "Weapon", "Armor", "Artifact", "Tool", "Consumable") |
| `rarity` | string or null | Rarity level (e.g., "Common", "Uncommon", "Rare", "Legendary", "Mythic") |
| `is_magical` | boolean | Whether the item has magical properties (default: false) |
| `owner_id` | integer or null | Reference to Character entity (current owner) |
| `location_id` | integer or null | Reference to Location entity (current location if not owned) |
| `value` | string or null | Monetary or trade value |
| `weight` | string, number, or null | Weight of the item |
| `properties` | string or null | Special properties or abilities description |

### Example

```json
{
  "item_type": "Weapon",
  "rarity": "Legendary",
  "is_magical": true,
  "owner_id": 15,
  "location_id": null,
  "value": "Priceless",
  "weight": 2.5,
  "properties": "Never dulls, glows blue in darkness, +2 bonus to hit"
}
```

---

## 6. Note

Notes represent miscellaneous information - lore snippets, plot points, secrets, references.

**Schema file**: `src/Config/schemas/note.json`

### Fields

| Field | Type | Description |
|-------|------|-------------|
| `note_type` | string or null | Type or category (e.g., "Lore", "Secret", "Plot", "Reference", "TODO") |
| `is_secret` | boolean | Whether this note contains hidden/secret information (default: false) |
| `importance` | string or null | Importance level (e.g., "Low", "Medium", "High", "Critical") |

### Example

```json
{
  "note_type": "Secret",
  "is_secret": true,
  "importance": "Critical"
}
```

*Typical note content stored in base `description` field: "The prophecy speaks of a child born under the red comet..."*

---

## 7. Event

Events represent historical or campaign moments - battles, treaties, celebrations, disasters, etc.

**Schema file**: `src/Config/schemas/event.json`

### Fields

| Field | Type | Description |
|-------|------|-------------|
| `date` | string or null | Date of the event (in-world calendar date format) |
| `era` | string or null | Historical era or age (e.g., "Age of Heroes", "First Age") |
| `calendar_id` | integer or null | Reference to Calendar entity |
| `event_type` | string or null | Type of event (e.g., "Battle", "Treaty", "Coronation", "Disaster", "Festival") |
| `location_id` | integer or null | Reference to Location entity where event occurred |
| `participant_ids` | array of integers | References to Character entities who participated |
| `outcome` | string or null | Result or outcome description |

### Example

```json
{
  "date": "Year 1003, Month 6, Day 15",
  "era": "The War of Succession",
  "calendar_id": 2,
  "event_type": "Battle",
  "location_id": 20,
  "participant_ids": [5, 12, 45, 67],
  "outcome": "Northern forces defeated, King crowned"
}
```

---

## 8. Calendar

Calendars represent custom calendar systems for your world.

**Schema file**: `src/Config/schemas/calendar.json`

### Fields

| Field | Type | Description |
|-------|------|-------------|
| `months` | array of objects | List of months with name, days, position |
| `weekdays` | array of strings | List of weekday names |
| `current_date` | object or null | Current in-world date {year, month, day} |
| `year_zero` | integer or null | Starting year of the calendar system |
| `suffix` | string or null | Year suffix (e.g., "AD", "CE", "Age of Wonders") |
| `moons` | array of objects | List of moons with name and cycle |

### Example

```json
{
  "months": [
    {"name": "Wintervale", "days": 30, "position": 1},
    {"name": "Snowmelt", "days": 28, "position": 2},
    {"name": "Spring", "days": 31, "position": 3}
  ],
  "weekdays": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"],
  "current_date": {
    "year": 1003,
    "month": 6,
    "day": 15
  },
  "year_zero": 0,
  "suffix": "AW",
  "moons": [
    {"name": "Luna", "cycle": 29.5},
    {"name": "Solaris", "cycle": 45.2}
  ]
}
```

---

## 9. Race

Races represent species or ethnic groups - humans, elves, dwarves, etc.

**Schema file**: `src/Config/schemas/race.json`

### Fields

| Field | Type | Description |
|-------|------|-------------|
| `lifespan` | string, integer, or null | Typical lifespan |
| `size` | string or null | Size category (e.g., "Tiny", "Small", "Medium", "Large", "Huge", "Gargantuan") |
| `traits` | array of strings | Racial traits or abilities |
| `homeland_id` | integer or null | Reference to Location entity (racial homeland) |
| `languages` | array of strings | Languages commonly spoken |
| `physical_description` | string or null | General physical characteristics |

### Example

```json
{
  "lifespan": 300,
  "size": "Medium",
  "traits": ["Darkvision", "Dwarven Resilience", "Stonesinging"],
  "homeland_id": 25,
  "languages": ["Khuzdul", "Common"],
  "physical_description": "Stocky humanoids, 4-5 feet tall, broad shoulders, often with long beards"
}
```

---

## 10. Quest

Quests represent missions, objectives, or plot hooks for campaigns.

**Schema file**: `src/Config/schemas/quest.json`

### Fields

| Field | Type | Description |
|-------|------|-------------|
| `quest_type` | string or null | Type of quest (e.g., "Main", "Side", "Character", "Bounty", "Optional") |
| `status` | string | Status - must be one of: "planned", "ongoing", "completed", "failed", "abandoned" |
| `giver_id` | integer or null | Reference to Character entity (quest giver) |
| `location_ids` | array of integers | References to Location entities involved |
| `character_ids` | array of integers | References to Character entities involved |
| `reward` | string or null | Reward for completion |
| `difficulty` | string or null | Difficulty level (e.g., "Easy", "Medium", "Hard", "Deadly") |

### Example

```json
{
  "quest_type": "Main",
  "status": "ongoing",
  "giver_id": 8,
  "location_ids": [15, 22, 30],
  "character_ids": [5, 12],
  "reward": "10,000 gold + rare artifact",
  "difficulty": "Hard"
}
```

---

## 11. Journal

Journals represent session logs, diaries, or campaign notes - useful for tracking campaign progress.

**Schema file**: `src/Config/schemas/journal.json`

### Fields

| Field | Type | Description |
|-------|------|-------------|
| `date` | string or null | Date of entry (in-world or real-world) |
| `session_number` | integer or null | Session number for campaign session logs |
| `author_id` | integer or null | Reference to Character entity (in-world author) or user |
| `journal_type` | string or null | Type of journal (e.g., "Session Log", "Diary", "Report", "Chronicle") |
| `calendar_id` | integer or null | Reference to Calendar entity for in-world dating |
| `location_id` | integer or null | Reference to Location entity where events occurred |

### Example

```json
{
  "date": "2024-01-15",
  "session_number": 12,
  "author_id": 5,
  "journal_type": "Session Log",
  "calendar_id": 2,
  "location_id": 18
}
```

*Content stored in base `description` field: "The party arrived at the temple and discovered...*

---

## 12. Map

Maps represent world maps or area maps with markers and metadata.

**Schema file**: `src/Config/schemas/map.json`

### Fields

| Field | Type | Description |
|-------|------|-------------|
| `image_path` | string or null | Path to the map image file |
| `markers` | array of objects | Map markers with position, label, and linked entity |
| `bounds` | object or null | Map dimensions {width, height, min_zoom, max_zoom} |
| `map_type` | string or null | Type of map (e.g., "World", "Region", "City", "Building", "Dungeon") |
| `grid_size` | integer or null | Size of grid if applicable |

### Marker Object

```json
{
  "id": "unique-marker-id",
  "x": 125.5,
  "y": 204.3,
  "label": "Marker label",
  "entity_id": 15,        // Links to location, city, etc.
  "icon": "castle"        // Icon type
}
```

### Example

```json
{
  "image_path": "/uploads/world-map.png",
  "markers": [
    {
      "id": "capital",
      "x": 150,
      "y": 200,
      "label": "King's Landing",
      "entity_id": 3,
      "icon": "castle"
    },
    {
      "id": "dungeon",
      "x": 280,
      "y": 120,
      "label": "Shadow's Tomb",
      "entity_id": 45,
      "icon": "dungeon"
    }
  ],
  "bounds": {
    "width": 1024,
    "height": 768,
    "min_zoom": 0.5,
    "max_zoom": 4.0
  },
  "map_type": "World",
  "grid_size": 50
}
```

---

## 13. Timeline

Timelines represent historical progressions with eras and dated events.

**Schema file**: `src/Config/schemas/timeline.json`

### Fields

| Field | Type | Description |
|-------|------|-------------|
| `eras` | array of objects | Historical eras with name, start/end years, description, color |
| `entries` | array of objects | Timeline entries with year, date, title, description |
| `calendar_id` | integer or null | Reference to Calendar entity |

### Era Object

```json
{
  "name": "Era name",
  "start_year": 0,
  "end_year": 1000,
  "description": "Description of the era",
  "colour": "#FF0000"
}
```

### Entry Object

```json
{
  "year": 500,
  "date": "Month 3, Day 15",
  "title": "Entry title",
  "description": "Entry description",
  "entity_id": 20,        // Links to event, etc.
  "era": "Age of Heroes"
}
```

### Example

```json
{
  "eras": [
    {
      "name": "Age of Creation",
      "start_year": 0,
      "end_year": 500,
      "description": "The world was created and the first races emerged",
      "colour": "#FFD700"
    },
    {
      "name": "Age of Heroes",
      "start_year": 500,
      "end_year": 1000,
      "description": "The great heroes rose and fell",
      "colour": "#FF6347"
    }
  ],
  "entries": [
    {
      "year": 0,
      "date": "First day",
      "title": "Creation",
      "description": "The world was born",
      "entity_id": null,
      "era": "Age of Creation"
    },
    {
      "year": 500,
      "date": "Month 1, Day 1",
      "title": "First Hero",
      "description": "The first hero began their quest",
      "entity_id": 8,
      "era": "Age of Heroes"
    }
  ],
  "calendar_id": 1
}
```

---

## 14. Ability

Abilities represent spells, skills, feats, powers - special capabilities that characters can have.

**Schema file**: `src/Config/schemas/ability.json`

### Fields

| Field | Type | Description |
|-------|------|-------------|
| `ability_type` | string or null | Type of ability (e.g., "Spell", "Skill", "Feat", "Power", "Trait") |
| `charges` | integer or null | Number of charges or uses per rest/day |
| `effects` | array of strings | List of effects or consequences |
| `range` | string or null | Range or area of effect (e.g., "Self", "30 feet", "Sight") |
| `duration` | string or null | Duration of effects (e.g., "Instantaneous", "1 hour", "Permanent") |
| `requirements` | string or null | Prerequisites or requirements to use |
| `cooldown` | string or null | Cooldown or recharge time |

### Example

```json
{
  "ability_type": "Spell",
  "charges": 1,
  "effects": ["Deal 6d6 fire damage", "Ignite flammable objects"],
  "range": "120 feet",
  "duration": "Instantaneous",
  "requirements": "Character must know fireball spell",
  "cooldown": "1 minute"
}
```

---

## 15. Creature

Creatures represent non-player characters that are animals or monsters - beasts, undead, dragons.

**Schema file**: `src/Config/schemas/creature.json`

### Fields

| Field | Type | Description |
|-------|------|-------------|
| `creature_type` | string or null | Type of creature (e.g., "Beast", "Undead", "Dragon", "Elemental", "Humanoid") |
| `habitat` | string or null | Natural habitat or environment |
| `is_sentient` | boolean | Whether the creature is sentient/intelligent (default: false) |
| `size` | string or null | Size category (e.g., "Tiny", "Small", "Medium", "Large", "Huge", "Gargantuan") |
| `diet` | string or null | Dietary habits (e.g., "Carnivore", "Herbivore", "Omnivore") |
| `abilities` | array of strings | List of special abilities or attacks |
| `danger_level` | string or null | Challenge rating or danger level (e.g., "CR 5", "Easy", "Deadly") |
| `location_ids` | array of integers | References to Location entities where creature is found |

### Example

```json
{
  "creature_type": "Dragon",
  "habitat": "Mountain peaks and caves",
  "is_sentient": true,
  "size": "Huge",
  "diet": "Carnivore",
  "abilities": ["Fire breath (60 ft cone)", "Tail attack", "Claw attack", "Resistance to fire"],
  "danger_level": "CR 21",
  "location_ids": [35, 42]
}
```

---

## Schema Validation

All entity data is validated against JSON Schema during create/update operations. Invalid data will be rejected with a validation error.

### Validation Rules

- Fields marked as `"type": ["string", "null"]` accept string or null
- Arrays have `"items"` defining element type
- Required fields don't have `null` in type union
- `"default"` values are auto-filled if field is omitted
- `"additionalProperties": true` allows extra custom fields

### Example Validation

```php
// This will pass validation
$character = Character::create([
    'name' => 'Aragorn',
    'description' => 'The ranger who became king',
    'data' => [
        'age' => 87,
        'pronouns' => 'he/him',
        'race_id' => 2,
        'custom_field' => 'Any custom data allowed'
    ]
]);

// This will fail - age must be string, integer, or null
$character = Character::create([
    'name' => 'Frodo',
    'data' => [
        'age' => ['too', 'complex']
    ]
]);
```

---

## Schema Evolution

When modifying schemas:

1. Edit the JSON schema file in `src/Config/schemas/`
2. Create a database migration if schema changes require data transformation
3. Update tests to reflect new fields
4. Document changes in migration notes
5. Ensure backward compatibility where possible

## Resources

- [JSON Schema Draft 7 Specification](https://json-schema.org/draft-07/json-schema-core.html)
- Schema validation handled by `Worlds\Config\SchemaValidator`
- Schema files located in `src/Config/schemas/`
