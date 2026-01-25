<?php
/**
 * Dashboard Template
 *
 * Main dashboard page showing campaign summary, recent entities,
 * and quick create buttons.
 */

$this->layout('layouts/base', ['title' => 'Dashboard - Worlds']);
$this->section('content');
?>

<?php if (!isset($currentCampaign)): ?>
    <!-- No Campaign State -->
    <div class="flex items-center justify-center min-h-[60vh]">
        <div class="max-w-lg text-center">
            <div class="mb-8">
                <div class="inline-flex items-center justify-center w-24 h-24 bg-zinc-800 border-2 border-zinc-700 mb-6">
                    <svg class="w-12 h-12 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-zinc-200 mb-3">No Active Campaign</h2>
                <p class="text-zinc-500 mb-8">
                    Create your first campaign to start building your world.
                </p>
            </div>

            <a
                href="<?= url('/campaigns/create') ?>"
                class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-500 text-zinc-900 font-bold hover:bg-emerald-400 transition-colors"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Create Campaign
            </a>
        </div>
    </div>
<?php else: ?>
    <!-- Campaign Active -->
    <div class="space-y-8">
        <!-- Campaign Header -->
        <div class="border-b border-zinc-800 pb-6">
            <h1 class="text-4xl font-bold text-zinc-100 mb-2"><?= e($currentCampaign['name']) ?></h1>
            <?php if (!empty($currentCampaign['description'])): ?>
                <p class="text-zinc-400"><?= e($currentCampaign['description']) ?></p>
            <?php endif; ?>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <?php
            $stats = [
                ['label' => 'Characters', 'count' => $stats['character'] ?? 0, 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'color' => 'emerald'],
                ['label' => 'Locations', 'count' => $stats['location'] ?? 0, 'icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z', 'color' => 'blue'],
                ['label' => 'Quests', 'count' => $stats['quest'] ?? 0, 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'purple'],
                ['label' => 'Total Entities', 'count' => $stats['total'] ?? 0, 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'color' => 'amber'],
            ];
            ?>

            <?php foreach ($stats as $stat): ?>
                <div class="bg-zinc-900 border border-zinc-800 p-6 hover:border-<?= $stat['color'] ?>-400 transition-colors">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-zinc-500 text-sm uppercase tracking-wider mb-2"><?= e($stat['label']) ?></p>
                            <p class="text-3xl font-bold text-zinc-100"><?= $stat['count'] ?></p>
                        </div>
                        <div class="bg-<?= $stat['color'] ?>-500/10 p-3">
                            <svg class="w-6 h-6 text-<?= $stat['color'] ?>-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $stat['icon'] ?>"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Quick Create -->
        <div>
            <h2 class="text-xl font-bold text-zinc-200 mb-4 flex items-center gap-2">
                <span class="w-1 h-6 bg-emerald-400"></span>
                Quick Create
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
                <?php
                $quickCreate = [
                    ['type' => 'character', 'label' => 'Character', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                    ['type' => 'location', 'label' => 'Location', 'icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z'],
                    ['type' => 'item', 'label' => 'Item', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
                    ['type' => 'quest', 'label' => 'Quest', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['type' => 'note', 'label' => 'Note', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                ];
                ?>

                <?php foreach ($quickCreate as $entity): ?>
                    <a
                        href="<?= url('/entities/' . $entity['type'] . '/create') ?>"
                        class="flex flex-col items-center gap-3 p-4 bg-zinc-900 border border-zinc-800 hover:border-emerald-400 hover:bg-zinc-800 transition-colors group"
                    >
                        <svg class="w-8 h-8 text-zinc-600 group-hover:text-emerald-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $entity['icon'] ?>"></path>
                        </svg>
                        <span class="text-sm font-medium text-zinc-400 group-hover:text-zinc-200 transition-colors">
                            <?= e($entity['label']) ?>
                        </span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Recent Entities -->
        <?php if (!empty($recentEntities)): ?>
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-zinc-200 flex items-center gap-2">
                        <span class="w-1 h-6 bg-emerald-400"></span>
                        Recent Entities
                    </h2>
                    <a href="<?= url('/entities') ?>" class="text-sm text-emerald-400 hover:text-emerald-300 transition-colors">
                        View All →
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <?php foreach (array_slice($recentEntities, 0, 6) as $entity): ?>
                        <a
                            href="<?= url('/entities/' . $entity['entity_type'] . '/' . $entity['id']) ?>"
                            class="bg-zinc-900 border border-zinc-800 p-4 hover:border-emerald-400 transition-colors group"
                        >
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-12 h-12 bg-zinc-800 border border-zinc-700 group-hover:border-emerald-400 transition-colors flex items-center justify-center">
                                    <span class="text-zinc-500 font-bold text-xs uppercase">
                                        <?= strtoupper(substr($entity['entity_type'], 0, 2)) ?>
                                    </span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-zinc-200 font-semibold mb-1 truncate group-hover:text-emerald-400 transition-colors">
                                        <?= e($entity['name']) ?>
                                    </h3>
                                    <p class="text-zinc-500 text-sm capitalize">
                                        <?= e($entity['entity_type']) ?>
                                    </p>
                                    <p class="text-zinc-600 text-xs mt-1">
                                        Updated <?= date('M j, Y', strtotime($entity['updated_at'])) ?>
                                    </p>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else: ?>
            <!-- Empty State -->
            <div class="bg-zinc-900 border border-zinc-800 p-12 text-center">
                <svg class="w-16 h-16 text-zinc-700 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                <h3 class="text-xl font-bold text-zinc-400 mb-2">No Entities Yet</h3>
                <p class="text-zinc-600 mb-6">Start creating entities to populate your world.</p>
                <a
                    href="<?= url('/entities/character/create') ?>"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-500 text-zinc-900 font-bold hover:bg-emerald-400 transition-colors"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Create First Entity
                </a>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php $this->endSection(); ?>
