<?php
/**
 * Organisation Entity Type Partial
 * Displays organisation-specific fields and data
 *
 * Props:
 * - $entity: Full entity data including organisation-specific fields
 *   - organisation_type: Guild/Religion/Military/Government/etc.
 *   - headquarters_id: Location entity ID
 *   - goals: Primary objectives (text)
 *   - founding_date: When founded (date)
 *   - leader_id: Character entity ID
 *   - member_count: Approximate member count
 *   - ranks: JSON array of rank names/hierarchy
 */

$organisationType = $entity['organisation_type'] ?? 'Unknown';
$foundingDate = $entity['founding_date'] ?? null;
$memberCount = $entity['member_count'] ?? 0;
$goals = $entity['goals'] ?? '';
$ranks = !empty($entity['ranks']) ? json_decode($entity['ranks'], true) : [];
$headquartersId = $entity['headquarters_id'] ?? null;
$leaderId = $entity['leader_id'] ?? null;

// Type-specific colors and icons
$typeStyles = [
    'Guild' => ['bg' => 'from-amber-900/40 to-amber-950/20', 'border' => 'border-amber-700/50', 'text' => 'text-amber-400', 'icon' => 'briefcase'],
    'Religion' => ['bg' => 'from-violet-900/40 to-violet-950/20', 'border' => 'border-violet-700/50', 'text' => 'text-violet-400', 'icon' => 'star'],
    'Military' => ['bg' => 'from-red-900/40 to-red-950/20', 'border' => 'border-red-700/50', 'text' => 'text-red-400', 'icon' => 'shield'],
    'Government' => ['bg' => 'from-blue-900/40 to-blue-950/20', 'border' => 'border-blue-700/50', 'text' => 'text-blue-400', 'icon' => 'building'],
    'Trade' => ['bg' => 'from-emerald-900/40 to-emerald-950/20', 'border' => 'border-emerald-700/50', 'text' => 'text-emerald-400', 'icon' => 'currency'],
    'Criminal' => ['bg' => 'from-gray-900/40 to-black/20', 'border' => 'border-gray-700/50', 'text' => 'text-gray-400', 'icon' => 'mask'],
];

$style = $typeStyles[$organisationType] ?? ['bg' => 'from-zinc-900/40 to-zinc-950/20', 'border' => 'border-zinc-700/50', 'text' => 'text-zinc-400', 'icon' => 'building'];
?>

