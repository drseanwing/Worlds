<?php
/**
 * Family Entity Type Partial
 * Displays Family-specific fields with heraldic design
 *
 * Props:
 * - $entity: Entity data array containing family-specific fields
 * - $familyData: Parsed family data (seat_location_id, motto, coat_of_arms, founding_date, status)
 */

$familyData = $familyData ?? [];
$status = $familyData['status'] ?? null;
$motto = $familyData['motto'] ?? null;
$coatOfArms = $familyData['coat_of_arms'] ?? null;
$foundingDate = $familyData['founding_date'] ?? null;
$seatLocationId = $familyData['seat_location_id'] ?? null;

// Status color mapping with regal palette
$statusColors = [
    'Royal' => ['bg' => 'bg-amber-500/20', 'border' => 'border-amber-400/60', 'text' => 'text-amber-300', 'icon' => '👑'],
    'Noble' => ['bg' => 'bg-purple-500/20', 'border' => 'border-purple-400/60', 'text' => 'text-purple-300', 'icon' => '⚜️'],
    'Common' => ['bg' => 'bg-slate-500/20', 'border' => 'border-slate-400/60', 'text' => 'text-slate-300', 'icon' => '🏠'],
    'Extinct' => ['bg' => 'bg-red-900/30', 'border' => 'border-red-700/60', 'text' => 'text-red-400', 'icon' => '💀'],
    'Fallen' => ['bg' => 'bg-orange-900/30', 'border' => 'border-orange-700/60', 'text' => 'text-orange-400', 'icon' => '⬇️'],
];

$statusStyle = $statusColors[$status] ?? ['bg' => 'bg-gray-500/20', 'border' => 'border-gray-400/60', 'text' => 'text-gray-300', 'icon' => '🛡️'];
?>

