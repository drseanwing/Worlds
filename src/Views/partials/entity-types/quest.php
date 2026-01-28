<?php
/**
 * Quest Entity Type Partial
 * Displays quest-specific attributes and details
 *
 * Props:
 * - $entity: Entity data containing quest-specific attributes
 * - $attributes: Array of custom attributes (indexed by name for easy access)
 */

// Helper function to safely get attribute value
function getQuestAttr($attributes, $name, $default = null) {
    foreach ($attributes as $attr) {
        if ($attr['name'] === $name) {
            return $attr['value'] ?? $default;
        }
    }
    return $default;
}

// Extract quest-specific attributes
$questType = getQuestAttr($attributes, 'quest_type', 'side');
$status = getQuestAttr($attributes, 'status', 'planned');
$giverId = getQuestAttr($attributes, 'giver_id');
$reward = getQuestAttr($attributes, 'reward');
$difficulty = getQuestAttr($attributes, 'difficulty', 'medium');
$locationIds = getQuestAttr($attributes, 'location_ids');
$characterIds = getQuestAttr($attributes, 'character_ids');
$parentQuestId = getQuestAttr($attributes, 'parent_id');

// Parse JSON arrays
$locationIds = $locationIds ? json_decode($locationIds, true) : [];
$characterIds = $characterIds ? json_decode($characterIds, true) : [];

// Status badge configuration
$statusConfig = [
    'planned' => [
        'color' => 'from-gray-600 to-gray-700',
        'border' => 'border-gray-500/50',
        'text' => 'text-gray-200',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>',
        'label' => 'Planned'
    ],
    'ongoing' => [
        'color' => 'from-amber-500 to-orange-600',
        'border' => 'border-amber-400/60',
        'text' => 'text-amber-100',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>',
        'label' => 'In Progress',
        'glow' => 'shadow-amber-500/40'
    ],
    'completed' => [
        'color' => 'from-emerald-500 to-green-600',
        'border' => 'border-emerald-400/60',
        'text' => 'text-emerald-100',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        'label' => 'Completed',
        'glow' => 'shadow-emerald-500/40'
    ],
    'failed' => [
        'color' => 'from-red-600 to-rose-700',
        'border' => 'border-red-500/60',
        'text' => 'text-red-100',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        'label' => 'Failed'
    ],
    'abandoned' => [
        'color' => 'from-purple-600 to-violet-700',
        'border' => 'border-purple-500/60',
        'text' => 'text-purple-100',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>',
        'label' => 'Abandoned'
    ]
];

$currentStatus = $statusConfig[$status] ?? $statusConfig['planned'];

// Difficulty configuration
$difficultyConfig = [
    'easy' => [
        'color' => 'text-green-400',
        'bg' => 'bg-green-500/10',
        'border' => 'border-green-500/30',
        'label' => 'Easy',
        'stars' => 1
    ],
    'medium' => [
        'color' => 'text-yellow-400',
        'bg' => 'bg-yellow-500/10',
        'border' => 'border-yellow-500/30',
        'label' => 'Medium',
        'stars' => 2
    ],
    'hard' => [
        'color' => 'text-orange-400',
        'bg' => 'bg-orange-500/10',
        'border' => 'border-orange-500/30',
        'label' => 'Hard',
        'stars' => 3
    ],
    'deadly' => [
        'color' => 'text-red-400',
        'bg' => 'bg-red-500/10',
        'border' => 'border-red-500/30',
        'label' => 'Deadly',
        'stars' => 4
    ]
];

$currentDifficulty = $difficultyConfig[$difficulty] ?? $difficultyConfig['medium'];

// Quest type configuration
$questTypeConfig = [
    'main' => ['color' => 'text-yellow-400', 'bg' => 'bg-yellow-500/10', 'border' => 'border-yellow-500/40', 'label' => 'Main Quest'],
    'side' => ['color' => 'text-blue-400', 'bg' => 'bg-blue-500/10', 'border' => 'border-blue-500/40', 'label' => 'Side Quest'],
    'character' => ['color' => 'text-purple-400', 'bg' => 'bg-purple-500/10', 'border' => 'border-purple-500/40', 'label' => 'Character Quest'],
    'bounty' => ['color' => 'text-red-400', 'bg' => 'bg-red-500/10', 'border' => 'border-red-500/40', 'label' => 'Bounty'],
    'optional' => ['color' => 'text-gray-400', 'bg' => 'bg-gray-500/10', 'border' => 'border-gray-500/40', 'label' => 'Optional']
];

$currentType = $questTypeConfig[$questType] ?? $questTypeConfig['side'];
?>

