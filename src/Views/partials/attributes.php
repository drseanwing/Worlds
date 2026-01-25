<?php
/**
 * Entity Attributes List Partial
 * Displays custom entity attributes
 *
 * Props:
 * - $attributes: Array of attribute data (id, name, value, type)
 * - $entityId: Current entity ID
 */

$attributes = $attributes ?? [];
?>

<div class="space-y-4">
    <!-- Header -->
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-bold text-zinc-200">Attributes</h3>
        <button
            @click="$dispatch('add-attribute')"
            class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-500 hover:bg-emerald-400 text-zinc-900 font-semibold text-sm transition-colors"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add Attribute
        </button>
    </div>

    <?php if (empty($attributes)): ?>
        <!-- Empty State -->
        <div class="bg-zinc-900/50 border border-zinc-800 p-8 text-center">
            <svg class="w-12 h-12 text-zinc-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
            </svg>
            <p class="text-zinc-500 text-sm">No attributes yet</p>
            <p class="text-zinc-600 text-xs mt-1">Add custom attributes to track details</p>
        </div>
    <?php else: ?>
        <!-- Attributes Table -->
        <div class="bg-zinc-900 border border-zinc-800 overflow-hidden">
            <table class="w-full">
                <thead class="bg-zinc-950 border-b border-zinc-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-zinc-400 uppercase tracking-wider">
                            Attribute
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-zinc-400 uppercase tracking-wider">
                            Value
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-zinc-400 uppercase tracking-wider w-24">
                            Type
                        </th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-zinc-400 uppercase tracking-wider w-20">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800">
                    <?php foreach ($attributes as $attribute): ?>
                        <tr
                            class="hover:bg-zinc-800/50 transition-colors"
                            x-data="{ editing: false }"
                        >
                            <td class="px-4 py-3">
                                <span class="text-zinc-100 font-medium">
                                    <?= e($attribute['name'] ?? 'Unknown') ?>
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <template x-if="!editing">
                                    <span class="text-zinc-400">
                                        <?= e($attribute['value'] ?? '') ?>
                                    </span>
                                </template>
                                <template x-if="editing">
                                    <input
                                        type="text"
                                        value="<?= e($attribute['value'] ?? '') ?>"
                                        class="w-full px-2 py-1 bg-zinc-800 border border-zinc-700 text-zinc-100 text-sm focus:outline-none focus:border-emerald-400"
                                    >
                                </template>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-block px-2 py-1 bg-zinc-800 border border-zinc-700 text-zinc-500 text-xs font-medium uppercase">
                                    <?= e($attribute['type'] ?? 'text') ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button
                                        @click="editing = !editing"
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
                                        @click="if(confirm('Delete this attribute?')) { $dispatch('delete-attribute', { id: <?= $attribute['id'] ?? 0 ?> }) }"
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
    <?php endif; ?>
</div>
