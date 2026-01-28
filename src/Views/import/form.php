<?php $this->extends('layouts/base') ?>

<?php $this->section('title') ?>
Import Data - Worlds
<?php $this->endSection() ?>

<?php $this->section('content') ?>
<div class="min-h-screen bg-gradient-to-br from-zinc-950 via-slate-900 to-zinc-900 px-4 py-12 relative overflow-hidden">
    <!-- Atmospheric background elements - cyan/blue theme for import -->
    <div class="absolute inset-0 opacity-5">
        <div class="absolute top-20 right-20 w-96 h-96 bg-cyan-500 rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 left-20 w-96 h-96 bg-blue-600 rounded-full blur-3xl"></div>
    </div>

    <!-- Noise texture overlay -->
    <div class="absolute inset-0 opacity-20 mix-blend-soft-light" style="background-image: url('data:image/svg+xml,%3Csvg viewBox=%220 0 400 400%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noiseFilter%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.9%22 numOctaves=%223%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noiseFilter)%22/%3E%3C/svg%3E');"></div>

    <div class="relative z-10 max-w-4xl mx-auto">
        <!-- Breadcrumb -->
        <nav class="mb-8 flex items-center space-x-2 text-sm">
            <a href="<?= url('/dashboard') ?>" class="text-gray-500 hover:text-cyan-400 transition-colors">
                Dashboard
            </a>
            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-gray-400">Import Data</span>
        </nav>

        <!-- Header -->
        <div class="mb-10">
            <h1 class="text-5xl font-bold mb-3 bg-gradient-to-r from-cyan-200 via-blue-400 to-cyan-300 bg-clip-text text-transparent tracking-tight">
                Import Campaign Data
            </h1>
            <p class="text-gray-400 text-lg font-light tracking-wide">
                Import entities, relations, and tags from JSON export files
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

        <?php if (!empty($errors)): ?>
        <div class="mb-8 p-4 bg-red-900/30 border border-red-700/50 rounded-lg backdrop-blur-sm">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-red-400 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <div class="flex-1">
                    <ul class="text-red-200 text-sm space-y-1">
                        <?php foreach ($errors as $error_msg): ?>
                            <li><?= e($error_msg) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Form Card -->
        <div class="relative group">
            <!-- Glow effect -->
            <div class="absolute -inset-0.5 bg-gradient-to-r from-cyan-600 to-blue-600 rounded-2xl opacity-0 group-hover:opacity-20 blur transition duration-500"></div>

            <div class="relative bg-slate-800/80 backdrop-blur-xl border border-slate-700/50 rounded-2xl shadow-2xl p-8">
                <form method="POST" action="<?= url('/import/preview') ?>" enctype="multipart/form-data" class="space-y-8">
                    <?= csrf_field() ?>

                    <!-- File Upload Field -->
                    <div class="space-y-2">
                        <label for="import_file" class="block text-sm font-medium text-gray-300 tracking-wide">
                            JSON Export File
                            <span class="text-red-400">*</span>
                        </label>
                        <div class="relative">
                            <input
                                type="file"
                                name="import_file"
                                id="import_file"
                                accept=".json,application/json"
                                required
                                class="block w-full px-4 py-3.5 bg-slate-900/50 border border-slate-600/50 rounded-lg text-gray-100 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-cyan-500 file:text-white hover:file:bg-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 transition duration-200"
                            >
                        </div>
                        <p class="text-xs text-gray-500 pl-1">
                            Upload a JSON file exported from Worlds or Kanka
                        </p>
                    </div>

                    <!-- Source Format Selector -->
                    <div class="space-y-2">
                        <label for="source_format" class="block text-sm font-medium text-gray-300 tracking-wide">
                            Source Format
                        </label>
                        <select
                            name="source_format"
                            id="source_format"
                            class="block w-full px-4 py-3.5 bg-slate-900/50 border border-slate-600/50 rounded-lg text-gray-100 focus:outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 transition duration-200"
                        >
                            <option value="auto">Auto-detect</option>
                            <option value="worlds">Worlds Export</option>
                            <option value="kanka">Kanka Export</option>
                        </select>
                        <p class="text-xs text-gray-500 pl-1">
                            The system will attempt to detect the format automatically
                        </p>
                    </div>

                    <!-- Import Target Options -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-300 tracking-wide flex items-center">
                            <svg class="w-5 h-5 mr-2 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            Import Destination
                        </h3>

                        <!-- Create New Campaign Option -->
                        <div class="bg-slate-900/30 border border-slate-700/30 rounded-lg p-6 space-y-4">
                            <label class="flex items-start cursor-pointer">
                                <input
                                    type="checkbox"
                                    name="create_new_campaign"
                                    id="create_new_campaign"
                                    value="1"
                                    class="mt-1 w-4 h-4 text-cyan-500 bg-slate-900 border-slate-600 rounded focus:ring-cyan-500/50 focus:ring-2"
                                    onchange="document.getElementById('new_campaign_name_field').classList.toggle('hidden', !this.checked); document.getElementById('target_campaign_field').classList.toggle('hidden', this.checked);"
                                >
                                <div class="ml-3">
                                    <span class="text-sm font-medium text-gray-300">Create New Campaign</span>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Import into a new campaign instead of an existing one
                                    </p>
                                </div>
                            </label>

                            <!-- New Campaign Name -->
                            <div id="new_campaign_name_field" class="hidden ml-7">
                                <label for="new_campaign_name" class="block text-sm font-medium text-gray-400 mb-2">
                                    New Campaign Name
                                </label>
                                <input
                                    type="text"
                                    name="new_campaign_name"
                                    id="new_campaign_name"
                                    class="block w-full px-4 py-2.5 bg-slate-900/50 border border-slate-600/50 rounded-lg text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 transition duration-200"
                                    placeholder="Leave empty to use name from import file"
                                >
                            </div>
                        </div>

                        <!-- Existing Campaign Selector -->
                        <div id="target_campaign_field" class="space-y-2">
                            <label for="target_campaign" class="block text-sm font-medium text-gray-300 tracking-wide">
                                Target Campaign
                                <span class="text-red-400">*</span>
                            </label>
                            <select
                                name="target_campaign"
                                id="target_campaign"
                                required
                                class="block w-full px-4 py-3.5 bg-slate-900/50 border border-slate-600/50 rounded-lg text-gray-100 focus:outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 transition duration-200"
                            >
                                <option value="">-- Select Campaign --</option>
                                <?php foreach ($campaigns as $campaign): ?>
                                <option value="<?= $campaign['id'] ?>" <?= $campaign['id'] === $activeCampaignId ? 'selected' : '' ?>>
                                    <?= e($campaign['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="text-xs text-gray-500 pl-1">
                                Select which campaign to import the data into
                            </p>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-between pt-6 border-t border-slate-700/50">
                        <a href="<?= url('/dashboard') ?>"
                           class="inline-flex items-center bg-slate-800/60 backdrop-blur-sm border border-slate-700/50 text-gray-300 hover:text-gray-200 hover:border-slate-600/50 font-medium px-6 py-3 rounded-lg transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Cancel
                        </a>

                        <button
                            type="submit"
                            class="group relative overflow-hidden bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-400 hover:to-blue-400 text-white font-semibold px-8 py-3.5 rounded-lg shadow-lg shadow-cyan-900/30 hover:shadow-cyan-900/50 focus:outline-none focus:ring-2 focus:ring-cyan-500/50 focus:ring-offset-2 focus:ring-offset-slate-800 transition-all duration-300 transform hover:scale-105"
                        >
                            <span class="relative z-10 flex items-center tracking-wide">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Preview Import
                            </span>
                            <div class="absolute inset-0 bg-gradient-to-r from-blue-400 to-cyan-400 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Help Card -->
        <div class="mt-8 bg-slate-800/40 backdrop-blur-sm border border-slate-700/30 rounded-lg p-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-300 mb-1">Import Tips</h3>
                    <ul class="text-xs text-gray-500 space-y-1 list-disc list-inside">
                        <li>Supported formats: Worlds native export and Kanka export</li>
                        <li>You can preview the import before committing changes</li>
                        <li>Conflict resolution options will be available in the preview step</li>
                        <li>All imports are transactional - they either complete fully or roll back</li>
                        <li>Entity relationships and tags are preserved during import</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection() ?>