<!-- Quest Details Card -->
<div class="relative overflow-hidden">
    <!-- Parchment texture background -->
    <div class="absolute inset-0 opacity-5 pointer-events-none" style="background-image: url('data:image/svg+xml,%3Csvg width=%22100%22 height=%22100%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noise%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.8%22 numOctaves=%224%22/%3E%3C/filter%3E%3Crect width=%22100%22 height=%22100%22 filter=%22url(%23noise)%22 opacity=%220.3%22/%3E%3C/svg%3E');"></div>

    <!-- Decorative corner flourishes -->
    <div class="absolute top-0 left-0 w-24 h-24 opacity-10">
        <svg viewBox="0 0 100 100" class="w-full h-full text-amber-500">
            <path d="M0,0 L30,0 Q40,10 30,20 L0,50 Z" fill="currentColor"/>
        </svg>
    </div>
    <div class="absolute top-0 right-0 w-24 h-24 opacity-10 transform rotate-90">
        <svg viewBox="0 0 100 100" class="w-full h-full text-amber-500">
            <path d="M0,0 L30,0 Q40,10 30,20 L0,50 Z" fill="currentColor"/>
        </svg>
    </div>

    <div class="relative bg-gradient-to-br from-amber-950/30 via-slate-900/50 to-slate-800/30 backdrop-blur-sm border-2 border-amber-700/20 rounded-lg p-8">

        <!-- Quest Header -->
        <div class="flex items-start justify-between mb-6 pb-6 border-b border-amber-700/20">
            <div class="flex-1">
                <!-- Quest Icon & Title -->
                <div class="flex items-start gap-4 mb-4">
                    <div class="flex-shrink-0 w-16 h-16 bg-gradient-to-br from-amber-600/20 to-yellow-600/20 border-2 border-amber-500/30 rounded-lg flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-8 h-8 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-2xl font-bold text-amber-100 mb-2 tracking-wide font-serif">Quest Details</h3>
                        <p class="text-gray-400 text-sm italic">A tale of adventure awaits...</p>
                    </div>
                </div>

                <!-- Status & Type Badges -->
                <div class="flex flex-wrap gap-3">
                    <!-- Status Badge -->
                    <div class="relative group">
                        <div class="absolute -inset-0.5 bg-gradient-to-r <?= $currentStatus['color'] ?> opacity-30 blur group-hover:opacity-50 transition-opacity rounded-lg"></div>
                        <div class="relative flex items-center gap-2 px-4 py-2 bg-gradient-to-br <?= $currentStatus['color'] ?> border <?= $currentStatus['border'] ?> rounded-lg backdrop-blur-sm <?= $currentStatus['glow'] ?? '' ?> shadow-lg">
                            <svg class="w-5 h-5 <?= $currentStatus['text'] ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <?= $currentStatus['icon'] ?>
                            </svg>
                            <span class="font-bold text-sm uppercase tracking-wider <?= $currentStatus['text'] ?>">
                                <?= $currentStatus['label'] ?>
                            </span>
                        </div>
                    </div>

                    <!-- Quest Type Badge -->
                    <div class="flex items-center gap-2 px-4 py-2 <?= $currentType['bg'] ?> border <?= $currentType['border'] ?> rounded-lg backdrop-blur-sm">
                        <svg class="w-4 h-4 <?= $currentType['color'] ?>" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-semibold text-xs uppercase tracking-wider <?= $currentType['color'] ?>">
                            <?= $currentType['label'] ?>
                        </span>
                    </div>

                    <!-- Difficulty Badge -->
                    <div class="flex items-center gap-2 px-4 py-2 <?= $currentDifficulty['bg'] ?> border <?= $currentDifficulty['border'] ?> rounded-lg backdrop-blur-sm">
                        <div class="flex items-center gap-0.5">
                            <?php for ($i = 0; $i < 4; $i++): ?>
                                <svg class="w-3 h-3 <?= $i < $currentDifficulty['stars'] ? $currentDifficulty['color'] : 'text-gray-700' ?>" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            <?php endfor; ?>
                        </div>
                        <span class="font-semibold text-xs uppercase tracking-wider <?= $currentDifficulty['color'] ?>">
                            <?= $currentDifficulty['label'] ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Bar for Ongoing Quests -->
        <?php if ($status === 'ongoing'): ?>
        <div class="mb-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-semibold text-gray-400">Quest Progress</span>
                <span class="text-sm font-bold text-amber-400">In Progress</span>
            </div>
            <div class="relative h-3 bg-slate-800/50 rounded-full overflow-hidden border border-amber-700/30">
                <div class="absolute inset-0 bg-gradient-to-r from-amber-600 via-yellow-500 to-amber-600 animate-pulse" style="width: 60%; background-size: 200% 100%; animation: shimmer 2s infinite linear;">
                </div>
                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent animate-shine"></div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Quest Information Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Quest Giver -->
            <?php if ($giverId): ?>
            <div class="bg-slate-900/30 border border-slate-700/30 rounded-lg p-4 hover:border-amber-600/40 transition-all group">
                <div class="flex items-center gap-3 mb-2">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Quest Giver</span>
                </div>
                <a href="<?= url('/entities/character/' . $giverId) ?>" class="text-amber-300 hover:text-amber-200 font-semibold text-lg group-hover:underline transition-colors">
                    Character #<?= e($giverId) ?>
                </a>
            </div>
            <?php endif; ?>

            <!-- Reward -->
            <?php if ($reward): ?>
            <div class="bg-slate-900/30 border border-slate-700/30 rounded-lg p-4 hover:border-yellow-600/40 transition-all group">
                <div class="flex items-center gap-3 mb-2">
                    <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Reward</span>
                </div>
                <p class="text-yellow-300 font-semibold text-lg"><?= e($reward) ?></p>
            </div>
            <?php endif; ?>

        </div>

        <!-- Quest Locations -->
        <?php if (!empty($locationIds)): ?>
        <div class="mt-6 bg-slate-900/30 border border-slate-700/30 rounded-lg p-5">
            <div class="flex items-center gap-3 mb-4">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <h4 class="text-sm font-bold uppercase tracking-wider text-gray-400">Quest Locations</h4>
            </div>
            <div class="flex flex-wrap gap-2">
                <?php foreach ($locationIds as $locationId): ?>
                <a href="<?= url('/entities/location/' . $locationId) ?>"
                   class="inline-flex items-center gap-2 px-3 py-2 bg-emerald-500/10 hover:bg-emerald-500/20 border border-emerald-600/30 hover:border-emerald-500/50 rounded-lg transition-all group">
                    <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-emerald-300 group-hover:text-emerald-200 font-medium text-sm">Location #<?= e($locationId) ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Quest Characters -->
        <?php if (!empty($characterIds)): ?>
        <div class="mt-6 bg-slate-900/30 border border-slate-700/30 rounded-lg p-5">
            <div class="flex items-center gap-3 mb-4">
                <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <h4 class="text-sm font-bold uppercase tracking-wider text-gray-400">Related Characters</h4>
            </div>
            <div class="flex flex-wrap gap-2">
                <?php foreach ($characterIds as $characterId): ?>
                <a href="<?= url('/entities/character/' . $characterId) ?>"
                   class="inline-flex items-center gap-2 px-3 py-2 bg-purple-500/10 hover:bg-purple-500/20 border border-purple-600/30 hover:border-purple-500/50 rounded-lg transition-all group">
                    <svg class="w-4 h-4 text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-purple-300 group-hover:text-purple-200 font-medium text-sm">Character #<?= e($characterId) ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Parent Quest / Quest Chain -->
        <?php if ($parentQuestId): ?>
        <div class="mt-6 bg-gradient-to-r from-indigo-950/30 to-purple-950/30 border border-indigo-700/30 rounded-lg p-5">
            <div class="flex items-center gap-3 mb-3">
                <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                <h4 class="text-sm font-bold uppercase tracking-wider text-indigo-300">Quest Chain</h4>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-gray-400 text-sm">Part of quest:</span>
                <a href="<?= url('/entities/quest/' . $parentQuestId) ?>"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-500/20 hover:bg-indigo-500/30 border border-indigo-500/40 hover:border-indigo-400/60 rounded-lg transition-all group">
                    <svg class="w-4 h-4 text-indigo-400 group-hover:text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    <span class="text-indigo-200 group-hover:text-indigo-100 font-semibold">Quest #<?= e($parentQuestId) ?></span>
                </a>
            </div>
        </div>
        <?php endif; ?>

        <!-- Decorative Seal/Stamp -->
        <div class="absolute bottom-4 right-4 w-20 h-20 opacity-10">
            <svg viewBox="0 0 100 100" class="w-full h-full text-amber-600">
                <circle cx="50" cy="50" r="45" fill="none" stroke="currentColor" stroke-width="3"/>
                <circle cx="50" cy="50" r="35" fill="none" stroke="currentColor" stroke-width="2"/>
                <path d="M50 20 L55 45 L50 50 L45 45 Z" fill="currentColor"/>
                <path d="M50 80 L55 55 L50 50 L45 55 Z" fill="currentColor"/>
                <path d="M20 50 L45 45 L50 50 L45 55 Z" fill="currentColor"/>
                <path d="M80 50 L55 45 L50 50 L55 55 Z" fill="currentColor"/>
            </svg>
        </div>
    </div>
</div>

<!-- Inline Animation Styles -->
<style>
@keyframes shimmer {
    0% { background-position: -200% 0; }
    100% { background-position: 200% 0; }
}

@keyframes shine {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

.animate-shine {
    animation: shine 3s infinite;
}
</style>
