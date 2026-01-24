-- Files table
-- File attachments for entities

CREATE TABLE IF NOT EXISTS files (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    entity_id INTEGER NOT NULL,
    filename TEXT NOT NULL, -- Original filename
    path TEXT NOT NULL, -- Storage path relative to uploads directory
    mime_type TEXT NOT NULL,
    size INTEGER NOT NULL, -- File size in bytes
    description TEXT,
    is_private INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (entity_id) REFERENCES entities(id) ON DELETE CASCADE
);

-- Trigger to update updated_at timestamp
CREATE TRIGGER IF NOT EXISTS files_updated_at
AFTER UPDATE ON files
FOR EACH ROW
BEGIN
    UPDATE files SET updated_at = CURRENT_TIMESTAMP WHERE id = OLD.id;
END;
