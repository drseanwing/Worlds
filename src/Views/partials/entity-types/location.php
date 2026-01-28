<?php
/**
 * Location Entity Type Partial
 * Displays Location-specific fields with cartographic aesthetic
 *
 * Props:
 * - $entity: Full entity data including type_data
 * - $childLocations: Array of child location entities (optional)
 * - $characters: Array of characters at this location (optional)
 * - $organisations: Array of organisations at this location (optional)
 */

$typeData = $entity['type_data'] ?? [];
$locationType = $typeData['location_type'] ?? null;
$population = $typeData['population'] ?? null;
$geography = $typeData['geography'] ?? null;
$climate = $typeData['climate'] ?? null;
$government = $typeData['government'] ?? null;
$coordinates = $typeData['coordinates'] ?? null;
$mapId = $typeData['map_id'] ?? null;

$childLocations = $childLocations ?? [];
$characters = $characters ?? [];
$organisations = $organisations ?? [];

// Location type icons and colors
$locationTypes = [
    'City' => ['icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'color' => 'from-amber-500 to-orange-500', 'bg' => 'bg-amber-500/20', 'border' => 'border-amber-500/40', 'text' => 'text-amber-300'],
    'Village' => ['icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', 'color' => 'from-green-500 to-emerald-500', 'bg' => 'bg-green-500/20', 'border' => 'border-green-500/40', 'text' => 'text-green-300'],
    'Region' => ['icon' => 'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7', 'color' => 'from-purple-500 to-indigo-500', 'bg' => 'bg-purple-500/20', 'border' => 'border-purple-500/40', 'text' => 'text-purple-300'],
    'Building' => ['icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'color' => 'from-slate-500 to-zinc-500', 'bg' => 'bg-slate-500/20', 'border' => 'border-slate-500/40', 'text' => 'text-slate-300'],
    'POI' => ['icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z', 'color' => 'from-cyan-500 to-blue-500', 'bg' => 'bg-cyan-500/20', 'border' => 'border-cyan-500/40', 'text' => 'text-cyan-300'],
    'Wilderness' => ['icon' => 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064', 'color' => 'from-lime-500 to-green-600', 'bg' => 'bg-lime-500/20', 'border' => 'border-lime-500/40', 'text' => 'text-lime-300'],
    'Dungeon' => ['icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z', 'color' => 'from-red-600 to-rose-700', 'bg' => 'bg-red-600/20', 'border' => 'border-red-600/40', 'text' => 'text-red-300'],
];

$typeStyle = $locationTypes[$locationType] ?? $locationTypes['POI'];
?>

<!-- Location-Specific Content -->
<div class="space-y-6">

    <!-- Location Type Hero Banner -->
    <?php if ($locationType): ?>
    <div class="relative overflow-hidden rounded-xl border border-slate-700/50 bg-gradient-to-br <?= $typeStyle['color'] ?> p-[2px]">
        <div class="relative bg-slate-900/95 backdrop-blur-xl rounded-[10px] p-6">
            <!-- Decorative Corner Elements -->
            <div class="absolute top-0 left-0 w-32 h-32 bg-gradient-to-br <?= $typeStyle['color'] ?> opacity-10 blur-3xl"></div>
            <div class="absolute bottom-0 right-0 w-32 h-32 bg-gradient-to-tl <?= $typeStyle['color'] ?> opacity-10 blur-3xl"></div>

            <div class="relative flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="<?= $typeStyle['bg'] ?> <?= $typeStyle['border'] ?> border-2 rounded-xl p-4 backdrop-blur-sm">
                        <svg class="w-8 h-8 <?= $typeStyle['text'] ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $typeStyle['icon'] ?>"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-xs font-bold tracking-wider uppercase text-gray-500 mb-1">Location Type</div>
                        <div class="text-2xl font-black bg-gradient-to-r <?= $typeStyle['color'] ?> bg-clip-text text-transparent tracking-tight">
                            <?= e($locationType) ?>
                        </div>
                    </div>
                </div>

                <?php if ($population): ?>
                <div class="text-right">
                    <div class="text-xs font-bold tracking-wider uppercase text-gray-500 mb-1">Population</div>
                    <div class="flex items-center justify-end space-x-2">
                        <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span class="text-2xl font-bold text-purple-200">
                            <?= is_numeric($population) ? number_format($population) : e($population) ?>
                        </span>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Geographic Details Grid -->
    <?php if ($geography || $climate || $government): ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        <!-- Geography Card -->
        <?php if ($geography): ?>
        <div class="relative group">
            <div class="absolute -inset-0.5 bg-gradient-to-r from-emerald-600 to-teal-600 rounded-xl opacity-20 group-hover:opacity-30 blur transition-opacity"></div>
            <div class="relative bg-slate-800/80 backdrop-blur-sm border border-slate-700/50 rounded-xl p-6 h-full">
                <div class="flex items-start space-x-3 mb-4">
                    <div class="bg-emerald-500/20 border border-emerald-500/40 rounded-lg p-2 backdrop-blur-sm">
                        <svg class="w-6 h-6 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm font-bold tracking-wider uppercase text-emerald-400 mb-1">Geography</h3>
                        <div class="w-12 h-0.5 bg-gradient-to-r from-emerald-500 to-transparent"></div>
                    </div>
                </div>
                <p class="text-gray-300 text-sm leading-relaxed"><?= e($geography) ?></p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Climate Card -->
        <?php if ($climate): ?>
        <div class="relative group">
            <div class="absolute -inset-0.5 bg-gradient-to-r from-sky-600 to-blue-600 rounded-xl opacity-20 group-hover:opacity-30 blur transition-opacity"></div>
            <div class="relative bg-slate-800/80 backdrop-blur-sm border border-slate-700/50 rounded-xl p-6 h-full">
                <div class="flex items-start space-x-3 mb-4">
                    <div class="bg-sky-500/20 border border-sky-500/40 rounded-lg p-2 backdrop-blur-sm">
                        <svg class="w-6 h-6 text-sky-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm font-bold tracking-wider uppercase text-sky-400 mb-1">Climate</h3>
                        <div class="w-12 h-0.5 bg-gradient-to-r from-sky-500 to-transparent"></div>
                    </div>
                </div>
                <p class="text-gray-300 text-sm leading-relaxed"><?= e($climate) ?></p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Government Card -->
        <?php if ($government): ?>
        <div class="relative group">
            <div class="absolute -inset-0.5 bg-gradient-to-r from-amber-600 to-yellow-600 rounded-xl opacity-20 group-hover:opacity-30 blur transition-opacity"></div>
            <div class="relative bg-slate-800/80 backdrop-blur-sm border border-slate-700/50 rounded-xl p-6 h-full">
                <div class="flex items-start space-x-3 mb-4">
                    <div class="bg-amber-500/20 border border-amber-500/40 rounded-lg p-2 backdrop-blur-sm">
                        <svg class="w-6 h-6 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm font-bold tracking-wider uppercase text-amber-400 mb-1">Government</h3>
                        <div class="w-12 h-0.5 bg-gradient-to-r from-amber-500 to-transparent"></div>
                    </div>
                </div>
                <p class="text-gray-300 text-sm leading-relaxed"><?= e($government) ?></p>
            </div>
        </div>
        <?php endif; ?>

    </div>
    <?php endif; ?>

    <!-- Map & Coordinates Section -->
    <?php if ($mapId || $coordinates): ?>
    <div class="relative overflow-hidden rounded-xl border border-slate-700/50 bg-slate-800/60 backdrop-blur-sm p-6">
        <!-- Topographic Background Pattern -->
        <div class="absolute inset-0 opacity-5" style="background-image: repeating-radial-gradient(circle at 50% 50%, transparent 0, transparent 20px, rgba(139, 92, 246, 0.3) 20px, rgba(139, 92, 246, 0.3) 21px);"></div>

        <div class="relative flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="bg-violet-500/20 border-2 border-violet-500/40 rounded-xl p-3 backdrop-blur-sm">
                    <svg class="w-7 h-7 text-violet-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold tracking-wider uppercase text-violet-400 mb-1">Map Information</h3>
                    <?php if ($coordinates): ?>
                    <div class="flex items-center space-x-3 text-gray-300">
                        <svg class="w-4 h-4 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span class="font-mono text-sm bg-slate-900/50 px-3 py-1 rounded-md border border-slate-700/50">
                            <?= e($coordinates['x'] ?? 0) ?>, <?= e($coordinates['y'] ?? 0) ?>
                        </span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($mapId): ?>
            <a href="<?= url('/entities/map/' . $mapId) ?>"
               class="group inline-flex items-center space-x-2 bg-violet-500/20 hover:bg-violet-500/30 border border-violet-500/40 hover:border-violet-400/60 text-violet-300 hover:text-violet-200 px-4 py-2 rounded-lg transition-all duration-200 font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <span>View on Map</span>
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Child Locations -->
    <?php if (!empty($childLocations)): ?>
    <div class="relative">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-3">
                <div class="bg-indigo-500/20 border border-indigo-500/40 rounded-lg p-2">
                    <svg class="w-5 h-5 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold bg-gradient-to-r from-indigo-200 to-indigo-400 bg-clip-text text-transparent">
                    Locations Within
                </h3>
            </div>
            <span class="bg-indigo-500/20 border border-indigo-500/40 text-indigo-300 px-3 py-1 rounded-full text-sm font-bold">
                <?= count($childLocations) ?>
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?php foreach ($childLocations as $child): ?>
            <a href="<?= url('/entities/location/' . $child['id']) ?>"
               class="group relative overflow-hidden bg-slate-800/40 hover:bg-slate-800/60 backdrop-blur-sm border border-slate-700/50 hover:border-indigo-500/50 rounded-lg p-4 transition-all duration-200">
                <div class="flex items-start space-x-3">
                    <div class="bg-indigo-500/10 border border-indigo-500/30 rounded-lg p-2 group-hover:bg-indigo-500/20 transition-colors">
                        <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-1">
                            <h4 class="font-bold text-gray-200 group-hover:text-indigo-300 transition-colors truncate">
                                <?= e($child['name'] ?? 'Unnamed') ?>
                            </h4>
                            <svg class="w-4 h-4 text-slate-600 group-hover:text-indigo-400 group-hover:translate-x-1 transition-all flex-shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                        <?php if (!empty($child['type_data']['location_type'])): ?>
                        <span class="inline-block text-xs text-indigo-400 font-medium">
                            <?= e($child['type_data']['location_type']) ?>
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php elseif (isset($childLocations)): ?>
    <!-- Empty State for Child Locations -->
    <div class="bg-slate-900/30 border border-slate-800 rounded-xl p-8">
        <div class="text-center">
            <div class="bg-indigo-500/10 border border-indigo-500/30 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-indigo-500/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/>
                </svg>
            </div>
            <p class="text-gray-500 text-sm font-medium mb-1">No sub-locations</p>
            <p class="text-gray-600 text-xs">This location has no defined areas within it</p>
        </div>
    </div>
    <?php endif; ?>

    <!-- Characters at this Location -->
    <?php if (!empty($characters)): ?>
    <div class="relative">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-3">
                <div class="bg-rose-500/20 border border-rose-500/40 rounded-lg p-2">
                    <svg class="w-5 h-5 text-rose-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold bg-gradient-to-r from-rose-200 to-rose-400 bg-clip-text text-transparent">
                    Characters Here
                </h3>
            </div>
            <span class="bg-rose-500/20 border border-rose-500/40 text-rose-300 px-3 py-1 rounded-full text-sm font-bold">
                <?= count($characters) ?>
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <?php foreach ($characters as $character): ?>
            <a href="<?= url('/entities/character/' . $character['id']) ?>"
               class="group relative overflow-hidden bg-slate-800/40 hover:bg-slate-800/60 backdrop-blur-sm border border-slate-700/50 hover:border-rose-500/50 rounded-lg p-4 transition-all duration-200">
                <div class="flex flex-col items-center text-center space-y-3">
                    <div class="bg-rose-500/10 border-2 border-rose-500/30 rounded-full p-3 group-hover:bg-rose-500/20 group-hover:border-rose-500/50 transition-colors">
                        <svg class="w-6 h-6 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-gray-200 group-hover:text-rose-300 transition-colors mb-1">
                            <?= e($character['name'] ?? 'Unnamed') ?>
                        </h4>
                        <?php if (!empty($character['type_data']['title'])): ?>
                        <p class="text-xs text-rose-400/80 line-clamp-1">
                            <?= e($character['type_data']['title']) ?>
                        </p>
                        <?php endif; ?>
                    </div>
                    <svg class="w-4 h-4 text-slate-600 group-hover:text-rose-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php elseif (isset($characters)): ?>
    <!-- Empty State for Characters -->
    <div class="bg-slate-900/30 border border-slate-800 rounded-xl p-8">
        <div class="text-center">
            <div class="bg-rose-500/10 border border-rose-500/30 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-rose-500/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <p class="text-gray-500 text-sm font-medium mb-1">No characters present</p>
            <p class="text-gray-600 text-xs">No characters are currently located here</p>
        </div>
    </div>
    <?php endif; ?>

    <!-- Organisations at this Location -->
    <?php if (!empty($organisations)): ?>
    <div class="relative">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-3">
                <div class="bg-cyan-500/20 border border-cyan-500/40 rounded-lg p-2">
                    <svg class="w-5 h-5 text-cyan-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold bg-gradient-to-r from-cyan-200 to-cyan-400 bg-clip-text text-transparent">
                    Organisations Based Here
                </h3>
            </div>
            <span class="bg-cyan-500/20 border border-cyan-500/40 text-cyan-300 px-3 py-1 rounded-full text-sm font-bold">
                <?= count($organisations) ?>
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?php foreach ($organisations as $org): ?>
            <a href="<?= url('/entities/organisation/' . $org['id']) ?>"
               class="group relative overflow-hidden bg-slate-800/40 hover:bg-slate-800/60 backdrop-blur-sm border border-slate-700/50 hover:border-cyan-500/50 rounded-lg p-4 transition-all duration-200">
                <div class="flex items-start space-x-3">
                    <div class="bg-cyan-500/10 border border-cyan-500/30 rounded-lg p-2 group-hover:bg-cyan-500/20 transition-colors">
                        <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-bold text-gray-200 group-hover:text-cyan-300 transition-colors truncate">
                                <?= e($org['name'] ?? 'Unnamed') ?>
                            </h4>
                            <svg class="w-4 h-4 text-slate-600 group-hover:text-cyan-400 group-hover:translate-x-1 transition-all flex-shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                        <?php if (!empty($org['type_data']['organisation_type'])): ?>
                        <span class="inline-block text-xs text-cyan-400 font-medium">
                            <?= e($org['type_data']['organisation_type']) ?>
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php elseif (isset($organisations)): ?>
    <!-- Empty State for Organisations -->
    <div class="bg-slate-900/30 border border-slate-800 rounded-xl p-8">
        <div class="text-center">
            <div class="bg-cyan-500/10 border border-cyan-500/30 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-cyan-500/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <p class="text-gray-500 text-sm font-medium mb-1">No organisations based here</p>
            <p class="text-gray-600 text-xs">No organisations have their headquarters at this location</p>
        </div>
    </div>
    <?php endif; ?>

</div>
