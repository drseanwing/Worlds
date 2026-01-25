<?php $this->extends('layouts/base') ?>

<?php $this->section('title') ?>
Search Results - Worlds
<?php $this->endSection() ?>

<?php $this->section('content') ?>
<div class="space-y-6">
    <!-- Search Header -->
    <div class="bg-zinc-900 border border-zinc-800 rounded-lg p-6">
        <h1 class="text-3xl font-bold text-emerald-400 mb-4">
            <svg class="inline-block w-8 h-8 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            Search
        </h1>

        <!-- Search Form -->
        <form method="GET" action="/search" class="space-y-4">
            <div class="flex gap-4">
                <div class="flex-1">
                    <label for="search-query" class="block text-sm font-medium text-zinc-400 mb-2">
                        Search Query
                    </label>
                    <input
                        type="text"
                        id="search-query"
                        name="q"
                        value="<?= htmlspecialchars($query) ?>"
                        placeholder="Enter search terms..."
                        class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-zinc-100 placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                        autofocus
                    >
                </div>

                <div class="w-64">
                    <label for="type-filter" class="block text-sm font-medium text-zinc-400 mb-2">
                        Filter by Type
                    </label>
                    <select
                        id="type-filter"
                        name="type"
                        class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-zinc-100 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    >
                        <option value="">All Types</option>
                        <?php foreach ($availableTypes as $typeKey => $typeConfig): ?>
                            <option value="<?= htmlspecialchars($typeKey) ?>" <?= $typeFilter === $typeKey ? 'selected' : '' ?>>
                                <?= htmlspecialchars($typeConfig['plural_label'] ?? ucfirst($typeKey)) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="flex items-end">
                    <button
                        type="submit"
                        class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    >
                        Search
                    </button>
                </div>
            </div>
        </form>
    </div>

    <?php if (!empty($query)): ?>
        <!-- Results Summary -->
        <div class="flex justify-between items-center">
            <div class="text-zinc-400">
                <?php if ($totalResults > 0): ?>
                    Found <span class="text-emerald-400 font-semibold"><?= $totalResults ?></span>
                    result<?= $totalResults !== 1 ? 's' : '' ?> for
                    "<span class="text-zinc-100"><?= $highlightedQuery ?></span>"
                    <?php if (!empty($typeFilter)): ?>
                        in <span class="text-emerald-400"><?= htmlspecialchars($availableTypes[$typeFilter]['plural_label'] ?? $typeFilter) ?></span>
                    <?php endif; ?>
                <?php else: ?>
                    No results found for "<span class="text-zinc-100"><?= $highlightedQuery ?></span>"
                <?php endif; ?>
            </div>
        </div>

        <?php if ($totalResults > 0): ?>
            <!-- Search Results -->
            <div class="space-y-4">
                <?php foreach ($results as $entity): ?>
                    <div class="bg-zinc-900 border border-zinc-800 rounded-lg p-6 hover:border-emerald-600 transition-colors">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <!-- Entity Type Badge -->
                                <div class="mb-2">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-emerald-900/50 text-emerald-400 border border-emerald-700">
                                        <?php
                                        $typeLabel = $availableTypes[$entity['entity_type']]['label'] ?? ucfirst($entity['entity_type']);
                                        echo htmlspecialchars($typeLabel);
                                        ?>
                                    </span>

                                    <?php if ($entity['is_private']): ?>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-amber-900/50 text-amber-400 border border-amber-700 ml-2">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            Private
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <!-- Entity Name -->
                                <h3 class="text-xl font-bold text-zinc-100 mb-2">
                                    <a href="/entities/<?= htmlspecialchars($entity['entity_type']) ?>/<?= $entity['id'] ?>"
                                       class="hover:text-emerald-400 transition-colors">
                                        <?= htmlspecialchars($entity['name']) ?>
                                    </a>
                                </h3>

                                <!-- Entity Type (if present) -->
                                <?php if (!empty($entity['type'])): ?>
                                    <p class="text-sm text-zinc-500 mb-2">
                                        <?= htmlspecialchars($entity['type']) ?>
                                    </p>
                                <?php endif; ?>

                                <!-- Entity Entry Preview -->
                                <?php if (!empty($entity['entry'])): ?>
                                    <div class="text-zinc-400 text-sm line-clamp-3">
                                        <?php
                                        // Strip markdown and HTML, truncate to 200 chars
                                        $preview = strip_tags($entity['entry']);
                                        $preview = mb_substr($preview, 0, 200);
                                        if (mb_strlen($entity['entry']) > 200) {
                                            $preview .= '...';
                                        }
                                        echo htmlspecialchars($preview);
                                        ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Relevance Score (if available) -->
                                <?php if (isset($entity['rank'])): ?>
                                    <div class="mt-2 text-xs text-zinc-600">
                                        Relevance: <?= number_format($entity['rank'], 2) ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Entity Image -->
                            <?php if (!empty($entity['image_path'])): ?>
                                <div class="ml-4">
                                    <img
                                        src="<?= htmlspecialchars($entity['image_path']) ?>"
                                        alt="<?= htmlspecialchars($entity['name']) ?>"
                                        class="w-24 h-24 object-cover rounded-lg border-2 border-zinc-700"
                                    >
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="flex justify-center items-center gap-2 mt-8">
                    <?php if ($currentPage > 1): ?>
                        <a
                            href="/search?q=<?= urlencode($query) ?>&type=<?= urlencode($typeFilter) ?>&page=<?= $currentPage - 1 ?>"
                            class="px-4 py-2 bg-zinc-800 hover:bg-zinc-700 text-zinc-100 rounded-lg transition-colors"
                        >
                            Previous
                        </a>
                    <?php endif; ?>

                    <span class="px-4 py-2 text-zinc-400">
                        Page <?= $currentPage ?> of <?= $totalPages ?>
                    </span>

                    <?php if ($currentPage < $totalPages): ?>
                        <a
                            href="/search?q=<?= urlencode($query) ?>&type=<?= urlencode($typeFilter) ?>&page=<?= $currentPage + 1 ?>"
                            class="px-4 py-2 bg-zinc-800 hover:bg-zinc-700 text-zinc-100 rounded-lg transition-colors"
                        >
                            Next
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <!-- No Results State -->
            <div class="bg-zinc-900 border border-zinc-800 rounded-lg p-12 text-center">
                <svg class="w-16 h-16 text-zinc-700 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="text-xl font-semibold text-zinc-400 mb-2">No results found</h3>
                <p class="text-zinc-500 mb-6">
                    Try different search terms or check your spelling.
                </p>
                <a
                    href="/search"
                    class="inline-block px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition-colors"
                >
                    Clear Search
                </a>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <!-- Empty State - No Query -->
        <div class="bg-zinc-900 border border-zinc-800 rounded-lg p-12 text-center">
            <svg class="w-16 h-16 text-zinc-700 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <h3 class="text-xl font-semibold text-zinc-400 mb-2">Enter a search query</h3>
            <p class="text-zinc-500">
                Search across all entities in your campaign by name and description.
            </p>
        </div>
    <?php endif; ?>
</div>
<?php $this->endSection() ?>
