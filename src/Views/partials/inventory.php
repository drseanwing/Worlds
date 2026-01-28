<?php
/**
 * Entity Inventory List Partial
 * Displays inventory items for an entity
 *
 * Props:
 * - $inventory: Array of inventory item data
 * - $entityId: Current entity ID
 */

$inventory = $inventory ?? [];
?>

<div class="space-y-4">
    <!-- Header -->
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-bold text-zinc-200">Inventory</h3>
        <button
            @click="$dispatch('add-inventory-item')"
            class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-500 hover:bg-emerald-400 text-zinc-900 font-semibold text-sm transition-colors"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add Item
        </button>
    </div>

    <?php if (empty($inventory)): ?>
        <!-- Empty State -->
        <div class="bg-zinc-900/50 border border-zinc-800 p-8 text-center">
            <svg class="w-12 h-12 text-zinc-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
            </svg>
            <p class="text-zinc-500 text-sm">No items in inventory</p>
            <p class="text-zinc-600 text-xs mt-1">Add items to track possessions</p>
        </div>
    <?php else: ?>
        <!-- Inventory Table -->
        <div class="bg-zinc-900 border border-zinc-800 overflow-hidden">
            <table class="w-full">
                <thead class="bg-zinc-950 border-b border-zinc-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-zinc-400 uppercase tracking-wider">
                            Item
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-zinc-400 uppercase tracking-wider w-24">
                            Quantity
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-zinc-400 uppercase tracking-wider w-24">
                            Equipped
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-zinc-400 uppercase tracking-wider">
                            Description
                        </th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-zinc-400 uppercase tracking-wider w-20">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800">
                    <?php foreach ($inventory as $item): ?>
                        <?php $itemData = json_encode([
                            'editing' => false,
                            'name' => $item['name'] ?? '',
                            'quantity' => (int)($item['quantity'] ?? 1),
                            'description' => $item['description'] ?? '',
                            'isEquipped' => (bool)($item['is_equipped'] ?? 0)
                        ]); ?>
                        <tr
                            class="hover:bg-zinc-800/50 transition-colors"
                            x-data="<?= htmlspecialchars($itemData, ENT_QUOTES, 'UTF-8') ?>"
                        >
                            <td class="px-4 py-3">
                                <template x-if="!editing">
                                    <div class="flex items-center gap-2">
                                        <span class="text-zinc-100 font-medium">
                                            <?= e($item['name'] ?? 'Unknown') ?>
                                        </span>
                                        <?php if (!empty($item['item_entity_id'])): ?>
                                            <a
                                                href="/entities/item/<?= (int)$item['item_entity_id'] ?>"
                                                class="text-emerald-400 hover:text-emerald-300 text-xs"
                                                title="View item details"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </template>
                                <template x-if="editing">
                                    <input
                                        type="text"
                                        x-model="name"
                                        class="w-full px-2 py-1 bg-zinc-800 border border-zinc-700 text-zinc-100 text-sm focus:outline-none focus:border-emerald-400"
                                    >
                                </template>
                            </td>
                            <td class="px-4 py-3">
                                <template x-if="!editing">
                                    <span class="text-zinc-400">
                                        <?= (int)($item['quantity'] ?? 1) ?>
                                    </span>
                                </template>
                                <template x-if="editing">
                                    <input
                                        type="number"
                                        x-model.number="quantity"
                                        min="1"
                                        class="w-full px-2 py-1 bg-zinc-800 border border-zinc-700 text-zinc-100 text-sm focus:outline-none focus:border-emerald-400"
                                    >
                                </template>
                            </td>
                            <td class="px-4 py-3">
                                <template x-if="!editing">
                                    <span class="inline-flex items-center">
                                        <?php if ($item['is_equipped'] ?? 0): ?>
                                            <svg class="w-5 h-5 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                        <?php else: ?>
                                            <span class="text-zinc-600">—</span>
                                        <?php endif; ?>
                                    </span>
                                </template>
                                <template x-if="editing">
                                    <input
                                        type="checkbox"
                                        x-model="isEquipped"
                                        class="h-4 w-4 bg-zinc-800 border-zinc-700 text-emerald-500 focus:ring-emerald-500"
                                    >
                                </template>
                            </td>
                            <td class="px-4 py-3">
                                <template x-if="!editing">
                                    <span class="text-zinc-400 text-sm">
                                        <?= e($item['description'] ?? '') ?>
                                    </span>
                                </template>
                                <template x-if="editing">
                                    <input
                                        type="text"
                                        x-model="description"
                                        class="w-full px-2 py-1 bg-zinc-800 border border-zinc-700 text-zinc-100 text-sm focus:outline-none focus:border-emerald-400"
                                    >
                                </template>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button
                                        @click="
                                            if (editing) {
                                                $dispatch('update-inventory-item', {
                                                    id: <?= $item['id'] ?? 0 ?>,
                                                    name: name,
                                                    quantity: quantity,
                                                    description: description,
                                                    is_equipped: isEquipped ? 1 : 0
                                                });
                                            }
                                            editing = !editing;
                                        "
                                        class="text-emerald-400 hover:text-emerald-300 text-xs"
                                    >
                                        <template x-if="!editing">
                                            <span>Edit</span>
                                        </template>
                                        <template x-if="editing">
                                            <span>Save</span>
                                        </template>
                                    </button>
                                    <button
                                        @click="if(confirm('Delete this item?')) { $dispatch('delete-inventory-item', { id: <?= $item['id'] ?? 0 ?> }) }"
                                        class="text-red-400 hover:text-red-300 text-xs"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <!-- Drag-and-drop hint -->
        <p class="text-zinc-600 text-xs mt-2">
            Tip: Drag rows to reorder items (future feature)
        </p>
    <?php endif; ?>
</div>
