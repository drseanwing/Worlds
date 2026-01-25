<?php
/**
 * Sidebar Navigation Component
 *
 * Displays entity type navigation with collapsible mobile support.
 * Shows active state for current entity type.
 */

$entityTypes = [
    ['type' => 'character', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'label' => 'Characters'],
    ['type' => 'location', 'icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z', 'label' => 'Locations'],
    ['type' => 'item', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'label' => 'Items'],
    ['type' => 'family', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z', 'label' => 'Families'],
    ['type' => 'organisation', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'label' => 'Organisations'],
    ['type' => 'quest', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'label' => 'Quests'],
    ['type' => 'event', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'label' => 'Events'],
    ['type' => 'note', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'label' => 'Notes'],
    ['type' => 'journal', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', 'label' => 'Journals'],
    ['type' => 'race', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z', 'label' => 'Races'],
    ['type' => 'creature', 'icon' => 'M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'label' => 'Creatures'],
    ['type' => 'ability', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'label' => 'Abilities'],
    ['type' => 'map', 'icon' => 'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7', 'label' => 'Maps'],
    ['type' => 'timeline', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'label' => 'Timelines'],
    ['type' => 'calendar', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'label' => 'Calendars'],
];

$currentType = $currentEntityType ?? null;
?>

<aside
    x-data="{ open: false, expanded: window.innerWidth >= 1024 }"
    @resize.window="expanded = window.innerWidth >= 1024"
    class="sticky top-0 h-screen bg-zinc-900 border-r border-zinc-800 transition-all duration-300"
    :class="expanded ? 'w-64' : 'w-0 lg:w-16'"
>
    <!-- Toggle Button (Mobile) -->
    <button
        @click="open = !open; if (window.innerWidth >= 1024) expanded = !expanded"
        class="absolute -right-12 top-4 lg:right-4 p-2 bg-zinc-800 border border-zinc-700 hover:border-emerald-400 transition-colors z-10"
        aria-label="Toggle sidebar"
    >
        <svg class="w-5 h-5 text-zinc-400 transition-transform" :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
        </svg>
    </button>

    <!-- Sidebar Content -->
    <div class="h-full overflow-y-auto scrollbar-thin scrollbar-thumb-zinc-700 scrollbar-track-zinc-900 py-6">
        <nav class="space-y-1 px-3">
            <div :class="expanded ? 'opacity-100' : 'opacity-0 lg:opacity-100'" class="transition-opacity">
                <div class="px-3 py-2 text-xs font-bold text-zinc-500 uppercase tracking-wider" :class="expanded ? '' : 'hidden lg:block text-center'">
                    <span x-show="expanded">Entities</span>
                    <span x-show="!expanded" class="hidden lg:block">···</span>
                </div>
            </div>

            <?php foreach ($entityTypes as $entity): ?>
                <a
                    href="<?= url('/entities/' . $entity['type']) ?>"
                    class="flex items-center gap-3 px-3 py-2 transition-all group <?= $currentType === $entity['type'] ? 'bg-emerald-500/10 border-l-2 border-emerald-400 text-emerald-400' : 'text-zinc-400 hover:text-zinc-100 hover:bg-zinc-800 border-l-2 border-transparent' ?>"
                    title="<?= e($entity['label']) ?>"
                >
                    <svg class="w-5 h-5 flex-shrink-0 <?= $currentType === $entity['type'] ? 'text-emerald-400' : 'text-zinc-500 group-hover:text-emerald-400' ?> transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $entity['icon'] ?>"></path>
                    </svg>
                    <span
                        x-show="expanded"
                        x-transition
                        class="text-sm font-medium whitespace-nowrap <?= $currentType === $entity['type'] ? 'text-emerald-400' : '' ?>"
                    >
                        <?= e($entity['label']) ?>
                    </span>
                </a>
            <?php endforeach; ?>
        </nav>

        <!-- Divider -->
        <div class="my-6 border-t border-zinc-800"></div>

        <!-- Additional Links -->
        <nav class="space-y-1 px-3">
            <a
                href="<?= url('/campaigns') ?>"
                class="flex items-center gap-3 px-3 py-2 text-zinc-400 hover:text-zinc-100 hover:bg-zinc-800 border-l-2 border-transparent transition-all group"
                title="Campaigns"
            >
                <svg class="w-5 h-5 flex-shrink-0 text-zinc-500 group-hover:text-emerald-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                </svg>
                <span x-show="expanded" x-transition class="text-sm font-medium whitespace-nowrap">
                    Campaigns
                </span>
            </a>

            <a
                href="<?= url('/tags') ?>"
                class="flex items-center gap-3 px-3 py-2 text-zinc-400 hover:text-zinc-100 hover:bg-zinc-800 border-l-2 border-transparent transition-all group"
                title="Tags"
            >
                <svg class="w-5 h-5 flex-shrink-0 text-zinc-500 group-hover:text-emerald-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                </svg>
                <span x-show="expanded" x-transition class="text-sm font-medium whitespace-nowrap">
                    Tags
                </span>
            </a>
        </nav>
    </div>
</aside>
