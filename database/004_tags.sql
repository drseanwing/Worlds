-- Tags table
-- Stores tags for categorizing entities within a campaign

CREATE TABLE IF NOT EXISTS tags (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    campaign_id INTEGER NOT NULL,
    name TEXT NOT NULL,
    colour TEXT, -- Hex colour code for tag display (e.g., "#3498db")
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE
);

-- Trigger to update updated_at timestamp
CREATE TRIGGER IF NOT EXISTS tags_updated_at
AFTER UPDATE ON tags
FOR EACH ROW
BEGIN
    UPDATE tags SET updated_at = CURRENT_TIMESTAMP WHERE id = OLD.id;
END;
