<?php $this->extends('layouts/base') ?>

<?php $this->section('title') ?>
Export Data - Worlds
<?php $this->endSection() ?>

<?php $this->section('content') ?>
<div class="min-h-screen bg-gradient-to-br from-zinc-950 via-slate-900 to-zinc-900 px-4 py-12 relative overflow-hidden">
    <!-- Atmospheric background elements - purple/magenta theme for export -->
    <div class="absolute inset-0 opacity-5">
        <div class="absolute top-20 right-20 w-96 h-96 bg-purple-500 rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 left-20 w-96 h-96 bg-magenta-600 rounded-full blur-3xl"></div>
    </div>

    <!-- Noise texture overlay -->
    <div class="absolute inset-0 opacity-20 mix-blend-soft-light" style="background-image: url('data:image/svg+xml,%3Csvg viewBox=%220 0 400 400%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noiseFilter%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.9%22 numOctaves=%223%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noiseFilter)%22/%3E%3C/svg%3E');"></div>

    <div class="relative z-10 max-w-6xl mx-auto">
        <!-- Breadcrumb -->
        <nav class="mb-8 flex items-center space-x-2 text-sm">
            <a href="<?= url('/dashboard') ?>" class="text-gray-500 hover:text-purple-400 transition-colors">
                Dashboard
            </a>
            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-gray-400">Export Data</span>
        </nav>

        <!-- Header -->
        <div class="mb-10">
            <h1 class="text-5xl font-bold mb-3 bg-gradient-to-r from-purple-200 via-fuchsia-400 to-purple-300 bg-clip-text text-transparent tracking-tight">
                Export Campaign Data
            </h1>
            <p class="text-gray-400 text-lg font-light tracking-wide">
                Download your campaign data as JSON or backup your database
            </p>
        </div>

        <!-- Flash Messages -->
        <?php if ($success = get_flash('success')): ?>
        <div class="mb-8 p-4 bg-green-900/30 border border-green-700/50 rounded-lg backdrop-blur-sm">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-green-400 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <p class="text-green-200 text-sm leading-relaxed"><?= e($success) ?></p>
            </div>
        </div>
        <?php endif; ?>

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

        <!-- Export Options Grid -->
        <div class="grid md:grid-cols-2 gap-6 mb-8">
            <!-- Campaign Export Card -->
            <div class="relative group">
                <div class="absolute -inset-0.5 bg-gradient-to-r from-purple-600 to-fuchsia-600 rounded-2xl opacity-0 group-hover:opacity-20 blur transition duration-500"></div>

                <div class="relative bg-slate-800/80 backdrop-blur-xl border border-slate-700/50 rounded-2xl shadow-2xl p-8">
                    <div class="flex items-center mb-4">
                        <div class="p-3 bg-purple-500/20 rounded-lg mr-4">
                            <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold text-gray-200">Campaign Export</h2>
                            <p class="text-sm text-gray-400">Export entire campaign as JSON</p>
                        </div>
                    </div>

                    <p class="text-gray-400 text-sm mb-6">
                        Download all entities, relations, tags, attributes, and posts from a campaign in a portable JSON format compatible with Worlds.
                    </p>

                    <?php if (empty($campaigns)): ?>
                    <div class="text-sm text-gray-500 italic">
                        No campaigns available to export
                    </div>
                    <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($campaigns as $campaign): ?>
                        <a href="<?= url('/export/campaign/' . $campaign['id']) ?>"
                           class="flex items-center justify-between p-4 bg-slate-900/50 border border-slate-700/50 rounded-lg hover:border-purple-500/50 hover:bg-purple-500/10 transition-all duration-200 group/item">
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-200 group-hover/item:text-purple-300 transition-colors">
                                    <?= e($campaign['name']) ?>
                                </div>
                                <?php if ($campaign['id'] === $activeCampaignId): ?>
                                <div class="text-xs text-purple-400 mt-1">Active Campaign</div>
                                <?php endif; ?>
                            </div>
                            <svg class="w-5 h-5 text-gray-500 group-hover/item:text-purple-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Database Backup Card -->
            <div class="relative group">
                <div class="absolute -inset-0.5 bg-gradient-to-r from-fuchsia-600 to-pink-600 rounded-2xl opacity-0 group-hover:opacity-20 blur transition duration-500"></div>

                <div class="relative bg-slate-800/80 backdrop-blur-xl border border-slate-700/50 rounded-2xl shadow-2xl p-8">
                    <div class="flex items-center mb-4">
                        <div class="p-3 bg-fuchsia-500/20 rounded-lg mr-4">
                            <svg class="w-8 h-8 text-fuchsia-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold text-gray-200">Database Backup</h2>
                            <p class="text-sm text-gray-400">Complete SQLite backup</p>
                        </div>
                    </div>

                    <p class="text-gray-400 text-sm mb-6">
                        Download a complete copy of the SQLite database file. This includes all users, campaigns, and data. Requires admin access.
                    </p>

                    <?php if (\Worlds\Config\Auth::isAdmin()): ?>
                    <a href="<?= url('/export/backup') ?>"
                       class="inline-flex items-center justify-center w-full bg-gradient-to-r from-fuchsia-500 to-pink-500 hover:from-fuchsia-400 hover:to-pink-400 text-white font-semibold px-6 py-3.5 rounded-lg shadow-lg shadow-fuchsia-900/30 hover:shadow-fuchsia-900/50 focus:outline-none focus:ring-2 focus:ring-fuchsia-500/50 focus:ring-offset-2 focus:ring-offset-slate-800 transition-all duration-300 transform hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Download Database Backup
                    </a>

                    <!-- Recent Backups -->
                    <?php if (!empty($backups)): ?>
                    <div class="mt-6 pt-6 border-t border-slate-700/50">
                        <h3 class="text-sm font-medium text-gray-300 mb-3">Recent Backups</h3>
                        <div class="space-y-2">
                            <?php foreach (array_slice($backups, 0, 5) as $backup): ?>
                            <div class="flex items-center justify-between text-xs text-gray-400">
                                <span><?= e($backup['filename']) ?></span>
                                <span><?= number_format($backup['size'] / 1024, 1) ?> KB</span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php else: ?>
                    <div class="p-4 bg-yellow-900/20 border border-yellow-700/50 rounded-lg">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-yellow-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                            </svg>
                            <p class="text-yellow-200 text-xs">
                                Database backups require administrator privileges
                            </p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Help Card -->
        <div class="bg-slate-800/40 backdrop-blur-sm border border-slate-700/30 rounded-lg p-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-300 mb-1">Export Information</h3>
                    <ul class="text-xs text-gray-500 space-y-1 list-disc list-inside">
                        <li><strong>Campaign Export:</strong> JSON format containing all campaign data (entities, relations, tags, etc.)</li>
                        <li><strong>Database Backup:</strong> Complete SQLite database file for disaster recovery</li>
                        <li>Campaign exports can be imported into other Worlds installations</li>
                        <li>Export format is compatible with Kanka import (some fields may need manual mapping)</li>
                        <li>Database backups include all users and campaigns - store securely</li>
                        <li>Regular backups are recommended before major changes</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8 flex items-center justify-between">
            <a href="<?= url('/dashboard') ?>"
               class="inline-flex items-center text-gray-400 hover:text-gray-300 text-sm transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Dashboard
            </a>

            <a href="<?= url('/import') ?>"
               class="inline-flex items-center text-cyan-400 hover:text-cyan-300 text-sm font-medium transition-colors">
                Import Data
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </a>
        </div>
    </div>
</div>
<?php $this->endSection() ?>
