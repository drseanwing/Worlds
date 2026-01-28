<?php
/**
 * Ability Picker Partial
 *
 * Modal content for selecting and attaching abilities to an entity.
 * This partial is meant to be included inside a modal container.
 * Expected variables from parent scope:
 * - $entity['id']: Entity ID to attach abilities to
 * - $entity['campaign_id']: Campaign ID for filtering abilities
 */

use Worlds\Repositories\EntityRepository;

// Fetch all abilities in the current campaign
$entityRepo = new EntityRepository();
$campaignId = $entity['campaign_id'] ?? session('campaign_id');
$availableAbilities = $entityRepo->findByType('ability', $campaignId, 1, 100);
?>

<div x-data="abilityPicker()" class="h-full flex flex-col">
    <div class="p-6 border-b border-gray-200">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-800">Select Ability</h3>
            <button
                @click="$parent.showPicker = false"
                class="text-gray-400 hover:text-gray-600"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Search/Filter -->
        <input
            type="text"
            x-model="searchQuery"
            placeholder="Search abilities..."
            class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
    </div>

    <div class="flex-1 overflow-y-auto p-6">
        <?php if (empty($availableAbilities)): ?>
            <div class="text-center py-8">
                <p class="text-gray-500 mb-4">No abilities found in this campaign.</p>
                <a
                    href="/entities/ability/create"
                    class="inline-block bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded transition"
                >
                    Create Ability
                </a>
            </div>
        <?php else: ?>
            <div class="space-y-2">
                <template x-for="ability in filteredAbilities" :key="ability.id">
                    <div
                        @click="selectAbility(ability)"
                        class="border border-gray-200 rounded-lg p-4 hover:border-blue-500 hover:bg-blue-50 cursor-pointer transition"
                        :class="{ 'border-blue-500 bg-blue-50': selectedAbility && selectedAbility.id === ability.id }"
                    >
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-800" x-text="ability.name"></h4>
                                <p class="text-sm text-gray-600 mt-1" x-text="ability.type || 'Untyped'"></p>
                                <template x-if="ability.data && ability.data.charges">
                                    <p class="text-sm text-gray-500 mt-1">
                                        <span x-text="ability.data.charges"></span> charges
                                    </p>
                                </template>
                            </div>
                            <svg
                                x-show="selectedAbility && selectedAbility.id === ability.id"
                                class="w-6 h-6 text-blue-500"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                            >
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                </template>

                <!-- No results message -->
                <div x-show="filteredAbilities.length === 0" class="text-center py-8 text-gray-500">
                    No abilities match your search.
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php if (!empty($availableAbilities)): ?>
        <div class="p-6 border-t border-gray-200 bg-gray-50" x-show="selectedAbility">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Initial Charges Used (optional)
                </label>
                <input
                    type="number"
                    x-model.number="chargesUsed"
                    min="0"
                    :max="selectedAbility && selectedAbility.data && selectedAbility.data.charges ? selectedAbility.data.charges : 999"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="0"
                >
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Notes (optional)
                </label>
                <textarea
                    x-model="notes"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 h-20"
                    placeholder="Add any notes about this ability..."
                ></textarea>
            </div>

            <div class="flex justify-end gap-2">
                <button
                    @click="$parent.showPicker = false"
                    class="px-4 py-2 text-gray-600 hover:text-gray-800"
                >
                    Cancel
                </button>
                <button
                    @click="attachAbility()"
                    class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed"
                    :disabled="!selectedAbility || isAttaching"
                >
                    <span x-show="!isAttaching">Attach Ability</span>
                    <span x-show="isAttaching">Attaching...</span>
                </button>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function abilityPicker() {
    return {
        abilities: <?= json_encode($availableAbilities) ?>,
        searchQuery: '',
        selectedAbility: null,
        chargesUsed: 0,
        notes: '',
        isAttaching: false,

        get filteredAbilities() {
            if (!this.searchQuery) {
                return this.abilities;
            }

            const query = this.searchQuery.toLowerCase();
            return this.abilities.filter(ability => {
                return ability.name.toLowerCase().includes(query) ||
                       (ability.type && ability.type.toLowerCase().includes(query)) ||
                       (ability.entry && ability.entry.toLowerCase().includes(query));
            });
        },

        selectAbility(ability) {
            this.selectedAbility = ability;
            this.chargesUsed = 0;
            this.notes = '';
        },

        async attachAbility() {
            if (!this.selectedAbility) return;

            this.isAttaching = true;

            try {
                const formData = new FormData();
                formData.append('_csrf_token', '<?= csrf_token() ?>');
                formData.append('ability_entity_id', this.selectedAbility.id);
                formData.append('charges_used', this.chargesUsed || 0);
                formData.append('notes', this.notes || '');

                const response = await fetch('/api/entities/<?= $entity['id'] ?>/abilities', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    // Close modal and reload page to show new ability
                    this.$parent.showPicker = false;
                    location.reload();
                } else {
                    alert('Failed to attach ability: ' + (data.error || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error attaching ability:', error);
                alert('Failed to attach ability');
            } finally {
                this.isAttaching = false;
            }
        }
    };
}
</script>
