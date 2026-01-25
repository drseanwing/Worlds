<?php
/**
 * Post Form Modal Partial
 * Modal form to add/edit entity posts
 *
 * Props:
 * - $entityId: Current entity ID
 */
?>

<div
    x-data="{
        open: false,
        postId: null,
        title: '',
        content: '',
        init() {
            this.$watch('open', value => {
                if (value) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = '';
                    this.reset();
                }
            });
        },
        reset() {
            this.postId = null;
            this.title = '';
            this.content = '';
        }
    }"
    @open-post-form.window="open = true"
    @edit-post.window="open = true; postId = $event.detail.id"
    @keydown.escape.window="open = false"
    class="relative z-50"
>
    <!-- Backdrop -->
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="open = false"
        class="fixed inset-0 bg-zinc-950/80 backdrop-blur-sm"
        style="display: none;"
    ></div>

    <!-- Modal -->
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform scale-95"
        x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        class="fixed inset-0 flex items-center justify-center p-4 pointer-events-none"
        style="display: none;"
    >
        <div class="bg-zinc-900 border border-zinc-800 shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden pointer-events-auto flex flex-col">
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-zinc-800 flex-shrink-0">
                <h3 class="text-xl font-bold text-zinc-100" x-text="postId ? 'Edit Post' : 'Add Post'"></h3>
                <button
                    @click="open = false"
                    class="text-zinc-500 hover:text-zinc-300 transition-colors"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Form -->
            <form
                action="<?= url('/entities/' . ($entityId ?? '') . '/posts') ?>"
                method="POST"
                class="flex-1 overflow-y-auto"
            >
                <?= csrf_field() ?>

                <div class="p-6 space-y-4">
                    <!-- Title -->
                    <div>
                        <label class="block text-sm font-semibold text-zinc-300 mb-2">
                            Post Title
                        </label>
                        <input
                            type="text"
                            name="title"
                            x-model="title"
                            required
                            placeholder="e.g., The Battle of Winterfell, Discovery of the Artifact"
                            class="w-full px-3 py-2 bg-zinc-800 border border-zinc-700 text-zinc-100 placeholder-zinc-600 focus:outline-none focus:border-emerald-400 transition-colors"
                        >
                    </div>

                    <!-- Content -->
                    <div>
                        <label class="block text-sm font-semibold text-zinc-300 mb-2">
                            Content
                        </label>
                        <textarea
                            name="content"
                            x-model="content"
                            rows="12"
                            placeholder="Describe what happened, story developments, notes..."
                            class="w-full px-3 py-2 bg-zinc-800 border border-zinc-700 text-zinc-100 placeholder-zinc-600 focus:outline-none focus:border-emerald-400 transition-colors resize-none"
                        ></textarea>
                        <p class="mt-2 text-xs text-zinc-600">
                            <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Posts appear in chronological order on the entity timeline
                        </p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 p-6 border-t border-zinc-800 bg-zinc-950/50 flex-shrink-0">
                    <button
                        type="button"
                        @click="open = false"
                        class="px-4 py-2 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 font-medium transition-colors"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="px-4 py-2 bg-emerald-500 hover:bg-emerald-400 text-zinc-900 font-bold transition-colors"
                    >
                        <span x-text="postId ? 'Update Post' : 'Add Post'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
