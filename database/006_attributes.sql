-- Attributes table
-- Custom key-value attributes for entities

CREATE TABLE IF NOT EXISTS attributes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    entity_id INTEGER NOT NULL,
    name TEXT NOT NULL,
    value TEXT,
    is_private INTEGER DEFAULT 0,
    position INTEGER DEFAULT 0, -- For ordering attributes
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (entity_id) REFERENCES entities(id) ON DELETE CASCADE
);

-- Trigger to update updated_at timestamp
CREATE TRIGGER IF NOT EXISTS attributes_updated_at
AFTER UPDATE ON attributes
FOR EACH ROW
BEGIN
    UPDATE attributes SET updated_at = CURRENT_TIMESTAMP WHERE id = OLD.id;
END;
