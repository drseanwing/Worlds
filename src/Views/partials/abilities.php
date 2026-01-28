<?php
/**
 * Abilities Partial
 *
 * Displays abilities attached to an entity with usage tracking.
 * Expected variables:
 * - $entity: Entity array with 'id' key
 * - $abilities: Array of ability attachments (optional, will fetch if not provided)
 */

use Worlds\Repositories\AbilityAttachmentRepository;

// Fetch abilities if not provided
if (!isset($abilities)) {
    $abilityRepo = new AbilityAttachmentRepository();
    $abilities = $abilityRepo->findByEntity($entity['id']);
}
?>

<div class="abilities-section bg-white rounded-lg shadow-md p-6 mb-6" x-data="abilityManager(<?= htmlspecialchars(json_encode($entity['id'])) ?>)">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-semibold text-gray-800">Abilities</h3>
        <button
            @click="showPicker = true"
            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm transition"
        >
            + Add Ability
        </button>
    </div>

    <?php if (empty($abilities)): ?>
        <p class="text-gray-500 italic">No abilities attached yet.</p>
    <?php else: ?>
        <div class="space-y-3">
            <?php foreach ($abilities as $ability): ?>
                <?php
                    $abilityData = $ability['ability_data'] ?? [];
                    $maxCharges = $abilityData['charges'] ?? null;
                    $chargesUsed = $ability['charges_used'] ?? 0;
                    $chargesRemaining = $maxCharges !== null ? ($maxCharges - $chargesUsed) : null;
                ?>
                <div class="border border-gray-200 rounded-lg p-4 hover:border-gray-300 transition">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <a
                                    href="/entities/ability/<?= htmlspecialchars($ability['ability_entity_id']) ?>"
                                    class="text-lg font-medium text-blue-600 hover:text-blue-800"
                                >
                                    <?= htmlspecialchars($ability['ability_name']) ?>
                                </a>
                                <?php if (!empty($ability['ability_type'])): ?>
                                    <span class="text-sm text-gray-500 bg-gray-100 px-2 py-1 rounded">
                                        <?= htmlspecialchars($ability['ability_type']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <?php if ($maxCharges !== null): ?>
                                <div class="mb-2">
                                    <div class="flex items-center gap-2 text-sm">
                                        <span class="text-gray-600">Charges:</span>
                                        <span class="font-medium <?= $chargesRemaining <= 0 ? 'text-red-600' : 'text-gray-800' ?>">
                                            <?= htmlspecialchars($chargesRemaining) ?> / <?= htmlspecialchars($maxCharges) ?> remaining
                                        </span>
                                    </div>
                                    <input
                                        type="range"
                                        min="0"
                                        max="<?= htmlspecialchars($maxCharges) ?>"
                                        value="<?= htmlspecialchars($chargesUsed) ?>"
                                        @change="updateCharges(<?= htmlspecialchars($ability['id']) ?>, $event.target.value)"
                                        class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer mt-1"
                                    >
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($ability['ability_entry'])): ?>
                                <div class="text-sm text-gray-600 mb-2 line-clamp-2">
                                    <?= nl2br(htmlspecialchars(substr($ability['ability_entry'], 0, 150))) ?>
                                    <?php if (strlen($ability['ability_entry']) > 150): ?>...<?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($ability['notes'])): ?>
                                <div class="text-sm bg-yellow-50 border-l-4 border-yellow-400 p-2 mt-2">
                                    <span class="font-medium text-yellow-800">Notes:</span>
                                    <span class="text-yellow-700"><?= nl2br(htmlspecialchars($ability['notes'])) ?></span>
                                </div>
                            <?php endif; ?>

                            <div class="mt-2">
                                <button
                                    @click="editNotes(<?= (int)$ability['id'] ?>, <?= json_encode($ability['notes'] ?? '') ?>)"
                                    class="text-sm text-blue-600 hover:text-blue-800"
                                >
                                    <?= empty($ability['notes']) ? 'Add notes' : 'Edit notes' ?>
                                </button>
                            </div>
                        </div>

                        <button
                            @click="removeAbility(<?= htmlspecialchars($ability['id']) ?>)"
                            class="text-red-600 hover:text-red-800 ml-4"
                            title="Remove ability"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Ability Picker Modal -->
    <div x-show="showPicker"
         x-cloak
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
         @click.self="showPicker = false"
    >
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[80vh] overflow-hidden">
            <?php include __DIR__ . '/ability-picker.php'; ?>
        </div>
    </div>

    <!-- Notes Edit Modal -->
    <div x-show="editingNotes"
         x-cloak
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
         @click.self="editingNotes = false"
    >
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full mx-4 p-6">
            <h4 class="text-lg font-semibold mb-4">Edit Notes</h4>
            <textarea
                x-model="currentNotes"
                class="w-full border border-gray-300 rounded p-2 mb-4 h-32"
                placeholder="Add notes about this ability..."
            ></textarea>
            <div class="flex justify-end gap-2">
                <button
                    @click="editingNotes = false"
                    class="px-4 py-2 text-gray-600 hover:text-gray-800"
                >
                    Cancel
                </button>
                <button
                    @click="saveNotes()"
                    class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                >
                    Save
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function abilityManager(entityId) {
    return {
        entityId: entityId,
        showPicker: false,
        editingNotes: false,
        currentAttachmentId: null,
        currentNotes: '',

        async updateCharges(attachmentId, chargesUsed) {
            try {
                const formData = new FormData();
                formData.append('_csrf_token', '<?= csrf_token() ?>');
                formData.append('charges_used', chargesUsed);

                const response = await fetch(`/api/ability-attachments/${attachmentId}`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-HTTP-Method-Override': 'PUT'
                    }
                });

                const data = await response.json();

                if (!data.success) {
                    alert('Failed to update charges: ' + (data.error || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error updating charges:', error);
                alert('Failed to update charges');
            }
        },

        editNotes(attachmentId, currentNotes) {
            this.currentAttachmentId = attachmentId;
            this.currentNotes = currentNotes;
            this.editingNotes = true;
        },

        async saveNotes() {
            try {
                const formData = new FormData();
                formData.append('_csrf_token', '<?= csrf_token() ?>');
                formData.append('notes', this.currentNotes);

                const response = await fetch(`/api/ability-attachments/${this.currentAttachmentId}`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-HTTP-Method-Override': 'PUT'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    this.editingNotes = false;
                    location.reload(); // Reload to show updated notes
                } else {
                    alert('Failed to save notes: ' + (data.error || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error saving notes:', error);
                alert('Failed to save notes');
            }
        },

        async removeAbility(attachmentId) {
            if (!confirm('Are you sure you want to remove this ability?')) {
                return;
            }

            try {
                const formData = new FormData();
                formData.append('_csrf_token', '<?= csrf_token() ?>');

                const response = await fetch(`/api/ability-attachments/${attachmentId}`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-HTTP-Method-Override': 'DELETE'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    location.reload(); // Reload to show updated list
                } else {
                    alert('Failed to remove ability: ' + (data.error || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error removing ability:', error);
                alert('Failed to remove ability');
            }
        }
    };
}
</script>

<style>
[x-cloak] { display: none !important; }

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
