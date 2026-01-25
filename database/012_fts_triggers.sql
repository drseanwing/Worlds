-- FTS5 Synchronization Triggers
-- Keeps the entities_fts full-text search index in sync with entities table

-- Insert trigger: Add new entity to FTS index
CREATE TRIGGER IF NOT EXISTS entities_fts_insert
AFTER INSERT ON entities
BEGIN
    INSERT INTO entities_fts(rowid, name, entry)
    VALUES (NEW.id, NEW.name, NEW.entry);
END;

-- Update trigger: Remove old and insert updated entity in FTS index
CREATE TRIGGER IF NOT EXISTS entities_fts_update
AFTER UPDATE ON entities
BEGIN
    INSERT INTO entities_fts(entities_fts, rowid, name, entry)
    VALUES ('delete', OLD.id, OLD.name, OLD.entry);
    INSERT INTO entities_fts(rowid, name, entry)
    VALUES (NEW.id, NEW.name, NEW.entry);
END;

-- Delete trigger: Remove deleted entity from FTS index
CREATE TRIGGER IF NOT EXISTS entities_fts_delete
AFTER DELETE ON entities
BEGIN
    INSERT INTO entities_fts(entities_fts, rowid, name, entry)
    VALUES ('delete', OLD.id, OLD.name, OLD.entry);
END;
