-- Entities table (polymorphic)
-- Unified storage for all entity types: characters, locations, items, etc.

CREATE TABLE IF NOT EXISTS entities (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    campaign_id INTEGER NOT NULL,
    entity_type TEXT NOT NULL, -- 'character', 'location', 'item', 'organisation', 'family', 'note', 'event', 'calendar', 'race', 'quest', 'journal', 'map', 'timeline', 'ability', 'conversation', 'creature'
    name TEXT NOT NULL,
    type TEXT, -- User-defined subtype (e.g., "NPC", "City", "Artifact")
    entry TEXT, -- Main description (markdown)
    image_path TEXT, -- Path to entity image
    parent_id INTEGER, -- Self-referential for nesting/hierarchy
    is_private INTEGER DEFAULT 0,
    data TEXT DEFAULT '{}', -- JSON column for type-specific fields
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES entities(id) ON DELETE SET NULL
);

-- Trigger to update updated_at timestamp
CREATE TRIGGER IF NOT EXISTS entities_updated_at
AFTER UPDATE ON entities
FOR EACH ROW
BEGIN
    UPDATE entities SET updated_at = CURRENT_TIMESTAMP WHERE id = OLD.id;
END;
