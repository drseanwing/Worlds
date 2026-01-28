-- ============================================
-- 013: Inventory Items table
-- ============================================
-- Tracks items in entity inventories, supporting both simple items
-- and links to full Item entities for detailed item properties.

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
