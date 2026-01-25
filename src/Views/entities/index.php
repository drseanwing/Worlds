<?php $this->extends('layouts/base') ?>

<?php $this->section('title') ?>
<?= e($entityType ?? 'All') ?> Entities - Worlds
<?php $this->endSection() ?>

<?php $this->section('content') ?>
<div class="min-h-screen bg-gradient-to-br from-slate-950 via-gray-900 to-slate-900 px-4 py-12">
    <!-- Atmospheric background elements -->
    <div class="fixed inset-0 pointer-events-none opacity-5">
        <div class="absolute top-40 right-20 w-96 h-96 bg-cyan-500 rounded-full blur-3xl"></div>
        <div class="absolute bottom-40 left-20 w-96 h-96 bg-blue-600 rounded-full blur-3xl"></div>
    </div>

    <!-- Noise texture overlay -->
    <div class="fixed inset-0 pointer-events-none opacity-20 mix-blend-soft-light" style="background-image: url('data:image/svg+xml,%3Csvg viewBox=%220 0 400 400%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noiseFilter%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.9%22 numOctaves=%223%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noiseFilter)%22/%3E%3C/svg%3E');"></div>

    <div class="relative z-10 max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="mb-12">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-6xl font-bold mb-3 bg-gradient-to-r from-cyan-200 via-blue-400 to-cyan-200 bg-clip-text text-transparent tracking-tight">
                        <?= e($entityType ?? 'All Entities') ?>
                    </h1>
                    <p class="text-gray-400 text-lg font-light tracking-wide">
                        <?php if (isset($totalEntities)): ?>
                            <?= $totalEntities ?> <?= $totalEntities === 1 ? 'entity' : 'entities' ?> discovered
                        <?php else: ?>
                            Explore the realms you've created
                        <?php endif; ?>
                    </p>
                </div>

                <!-- Create Button -->
                <a href="<?= url('/entities/create') ?>"
                   class="group relative overflow-hidden bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-400 hover:to-blue-400 text-white font-semibold px-8 py-4 rounded-lg shadow-lg shadow-cyan-900/30 hover:shadow-cyan-900/50 focus:outline-none focus:ring-2 focus:ring-cyan-500/50 focus:ring-offset-2 focus:ring-offset-slate-900 transition-all duration-300 transform hover:scale-105">
                    <span class="relative z-10 flex items-center tracking-wide">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        Create Entity
                    </span>
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-400 to-cyan-400 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                </a>
            </div>

            <!-- Filter/Type Indicator -->
            <?php if (isset($entityType) && $entityType !== 'All'): ?>
            <div class="inline-flex items-center space-x-2 bg-slate-800/60 backdrop-blur-sm border border-slate-700/50 rounded-lg px-4 py-2">
                <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                <span class="text-gray-300 text-sm font-medium tracking-wide">Filtered by: <?= e($entityType) ?></span>
                <a href="<?= url('/entities') ?>" class="text-cyan-400 hover:text-cyan-300 text-sm font-medium">
                    Clear
                </a>
            </div>
            <?php endif; ?>
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
        <?php if (empty($entities)): ?>
        <div class="relative group">
            <div class="absolute -inset-0.5 bg-gradient-to-r from-cyan-600 to-blue-600 rounded-2xl opacity-20 blur"></div>
            <div class="relative bg-slate-800/60 backdrop-blur-xl border border-slate-700/50 rounded-2xl shadow-2xl p-16 text-center">
                <div class="max-w-md mx-auto">
                    <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-slate-700 to-slate-800 rounded-full flex items-center justify-center">
                        <svg class="w-12 h-12 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-300 mb-3 tracking-tight">No entities yet</h3>
                    <p class="text-gray-500 mb-8 leading-relaxed">
                        Begin your worldbuilding journey by creating your first entity.
                    </p>
                    <a href="<?= url('/entities/create') ?>"
                       class="inline-flex items-center bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-400 hover:to-blue-400 text-white font-semibold px-6 py-3 rounded-lg shadow-lg shadow-cyan-900/30 transition-all duration-300 transform hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        Create First Entity
                    </a>
                </div>
            </div>
        </div>
        <?php else: ?>

        <!-- Entity Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
            <?php foreach ($entities as $entity): ?>
            <a href="<?= url('/entities/' . $entity['id']) ?>" class="group relative block">
                <!-- Card Glow Effect -->
                <div class="absolute -inset-0.5 bg-gradient-to-r from-cyan-600 to-blue-600 rounded-xl opacity-0 group-hover:opacity-30 blur transition duration-500"></div>

                <div class="relative h-full bg-slate-800/60 backdrop-blur-xl border border-slate-700/50 rounded-xl shadow-xl overflow-hidden transition-all duration-300 group-hover:border-cyan-500/50 group-hover:shadow-2xl group-hover:shadow-cyan-900/20">
                    <!-- Entity Image/Icon Area -->
                    <div class="h-48 bg-gradient-to-br from-slate-700 via-slate-800 to-slate-900 relative overflow-hidden">
                        <!-- Decorative Pattern -->
                        <div class="absolute inset-0 opacity-10" style="background-image: repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(255,255,255,.05) 10px, rgba(255,255,255,.05) 20px);"></div>

                        <!-- Entity Type Icon -->
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="w-20 h-20 rounded-full bg-gradient-to-br from-cyan-500/20 to-blue-500/20 backdrop-blur-sm border border-cyan-500/30 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-10 h-10 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                                </svg>
                            </div>
                        </div>

                        <!-- Entity Type Badge -->
                        <div class="absolute top-4 right-4">
                            <span class="inline-block bg-cyan-500/20 backdrop-blur-sm border border-cyan-400/30 text-cyan-300 px-3 py-1.5 rounded-lg text-xs font-semibold tracking-wide uppercase">
                                <?= e($entity['entity_type'] ?? 'Unknown') ?>
                            </span>
                        </div>
                    </div>

                    <!-- Card Content -->
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-100 mb-2 tracking-tight group-hover:text-cyan-300 transition-colors line-clamp-1">
                            <?= e($entity['name'] ?? 'Unnamed Entity') ?>
                        </h3>

                        <?php if (!empty($entity['entry'])): ?>
                        <p class="text-gray-400 text-sm leading-relaxed line-clamp-3 mb-4">
                            <?= e(substr($entity['entry'], 0, 150)) ?><?= strlen($entity['entry']) > 150 ? '...' : '' ?>
                        </p>
                        <?php else: ?>
                        <p class="text-gray-600 text-sm italic mb-4">No description yet</p>
                        <?php endif; ?>

                        <!-- Metadata Footer -->
                        <div class="flex items-center justify-between pt-4 border-t border-slate-700/50">
                            <div class="flex items-center text-xs text-gray-500">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <?= date('M j, Y', strtotime($entity['created_at'] ?? 'now')) ?>
                            </div>
                            <div class="text-cyan-400 group-hover:text-cyan-300 transition-colors">
                                <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if (isset($pagination) && $pagination['total_pages'] > 1): ?>
        <div class="flex items-center justify-center space-x-2">
            <!-- Previous Button -->
            <?php if ($pagination['current_page'] > 1): ?>
            <a href="<?= url('/entities', ['page' => $pagination['current_page'] - 1]) ?>"
               class="px-4 py-2 bg-slate-800/60 backdrop-blur-sm border border-slate-700/50 rounded-lg text-gray-300 hover:text-cyan-400 hover:border-cyan-500/50 transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <?php endif; ?>

            <!-- Page Numbers -->
            <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                <?php if ($i === $pagination['current_page']): ?>
                <span class="px-4 py-2 bg-gradient-to-r from-cyan-500 to-blue-500 rounded-lg text-white font-semibold">
                    <?= $i ?>
                </span>
                <?php else: ?>
                <a href="<?= url('/entities', ['page' => $i]) ?>"
                   class="px-4 py-2 bg-slate-800/60 backdrop-blur-sm border border-slate-700/50 rounded-lg text-gray-300 hover:text-cyan-400 hover:border-cyan-500/50 transition-all duration-200">
                    <?= $i ?>
                </a>
                <?php endif; ?>
            <?php endfor; ?>

            <!-- Next Button -->
            <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
            <a href="<?= url('/entities', ['page' => $pagination['current_page'] + 1]) ?>"
               class="px-4 py-2 bg-slate-800/60 backdrop-blur-sm border border-slate-700/50 rounded-lg text-gray-300 hover:text-cyan-400 hover:border-cyan-500/50 transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php endif; ?>
    </div>
</div>
<?php $this->endSection() ?>
