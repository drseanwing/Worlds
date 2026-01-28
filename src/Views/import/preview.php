<?php $this->extends('layouts/base') ?>

<?php $this->section('title') ?>
Import Preview - Worlds
<?php $this->endSection() ?>

<?php $this->section('content') ?>
<div class="min-h-screen bg-gradient-to-br from-zinc-950 via-slate-900 to-zinc-900 px-4 py-12 relative overflow-hidden">
    <!-- Atmospheric background elements -->
    <div class="absolute inset-0 opacity-5">
        <div class="absolute top-20 right-20 w-96 h-96 bg-cyan-500 rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 left-20 w-96 h-96 bg-blue-600 rounded-full blur-3xl"></div>
    </div>

    <!-- Noise texture overlay -->
    <div class="absolute inset-0 opacity-20 mix-blend-soft-light" style="background-image: url('data:image/svg+xml,%3Csvg viewBox=%220 0 400 400%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noiseFilter%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.9%22 numOctaves=%223%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noiseFilter)%22/%3E%3C/svg%3E');"></div>

    <div class="relative z-10 max-w-6xl mx-auto">
        <!-- Breadcrumb -->
        <nav class="mb-8 flex items-center space-x-2 text-sm">
            <a href="<?= url('/dashboard') ?>" class="text-gray-500 hover:text-cyan-400 transition-colors">
                Dashboard
            </a>
            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <a href="<?= url('/import') ?>" class="text-gray-500 hover:text-cyan-400 transition-colors">
                Import
            </a>
            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-gray-400">Preview</span>
        </nav>

        <!-- Header -->
        <div class="mb-10">
            <h1 class="text-5xl font-bold mb-3 bg-gradient-to-r from-cyan-200 via-blue-400 to-cyan-300 bg-clip-text text-transparent tracking-tight">
                Import Preview
            </h1>
            <p class="text-gray-400 text-lg font-light tracking-wide">
                Review the data to be imported and resolve any conflicts
            </p>
        </div>

        <!-- Summary Statistics -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
            <div class="bg-slate-800/60 backdrop-blur-sm border border-slate-700/50 rounded-lg p-4">
                <div class="text-2xl font-bold text-cyan-400"><?= count($data['entities']) ?></div>
                <div class="text-xs text-gray-400 mt-1">Entities</div>
            </div>
            <div class="bg-slate-800/60 backdrop-blur-sm border border-slate-700/50 rounded-lg p-4">
                <div class="text-2xl font-bold text-blue-400"><?= count($data['relations']) ?></div>
                <div class="text-xs text-gray-400 mt-1">Relations</div>
            </div>
            <div class="bg-slate-800/60 backdrop-blur-sm border border-slate-700/50 rounded-lg p-4">
                <div class="text-2xl font-bold text-purple-400"><?= count($data['tags']) ?></div>
                <div class="text-xs text-gray-400 mt-1">Tags</div>
            </div>
            <div class="bg-slate-800/60 backdrop-blur-sm border border-slate-700/50 rounded-lg p-4">
                <div class="text-2xl font-bold text-green-400"><?= count($data['attributes']) ?></div>
                <div class="text-xs text-gray-400 mt-1">Attributes</div>
            </div>
            <div class="bg-slate-800/60 backdrop-blur-sm border border-slate-700/50 rounded-lg p-4">
                <div class="text-2xl font-bold text-yellow-400"><?= count($data['posts']) ?></div>
                <div class="text-xs text-gray-400 mt-1">Posts</div>
            </div>
            <div class="bg-slate-800/60 backdrop-blur-sm border border-slate-700/50 rounded-lg p-4">
                <div class="text-xs font-semibold text-gray-300 uppercase"><?= e($format) ?></div>
                <div class="text-xs text-gray-400 mt-1">Format</div>
            </div>
        </div>

        <!-- Conflicts Warning -->
        <?php if (!empty($conflicts['entity_names']) || !empty($conflicts['tag_names'])): ?>
        <div class="mb-8 p-6 bg-yellow-900/30 border border-yellow-700/50 rounded-lg backdrop-blur-sm">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-yellow-400 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <div class="flex-1">
                    <h3 class="text-sm font-medium text-yellow-200 mb-2">Conflicts Detected</h3>
                    <p class="text-xs text-yellow-300 mb-3">
                        Some items already exist in the target campaign. Choose how to handle conflicts below.
                    </p>
                    <?php if (!empty($conflicts['entity_names'])): ?>
                    <div class="mb-2">
                        <div class="text-xs font-medium text-yellow-200 mb-1">Entity Conflicts (<?= count($conflicts['entity_names']) ?>):</div>
                        <div class="text-xs text-yellow-300 space-y-0.5">
                            <?php foreach (array_slice($conflicts['entity_names'], 0, 5) as $conflict): ?>
                            <div>• <?= e($conflict['name']) ?> (<?= e($conflict['type']) ?>)</div>
                            <?php endforeach; ?>
                            <?php if (count($conflicts['entity_names']) > 5): ?>
                            <div class="text-yellow-400">... and <?= count($conflicts['entity_names']) - 5 ?> more</div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($conflicts['tag_names'])): ?>
                    <div>
                        <div class="text-xs font-medium text-yellow-200 mb-1">Tag Conflicts (<?= count($conflicts['tag_names']) ?>):</div>
                        <div class="text-xs text-yellow-300 space-y-0.5">
                            <?php foreach (array_slice($conflicts['tag_names'], 0, 5) as $conflict): ?>
                            <div>• <?= e($conflict['name']) ?></div>
                            <?php endforeach; ?>
                            <?php if (count($conflicts['tag_names']) > 5): ?>
                            <div class="text-yellow-400">... and <?= count($conflicts['tag_names']) - 5 ?> more</div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="mb-8 p-4 bg-green-900/30 border border-green-700/50 rounded-lg backdrop-blur-sm">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-green-400 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <p class="text-green-200 text-sm leading-relaxed">No conflicts detected. All data can be imported safely.</p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Entity Type Breakdown -->
        <div class="mb-8 bg-slate-800/80 backdrop-blur-xl border border-slate-700/50 rounded-2xl shadow-2xl p-6">
            <h2 class="text-xl font-semibold text-gray-300 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Entity Breakdown by Type
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                <?php
                $entityTypes = [];
                foreach ($data['entities'] as $entity) {
                    $type = $entity['entity_type'];
                    if (!isset($entityTypes[$type])) {
                        $entityTypes[$type] = 0;
                    }
                    $entityTypes[$type]++;
                }
                ksort($entityTypes);
                ?>
                <?php foreach ($entityTypes as $type => $count): ?>
                <div class="bg-slate-900/50 border border-slate-700/30 rounded-lg p-3">
                    <div class="text-lg font-bold text-gray-200"><?= $count ?></div>
                    <div class="text-xs text-gray-400 capitalize"><?= e($type) ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Conflict Resolution Form -->
        <div class="relative group">
            <div class="absolute -inset-0.5 bg-gradient-to-r from-cyan-600 to-blue-600 rounded-2xl opacity-0 group-hover:opacity-20 blur transition duration-500"></div>

            <div class="relative bg-slate-800/80 backdrop-blur-xl border border-slate-700/50 rounded-2xl shadow-2xl p-8">
                <form method="POST" action="<?= url('/import/process') ?>" class="space-y-6">
                    <?= csrf_field() ?>

                    <!-- Conflict Resolution Strategy -->
                    <div class="space-y-3">
                        <label class="block text-sm font-medium text-gray-300 tracking-wide">
                            Conflict Resolution Strategy
                        </label>
                        <div class="space-y-3">
                            <label class="flex items-start p-4 bg-slate-900/50 border border-slate-700/50 rounded-lg cursor-pointer hover:border-cyan-500/50 transition-colors">
                                <input
                                    type="radio"
                                    name="conflict_resolution"
                                    value="skip"
                                    checked
                                    class="mt-1 w-4 h-4 text-cyan-500 bg-slate-900 border-slate-600 focus:ring-cyan-500/50 focus:ring-2"
                                >
                                <div class="ml-3 flex-1">
                                    <span class="text-sm font-medium text-gray-300">Skip Conflicting Items</span>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Keep existing data and skip importing items that already exist
                                    </p>
                                </div>
                            </label>

                            <label class="flex items-start p-4 bg-slate-900/50 border border-slate-700/50 rounded-lg cursor-pointer hover:border-cyan-500/50 transition-colors">
                                <input
                                    type="radio"
                                    name="conflict_resolution"
                                    value="overwrite"
                                    class="mt-1 w-4 h-4 text-cyan-500 bg-slate-900 border-slate-600 focus:ring-cyan-500/50 focus:ring-2"
                                >
                                <div class="ml-3 flex-1">
                                    <span class="text-sm font-medium text-gray-300">Overwrite Existing</span>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Replace existing items with data from the import file
                                    </p>
                                </div>
                            </label>

                            <label class="flex items-start p-4 bg-slate-900/50 border border-slate-700/50 rounded-lg cursor-pointer hover:border-cyan-500/50 transition-colors">
                                <input
                                    type="radio"
                                    name="conflict_resolution"
                                    value="keep_both"
                                    class="mt-1 w-4 h-4 text-cyan-500 bg-slate-900 border-slate-600 focus:ring-cyan-500/50 focus:ring-2"
                                >
                                <div class="ml-3 flex-1">
                                    <span class="text-sm font-medium text-gray-300">Keep Both</span>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Import all items, appending "(imported)" to conflicting names
                                    </p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Import Summary -->
                    <div class="bg-slate-900/30 border border-slate-700/30 rounded-lg p-6">
                        <h3 class="text-sm font-medium text-gray-300 mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Import Summary
                        </h3>
                        <div class="text-xs text-gray-400 space-y-1">
                            <div><strong>Campaign:</strong> <?= e($data['campaign']['name']) ?></div>
                            <?php if ($createNewCampaign): ?>
                            <div><strong>Destination:</strong> New Campaign "<?= e($newCampaignName ?: $data['campaign']['name']) ?>"</div>
                            <?php else: ?>
                            <div><strong>Destination:</strong> Campaign ID <?= $targetCampaignId ?></div>
                            <?php endif; ?>
                            <div><strong>Format:</strong> <?= ucfirst(e($format)) ?></div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-between pt-6 border-t border-slate-700/50">
                        <a href="<?= url('/import') ?>"
                           class="inline-flex items-center bg-slate-800/60 backdrop-blur-sm border border-slate-700/50 text-gray-300 hover:text-gray-200 hover:border-slate-600/50 font-medium px-6 py-3 rounded-lg transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Back
                        </a>

                        <button
                            type="submit"
                            class="group relative overflow-hidden bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-400 hover:to-blue-400 text-white font-semibold px-8 py-3.5 rounded-lg shadow-lg shadow-cyan-900/30 hover:shadow-cyan-900/50 focus:outline-none focus:ring-2 focus:ring-cyan-500/50 focus:ring-offset-2 focus:ring-offset-slate-800 transition-all duration-300 transform hover:scale-105"
                        >
                            <span class="relative z-10 flex items-center tracking-wide">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                Confirm Import
                            </span>
                            <div class="absolute inset-0 bg-gradient-to-r from-blue-400 to-cyan-400 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sample Entities Preview -->
        <?php if (!empty($data['entities'])): ?>
        <div class="mt-8 bg-slate-800/40 backdrop-blur-sm border border-slate-700/30 rounded-lg p-6">
            <h3 class="text-sm font-medium text-gray-300 mb-3">Sample Entities (showing first 10)</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="text-left text-gray-400 border-b border-slate-700">
                            <th class="pb-2 pr-4">Name</th>
                            <th class="pb-2 pr-4">Type</th>
                            <th class="pb-2">Entry Preview</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-300">
                        <?php foreach (array_slice($data['entities'], 0, 10) as $entity): ?>
                        <tr class="border-b border-slate-800">
                            <td class="py-2 pr-4 font-medium"><?= e($entity['name']) ?></td>
                            <td class="py-2 pr-4 text-gray-400 capitalize"><?= e($entity['entity_type']) ?></td>
                            <td class="py-2 text-gray-500">
                                <?php
                                $entry = $entity['entry'] ?? '';
                                echo e(mb_substr(strip_tags($entry), 0, 80) . (mb_strlen($entry) > 80 ? '...' : ''));
                                ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $this->endSection() ?>
