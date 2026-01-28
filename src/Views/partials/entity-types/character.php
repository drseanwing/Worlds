<?php
/**
 * Character-specific view partial
 * Displays character-specific fields with atmospheric styling
 */

$data = $entity['data'] ?? [];
$title = $data['title'] ?? null;
$age = $data['age'] ?? null;
$pronouns = $data['pronouns'] ?? null;
$gender = $data['gender'] ?? null;
$is_dead = $data['is_dead'] ?? false;
$race_id = $data['race_id'] ?? null;
$location_id = $data['location_id'] ?? null;
$personality_traits = $data['personality_traits'] ?? [];
$appearance_traits = $data['appearance_traits'] ?? [];
$organisation_ids = $data['organisation_ids'] ?? [];
$family_ids = $data['family_ids'] ?? [];
?>

<!-- Character Profile Header -->
<div class="p-8 border-b border-slate-700/50">
    <div class="flex items-start justify-between mb-6">
        <div class="flex-1">
            <h2 class="text-xl font-bold text-gray-300 mb-4 tracking-wide flex items-center">
                <svg class="w-6 h-6 mr-2 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Character Profile
            </h2>

            <!-- Profile Details Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <?php if ($title): ?>
                <div class="bg-slate-900/50 backdrop-blur-sm rounded-lg p-4 border border-slate-700/30">
                    <div class="text-xs text-gray-500 uppercase tracking-wider mb-1">Title</div>
                    <div class="text-sm font-semibold bg-gradient-to-r from-amber-200 to-yellow-400 bg-clip-text text-transparent">
                        <?= e($title) ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($age): ?>
                <div class="bg-slate-900/50 backdrop-blur-sm rounded-lg p-4 border border-slate-700/30">
                    <div class="text-xs text-gray-500 uppercase tracking-wider mb-1">Age</div>
                    <div class="text-sm font-semibold text-gray-300">
                        <?= e($age) ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($gender): ?>
                <div class="bg-slate-900/50 backdrop-blur-sm rounded-lg p-4 border border-slate-700/30">
                    <div class="text-xs text-gray-500 uppercase tracking-wider mb-1">Gender</div>
                    <div class="text-sm font-semibold text-gray-300">
                        <?= e($gender) ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($pronouns): ?>
                <div class="bg-slate-900/50 backdrop-blur-sm rounded-lg p-4 border border-slate-700/30">
                    <div class="text-xs text-gray-500 uppercase tracking-wider mb-1">Pronouns</div>
                    <div class="text-sm font-semibold text-gray-300">
                        <?= e($pronouns) ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Deceased Indicator -->
        <?php if ($is_dead): ?>
        <div class="ml-6 flex-shrink-0">
            <div class="relative group">
                <div class="absolute -inset-1 bg-gradient-to-r from-red-600 to-rose-600 rounded-lg opacity-50 blur animate-pulse"></div>
                <div class="relative bg-red-900/40 backdrop-blur-sm border border-red-700/50 rounded-lg px-4 py-3 flex items-center">
                    <svg class="w-6 h-6 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <svg class="w-7 h-7 text-red-300" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/>
                    </svg>
                    <span class="ml-2 text-red-300 font-bold uppercase tracking-wider text-xs">Deceased</span>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Race/Ancestry -->
<?php if ($race_id): ?>
<div class="p-8 border-b border-slate-700/50">
    <h3 class="text-lg font-bold text-gray-300 mb-3 tracking-wide flex items-center">
        <svg class="w-5 h-5 mr-2 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
        </svg>
        Race / Ancestry
    </h3>
    <a href="<?= url('/entities/' . $race_id) ?>"
       class="inline-flex items-center bg-gradient-to-r from-indigo-900/30 to-purple-900/30 backdrop-blur-sm border border-indigo-700/40 hover:border-indigo-500/60 rounded-lg px-4 py-3 transition-all duration-200 group">
        <svg class="w-5 h-5 mr-2 text-indigo-400 group-hover:text-indigo-300 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
        </svg>
        <span class="text-indigo-300 group-hover:text-indigo-200 font-medium transition-colors">View Race Details</span>
    </a>
</div>
<?php endif; ?>

<!-- Current Location -->
<?php if ($location_id): ?>
<div class="p-8 border-b border-slate-700/50">
    <h3 class="text-lg font-bold text-gray-300 mb-3 tracking-wide flex items-center">
        <svg class="w-5 h-5 mr-2 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        Current Location
    </h3>
    <a href="<?= url('/entities/' . $location_id) ?>"
       class="inline-flex items-center bg-gradient-to-r from-emerald-900/30 to-teal-900/30 backdrop-blur-sm border border-emerald-700/40 hover:border-emerald-500/60 rounded-lg px-4 py-3 transition-all duration-200 group">
        <svg class="w-5 h-5 mr-2 text-emerald-400 group-hover:text-emerald-300 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
        </svg>
        <span class="text-emerald-300 group-hover:text-emerald-200 font-medium transition-colors">View Location Details</span>
    </a>
