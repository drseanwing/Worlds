-- Master Migration Script
-- Combines all SQL migration files in correct order
-- Run this file to initialize or update the database schema

-- Enable foreign key enforcement
PRAGMA foreign_keys = ON;

-- ============================================
-- 001: Campaigns table
-- ============================================
CREATE TABLE IF NOT EXISTS campaigns (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    description TEXT,
    settings TEXT DEFAULT '{}',
    user_id INTEGER DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TRIGGER IF NOT EXISTS campaigns_updated_at
AFTER UPDATE ON campaigns
FOR EACH ROW
BEGIN
    UPDATE campaigns SET updated_at = CURRENT_TIMESTAMP WHERE id = OLD.id;
END;

CREATE INDEX IF NOT EXISTS idx_campaigns_user_id ON campaigns(user_id);

-- ============================================
-- 002: Entities table (polymorphic)
-- ============================================
CREATE TABLE IF NOT EXISTS entities (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    campaign_id INTEGER NOT NULL,
    entity_type TEXT NOT NULL,
    name TEXT NOT NULL,
    type TEXT,
    entry TEXT,
    image_path TEXT,
    parent_id INTEGER,
    is_private INTEGER DEFAULT 0,
    data TEXT DEFAULT '{}',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES entities(id) ON DELETE SET NULL
);

CREATE TRIGGER IF NOT EXISTS entities_updated_at
AFTER UPDATE ON entities
FOR EACH ROW
BEGIN
    UPDATE entities SET updated_at = CURRENT_TIMESTAMP WHERE id = OLD.id;
END;

-- ============================================
-- 003: Relations table
-- ============================================
CREATE TABLE IF NOT EXISTS relations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    source_id INTEGER NOT NULL,
    target_id INTEGER NOT NULL,
    relation TEXT,
    mirror_relation TEXT,
    description TEXT,
    is_private INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (source_id) REFERENCES entities(id) ON DELETE CASCADE,
    FOREIGN KEY (target_id) REFERENCES entities(id) ON DELETE CASCADE
);

CREATE TRIGGER IF NOT EXISTS relations_updated_at
AFTER UPDATE ON relations
FOR EACH ROW
BEGIN
    UPDATE relations SET updated_at = CURRENT_TIMESTAMP WHERE id = OLD.id;
END;

-- ============================================
-- 004: Tags table
-- ============================================
CREATE TABLE IF NOT EXISTS tags (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    campaign_id INTEGER NOT NULL,
    name TEXT NOT NULL,
    colour TEXT,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE
);

CREATE TRIGGER IF NOT EXISTS tags_updated_at
AFTER UPDATE ON tags
FOR EACH ROW
BEGIN
    UPDATE tags SET updated_at = CURRENT_TIMESTAMP WHERE id = OLD.id;
END;

-- ============================================
-- 005: Entity-Tags junction table
-- ============================================
CREATE TABLE IF NOT EXISTS entity_tags (
    entity_id INTEGER NOT NULL,
    tag_id INTEGER NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (entity_id, tag_id),
    FOREIGN KEY (entity_id) REFERENCES entities(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);

-- ============================================
-- 006: Attributes table
-- ============================================
CREATE TABLE IF NOT EXISTS attributes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    entity_id INTEGER NOT NULL,
    name TEXT NOT NULL,
    value TEXT,
    is_private INTEGER DEFAULT 0,
    position INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (entity_id) REFERENCES entities(id) ON DELETE CASCADE
);

CREATE TRIGGER IF NOT EXISTS attributes_updated_at
AFTER UPDATE ON attributes
FOR EACH ROW
BEGIN
    UPDATE attributes SET updated_at = CURRENT_TIMESTAMP WHERE id = OLD.id;
END;

-- ============================================
-- 007: Posts table
-- ============================================
CREATE TABLE IF NOT EXISTS posts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    entity_id INTEGER NOT NULL,
    name TEXT,
    entry TEXT,
    is_private INTEGER DEFAULT 0,
    position INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (entity_id) REFERENCES entities(id) ON DELETE CASCADE
);

CREATE TRIGGER IF NOT EXISTS posts_updated_at
AFTER UPDATE ON posts
FOR EACH ROW
BEGIN
    UPDATE posts SET updated_at = CURRENT_TIMESTAMP WHERE id = OLD.id;
END;

-- ============================================
-- 008: Files table
-- ============================================
CREATE TABLE IF NOT EXISTS files (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    entity_id INTEGER NOT NULL,
    filename TEXT NOT NULL,
    path TEXT NOT NULL,
    mime_type TEXT NOT NULL,
    size INTEGER NOT NULL,
    description TEXT,
    is_private INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (entity_id) REFERENCES entities(id) ON DELETE CASCADE
);

CREATE TRIGGER IF NOT EXISTS files_updated_at
AFTER UPDATE ON files
FOR EACH ROW
BEGIN
    UPDATE files SET updated_at = CURRENT_TIMESTAMP WHERE id = OLD.id;
