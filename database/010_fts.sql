-- Full-Text Search using FTS5
-- Virtual table for fast text search across entities

CREATE VIRTUAL TABLE IF NOT EXISTS entities_fts USING fts5(
    name,
    entry,
    content='entities',
    content_rowid='id'
);

-- Triggers to keep FTS index in sync with entities table

-- Insert trigger
CREATE TRIGGER IF NOT EXISTS entities_fts_insert
AFTER INSERT ON entities
BEGIN
    INSERT INTO entities_fts(rowid, name, entry)
    VALUES (NEW.id, NEW.name, NEW.entry);
END;

-- Update trigger
CREATE TRIGGER IF NOT EXISTS entities_fts_update
AFTER UPDATE ON entities
BEGIN
    INSERT INTO entities_fts(entities_fts, rowid, name, entry)
    VALUES ('delete', OLD.id, OLD.name, OLD.entry);
    INSERT INTO entities_fts(rowid, name, entry)
    VALUES (NEW.id, NEW.name, NEW.entry);
END;

-- Delete trigger
CREATE TRIGGER IF NOT EXISTS entities_fts_delete
AFTER DELETE ON entities
BEGIN
    INSERT INTO entities_fts(entities_fts, rowid, name, entry)
    VALUES ('delete', OLD.id, OLD.name, OLD.entry);
END;
