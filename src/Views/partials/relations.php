<?php
/**
 * Entity Relations List Partial
 * Displays connected entities with relation types
 *
 * Props:
 * - $relations: Array of relation data (id, name, entity_type, relation_type)
 * - $entityId: Current entity ID
 */

$relations = $relations ?? [];
?>

<div class="space-y-4">
    <!-- Add Relation Button -->
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-bold text-zinc-200">Relations</h3>
        <button
            @click="$dispatch('open-relation-form')"
            class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-500 hover:bg-emerald-400 text-zinc-900 font-semibold text-sm transition-colors"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add Relation
        </button>
    </div>

    <?php if (empty($relations)): ?>
        <!-- Empty State -->
        <div class="bg-zinc-900/50 border border-zinc-800 p-8 text-center">
            <svg class="w-12 h-12 text-zinc-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
            </svg>
            <p class="text-zinc-500 text-sm">No relations yet</p>
            <p class="text-zinc-600 text-xs mt-1">Connect this entity to other entities</p>
        </div>
    <?php else: ?>
        <!-- Relations Grid -->
        <div class="grid grid-cols-1 gap-3">
            <?php foreach ($relations as $relation): ?>
                <div
                    class="group bg-zinc-900 border border-zinc-800 hover:border-emerald-400 p-4 transition-colors"
                    x-data="{ showActions: false }"
                    @mouseenter="showActions = true"
                    @mouseleave="showActions = false"
                >
                    <div class="flex items-start gap-3">
                        <!-- Icon -->
                        <div class="flex-shrink-0 w-10 h-10 bg-zinc-800 border border-zinc-700 group-hover:border-emerald-400 flex items-center justify-center transition-colors">
                            <span class="text-zinc-500 font-bold text-xs uppercase">
                                <?= strtoupper(substr($relation['entity_type'] ?? 'EN', 0, 2)) ?>
                            </span>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <a
                                        href="<?= url('/entities/' . ($relation['entity_type'] ?? 'entity') . '/' . ($relation['id'] ?? '')) ?>"
                                        class="text-zinc-100 font-semibold hover:text-emerald-400 transition-colors"
                                    >
                                        <?= e($relation['name'] ?? 'Unknown') ?>
                                    </a>
                                    <p class="text-zinc-500 text-sm capitalize mt-1">
                                        <?= e($relation['entity_type'] ?? 'entity') ?>
                                    </p>
                                </div>

                                <!-- Relation Type Badge -->
                                <span class="inline-block px-2 py-1 bg-zinc-800 border border-zinc-700 text-zinc-400 text-xs font-medium uppercase tracking-wide whitespace-nowrap">
                                    <?= e($relation['relation_type'] ?? 'related') ?>
                                </span>
                            </div>

                            <!-- Actions (shown on hover) -->
                            <div
                                x-show="showActions"
                                x-transition
                                class="flex items-center gap-2 mt-3"
                                style="display: none;"
                            >
                                <button
                                    @click="$dispatch('edit-relation', { id: <?= $relation['id'] ?? 0 ?> })"
                                    class="text-xs text-emerald-400 hover:text-emerald-300"
                                >
                                    Edit
                                </button>
                                <span class="text-zinc-700">|</span>
                                <button
                                    @click="if(confirm('Remove this relation?')) { $dispatch('delete-relation', { id: <?= $relation['id'] ?? 0 ?> }) }"
                                    class="text-xs text-red-400 hover:text-red-300"
                                >
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
