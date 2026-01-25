<?php
/**
 * Attribute Form Partial
 * Inline form for adding/editing attributes
 *
 * Props:
 * - $entityId: Current entity ID
 */
?>

<div
    x-data="{
        open: false,
        name: '',
        value: '',
        type: 'text',
        reset() {
            this.name = '';
            this.value = '';
            this.type = 'text';
        }
    }"
    @add-attribute.window="open = true"
>
    <!-- Inline Form (shown when open) -->
    <div
        x-show="open"
        x-transition
        class="bg-zinc-900 border border-emerald-400 p-4 mb-4"
        style="display: none;"
    >
        <form
            action="<?= url('/entities/' . ($entityId ?? '') . '/attributes') ?>"
            method="POST"
            @submit="reset()"
            class="space-y-3"
        >
            <?= csrf_field() ?>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <!-- Attribute Name -->
                <div>
                    <label class="block text-xs font-semibold text-zinc-400 mb-1">
                        Attribute Name
                    </label>
                    <input
                        type="text"
                        name="name"
                        x-model="name"
                        required
                        placeholder="e.g., HP, Strength, Level"
                        class="w-full px-3 py-2 bg-zinc-800 border border-zinc-700 text-zinc-100 placeholder-zinc-600 text-sm focus:outline-none focus:border-emerald-400 transition-colors"
                    >
                </div>

                <!-- Value -->
                <div>
                    <label class="block text-xs font-semibold text-zinc-400 mb-1">
                        Value
                    </label>
                    <input
                        type="text"
                        name="value"
                        x-model="value"
                        required
                        placeholder="e.g., 100, High, Level 5"
                        class="w-full px-3 py-2 bg-zinc-800 border border-zinc-700 text-zinc-100 placeholder-zinc-600 text-sm focus:outline-none focus:border-emerald-400 transition-colors"
                    >
                </div>

                <!-- Type -->
                <div>
                    <label class="block text-xs font-semibold text-zinc-400 mb-1">
                        Type
                    </label>
                    <select
                        name="type"
                        x-model="type"
                        class="w-full px-3 py-2 bg-zinc-800 border border-zinc-700 text-zinc-100 text-sm focus:outline-none focus:border-emerald-400 transition-colors"
                    >
                        <option value="text">Text</option>
                        <option value="number">Number</option>
                        <option value="boolean">Boolean</option>
                        <option value="date">Date</option>
                        <option value="url">URL</option>
                    </select>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-2 pt-2 border-t border-zinc-800">
                <button
                    type="button"
                    @click="open = false; reset()"
                    class="px-3 py-1 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 text-sm font-medium transition-colors"
                >
                    Cancel
                </button>
                <button
                    type="submit"
                    class="px-3 py-1 bg-emerald-500 hover:bg-emerald-400 text-zinc-900 text-sm font-bold transition-colors"
                >
                    Add Attribute
                </button>
            </div>
        </form>
    </div>
</div>
