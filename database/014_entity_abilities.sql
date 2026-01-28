-- ============================================
-- 014: Entity Abilities (Junction table)
-- ============================================
CREATE TABLE IF NOT EXISTS entity_abilities (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    entity_id INTEGER NOT NULL,
    ability_entity_id INTEGER NOT NULL,
    charges_used INTEGER DEFAULT 0,
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (entity_id) REFERENCES entities(id) ON DELETE CASCADE,
    FOREIGN KEY (ability_entity_id) REFERENCES entities(id) ON DELETE CASCADE,
    UNIQUE(entity_id, ability_entity_id)
);

-- Add index for entity_abilities
CREATE INDEX IF NOT EXISTS idx_entity_abilities_entity_id ON entity_abilities(entity_id);
CREATE INDEX IF NOT EXISTS idx_entity_abilities_ability_entity_id ON entity_abilities(ability_entity_id);
