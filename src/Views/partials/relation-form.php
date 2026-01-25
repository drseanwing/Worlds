<?php
/**
 * Relation Form Modal Partial
 * Modal form to add/edit entity relation
 *
 * Props:
 * - $entityId: Current entity ID
 * - $entities: Available entities to relate to
 */

$entities = $entities ?? [];
?>

<div
    x-data="{
        open: false,
        relationId: null,
        targetEntityId: '',
        relationType: 'related',
        init() {
            this.$watch('open', value => {
                if (value) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = '';
                    this.reset();
                }
            });
        },
        reset() {
            this.relationId = null;
            this.targetEntityId = '';
            this.relationType = 'related';
        }
    }"
    @open-relation-form.window="open = true"
    @edit-relation.window="open = true; relationId = $event.detail.id"
    @keydown.escape.window="open = false"
    class="relative z-50"
>
    <!-- Backdrop -->
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="open = false"
        class="fixed inset-0 bg-zinc-950/80 backdrop-blur-sm"
        style="display: none;"
    ></div>

    <!-- Modal -->
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform scale-95"
        x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        class="fixed inset-0 flex items-center justify-center p-4 pointer-events-none"
        style="display: none;"
    >
        <div class="bg-zinc-900 border border-zinc-800 shadow-2xl w-full max-w-lg pointer-events-auto">
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-zinc-800">
                <h3 class="text-xl font-bold text-zinc-100" x-text="relationId ? 'Edit Relation' : 'Add Relation'"></h3>
                <button
                    @click="open = false"
                    class="text-zinc-500 hover:text-zinc-300 transition-colors"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Form -->
            <form
                action="<?= url('/entities/' . ($entityId ?? '') . '/relations') ?>"
                method="POST"
                class="p-6 space-y-4"
            >
                <?= csrf_field() ?>

                <!-- Target Entity -->
                <div>
                    <label class="block text-sm font-semibold text-zinc-300 mb-2">
                        Related Entity
                    </label>
                    <select
                        name="target_entity_id"
                        x-model="targetEntityId"
                        required
                        class="w-full px-3 py-2 bg-zinc-800 border border-zinc-700 text-zinc-100 focus:outline-none focus:border-emerald-400 transition-colors"
                    >
                        <option value="">Select an entity...</option>
                        <?php foreach ($entities as $entity): ?>
                            <option value="<?= $entity['id'] ?>">
                                <?= e($entity['name']) ?> (<?= e($entity['entity_type']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Relation Type -->
                <div>
                    <label class="block text-sm font-semibold text-zinc-300 mb-2">
                        Relation Type
                    </label>
                    <select
                        name="relation_type"
                        x-model="relationType"
                        class="w-full px-3 py-2 bg-zinc-800 border border-zinc-700 text-zinc-100 focus:outline-none focus:border-emerald-400 transition-colors"
                    >
                        <option value="related">Related</option>
                        <option value="child">Child</option>
                        <option value="parent">Parent</option>
                        <option value="ally">Ally</option>
                        <option value="enemy">Enemy</option>
                        <option value="mentor">Mentor</option>
                        <option value="student">Student</option>
                        <option value="owner">Owner</option>
                        <option value="member">Member</option>
                        <option value="located_in">Located In</option>
                    </select>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-800">
                    <button
                        type="button"
                        @click="open = false"
                        class="px-4 py-2 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 font-medium transition-colors"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="px-4 py-2 bg-emerald-500 hover:bg-emerald-400 text-zinc-900 font-bold transition-colors"
                    >
                        <span x-text="relationId ? 'Update' : 'Add Relation'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
