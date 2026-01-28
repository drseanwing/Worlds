<?php
/**
 * Inventory Item Form Partial
 * Form for adding or editing inventory items
 *
 * Props:
 * - $item: Item data (optional, for editing)
 * - $entityId: Current entity ID
 * - $itemEntities: Array of available Item entities to link (optional)
 */

$item = $item ?? [];
$itemEntities = $itemEntities ?? [];
$isEdit = !empty($item);
?>

<?php $formData = json_encode([
    'name' => $item['name'] ?? '',
    'quantity' => (int)($item['quantity'] ?? 1),
    'description' => $item['description'] ?? '',
    'itemEntityId' => $item['item_entity_id'] ?? '',
    'isEquipped' => (bool)($item['is_equipped'] ?? 0)
]); ?>
<form
    x-data="<?= htmlspecialchars($formData, ENT_QUOTES, 'UTF-8') ?>"
    @submit.prevent="
        <?php if ($isEdit): ?>
            $dispatch('update-inventory-item', {
                id: <?= $item['id'] ?? 0 ?>,
                name: name,
                quantity: quantity,
                description: description,
                item_entity_id: itemEntityId !== '' ? itemEntityId : null,
                is_equipped: isEquipped ? 1 : 0
            });
        <?php else: ?>
            $dispatch('add-inventory-item', {
                name: name,
                quantity: quantity,
                description: description,
                item_entity_id: itemEntityId !== '' ? itemEntityId : null,
                is_equipped: isEquipped ? 1 : 0
            });
        <?php endif; ?>
    "
    class="space-y-4 p-4 bg-zinc-900 border border-zinc-800"
>
    <!-- Form Header -->
    <h4 class="text-md font-bold text-zinc-200">
        <?= $isEdit ? 'Edit Inventory Item' : 'Add Inventory Item' ?>
    </h4>

    <!-- Item Name -->
    <div>
        <label for="inventory-name" class="block text-sm font-medium text-zinc-400 mb-1">
            Item Name <span class="text-red-400">*</span>
        </label>
        <input
            type="text"
            id="inventory-name"
            x-model="name"
            required
            class="w-full px-3 py-2 bg-zinc-800 border border-zinc-700 text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-emerald-400"
            placeholder="Enter item name"
        >
    </div>

    <!-- Quantity -->
    <div>
        <label for="inventory-quantity" class="block text-sm font-medium text-zinc-400 mb-1">
            Quantity
        </label>
        <input
            type="number"
            id="inventory-quantity"
            x-model.number="quantity"
            min="1"
            class="w-full px-3 py-2 bg-zinc-800 border border-zinc-700 text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-emerald-400"
        >
    </div>

    <!-- Description -->
    <div>
        <label for="inventory-description" class="block text-sm font-medium text-zinc-400 mb-1">
            Description
        </label>
        <textarea
            id="inventory-description"
            x-model="description"
            rows="3"
            class="w-full px-3 py-2 bg-zinc-800 border border-zinc-700 text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-emerald-400"
            placeholder="Optional item description"
        ></textarea>
    </div>

    <!-- Link to Item Entity -->
    <?php if (!empty($itemEntities)): ?>
    <div>
        <label for="inventory-item-entity" class="block text-sm font-medium text-zinc-400 mb-1">
            Link to Item Entity (optional)
        </label>
        <select
            id="inventory-item-entity"
            x-model="itemEntityId"
            class="w-full px-3 py-2 bg-zinc-800 border border-zinc-700 text-zinc-100 focus:outline-none focus:border-emerald-400"
        >
            <option value="">-- None --</option>
            <?php foreach ($itemEntities as $entity): ?>
                <option value="<?= (int)$entity['id'] ?>">
                    <?= e($entity['name'] ?? 'Unknown') ?>
                </option>
            <?php endforeach; ?>
        </select>
        <p class="text-zinc-600 text-xs mt-1">
            Link this inventory item to a detailed Item entity for more information
        </p>
    </div>
    <?php endif; ?>

    <!-- Is Equipped -->
    <div>
        <label class="flex items-center gap-2 text-sm text-zinc-400">
            <input
                type="checkbox"
                x-model="isEquipped"
                class="h-4 w-4 bg-zinc-800 border-zinc-700 text-emerald-500 focus:ring-emerald-500"
            >
            <span>Item is equipped</span>
        </label>
    </div>

    <!-- Form Actions -->
    <div class="flex items-center gap-2 pt-2">
        <button
            type="submit"
            class="px-4 py-2 bg-emerald-500 hover:bg-emerald-400 text-zinc-900 font-semibold text-sm transition-colors"
        >
            <?= $isEdit ? 'Update Item' : 'Add Item' ?>
        </button>
        <button
            type="button"
            @click="$dispatch('cancel-inventory-form')"
            class="px-4 py-2 bg-zinc-700 hover:bg-zinc-600 text-zinc-200 font-semibold text-sm transition-colors"
        >
            Cancel
        </button>
    </div>
</form>
