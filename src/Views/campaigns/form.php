<?php $this->extends('layouts/base') ?>

<?php $this->section('title') ?>
<?= isset($campaign) ? 'Edit' : 'Create' ?> Campaign - Worlds
<?php $this->endSection() ?>

<?php $this->section('content') ?>
<div class="min-h-screen bg-gradient-to-br from-zinc-950 via-slate-900 to-zinc-900 px-4 py-12 relative overflow-hidden">
    <!-- Atmospheric background elements - warm amber/orange theme -->
    <div class="absolute inset-0 opacity-5">
        <div class="absolute top-20 right-20 w-96 h-96 bg-orange-500 rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 left-20 w-96 h-96 bg-amber-600 rounded-full blur-3xl"></div>
    </div>

    <!-- Noise texture overlay -->
    <div class="absolute inset-0 opacity-20 mix-blend-soft-light" style="background-image: url('data:image/svg+xml,%3Csvg viewBox=%220 0 400 400%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noiseFilter%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.9%22 numOctaves=%223%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noiseFilter)%22/%3E%3C/svg%3E');"></div>

    <div class="relative z-10 max-w-4xl mx-auto">
        <!-- Breadcrumb -->
        <nav class="mb-8 flex items-center space-x-2 text-sm">
            <a href="<?= url('/campaigns') ?>" class="text-gray-500 hover:text-amber-400 transition-colors">
                Campaigns
            </a>
            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-gray-400"><?= isset($campaign) ? 'Edit' : 'Create New' ?></span>
        </nav>

        <!-- Header -->
        <div class="mb-10">
            <h1 class="text-5xl font-bold mb-3 bg-gradient-to-r from-amber-200 via-orange-400 to-amber-300 bg-clip-text text-transparent tracking-tight">
                <?= isset($campaign) ? 'Edit Campaign' : 'Create New Campaign' ?>
            </h1>
            <p class="text-gray-400 text-lg font-light tracking-wide">
                <?= isset($campaign) ? 'Update the details of your campaign' : 'Start a new campaign to organize your worldbuilding' ?>
            </p>
        </div>

        <!-- Flash Messages -->
        <?php if ($error = get_flash('error')): ?>
        <div class="mb-8 p-4 bg-red-900/30 border border-red-700/50 rounded-lg backdrop-blur-sm">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-red-400 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <p class="text-red-200 text-sm leading-relaxed"><?= e($error) ?></p>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($errors = get_flash('errors')): ?>
        <div class="mb-8 p-4 bg-red-900/30 border border-red-700/50 rounded-lg backdrop-blur-sm">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-red-400 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <div class="flex-1">
                    <?php if (is_array($errors)): ?>
                        <ul class="text-red-200 text-sm space-y-1">
                            <?php foreach ($errors as $error_msg): ?>
                                <li><?= e($error_msg) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-red-200 text-sm"><?= e($errors) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Form Card -->
        <div class="relative group">
            <!-- Glow effect -->
            <div class="absolute -inset-0.5 bg-gradient-to-r from-amber-600 to-orange-600 rounded-2xl opacity-0 group-hover:opacity-20 blur transition duration-500"></div>

            <div class="relative bg-slate-800/80 backdrop-blur-xl border border-slate-700/50 rounded-2xl shadow-2xl p-8">
                <form method="POST" action="<?= isset($campaign) ? url('/campaigns/' . $campaign['id'] . '/update') : url('/campaigns/store') ?>" class="space-y-8">
                    <?= csrf_field() ?>
                    <?php if (isset($campaign)): ?>
                    <input type="hidden" name="_method" value="PUT">
                    <?php endif; ?>

                    <!-- Campaign Name Field -->
                    <div class="space-y-2">
                        <label for="name" class="block text-sm font-medium text-gray-300 tracking-wide">
                            Campaign Name
                            <span class="text-red-400">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                            </div>
                            <input
                                type="text"
                                name="name"
                                id="name"
                                value="<?= e(old('name', $campaign['name'] ?? '')) ?>"
                                required
                                autocomplete="off"
                                class="block w-full pl-12 pr-4 py-3.5 bg-slate-900/50 border border-slate-600/50 rounded-lg text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500/50 transition duration-200"
                                placeholder="Enter a memorable campaign name..."
                            >
                        </div>
                        <p class="text-xs text-gray-500 pl-1">
                            Choose a name that captures the essence of this campaign setting
                        </p>
                    </div>

                    <!-- Campaign Description Field -->
                    <div class="space-y-2">
                        <label for="description" class="block text-sm font-medium text-gray-300 tracking-wide">
                            Description
                        </label>
                        <div class="relative">
                            <textarea
                                name="description"
                                id="description"
                                rows="8"
                                class="block w-full px-4 py-3.5 bg-slate-900/50 border border-slate-600/50 rounded-lg text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500/50 transition duration-200 resize-y"
                                placeholder="Describe your campaign world... What makes it unique? What themes or stories will it explore?"
                            ><?= e(old('description', $campaign['description'] ?? '')) ?></textarea>
                            <div class="absolute top-3 right-3">
                                <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 pl-1">
                            Provide an overview of the campaign world, its themes, tone, and key characteristics
                        </p>
                    </div>

                    <!-- Settings Section (Placeholder) -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-300 tracking-wide flex items-center">
                            <svg class="w-5 h-5 mr-2 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Campaign Settings
                        </h3>

                        <div class="bg-slate-900/30 border border-slate-700/30 rounded-lg p-6">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-gray-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium text-gray-400 mb-1">Advanced Settings</h4>
                                    <p class="text-xs text-gray-600">
                                        Campaign-specific settings and configuration options will appear here in a future update.
                                        This may include privacy settings, collaboration features, custom fields, and integration options.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-between pt-6 border-t border-slate-700/50">
                        <a href="<?= isset($campaign) ? url('/campaigns/' . $campaign['id']) : url('/campaigns') ?>"
                           class="inline-flex items-center bg-slate-800/60 backdrop-blur-sm border border-slate-700/50 text-gray-300 hover:text-gray-200 hover:border-slate-600/50 font-medium px-6 py-3 rounded-lg transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Cancel
                        </a>

                        <button
                            type="submit"
                            class="group relative overflow-hidden bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-400 hover:to-orange-400 text-white font-semibold px-8 py-3.5 rounded-lg shadow-lg shadow-amber-900/30 hover:shadow-amber-900/50 focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:ring-offset-2 focus:ring-offset-slate-800 transition-all duration-300 transform hover:scale-105"
                        >
                            <span class="relative z-10 flex items-center tracking-wide">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <?php if (isset($campaign)): ?>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    <?php else: ?>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    <?php endif; ?>
                                </svg>
                                <?= isset($campaign) ? 'Update Campaign' : 'Create Campaign' ?>
                            </span>
                            <div class="absolute inset-0 bg-gradient-to-r from-orange-400 to-amber-400 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Help Card -->
        <div class="mt-8 bg-slate-800/40 backdrop-blur-sm border border-slate-700/30 rounded-lg p-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-300 mb-1">Campaign Tips</h3>
                    <ul class="text-xs text-gray-500 space-y-1 list-disc list-inside">
                        <li>Campaigns help you organize entities into separate worlds or storylines</li>
                        <li>You can switch between campaigns at any time from the campaigns list</li>
                        <li>Each campaign maintains its own collection of entities</li>
                        <li>Use clear, descriptive names to easily identify different campaigns</li>
                        <li>The active campaign determines which entities you see and work with</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection() ?>
