-- Relations table
-- Stores relationships between entities (bidirectional supported via mirror_relation)

CREATE TABLE IF NOT EXISTS relations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    source_id INTEGER NOT NULL,
    target_id INTEGER NOT NULL,
    relation TEXT, -- Relationship type from source perspective (e.g., "ally", "enemy", "parent", "member")
    mirror_relation TEXT, -- Reverse label from target perspective
    description TEXT, -- Additional context about the relationship
    is_private INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (source_id) REFERENCES entities(id) ON DELETE CASCADE,
    FOREIGN KEY (target_id) REFERENCES entities(id) ON DELETE CASCADE
);

-- Trigger to update updated_at timestamp
CREATE TRIGGER IF NOT EXISTS relations_updated_at
AFTER UPDATE ON relations
FOR EACH ROW
BEGIN
    UPDATE relations SET updated_at = CURRENT_TIMESTAMP WHERE id = OLD.id;
END;
