-- Posts table
-- Sub-entries within entities (similar to sections or chapters)

CREATE TABLE IF NOT EXISTS posts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    entity_id INTEGER NOT NULL,
    name TEXT, -- Post title/heading
    entry TEXT, -- Post content (markdown)
    is_private INTEGER DEFAULT 0,
    position INTEGER DEFAULT 0, -- For ordering posts
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (entity_id) REFERENCES entities(id) ON DELETE CASCADE
);

-- Trigger to update updated_at timestamp
CREATE TRIGGER IF NOT EXISTS posts_updated_at
AFTER UPDATE ON posts
FOR EACH ROW
BEGIN
    UPDATE posts SET updated_at = CURRENT_TIMESTAMP WHERE id = OLD.id;
END;