<!-- Family-Specific Section -->
<div class="space-y-6">
    <!-- Heraldic Header with Status -->
    <div class="relative">
        <!-- Decorative border pattern -->
        <div class="absolute inset-0 opacity-10 pointer-events-none" style="background-image: repeating-linear-gradient(90deg, transparent, transparent 20px, rgba(147,51,234,0.3) 20px, rgba(147,51,234,0.3) 22px), repeating-linear-gradient(0deg, transparent, transparent 20px, rgba(147,51,234,0.3) 20px, rgba(147,51,234,0.3) 22px);"></div>

        <div class="relative bg-gradient-to-br from-slate-900/95 via-slate-800/90 to-slate-900/95 border-2 border-purple-500/30 rounded-lg p-6 backdrop-blur-sm">
            <div class="flex items-start justify-between gap-6">
                <!-- Shield emblem -->
                <div class="flex-shrink-0">
                    <div class="w-20 h-24 bg-gradient-to-b from-slate-700 via-slate-800 to-slate-900 border-2 border-purple-400/40 rounded-t-lg rounded-b-sm relative shadow-2xl shadow-purple-900/50 overflow-hidden">
                        <!-- Shield pattern -->
                        <div class="absolute inset-0 opacity-20" style="background: linear-gradient(135deg, transparent 25%, rgba(147,51,234,0.4) 25%, rgba(147,51,234,0.4) 50%, transparent 50%, transparent 75%, rgba(147,51,234,0.4) 75%); background-size: 8px 8px;"></div>

                        <!-- Shield icon -->
                        <div class="absolute inset-0 flex items-center justify-center">
                            <svg class="w-10 h-10 text-purple-300/80" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>

                        <!-- Bottom point -->
                        <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-8 border-r-8 border-t-8 border-l-transparent border-r-transparent border-t-slate-900"></div>
                    </div>
                </div>

                <!-- Status and Info -->
                <div class="flex-1">
                    <?php if ($status): ?>
                        <div class="flex items-center gap-3 mb-4">
                            <div class="flex-1 h-px bg-gradient-to-r from-transparent via-purple-500/40 to-transparent"></div>
                            <div class="<?= $statusStyle['bg'] ?> <?= $statusStyle['border'] ?> border-2 px-6 py-3 backdrop-blur-sm shadow-lg">
                                <div class="flex items-center gap-3">
                                    <span class="text-2xl" aria-hidden="true"><?= $statusStyle['icon'] ?></span>
                                    <div>
                                        <div class="text-xs text-gray-400 uppercase tracking-wider font-bold">Status</div>
                                        <div class="<?= $statusStyle['text'] ?> font-bold text-xl tracking-wide uppercase">
                                            <?= e($status) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-1 h-px bg-gradient-to-r from-purple-500/40 via-transparent to-transparent"></div>
                        </div>
                    <?php endif; ?>

                    <!-- Founding Date -->
                    <?php if ($foundingDate): ?>
                        <div class="flex items-center gap-3 text-sm">
                            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-gray-400">Founded:</span>
                            <span class="text-gray-200 font-semibold"><?= e($foundingDate) ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Family Motto (Ornate Display) -->
    <?php if ($motto): ?>
        <div class="relative group">
            <!-- Glow effect -->
            <div class="absolute -inset-1 bg-gradient-to-r from-purple-600/20 via-indigo-600/20 to-purple-600/20 rounded-lg blur opacity-40 group-hover:opacity-60 transition-opacity"></div>

            <div class="relative bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 border border-purple-400/40 rounded-lg p-8 overflow-hidden">
                <!-- Decorative corner elements -->
                <div class="absolute top-0 left-0 w-16 h-16 border-t-2 border-l-2 border-purple-400/60 rounded-tl-lg"></div>
                <div class="absolute top-0 right-0 w-16 h-16 border-t-2 border-r-2 border-purple-400/60 rounded-tr-lg"></div>
                <div class="absolute bottom-0 left-0 w-16 h-16 border-b-2 border-l-2 border-purple-400/60 rounded-bl-lg"></div>
                <div class="absolute bottom-0 right-0 w-16 h-16 border-b-2 border-r-2 border-purple-400/60 rounded-br-lg"></div>

                <!-- Noise overlay -->
                <div class="absolute inset-0 opacity-5 mix-blend-overlay pointer-events-none" style="background-image: url('data:image/svg+xml,%3Csvg viewBox=%220 0 200 200%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noiseFilter%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.65%22 numOctaves=%223%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noiseFilter)%22/%3E%3C/svg%3E');"></div>

                <div class="relative">
                    <div class="flex items-center justify-center gap-3 mb-4">
                        <div class="h-px flex-1 bg-gradient-to-r from-transparent via-purple-500/50 to-purple-500/20"></div>
                        <h3 class="text-sm font-bold text-purple-300 uppercase tracking-widest">Family Motto</h3>
                        <div class="h-px flex-1 bg-gradient-to-r from-purple-500/20 via-purple-500/50 to-transparent"></div>
                    </div>

                    <blockquote class="text-center">
                        <p class="text-2xl md:text-3xl font-serif italic text-transparent bg-clip-text bg-gradient-to-r from-purple-200 via-purple-100 to-indigo-200 leading-relaxed tracking-wide">
                            "<?= e($motto) ?>"
                        </p>
                    </blockquote>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Coat of Arms & Family Seat Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Coat of Arms -->
        <?php if ($coatOfArms): ?>
            <div class="bg-slate-900/60 border border-slate-700/50 rounded-lg p-6 backdrop-blur-sm hover:border-purple-500/50 transition-colors group">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-purple-500/20 to-indigo-500/20 border border-purple-400/40 rounded flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-2">Coat of Arms</h4>
                        <p class="text-gray-200 leading-relaxed text-sm"><?= e($coatOfArms) ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Family Seat -->
        <?php if ($seatLocationId): ?>
            <div class="bg-slate-900/60 border border-slate-700/50 rounded-lg p-6 backdrop-blur-sm hover:border-purple-500/50 transition-colors group">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-purple-500/20 to-indigo-500/20 border border-purple-400/40 rounded flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-2">Family Seat</h4>
                        <a href="<?= url('/entities/' . $seatLocationId) ?>"
                           class="inline-flex items-center gap-2 text-purple-300 hover:text-purple-200 font-semibold transition-colors">
                            <span>View Location</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Family Members Section -->
    <div class="bg-gradient-to-br from-slate-900/80 via-slate-800/70 to-slate-900/80 border border-slate-700/50 rounded-lg overflow-hidden">
        <div class="bg-gradient-to-r from-purple-900/30 via-indigo-900/30 to-purple-900/30 border-b border-purple-500/30 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <h3 class="text-lg font-bold text-gray-200 tracking-wide">Family Members</h3>
                </div>
                <button class="inline-flex items-center gap-2 px-4 py-2 bg-purple-500/20 hover:bg-purple-500/30 border border-purple-400/40 text-purple-300 hover:text-purple-200 text-sm font-semibold rounded transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Member
                </button>
            </div>
        </div>

        <div class="p-6">
            <!-- Placeholder for family members list -->
            <div class="text-center py-12">
                <div class="inline-block p-4 bg-slate-800/50 border border-slate-700/50 rounded-full mb-4">
                    <svg class="w-12 h-12 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <p class="text-slate-500 text-sm mb-1">No family members yet</p>
                <p class="text-slate-600 text-xs">Characters related to this family will appear here</p>
            </div>
        </div>
    </div>

    <!-- Branch Families / Cadet Houses Section -->
    <div class="bg-gradient-to-br from-slate-900/80 via-slate-800/70 to-slate-900/80 border border-slate-700/50 rounded-lg overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-900/30 via-purple-900/30 to-indigo-900/30 border-b border-indigo-500/30 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                    </svg>
                    <h3 class="text-lg font-bold text-gray-200 tracking-wide">Cadet Houses & Branches</h3>
                </div>
                <button class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-500/20 hover:bg-indigo-500/30 border border-indigo-400/40 text-indigo-300 hover:text-indigo-200 text-sm font-semibold rounded transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Branch
                </button>
            </div>
        </div>

        <div class="p-6">
            <!-- Placeholder for branch families -->
            <div class="text-center py-12">
                <div class="inline-block p-4 bg-slate-800/50 border border-slate-700/50 rounded-full mb-4">
                    <svg class="w-12 h-12 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                    </svg>
                </div>
                <p class="text-slate-500 text-sm mb-1">No cadet houses or branch families</p>
                <p class="text-slate-600 text-xs">Related family entities will appear here</p>
            </div>
        </div>
    </div>
</div>
