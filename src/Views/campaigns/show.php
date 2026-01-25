<?php $this->extends('layouts/base') ?>

<?php $this->section('title') ?>
<?= e($campaign['name'] ?? 'Campaign') ?> - Worlds
<?php $this->endSection() ?>

<?php $this->section('content') ?>
<div class="min-h-screen bg-gradient-to-br from-slate-950 via-zinc-900 to-slate-900 px-4 py-12">
    <!-- Atmospheric background elements - warm amber/orange theme -->
    <div class="fixed inset-0 pointer-events-none opacity-5">
        <div class="absolute top-20 left-20 w-96 h-96 bg-orange-500 rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 right-20 w-96 h-96 bg-amber-600 rounded-full blur-3xl"></div>
    </div>

    <!-- Noise texture overlay -->
    <div class="fixed inset-0 pointer-events-none opacity-20 mix-blend-soft-light" style="background-image: url('data:image/svg+xml,%3Csvg viewBox=%220 0 400 400%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noiseFilter%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.9%22 numOctaves=%223%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noiseFilter)%22/%3E%3C/svg%3E');"></div>

    <div class="relative z-10 max-w-5xl mx-auto">
        <!-- Breadcrumb -->
        <nav class="mb-8 flex items-center space-x-2 text-sm">
            <a href="<?= url('/campaigns') ?>" class="text-gray-500 hover:text-amber-400 transition-colors">
                Campaigns
            </a>
            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-gray-400"><?= e($campaign['name'] ?? 'Campaign') ?></span>
        </nav>

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

        <!-- Main Campaign Card -->
        <div class="relative group mb-8">
            <div class="absolute -inset-0.5 bg-gradient-to-r from-amber-600 to-orange-600 rounded-2xl opacity-20 blur"></div>
            <div class="relative bg-slate-800/80 backdrop-blur-xl border border-slate-700/50 rounded-2xl shadow-2xl overflow-hidden">

                <!-- Campaign Hero Section -->
                <div class="relative h-64 bg-gradient-to-br from-amber-900/40 via-orange-900/30 to-slate-900/60 overflow-hidden">
                    <!-- Decorative Diagonal Pattern -->
                    <div class="absolute inset-0 opacity-10" style="background-image: repeating-linear-gradient(-45deg, transparent, transparent 30px, rgba(255,255,255,.03) 30px, rgba(255,255,255,.03) 60px);"></div>

                    <!-- Gradient Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-800/95 via-slate-800/60 to-transparent"></div>

                    <!-- Campaign Icon -->
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-32 h-32 rounded-full bg-gradient-to-br from-amber-500/30 to-orange-500/30 backdrop-blur-sm border-2 border-amber-500/50 flex items-center justify-center shadow-2xl shadow-amber-900/50">
                            <svg class="w-16 h-16 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Active Badge -->
                    <?php if (isset($isActive) && $isActive): ?>
                    <div class="absolute top-6 right-6">
                        <div class="bg-gradient-to-r from-amber-500 to-orange-500 text-white px-4 py-2 rounded-lg text-sm font-bold tracking-wide uppercase shadow-xl shadow-amber-900/50 flex items-center space-x-2 backdrop-blur-sm border border-amber-400/30">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <span>Active Campaign</span>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Campaign Title and Metadata -->
                <div class="p-8 border-b border-slate-700/50">
                    <h1 class="text-5xl font-bold mb-4 bg-gradient-to-r from-amber-200 via-orange-400 to-amber-300 bg-clip-text text-transparent tracking-tight leading-tight">
                        <?= e($campaign['name'] ?? 'Unnamed Campaign') ?>
                    </h1>

                    <div class="flex items-center space-x-6 text-sm text-gray-400">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Created <?= date('F j, Y', strtotime($campaign['created_at'] ?? 'now')) ?>
                        </div>
                        <?php if (isset($campaign['updated_at'])): ?>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Updated <?= date('F j, Y', strtotime($campaign['updated_at'])) ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Campaign Description -->
                <?php if (!empty($campaign['description'])): ?>
                <div class="p-8 border-b border-slate-700/50">
                    <h2 class="text-xl font-bold text-gray-300 mb-4 tracking-wide flex items-center">
                        <svg class="w-5 h-5 mr-2 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Description
                    </h2>
                    <div class="prose prose-invert prose-amber max-w-none">
                        <p class="text-gray-300 leading-relaxed whitespace-pre-wrap"><?= e($campaign['description']) ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Entity Statistics Section (Placeholder) -->
                <div class="p-8 border-b border-slate-700/50">
                    <h2 class="text-xl font-bold text-gray-300 mb-6 tracking-wide flex items-center">
                        <svg class="w-5 h-5 mr-2 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Statistics
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Total Entities -->
                        <div class="bg-gradient-to-br from-amber-900/20 to-orange-900/10 border border-amber-700/30 rounded-lg p-5">
                            <div class="flex items-center justify-between mb-2">
                                <svg class="w-8 h-8 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                                <span class="text-3xl font-bold text-amber-300"><?= isset($entityStats['total']) ? $entityStats['total'] : 0 ?></span>
                            </div>
                            <p class="text-gray-400 text-sm font-medium">Total Entities</p>
                        </div>

                        <!-- Entity Types -->
                        <div class="bg-gradient-to-br from-orange-900/20 to-amber-900/10 border border-orange-700/30 rounded-lg p-5">
                            <div class="flex items-center justify-between mb-2">
                                <svg class="w-8 h-8 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                                <span class="text-3xl font-bold text-orange-300"><?= isset($entityStats['types']) ? $entityStats['types'] : 0 ?></span>
                            </div>
                            <p class="text-gray-400 text-sm font-medium">Entity Types</p>
                        </div>

                        <!-- Recent Activity -->
                        <div class="bg-gradient-to-br from-amber-900/20 to-slate-900/10 border border-amber-700/30 rounded-lg p-5">
                            <div class="flex items-center justify-between mb-2">
                                <svg class="w-8 h-8 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="text-3xl font-bold text-amber-300"><?= isset($entityStats['recent']) ? $entityStats['recent'] : 0 ?></span>
                            </div>
                            <p class="text-gray-400 text-sm font-medium">Last 7 Days</p>
                        </div>
                    </div>
                </div>

                <!-- Settings Section (Placeholder) -->
                <div class="p-8">
                    <h2 class="text-xl font-bold text-gray-300 mb-4 tracking-wide flex items-center">
                        <svg class="w-5 h-5 mr-2 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Campaign Settings
                    </h2>
                    <div class="bg-slate-900/50 rounded-lg p-6 border border-slate-700/30">
                        <p class="text-gray-500 text-sm text-center italic">
                            Advanced campaign settings will appear here in a future update
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between">
            <a href="<?= url('/campaigns') ?>"
               class="inline-flex items-center bg-slate-800/60 backdrop-blur-sm border border-slate-700/50 text-gray-300 hover:text-amber-400 hover:border-amber-500/50 font-medium px-6 py-3 rounded-lg transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to List
            </a>

            <div class="flex items-center space-x-4">
                <!-- Make Active Button (if not already active) -->
                <?php if (!isset($isActive) || !$isActive): ?>
                <form method="POST" action="<?= url('/campaigns/' . ($campaign['id'] ?? '') . '/activate') ?>">
                    <?= csrf_field() ?>
                    <button type="submit"
                            class="group relative overflow-hidden bg-gradient-to-r from-amber-500/10 to-orange-500/10 hover:from-amber-500/20 hover:to-orange-500/20 border border-amber-500/30 hover:border-amber-500/50 text-amber-300 hover:text-amber-200 font-medium px-6 py-3 rounded-lg transition-all duration-200">
                        <span class="flex items-center tracking-wide">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            Make Active
                        </span>
                    </button>
                </form>
                <?php endif; ?>

                <!-- Edit Button -->
                <a href="<?= url('/campaigns/' . ($campaign['id'] ?? '') . '/edit') ?>"
                   class="group relative overflow-hidden bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-400 hover:to-orange-400 text-white font-semibold px-8 py-3 rounded-lg shadow-lg shadow-amber-900/30 hover:shadow-amber-900/50 focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:ring-offset-2 focus:ring-offset-slate-900 transition-all duration-300 transform hover:scale-105">
                    <span class="relative z-10 flex items-center tracking-wide">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Campaign
                    </span>
                    <div class="absolute inset-0 bg-gradient-to-r from-orange-400 to-amber-400 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                </a>

                <!-- Delete Button -->
                <button
                    onclick="if(confirm('Are you sure you want to delete this campaign? This action cannot be undone.')) { document.getElementById('delete-form').submit(); }"
                    class="group relative overflow-hidden bg-slate-800/60 backdrop-blur-sm border border-red-700/50 hover:border-red-600/70 text-red-400 hover:text-red-300 font-medium px-6 py-3 rounded-lg transition-all duration-200 hover:bg-red-900/20">
                    <span class="flex items-center tracking-wide">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete
                    </span>
                </button>

                <!-- Hidden Delete Form -->
                <form id="delete-form" method="POST" action="<?= url('/campaigns/' . ($campaign['id'] ?? '') . '/delete') ?>" class="hidden">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="DELETE">
                </form>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection() ?>
