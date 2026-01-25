<?php $this->extends('layouts/base') ?>

<?php $this->section('title') ?>
Campaigns - Worlds
<?php $this->endSection() ?>

<?php $this->section('content') ?>
<div class="min-h-screen bg-gradient-to-br from-slate-950 via-zinc-900 to-slate-900 px-4 py-12">
    <!-- Atmospheric background elements - warm amber/orange theme -->
    <div class="fixed inset-0 pointer-events-none opacity-5">
        <div class="absolute top-40 right-20 w-96 h-96 bg-amber-500 rounded-full blur-3xl"></div>
        <div class="absolute bottom-40 left-20 w-96 h-96 bg-orange-600 rounded-full blur-3xl"></div>
    </div>

    <!-- Noise texture overlay -->
    <div class="fixed inset-0 pointer-events-none opacity-20 mix-blend-soft-light" style="background-image: url('data:image/svg+xml,%3Csvg viewBox=%220 0 400 400%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noiseFilter%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.9%22 numOctaves=%223%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noiseFilter)%22/%3E%3C/svg%3E');"></div>

    <div class="relative z-10 max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="mb-12">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-6xl font-bold mb-3 bg-gradient-to-r from-amber-200 via-orange-400 to-amber-200 bg-clip-text text-transparent tracking-tight">
                        Campaigns
                    </h1>
                    <p class="text-gray-400 text-lg font-light tracking-wide">
                        <?php if (isset($totalCampaigns)): ?>
                            <?= $totalCampaigns ?> <?= $totalCampaigns === 1 ? 'campaign' : 'campaigns' ?> created
                        <?php else: ?>
                            Organize your worlds into campaigns
                        <?php endif; ?>
                    </p>
                </div>

                <!-- Create Button -->
                <a href="<?= url('/campaigns/create') ?>"
                   class="group relative overflow-hidden bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-400 hover:to-orange-400 text-white font-semibold px-8 py-4 rounded-lg shadow-lg shadow-amber-900/30 hover:shadow-amber-900/50 focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:ring-offset-2 focus:ring-offset-slate-900 transition-all duration-300 transform hover:scale-105">
                    <span class="relative z-10 flex items-center tracking-wide">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        New Campaign
                    </span>
                    <div class="absolute inset-0 bg-gradient-to-r from-orange-400 to-amber-400 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                </a>
            </div>
        </div>

        <!-- Flash Messages -->
        <?php if ($success = get_flash('success')): ?>
        <div class="mb-8 p-4 bg-emerald-900/30 border border-emerald-700/50 rounded-lg backdrop-blur-sm animate-fade-in">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-emerald-400 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <p class="text-emerald-200 text-sm leading-relaxed"><?= e($success) ?></p>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($error = get_flash('error')): ?>
        <div class="mb-8 p-4 bg-red-900/30 border border-red-700/50 rounded-lg backdrop-blur-sm animate-fade-in">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-red-400 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <p class="text-red-200 text-sm leading-relaxed"><?= e($error) ?></p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Empty State -->
        <?php if (empty($campaigns)): ?>
        <div class="relative group">
            <div class="absolute -inset-0.5 bg-gradient-to-r from-amber-600 to-orange-600 rounded-2xl opacity-20 blur"></div>
            <div class="relative bg-slate-800/60 backdrop-blur-xl border border-slate-700/50 rounded-2xl shadow-2xl p-16 text-center">
                <div class="max-w-md mx-auto">
                    <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-slate-700 to-slate-800 rounded-full flex items-center justify-center">
                        <svg class="w-12 h-12 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-300 mb-3 tracking-tight">No campaigns yet</h3>
                    <p class="text-gray-500 mb-8 leading-relaxed">
                        Create your first campaign to organize and manage different worlds.
                    </p>
                    <a href="<?= url('/campaigns/create') ?>"
                       class="inline-flex items-center bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-400 hover:to-orange-400 text-white font-semibold px-6 py-3 rounded-lg shadow-lg shadow-amber-900/30 transition-all duration-300 transform hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        Create First Campaign
                    </a>
                </div>
            </div>
        </div>
        <?php else: ?>

        <!-- Campaigns Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
            <?php foreach ($campaigns as $campaign): ?>
            <?php $isActive = isset($activeCampaignId) && $activeCampaignId === $campaign['id']; ?>
            <div class="group relative">
                <!-- Active Campaign Crown -->
                <?php if ($isActive): ?>
                <div class="absolute -top-4 left-1/2 transform -translate-x-1/2 z-20">
                    <div class="bg-gradient-to-r from-amber-500 to-orange-500 text-white text-xs font-bold px-4 py-1.5 rounded-full shadow-lg flex items-center space-x-1.5">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        <span>ACTIVE</span>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Card Glow Effect -->
                <div class="absolute -inset-0.5 bg-gradient-to-r from-amber-600 to-orange-600 rounded-xl opacity-0 group-hover:opacity-30 blur transition duration-500"></div>

                <div class="relative h-full bg-slate-800/60 backdrop-blur-xl border <?= $isActive ? 'border-amber-500/50' : 'border-slate-700/50' ?> rounded-xl shadow-xl overflow-hidden transition-all duration-300 group-hover:border-amber-500/50 group-hover:shadow-2xl group-hover:shadow-amber-900/20">
                    <!-- Campaign Header Area -->
                    <div class="h-40 bg-gradient-to-br from-amber-900/30 via-orange-900/20 to-slate-900/50 relative overflow-hidden">
                        <!-- Decorative Pattern -->
                        <div class="absolute inset-0 opacity-10" style="background-image: repeating-linear-gradient(45deg, transparent, transparent 15px, rgba(255,255,255,.03) 15px, rgba(255,255,255,.03) 30px);"></div>

                        <!-- Campaign Icon -->
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-amber-500/30 to-orange-500/30 backdrop-blur-sm border border-amber-500/40 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-8 h-8 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                            </div>
                        </div>

                        <!-- Gradient Overlay -->
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-800/90 via-transparent to-transparent"></div>
                    </div>

                    <!-- Card Content -->
                    <div class="p-6">
                        <a href="<?= url('/campaigns/' . $campaign['id']) ?>" class="block mb-4">
                            <h3 class="text-xl font-bold text-gray-100 mb-2 tracking-tight group-hover:text-amber-300 transition-colors line-clamp-1">
                                <?= e($campaign['name'] ?? 'Unnamed Campaign') ?>
                            </h3>
                        </a>

                        <?php if (!empty($campaign['description'])): ?>
                        <p class="text-gray-400 text-sm leading-relaxed line-clamp-2 mb-4">
                            <?= e(substr($campaign['description'], 0, 120)) ?><?= strlen($campaign['description']) > 120 ? '...' : '' ?>
                        </p>
                        <?php else: ?>
                        <p class="text-gray-600 text-sm italic mb-4">No description</p>
                        <?php endif; ?>

                        <!-- Entity Count (placeholder) -->
                        <div class="flex items-center text-xs text-gray-500 mb-5 pt-3 border-t border-slate-700/50">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            <span><?= isset($campaign['entity_count']) ? $campaign['entity_count'] . ' entities' : 'No entities yet' ?></span>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center space-x-2">
                            <?php if (!$isActive): ?>
                            <form method="POST" action="<?= url('/campaigns/' . $campaign['id'] . '/activate') ?>" class="flex-1">
                                <?= csrf_field() ?>
                                <button type="submit"
                                        class="w-full bg-gradient-to-r from-amber-500/10 to-orange-500/10 hover:from-amber-500/20 hover:to-orange-500/20 border border-amber-500/30 hover:border-amber-500/50 text-amber-300 hover:text-amber-200 font-medium px-4 py-2.5 rounded-lg transition-all duration-200 text-sm flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                    Switch
                                </button>
                            </form>
                            <?php else: ?>
                            <div class="flex-1 bg-gradient-to-r from-amber-500/20 to-orange-500/20 border border-amber-500/40 text-amber-300 font-medium px-4 py-2.5 rounded-lg text-sm flex items-center justify-center">
                                <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Active
                            </div>
                            <?php endif; ?>

                            <a href="<?= url('/campaigns/' . $campaign['id']) ?>"
                               class="bg-slate-700/40 hover:bg-slate-700/60 border border-slate-600/40 hover:border-slate-600/60 text-gray-300 hover:text-gray-200 font-medium px-4 py-2.5 rounded-lg transition-all duration-200 text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php endif; ?>
    </div>
</div>
<?php $this->endSection() ?>
