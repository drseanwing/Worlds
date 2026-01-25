<?php
/**
 * Files List Partial
 * Display entity files with thumbnails and download links
 *
 * Props:
 * - $files: Array of file data (id, filename, url, size, type, created_at)
 * - $entityId: Current entity ID
 */

$files = $files ?? [];
?>

<div class="space-y-4">
    <!-- Header -->
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-bold text-zinc-200">Attached Files</h3>
        <span class="text-sm text-zinc-500">
            <?= count($files) ?> file<?= count($files) !== 1 ? 's' : '' ?>
        </span>
    </div>

    <?php if (empty($files)): ?>
        <!-- Empty State -->
        <div class="bg-zinc-900/50 border border-zinc-800 p-8 text-center">
            <svg class="w-12 h-12 text-zinc-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
            </svg>
            <p class="text-zinc-500 text-sm">No files attached</p>
        </div>
    <?php else: ?>
        <!-- Files Grid -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <?php foreach ($files as $file): ?>
                <div
                    class="group bg-zinc-900 border border-zinc-800 hover:border-emerald-400 transition-colors overflow-hidden"
                    x-data="{ showActions: false }"
                    @mouseenter="showActions = true"
                    @mouseleave="showActions = false"
                >
                    <!-- File Preview/Thumbnail -->
                    <div class="relative aspect-square bg-zinc-800">
                        <?php if (strpos($file['type'] ?? '', 'image/') === 0): ?>
                            <!-- Image Preview -->
                            <img
                                src="<?= e($file['url'] ?? '') ?>"
                                alt="<?= e($file['filename'] ?? '') ?>"
                                class="w-full h-full object-cover"
                            >
                        <?php else: ?>
                            <!-- File Icon -->
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-16 h-16 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        <?php endif; ?>

                        <!-- Overlay Actions -->
                        <div
                            x-show="showActions"
                            x-transition
                            class="absolute inset-0 bg-zinc-950/80 flex items-center justify-center gap-2"
                            style="display: none;"
                        >
                            <a
                                href="<?= e($file['url'] ?? '') ?>"
                                download
                                class="p-2 bg-emerald-500 hover:bg-emerald-400 text-zinc-900 transition-colors"
                                title="Download"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                            </a>
                            <a
                                href="<?= e($file['url'] ?? '') ?>"
                                target="_blank"
                                class="p-2 bg-zinc-700 hover:bg-zinc-600 text-zinc-100 transition-colors"
                                title="Open in new tab"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                            </a>
                            <button
                                @click="if(confirm('Delete this file?')) { $dispatch('delete-file', { id: <?= $file['id'] ?? 0 ?> }) }"
                                class="p-2 bg-red-500 hover:bg-red-400 text-zinc-900 transition-colors"
                                title="Delete"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- File Info -->
                    <div class="p-3 border-t border-zinc-800">
                        <p class="text-zinc-200 text-sm font-medium truncate mb-1" title="<?= e($file['filename'] ?? '') ?>">
                            <?= e($file['filename'] ?? 'Unknown') ?>
                        </p>
                        <div class="flex items-center justify-between text-xs text-zinc-600">
                            <span>
                                <?php
                                $size = $file['size'] ?? 0;
                                if ($size < 1024) {
                                    echo $size . ' B';
                                } elseif ($size < 1024 * 1024) {
                                    echo round($size / 1024, 1) . ' KB';
                                } else {
                                    echo round($size / (1024 * 1024), 1) . ' MB';
                                }
                                ?>
                            </span>
                            <span>
                                <?= date('M j', strtotime($file['created_at'] ?? 'now')) ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
