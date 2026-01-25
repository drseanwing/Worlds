<?php
/**
 * Entity Posts/Sub-entries List Partial
 * Displays timeline of posts/journal entries for entity
 *
 * Props:
 * - $posts: Array of post data (id, title, content, created_at, author)
 * - $entityId: Current entity ID
 */

$posts = $posts ?? [];
?>

<div class="space-y-4">
    <!-- Header -->
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-bold text-zinc-200">Timeline & Posts</h3>
        <button
            @click="$dispatch('open-post-form')"
            class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-500 hover:bg-emerald-400 text-zinc-900 font-semibold text-sm transition-colors"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add Post
        </button>
    </div>

    <?php if (empty($posts)): ?>
        <!-- Empty State -->
        <div class="bg-zinc-900/50 border border-zinc-800 p-8 text-center">
            <svg class="w-12 h-12 text-zinc-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p class="text-zinc-500 text-sm">No posts yet</p>
            <p class="text-zinc-600 text-xs mt-1">Create timeline entries and journal posts</p>
        </div>
    <?php else: ?>
        <!-- Posts Timeline -->
        <div class="space-y-4">
            <?php foreach ($posts as $index => $post): ?>
                <div class="relative">
                    <!-- Timeline Line (except for last item) -->
                    <?php if ($index < count($posts) - 1): ?>
                        <div class="absolute left-4 top-12 bottom-0 w-px bg-zinc-800"></div>
                    <?php endif; ?>

                    <!-- Post Card -->
                    <div class="relative bg-zinc-900 border border-zinc-800 hover:border-emerald-400 transition-colors">
                        <!-- Timeline Dot -->
                        <div class="absolute -left-1 top-6 w-8 h-8 bg-zinc-900 border-2 border-emerald-400 rounded-full flex items-center justify-center">
                            <div class="w-2 h-2 bg-emerald-400 rounded-full"></div>
                        </div>

                        <!-- Post Content -->
                        <div class="pl-12 pr-4 py-4">
                            <div class="flex items-start justify-between gap-3 mb-2">
                                <div class="flex-1">
                                    <h4 class="text-zinc-100 font-bold text-lg mb-1">
                                        <?= e($post['title'] ?? 'Untitled Post') ?>
                                    </h4>
                                    <div class="flex items-center gap-3 text-xs text-zinc-500">
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <?= date('M j, Y g:i A', strtotime($post['created_at'] ?? 'now')) ?>
                                        </span>
                                        <?php if (!empty($post['author'])): ?>
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                                <?= e($post['author']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Actions Dropdown -->
                                <div x-data="{ open: false }" class="relative">
                                    <button
                                        @click="open = !open"
                                        class="text-zinc-500 hover:text-zinc-300 p-1"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                        </svg>
                                    </button>

                                    <div
                                        x-show="open"
                                        @click.away="open = false"
                                        x-transition
                                        class="absolute right-0 top-full mt-1 w-32 bg-zinc-900 border border-zinc-700 shadow-xl z-10"
                                        style="display: none;"
                                    >
                                        <button
                                            @click="$dispatch('edit-post', { id: <?= $post['id'] ?? 0 ?> }); open = false"
                                            class="w-full px-3 py-2 text-left text-sm text-zinc-300 hover:bg-zinc-800"
                                        >
                                            Edit
                                        </button>
                                        <button
                                            @click="if(confirm('Delete this post?')) { $dispatch('delete-post', { id: <?= $post['id'] ?? 0 ?> }) }"
                                            class="w-full px-3 py-2 text-left text-sm text-red-400 hover:bg-zinc-800"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Post Body -->
                            <?php if (!empty($post['content'])): ?>
                                <div class="mt-3 text-zinc-400 text-sm leading-relaxed whitespace-pre-wrap">
                                    <?= e($post['content']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
