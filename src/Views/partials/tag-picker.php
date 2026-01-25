<?php
/**
 * Tag Picker Partial
 * Multi-select tag picker component
 *
 * Props:
 * - $availableTags: Array of available tags
 * - $selectedTags: Array of currently selected tag IDs
 * - $name: Form field name (default: 'tags[]')
 */

$availableTags = $availableTags ?? [];
$selectedTags = $selectedTags ?? [];
$name = $name ?? 'tags[]';
?>

<div
    x-data="{
        open: false,
        search: '',
        selected: <?= json_encode($selectedTags) ?>,
        tags: <?= json_encode(array_values($availableTags)) ?>,
        get filteredTags() {
            if (this.search === '') return this.tags;
            return this.tags.filter(tag =>
                tag.name.toLowerCase().includes(this.search.toLowerCase())
            );
        },
        isSelected(tagId) {
            return this.selected.includes(tagId);
        },
        toggle(tagId) {
            const index = this.selected.indexOf(tagId);
            if (index > -1) {
                this.selected.splice(index, 1);
            } else {
                this.selected.push(tagId);
            }
        },
        getSelectedTags() {
            return this.tags.filter(tag => this.selected.includes(tag.id));
        }
    }"
    class="relative"
>
    <!-- Selected Tags Display -->
    <div class="mb-3">
        <label class="block text-sm font-semibold text-zinc-300 mb-2">Tags</label>
        <div class="flex flex-wrap gap-2 min-h-[2.5rem] p-2 bg-zinc-800 border border-zinc-700">
            <template x-for="tag in getSelectedTags()" :key="tag.id">
                <span
                    class="inline-flex items-center gap-1 px-2 py-1 bg-zinc-700 border border-zinc-600 text-zinc-200 text-xs font-medium"
                    :style="`background-color: ${tag.color}33; border-color: ${tag.color}; color: ${tag.color}`"
                >
                    <span x-text="tag.name"></span>
                    <button
                        type="button"
                        @click="toggle(tag.id)"
                        class="hover:opacity-75"
                    >
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </span>
            </template>
            <template x-if="getSelectedTags().length === 0">
                <span class="text-zinc-600 text-sm py-1">No tags selected</span>
            </template>
        </div>
    </div>

    <!-- Tag Picker Button -->
    <button
        type="button"
        @click="open = !open"
        class="w-full px-3 py-2 bg-zinc-800 border border-zinc-700 hover:border-emerald-400 text-zinc-300 text-sm font-medium transition-colors flex items-center justify-between"
    >
        <span>Select Tags</span>
        <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

    <!-- Dropdown -->
    <div
        x-show="open"
        @click.away="open = false"
        x-transition
        class="absolute z-10 w-full mt-1 bg-zinc-900 border border-zinc-700 shadow-xl max-h-64 overflow-hidden"
        style="display: none;"
    >
        <!-- Search -->
        <div class="p-2 border-b border-zinc-800">
            <input
                type="text"
                x-model="search"
                placeholder="Search tags..."
                class="w-full px-3 py-2 bg-zinc-800 border border-zinc-700 text-zinc-100 placeholder-zinc-500 text-sm focus:outline-none focus:border-emerald-400"
            >
        </div>

        <!-- Tag List -->
        <div class="overflow-y-auto max-h-48">
            <template x-for="tag in filteredTags" :key="tag.id">
                <button
                    type="button"
                    @click="toggle(tag.id)"
                    class="w-full px-3 py-2 text-left hover:bg-zinc-800 transition-colors flex items-center justify-between"
                    :class="{ 'bg-zinc-800': isSelected(tag.id) }"
                >
                    <span
                        class="inline-block px-2 py-1 text-xs font-medium"
                        :style="`background-color: ${tag.color}33; border: 1px solid ${tag.color}; color: ${tag.color}`"
                        x-text="tag.name"
                    ></span>
                    <svg
                        x-show="isSelected(tag.id)"
                        class="w-4 h-4 text-emerald-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </button>
            </template>

            <template x-if="filteredTags.length === 0">
                <div class="px-3 py-4 text-center text-zinc-600 text-sm">
                    No tags found
                </div>
            </template>
        </div>
    </div>

    <!-- Hidden Inputs -->
    <template x-for="tagId in selected" :key="tagId">
        <input type="hidden" :name="'<?= $name ?>'" :value="tagId">
    </template>
</div>