END;

-- ============================================
-- 009: Users table
-- ============================================
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL UNIQUE,
    email TEXT UNIQUE,
    password_hash TEXT NOT NULL,
    display_name TEXT,
    is_admin INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TRIGGER IF NOT EXISTS users_updated_at
AFTER UPDATE ON users
FOR EACH ROW
BEGIN
    UPDATE users SET updated_at = CURRENT_TIMESTAMP WHERE id = OLD.id;
END;

-- ============================================
-- 010: Full-Text Search (FTS5)
-- ============================================
CREATE VIRTUAL TABLE IF NOT EXISTS entities_fts USING fts5(
    name,
    entry,
    content='entities',
    content_rowid='id'
);

CREATE TRIGGER IF NOT EXISTS entities_fts_insert
AFTER INSERT ON entities
BEGIN
    INSERT INTO entities_fts(rowid, name, entry)
    VALUES (NEW.id, NEW.name, NEW.entry);
END;

CREATE TRIGGER IF NOT EXISTS entities_fts_update
AFTER UPDATE ON entities
BEGIN
    INSERT INTO entities_fts(entities_fts, rowid, name, entry)
    VALUES ('delete', OLD.id, OLD.name, OLD.entry);
    INSERT INTO entities_fts(rowid, name, entry)
    VALUES (NEW.id, NEW.name, NEW.entry);
END;

CREATE TRIGGER IF NOT EXISTS entities_fts_delete
AFTER DELETE ON entities
BEGIN
    INSERT INTO entities_fts(entities_fts, rowid, name, entry)
    VALUES ('delete', OLD.id, OLD.name, OLD.entry);
END;

-- ============================================
-- 011: Indexes
-- ============================================
CREATE INDEX IF NOT EXISTS idx_entities_campaign_id ON entities(campaign_id);
CREATE INDEX IF NOT EXISTS idx_entities_entity_type ON entities(entity_type);
CREATE INDEX IF NOT EXISTS idx_entities_parent_id ON entities(parent_id);
CREATE INDEX IF NOT EXISTS idx_entities_campaign_type ON entities(campaign_id, entity_type);
CREATE INDEX IF NOT EXISTS idx_entities_name ON entities(name);

CREATE INDEX IF NOT EXISTS idx_relations_source_id ON relations(source_id);
CREATE INDEX IF NOT EXISTS idx_relations_target_id ON relations(target_id);

CREATE INDEX IF NOT EXISTS idx_tags_campaign_id ON tags(campaign_id);
CREATE INDEX IF NOT EXISTS idx_tags_name ON tags(name);

CREATE INDEX IF NOT EXISTS idx_entity_tags_entity_id ON entity_tags(entity_id);
CREATE INDEX IF NOT EXISTS idx_entity_tags_tag_id ON entity_tags(tag_id);

CREATE INDEX IF NOT EXISTS idx_attributes_entity_id ON attributes(entity_id);

CREATE INDEX IF NOT EXISTS idx_posts_entity_id ON posts(entity_id);
CREATE INDEX IF NOT EXISTS idx_posts_position ON posts(entity_id, position);

CREATE INDEX IF NOT EXISTS idx_files_entity_id ON files(entity_id);

CREATE INDEX IF NOT EXISTS idx_users_username ON users(username);
CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);

-- ============================================
-- 013: Inventory Items
-- ============================================
CREATE TABLE IF NOT EXISTS inventory_items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    entity_id INTEGER NOT NULL,
    name TEXT NOT NULL,
    quantity INTEGER DEFAULT 1,
    description TEXT,
    item_entity_id INTEGER,
    position INTEGER DEFAULT 0,
    is_equipped INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (entity_id) REFERENCES entities(id) ON DELETE CASCADE,
    FOREIGN KEY (item_entity_id) REFERENCES entities(id) ON DELETE SET NULL
);

CREATE TRIGGER IF NOT EXISTS inventory_items_updated_at
AFTER UPDATE ON inventory_items
FOR EACH ROW
BEGIN
    UPDATE inventory_items SET updated_at = CURRENT_TIMESTAMP WHERE id = OLD.id;
END;

CREATE INDEX IF NOT EXISTS idx_inventory_items_entity_id ON inventory_items(entity_id);
CREATE INDEX IF NOT EXISTS idx_inventory_items_item_entity_id ON inventory_items(item_entity_id);
CREATE INDEX IF NOT EXISTS idx_inventory_items_position ON inventory_items(entity_id, position);

-- ============================================
-- 015: API Tokens table
-- ============================================
CREATE TABLE IF NOT EXISTS api_tokens (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    token TEXT NOT NULL UNIQUE,
    name TEXT NOT NULL,
    last_used_at DATETIME,
    expires_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_api_tokens_token ON api_tokens(token);
CREATE INDEX IF NOT EXISTS idx_api_tokens_user_id ON api_tokens(user_id);
CREATE INDEX IF NOT EXISTS idx_api_tokens_expires_at ON api_tokens(expires_at);

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
