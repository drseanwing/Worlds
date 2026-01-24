-- Database indexes for performance optimization

-- Entity indexes
CREATE INDEX IF NOT EXISTS idx_entities_campaign_id ON entities(campaign_id);
CREATE INDEX IF NOT EXISTS idx_entities_entity_type ON entities(entity_type);
CREATE INDEX IF NOT EXISTS idx_entities_parent_id ON entities(parent_id);
CREATE INDEX IF NOT EXISTS idx_entities_campaign_type ON entities(campaign_id, entity_type);
CREATE INDEX IF NOT EXISTS idx_entities_name ON entities(name);

-- Relations indexes
CREATE INDEX IF NOT EXISTS idx_relations_source_id ON relations(source_id);
CREATE INDEX IF NOT EXISTS idx_relations_target_id ON relations(target_id);

-- Tags indexes
CREATE INDEX IF NOT EXISTS idx_tags_campaign_id ON tags(campaign_id);
CREATE INDEX IF NOT EXISTS idx_tags_name ON tags(name);

-- Entity tags indexes
CREATE INDEX IF NOT EXISTS idx_entity_tags_entity_id ON entity_tags(entity_id);
CREATE INDEX IF NOT EXISTS idx_entity_tags_tag_id ON entity_tags(tag_id);

-- Attributes indexes
CREATE INDEX IF NOT EXISTS idx_attributes_entity_id ON attributes(entity_id);

-- Posts indexes
CREATE INDEX IF NOT EXISTS idx_posts_entity_id ON posts(entity_id);
CREATE INDEX IF NOT EXISTS idx_posts_position ON posts(entity_id, position);

-- Files indexes
CREATE INDEX IF NOT EXISTS idx_files_entity_id ON files(entity_id);

-- Users indexes
CREATE INDEX IF NOT EXISTS idx_users_username ON users(username);
CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);