</div>
<?php endif; ?>

<!-- Personality Traits -->
<div class="p-8 border-b border-slate-700/50">
    <h3 class="text-lg font-bold text-gray-300 mb-4 tracking-wide flex items-center">
        <svg class="w-5 h-5 mr-2 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
        </svg>
        Personality
    </h3>
    <?php if (!empty($personality_traits)): ?>
    <div class="flex flex-wrap gap-2">
        <?php foreach ($personality_traits as $trait): ?>
        <span class="inline-flex items-center bg-gradient-to-br from-purple-900/40 to-pink-900/40 backdrop-blur-sm border border-purple-600/40 text-purple-200 px-4 py-2 rounded-full text-sm font-medium shadow-lg hover:shadow-purple-900/30 transition-shadow duration-200">
            <svg class="w-3.5 h-3.5 mr-1.5 text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <?= e($trait) ?>
        </span>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="bg-slate-900/50 rounded-lg p-6 border border-slate-700/30">
        <p class="text-gray-500 text-sm italic flex items-center justify-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            No personality traits defined
        </p>
    </div>
    <?php endif; ?>
</div>

<!-- Appearance Traits -->
<div class="p-8 border-b border-slate-700/50">
    <h3 class="text-lg font-bold text-gray-300 mb-4 tracking-wide flex items-center">
        <svg class="w-5 h-5 mr-2 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
        </svg>
        Appearance
    </h3>
    <?php if (!empty($appearance_traits)): ?>
    <div class="flex flex-wrap gap-2">
        <?php foreach ($appearance_traits as $trait): ?>
        <span class="inline-flex items-center bg-gradient-to-br from-cyan-900/40 to-blue-900/40 backdrop-blur-sm border border-cyan-600/40 text-cyan-200 px-4 py-2 rounded-full text-sm font-medium shadow-lg hover:shadow-cyan-900/30 transition-shadow duration-200">
            <svg class="w-3.5 h-3.5 mr-1.5 text-cyan-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <?= e($trait) ?>
        </span>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="bg-slate-900/50 rounded-lg p-6 border border-slate-700/30">
        <p class="text-gray-500 text-sm italic flex items-center justify-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            No appearance traits defined
        </p>
    </div>
    <?php endif; ?>
</div>

<!-- Organisation Memberships -->
<?php if (!empty($organisation_ids)): ?>
<div class="p-8 border-b border-slate-700/50">
    <h3 class="text-lg font-bold text-gray-300 mb-4 tracking-wide flex items-center">
        <svg class="w-5 h-5 mr-2 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
        </svg>
        Organisations
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <?php foreach ($organisation_ids as $org_id): ?>
        <a href="<?= url('/entities/' . $org_id) ?>"
           class="flex items-center bg-gradient-to-r from-amber-900/20 to-orange-900/20 backdrop-blur-sm border border-amber-700/30 hover:border-amber-500/50 rounded-lg px-4 py-3 transition-all duration-200 group">
            <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-amber-500/20 to-orange-500/20 rounded-lg flex items-center justify-center border border-amber-600/30">
                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div class="ml-3 flex-1">
                <span class="text-amber-200 group-hover:text-amber-100 font-medium text-sm transition-colors">Organisation #<?= e($org_id) ?></span>
            </div>
            <svg class="w-5 h-5 text-amber-600 group-hover:text-amber-400 transition-colors flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Family Memberships -->
<?php if (!empty($family_ids)): ?>
<div class="p-8">
    <h3 class="text-lg font-bold text-gray-300 mb-4 tracking-wide flex items-center">
        <svg class="w-5 h-5 mr-2 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
        </svg>
        Family
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <?php foreach ($family_ids as $family_id): ?>
        <a href="<?= url('/entities/' . $family_id) ?>"
           class="flex items-center bg-gradient-to-r from-rose-900/20 to-pink-900/20 backdrop-blur-sm border border-rose-700/30 hover:border-rose-500/50 rounded-lg px-4 py-3 transition-all duration-200 group">
            <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-rose-500/20 to-pink-500/20 rounded-lg flex items-center justify-center border border-rose-600/30">
                <svg class="w-5 h-5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </div>
            <div class="ml-3 flex-1">
                <span class="text-rose-200 group-hover:text-rose-100 font-medium text-sm transition-colors">Family #<?= e($family_id) ?></span>
            </div>
            <svg class="w-5 h-5 text-rose-600 group-hover:text-rose-400 transition-colors flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>