<div class="space-y-6">
    <!-- Organisation Type & Stats Hero -->
    <div class="relative bg-gradient-to-br <?= $style['bg'] ?> border <?= $style['border'] ?> overflow-hidden">
        <!-- Decorative Background Pattern -->
        <div class="absolute inset-0 opacity-5" style="background-image: url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"1\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>

        <div class="relative p-6">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1">
                    <!-- Organisation Type Badge -->
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-zinc-950/80 backdrop-blur-sm border <?= $style['border'] ?> mb-4">
                        <svg class="w-5 h-5 <?= $style['text'] ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <?php if ($style['icon'] === 'briefcase'): ?>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            <?php elseif ($style['icon'] === 'star'): ?>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                            <?php elseif ($style['icon'] === 'shield'): ?>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.618 5.984A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016zM12 9v2m0 4h.01"></path>
                            <?php elseif ($style['icon'] === 'building'): ?>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            <?php elseif ($style['icon'] === 'currency'): ?>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            <?php elseif ($style['icon'] === 'mask'): ?>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            <?php else: ?>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            <?php endif; ?>
                        </svg>
                        <span class="<?= $style['text'] ?> font-black text-lg uppercase tracking-widest">
                            <?= e($organisationType) ?>
                        </span>
                    </div>

                    <!-- Quick Stats -->
                    <div class="grid grid-cols-2 gap-4">
                        <?php if ($foundingDate): ?>
                            <div>
                                <p class="text-zinc-600 text-xs font-bold uppercase tracking-wider mb-1">Founded</p>
                                <p class="text-zinc-200 font-bold text-xl">
                                    <?= date('Y', strtotime($foundingDate)) ?>
                                </p>
                                <p class="text-zinc-500 text-sm">
                                    <?= date('F j', strtotime($foundingDate)) ?>
                                </p>
                            </div>
                        <?php endif; ?>

                        <?php if ($memberCount > 0): ?>
                            <div>
                                <p class="text-zinc-600 text-xs font-bold uppercase tracking-wider mb-1">Members</p>
                                <p class="text-zinc-200 font-bold text-xl flex items-center gap-2">
                                    <svg class="w-5 h-5 <?= $style['text'] ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <?= number_format($memberCount) ?>
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Goals Section -->
    <?php if (!empty($goals)): ?>
        <div class="bg-zinc-900 border border-zinc-800 p-6">
            <div class="flex items-start gap-3 mb-4">
                <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center">
                    <svg class="w-6 h-6 text-zinc-950" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-xl font-black text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-emerald-500 uppercase tracking-wide mb-1">
                        Primary Goals
                    </h3>
                    <p class="text-zinc-500 text-sm">Mission and objectives</p>
                </div>
            </div>

            <div class="bg-zinc-950/50 border border-zinc-800 p-4">
                <p class="text-zinc-300 leading-relaxed whitespace-pre-line">
                    <?= e($goals) ?>
                </p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Leadership & Location -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Leader -->
        <?php if ($leaderId): ?>
            <div class="bg-zinc-900 border border-zinc-800 hover:border-amber-400 transition-all group">
                <div class="p-4 border-b border-zinc-800 bg-zinc-950/50">
                    <h4 class="text-zinc-400 text-xs font-bold uppercase tracking-wider">Leader</h4>
                </div>
                <a href="<?= url('/entities/character/' . $leaderId) ?>" class="block p-4 hover:bg-zinc-800/50 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center">
                            <svg class="w-7 h-7 text-zinc-950" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-zinc-100 font-bold group-hover:text-amber-400 transition-colors">
                                View Leader
                            </p>
                            <p class="text-zinc-500 text-sm">Character details</p>
                        </div>
                        <svg class="w-5 h-5 text-zinc-600 group-hover:text-amber-400 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </a>
            </div>
        <?php endif; ?>

        <!-- Headquarters -->
        <?php if ($headquartersId): ?>
            <div class="bg-zinc-900 border border-zinc-800 hover:border-blue-400 transition-all group">
                <div class="p-4 border-b border-zinc-800 bg-zinc-950/50">
                    <h4 class="text-zinc-400 text-xs font-bold uppercase tracking-wider">Headquarters</h4>
                </div>
                <a href="<?= url('/entities/location/' . $headquartersId) ?>" class="block p-4 hover:bg-zinc-800/50 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
                            <svg class="w-7 h-7 text-zinc-950" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-zinc-100 font-bold group-hover:text-blue-400 transition-colors">
                                View Location
                            </p>
                            <p class="text-zinc-500 text-sm">Base of operations</p>
                        </div>
                        <svg class="w-5 h-5 text-zinc-600 group-hover:text-blue-400 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Ranks Hierarchy -->
    <?php if (!empty($ranks) && is_array($ranks)): ?>
        <div class="bg-zinc-900 border border-zinc-800 p-6">
            <div class="flex items-start gap-3 mb-6">
                <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br <?= $style['bg'] ?> border <?= $style['border'] ?> flex items-center justify-center">
                    <svg class="w-6 h-6 <?= $style['text'] ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-xl font-black text-transparent bg-clip-text bg-gradient-to-r <?= str_replace('text-', 'from-', $style['text']) ?> to-zinc-400 uppercase tracking-wide mb-1">
                        Rank Hierarchy
                    </h3>
                    <p class="text-zinc-500 text-sm"><?= count($ranks) ?> rank<?= count($ranks) !== 1 ? 's' : '' ?> in the structure</p>
                </div>
            </div>

            <!-- Ranks as stepped list (highest to lowest) -->
            <div class="relative">
                <!-- Connecting line -->
                <div class="absolute left-6 top-0 bottom-0 w-0.5 bg-gradient-to-b <?= str_replace('text-', 'from-', $style['text']) ?> to-zinc-800"></div>

                <div class="space-y-3">
                    <?php foreach (array_reverse($ranks) as $index => $rank): ?>
                        <div class="relative flex items-center gap-4 group">
                            <!-- Rank number badge -->
                            <div class="relative z-10 flex-shrink-0 w-12 h-12 bg-zinc-950 border-2 <?= $style['border'] ?> flex items-center justify-center">
                                <span class="<?= $style['text'] ?> font-black text-lg">
                                    <?= count($ranks) - $index ?>
                                </span>
                            </div>

                            <!-- Rank name -->
                            <div class="flex-1 bg-zinc-950/50 border border-zinc-800 group-hover:border-zinc-700 px-4 py-3 transition-colors">
                                <p class="text-zinc-100 font-bold text-lg">
                                    <?= e($rank) ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Child Organisations Section -->
    <div class="bg-zinc-900 border border-zinc-800 p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-violet-500 to-violet-600 flex items-center justify-center">
                    <svg class="w-6 h-6 text-zinc-950" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-black text-transparent bg-clip-text bg-gradient-to-r from-violet-400 to-violet-500 uppercase tracking-wide mb-1">
                        Sub-Organisations
                    </h3>
                    <p class="text-zinc-500 text-sm">Branches, chapters, and divisions</p>
                </div>
            </div>
        </div>

        <div class="bg-zinc-950/50 border border-zinc-800 p-8 text-center">
            <svg class="w-12 h-12 text-zinc-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            <p class="text-zinc-500 text-sm">No sub-organisations yet</p>
            <p class="text-zinc-600 text-xs mt-1">Add related organisations using Relations</p>
        </div>
    </div>

    <!-- Members Section -->
    <div class="bg-zinc-900 border border-zinc-800 p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center">
                    <svg class="w-6 h-6 text-zinc-950" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-black text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-emerald-500 uppercase tracking-wide mb-1">
                        Members
                    </h3>
                    <p class="text-zinc-500 text-sm">Characters affiliated with this organisation</p>
                </div>
            </div>
        </div>

        <div class="bg-zinc-950/50 border border-zinc-800 p-8 text-center">
            <svg class="w-12 h-12 text-zinc-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
            <p class="text-zinc-500 text-sm">No members listed yet</p>
            <p class="text-zinc-600 text-xs mt-1">Add members using Relations</p>
        </div>
    </div>
</div>
