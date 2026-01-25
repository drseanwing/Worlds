<?php $this->extends('layouts/base') ?>

<?php $this->section('title') ?>
<?= e($entity['name'] ?? 'Entity') ?> - Worlds
<?php $this->endSection() ?>

<?php $this->section('content') ?>
<div class="min-h-screen bg-gradient-to-br from-slate-950 via-gray-900 to-slate-900 px-4 py-12">
    <!-- Atmospheric background elements -->
    <div class="fixed inset-0 pointer-events-none opacity-5">
        <div class="absolute top-20 left-20 w-96 h-96 bg-purple-500 rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 right-20 w-96 h-96 bg-indigo-600 rounded-full blur-3xl"></div>
    </div>

    <!-- Noise texture overlay -->
    <div class="fixed inset-0 pointer-events-none opacity-20 mix-blend-soft-light" style="background-image: url('data:image/svg+xml,%3Csvg viewBox=%220 0 400 400%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noiseFilter%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.9%22 numOctaves=%223%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noiseFilter)%22/%3E%3C/svg%3E');"></div>

    <div class="relative z-10 max-w-5xl mx-auto">
        <!-- Breadcrumb -->
        <nav class="mb-8 flex items-center space-x-2 text-sm">
            <a href="<?= url('/entities') ?>" class="text-gray-500 hover:text-purple-400 transition-colors">
                Entities
            </a>
            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-gray-400"><?= e($entity['name'] ?? 'Entity') ?></span>
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

        <!-- Main Entity Card -->
        <div class="relative group mb-8">
            <div class="absolute -inset-0.5 bg-gradient-to-r from-purple-600 to-indigo-600 rounded-2xl opacity-20 blur"></div>
            <div class="relative bg-slate-800/80 backdrop-blur-xl border border-slate-700/50 rounded-2xl shadow-2xl overflow-hidden">

                <!-- Entity Header with Hero Image -->
                <div class="relative h-64 bg-gradient-to-br from-slate-700 via-slate-800 to-slate-900 overflow-hidden">
                    <!-- Decorative Grid Pattern -->
                    <div class="absolute inset-0 opacity-10" style="background-image: repeating-linear-gradient(0deg, transparent, transparent 40px, rgba(255,255,255,.05) 40px, rgba(255,255,255,.05) 41px), repeating-linear-gradient(90deg, transparent, transparent 40px, rgba(255,255,255,.05) 40px, rgba(255,255,255,.05) 41px);"></div>

                    <!-- Gradient Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-800/90 via-slate-800/50 to-transparent"></div>

                    <!-- Entity Icon -->
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-32 h-32 rounded-full bg-gradient-to-br from-purple-500/20 to-indigo-500/20 backdrop-blur-sm border-2 border-purple-500/40 flex items-center justify-center">
                            <svg class="w-16 h-16 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Entity Type Badge -->
                    <div class="absolute top-6 right-6">
                        <span class="inline-block bg-purple-500/30 backdrop-blur-sm border border-purple-400/40 text-purple-200 px-4 py-2 rounded-lg text-sm font-bold tracking-wide uppercase shadow-lg">
                            <?= e($entity['entity_type'] ?? 'Unknown') ?>
                        </span>
                    </div>
                </div>

                <!-- Entity Title and Metadata -->
                <div class="p-8 border-b border-slate-700/50">
                    <h1 class="text-5xl font-bold mb-4 bg-gradient-to-r from-purple-200 via-purple-400 to-indigo-300 bg-clip-text text-transparent tracking-tight leading-tight">
                        <?= e($entity['name'] ?? 'Unnamed Entity') ?>
                    </h1>

                    <div class="flex items-center space-x-6 text-sm text-gray-400">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Created <?= date('F j, Y', strtotime($entity['created_at'] ?? 'now')) ?>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Updated <?= date('F j, Y', strtotime($entity['updated_at'] ?? 'now')) ?>
                        </div>
                    </div>
                </div>

                <!-- Entity Description/Entry -->
                <?php if (!empty($entity['entry'])): ?>
                <div class="p-8 border-b border-slate-700/50">
                    <h2 class="text-xl font-bold text-gray-300 mb-4 tracking-wide flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Description
                    </h2>
                    <div class="prose prose-invert prose-purple max-w-none">
                        <p class="text-gray-300 leading-relaxed whitespace-pre-wrap"><?= e($entity['entry']) ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Relations Section (Placeholder) -->
                <div class="p-8 border-b border-slate-700/50">
                    <h2 class="text-xl font-bold text-gray-300 mb-4 tracking-wide flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                        </svg>
                        Relations
                    </h2>
                    <div class="bg-slate-900/50 rounded-lg p-6 border border-slate-700/30">
                        <p class="text-gray-500 text-sm text-center italic">
                            Relations system coming soon
                        </p>
                    </div>
                </div>

                <!-- Attributes Section (Placeholder) -->
                <div class="p-8">
                    <h2 class="text-xl font-bold text-gray-300 mb-4 tracking-wide flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                        </svg>
                        Attributes
                    </h2>
                    <div class="bg-slate-900/50 rounded-lg p-6 border border-slate-700/30">
                        <p class="text-gray-500 text-sm text-center italic">
                            Custom attributes coming soon
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between">
            <a href="<?= url('/entities') ?>"
               class="inline-flex items-center bg-slate-800/60 backdrop-blur-sm border border-slate-700/50 text-gray-300 hover:text-purple-400 hover:border-purple-500/50 font-medium px-6 py-3 rounded-lg transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to List
            </a>

            <div class="flex items-center space-x-4">
                <!-- Edit Button -->
                <a href="<?= url('/entities/' . ($entity['id'] ?? '') . '/edit') ?>"
                   class="group relative overflow-hidden bg-gradient-to-r from-purple-500 to-indigo-500 hover:from-purple-400 hover:to-indigo-400 text-white font-semibold px-8 py-3 rounded-lg shadow-lg shadow-purple-900/30 hover:shadow-purple-900/50 focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:ring-offset-2 focus:ring-offset-slate-900 transition-all duration-300 transform hover:scale-105">
                    <span class="relative z-10 flex items-center tracking-wide">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Entity
                    </span>
                    <div class="absolute inset-0 bg-gradient-to-r from-indigo-400 to-purple-400 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                </a>

                <!-- Delete Button -->
                <button
                    onclick="if(confirm('Are you sure you want to delete this entity? This action cannot be undone.')) { document.getElementById('delete-form').submit(); }"
                    class="group relative overflow-hidden bg-slate-800/60 backdrop-blur-sm border border-red-700/50 hover:border-red-600/70 text-red-400 hover:text-red-300 font-medium px-6 py-3 rounded-lg transition-all duration-200 hover:bg-red-900/20">
                    <span class="flex items-center tracking-wide">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete
                    </span>
                </button>

                <!-- Hidden Delete Form -->
                <form id="delete-form" method="POST" action="<?= url('/entities/' . ($entity['id'] ?? '') . '/delete') ?>" class="hidden">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="DELETE">
                </form>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection() ?>
