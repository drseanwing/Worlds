-- Add user_id column to campaigns table
-- Links campaigns to users for multi-user support

-- Add user_id column if it doesn't exist
-- SQLite doesn't have ALTER TABLE ADD COLUMN IF NOT EXISTS, so we use a workaround
-- First check if column exists by querying pragma
CREATE TABLE IF NOT EXISTS campaigns_new (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    description TEXT,
    settings TEXT DEFAULT '{}',
    user_id INTEGER DEFAULT 1, -- Default to user 1 for existing campaigns
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Copy data from old table if it exists
INSERT OR IGNORE INTO campaigns_new (id, name, description, settings, user_id, created_at, updated_at)
SELECT id, name, description, settings, 1 as user_id, created_at, updated_at FROM campaigns;

-- Drop old table
DROP TABLE IF EXISTS campaigns;

-- Rename new table
ALTER TABLE campaigns_new RENAME TO campaigns;

-- Recreate trigger
CREATE TRIGGER IF NOT EXISTS campaigns_updated_at
AFTER UPDATE ON campaigns
FOR EACH ROW
BEGIN
    UPDATE campaigns SET updated_at = CURRENT_TIMESTAMP WHERE id = OLD.id;
END;

-- Create index on user_id for faster queries
CREATE INDEX IF NOT EXISTS idx_campaigns_user_id ON campaigns(user_id);
