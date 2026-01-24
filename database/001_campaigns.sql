-- Campaigns table
-- Stores world/campaign containers

CREATE TABLE IF NOT EXISTS campaigns (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    description TEXT,
    settings TEXT DEFAULT '{}', -- JSON column for campaign-specific settings
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Trigger to update updated_at timestamp
CREATE TRIGGER IF NOT EXISTS campaigns_updated_at
AFTER UPDATE ON campaigns
FOR EACH ROW
BEGIN
    UPDATE campaigns SET updated_at = CURRENT_TIMESTAMP WHERE id = OLD.id;
END;
