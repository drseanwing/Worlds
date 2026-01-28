<?php
/**
 * Timeline Entity Type Partial
 * Displays timeline-specific fields with vertical timeline visualization
 *
 * Props:
 * - $entity: Entity data with timeline-specific fields
 *   - timeline_data: JSON object with eras and entries
 *     - eras: Array of { name, start_year, end_year, description, colour }
 *     - entries: Array of { year, date, title, description, entity_id, era }
 *     - calendar_id: Optional calendar reference
 */

$timelineData = !empty($entity['timeline_data']) ? json_decode($entity['timeline_data'], true) : null;
$eras = $timelineData['eras'] ?? [];
$entries = $timelineData['entries'] ?? [];
$calendarId = $timelineData['calendar_id'] ?? null;

// Sort entries by year
usort($entries, function($a, $b) {
    return ($a['year'] ?? 0) <=> ($b['year'] ?? 0);
});

// Group entries by era
$entriesByEra = [];
foreach ($entries as $entry) {
    $eraName = $entry['era'] ?? 'Uncategorized';
    if (!isset($entriesByEra[$eraName])) {
        $entriesByEra[$eraName] = [];
    }
    $entriesByEra[$eraName][] = $entry;
}
?>

<div x-data="{
    selectedEra: 'all',
    showForm: false,
    filterByEra(era) {
        this.selectedEra = era;
    }
}">
    <!-- Timeline Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold bg-gradient-to-r from-amber-200 via-amber-400 to-orange-300 bg-clip-text text-transparent tracking-tight flex items-center">
                <svg class="w-6 h-6 mr-3 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Timeline
            </h2>
            <button
                @click="showForm = !showForm"
                class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-400 text-zinc-900 font-semibold text-sm transition-all duration-200 shadow-lg shadow-amber-900/30 hover:shadow-amber-900/50 hover:scale-105"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Entry
            </button>
        </div>

        <!-- Calendar Reference -->
        <?php if ($calendarId): ?>
        <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-zinc-800/50 border border-zinc-700 text-zinc-400 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Calendar:
            <a href="<?= url('/entities/calendar/' . $calendarId) ?>" class="text-amber-400 hover:text-amber-300 font-medium transition-colors">
                View Calendar
            </a>
        </div>
        <?php endif; ?>
    </div>

    <!-- Eras Display -->
    <?php if (!empty($eras)): ?>
    <div class="mb-8">
        <h3 class="text-lg font-bold text-zinc-200 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
            </svg>
            Historical Eras
        </h3>

        <!-- Era Filter Buttons -->
        <div class="flex flex-wrap gap-2 mb-4">
            <button
                @click="filterByEra('all')"
                :class="selectedEra === 'all' ? 'bg-amber-500 text-zinc-900 border-amber-400' : 'bg-zinc-800/50 text-zinc-400 border-zinc-700 hover:border-amber-400/50'"
                class="px-3 py-1.5 border font-medium text-sm transition-all duration-200"
            >
                All Entries
            </button>
            <?php foreach ($eras as $era): ?>
            <button
                @click="filterByEra('<?= e($era['name'] ?? '') ?>')"
                :class="selectedEra === '<?= e($era['name'] ?? '') ?>' ? 'border-2' : 'border hover:scale-105'"
                style="
                    background: linear-gradient(135deg, <?= e($era['colour'] ?? '#888') ?>22 0%, <?= e($era['colour'] ?? '#888') ?>11 100%);
                    border-color: <?= e($era['colour'] ?? '#888') ?>;
                    color: <?= e($era['colour'] ?? '#888') ?>;
                "
                class="px-3 py-1.5 font-medium text-sm transition-all duration-200 shadow-sm"
            >
                <?= e($era['name'] ?? 'Unknown Era') ?>
            </button>
            <?php endforeach; ?>
        </div>

        <!-- Era Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach ($eras as $era): ?>
            <div
                class="relative group overflow-hidden border-l-4 transition-all duration-300 hover:shadow-lg"
                style="
                    background: linear-gradient(135deg, <?= e($era['colour'] ?? '#888') ?>11 0%, transparent 100%);
                    border-left-color: <?= e($era['colour'] ?? '#888') ?>;
                "
            >
                <div class="p-4 bg-zinc-900/60 backdrop-blur-sm">
                    <div class="flex items-start justify-between mb-2">
                        <h4 class="text-lg font-bold text-zinc-100 group-hover:text-amber-400 transition-colors">
                            <?= e($era['name'] ?? 'Unknown Era') ?>
                        </h4>
                        <div
                            class="w-3 h-3 rounded-full shadow-lg"
                            style="background: <?= e($era['colour'] ?? '#888') ?>; box-shadow: 0 0 12px <?= e($era['colour'] ?? '#888') ?>66;"
                        ></div>
                    </div>

                    <div class="flex items-center gap-2 text-sm text-zinc-400 mb-3 font-mono">
                        <span><?= e($era['start_year'] ?? '?') ?></span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                        <span><?= e($era['end_year'] ?? '?') ?></span>
                    </div>

                    <?php if (!empty($era['description'])): ?>
                    <p class="text-zinc-500 text-sm line-clamp-2">
                        <?= e($era['description']) ?>
                    </p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Timeline Entries -->
    <?php if (!empty($entries)): ?>
    <div class="mb-8">
        <h3 class="text-lg font-bold text-zinc-200 mb-6 flex items-center">
            <svg class="w-5 h-5 mr-2 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Timeline Entries
        </h3>

        <!-- Vertical Timeline -->
        <div class="relative">
            <!-- Timeline vertical line -->
            <div class="absolute left-16 top-0 bottom-0 w-0.5 bg-gradient-to-b from-amber-500/50 via-amber-500/30 to-transparent"></div>

            <!-- Timeline Entries -->
            <div class="space-y-6">
                <?php foreach ($entries as $index => $entry): ?>
                <?php
                    $entryEra = $entry['era'] ?? null;
                    $eraColor = '#888';

                    // Find era color
                    foreach ($eras as $era) {
                        if ($era['name'] === $entryEra) {
                            $eraColor = $era['colour'] ?? '#888';
                            break;
                        }
                    }
                ?>
                <div
                    x-show="selectedEra === 'all' || selectedEra === '<?= e($entryEra ?? '') ?>'"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform translate-y-4"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    class="relative flex gap-6 group"
                    style="animation-delay: <?= $index * 50 ?>ms;"
                >
                    <!-- Year Display (Left Side) -->
                    <div class="w-24 flex-shrink-0 text-right">
                        <div class="inline-block">
                            <div class="text-2xl font-bold text-zinc-100 font-mono tabular-nums">
                                <?= e($entry['year'] ?? '?') ?>
                            </div>
                            <?php if (!empty($entry['date'])): ?>
                            <div class="text-xs text-zinc-500 mt-1">
                                <?= e($entry['date']) ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Timeline Dot -->
                    <div class="relative flex-shrink-0" style="width: 1rem;">
                        <div
                            class="absolute top-2 left-1/2 -translate-x-1/2 w-4 h-4 rounded-full border-4 border-zinc-900 transition-all duration-300 group-hover:scale-150"
                            style="background: <?= e($eraColor) ?>; box-shadow: 0 0 16px <?= e($eraColor) ?>88;"
                        ></div>

                        <!-- Connecting line to card -->
                        <div
                            class="absolute top-3 left-1/2 w-6 h-0.5 opacity-50"
                            style="background: <?= e($eraColor) ?>;"
                        ></div>
                    </div>

                    <!-- Entry Card (Right Side) -->
                    <div class="flex-1 pb-2">
                        <div
                            class="relative group/card overflow-hidden border-l-4 bg-zinc-900/60 backdrop-blur-sm hover:bg-zinc-900/80 transition-all duration-300 hover:shadow-xl hover:-translate-y-1"
                            style="border-left-color: <?= e($eraColor) ?>;"
                        >
                            <!-- Gradient overlay -->
                            <div
                                class="absolute inset-0 opacity-5 group-hover/card:opacity-10 transition-opacity"
                                style="background: linear-gradient(135deg, <?= e($eraColor) ?> 0%, transparent 60%);"
                            ></div>

                            <div class="relative p-5">
                                <!-- Era Badge -->
                                <?php if ($entryEra): ?>
                                <div class="mb-3">
                                    <span
                                        class="inline-block px-2 py-1 text-xs font-bold uppercase tracking-wider border"
                                        style="
                                            background: <?= e($eraColor) ?>22;
                                            border-color: <?= e($eraColor) ?>;
                                            color: <?= e($eraColor) ?>;
                                        "
                                    >
                                        <?= e($entryEra) ?>
                                    </span>
                                </div>
                                <?php endif; ?>

                                <!-- Entry Title -->
                                <h4 class="text-xl font-bold text-zinc-100 mb-3 group-hover/card:text-amber-400 transition-colors">
                                    <?= e($entry['title'] ?? 'Untitled Event') ?>
                                </h4>

                                <!-- Entry Description -->
                                <?php if (!empty($entry['description'])): ?>
                                <p class="text-zinc-400 leading-relaxed mb-4">
                                    <?= nl2br(e($entry['description'])) ?>
                                </p>
                                <?php endif; ?>

                                <!-- Entity Link -->
                                <?php if (!empty($entry['entity_id'])): ?>
                                <div class="flex items-center gap-2 pt-3 border-t border-zinc-800">
                                    <svg class="w-4 h-4 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                    </svg>
                                    <span class="text-zinc-600 text-sm">Related:</span>
                                    <a
                                        href="<?= url('/entities/entity/' . $entry['entity_id']) ?>"
                                        class="text-amber-400 hover:text-amber-300 text-sm font-medium transition-colors"
                                    >
                                        View Entity
                                    </a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php else: ?>
    <!-- Empty State -->
    <div class="mb-8">
        <div class="bg-zinc-900/50 border border-zinc-800 p-12 text-center">
            <svg class="w-16 h-16 text-zinc-700 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-zinc-500 text-lg mb-2">No timeline entries yet</p>
            <p class="text-zinc-600 text-sm mb-4">Start building your timeline by adding historical events</p>
            <button
                @click="showForm = true"
                class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-400 text-zinc-900 font-semibold text-sm transition-all duration-200"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add First Entry
            </button>
        </div>
    </div>
    <?php endif; ?>

    <!-- Add Entry Form (Placeholder) -->
    <div
        x-show="showForm"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform scale-95"
        x-transition:enter-end="opacity-100 transform scale-100"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
        @click.self="showForm = false"
    >
        <div class="bg-zinc-900 border border-zinc-700 max-w-2xl w-full shadow-2xl overflow-hidden">
            <!-- Form Header -->
            <div class="bg-gradient-to-r from-amber-500/20 to-orange-500/20 border-b border-amber-500/30 px-6 py-4 flex items-center justify-between">
                <h3 class="text-xl font-bold text-zinc-100 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Timeline Entry
                </h3>
                <button
                    @click="showForm = false"
                    class="text-zinc-400 hover:text-zinc-100 transition-colors"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Form Content -->
            <div class="p-6">
                <div class="bg-zinc-800/50 border border-zinc-700 p-8 text-center">
                    <svg class="w-12 h-12 text-zinc-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <p class="text-zinc-400 text-sm">
                        Timeline entry form coming soon
                    </p>
                    <p class="text-zinc-600 text-xs mt-2">
                        This feature will allow you to add new events to your timeline
                    </p>
                </div>
            </div>

            <!-- Form Footer -->
            <div class="bg-zinc-950/50 border-t border-zinc-800 px-6 py-4 flex justify-end gap-3">
                <button
                    @click="showForm = false"
                    class="px-4 py-2 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 font-medium transition-colors"
                >
                    Cancel
                </button>
                <button
                    class="px-4 py-2 bg-amber-500 hover:bg-amber-400 text-zinc-900 font-semibold transition-colors"
                    disabled
                >
                    Add Entry
                </button>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.space-y-6 > div {
    animation: fadeInUp 0.4s ease-out backwards;
}
</style>
