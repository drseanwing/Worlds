<?php $this->extends('layouts/base') ?>

<?php $this->section('title') ?>
419 Session Expired - Worlds
<?php $this->endSection() ?>

<?php $this->section('sidebar', false) ?>

<?php $this->section('content') ?>
<div class="flex items-center justify-center min-h-[70vh]">
    <div class="text-center max-w-2xl mx-auto px-4">
        <!-- 419 Icon -->
        <div class="mb-8">
            <svg class="w-32 h-32 text-zinc-700 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>

        <!-- Error Code -->
        <h1 class="text-8xl font-bold text-orange-400 mb-4">419</h1>

        <!-- Error Message -->
        <h2 class="text-3xl font-bold text-zinc-100 mb-4">Session Expired</h2>

        <p class="text-zinc-400 text-lg mb-8">
            <?php if (isset($message)): ?>
                <?= htmlspecialchars($message) ?>
            <?php else: ?>
                Your session has expired or the security token is invalid. Please refresh the page and try again.
            <?php endif; ?>
        </p>

        <!-- Navigation Links -->
        <div class="space-y-4">
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <button
                    onclick="window.location.reload()"
                    class="inline-flex items-center justify-center px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-emerald-500"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Refresh Page
                </button>

                <a
                    href="/"
                    class="inline-flex items-center justify-center px-6 py-3 bg-zinc-800 hover:bg-zinc-700 text-zinc-100 font-medium rounded-lg border border-zinc-700 transition-colors focus:outline-none focus:ring-2 focus:ring-zinc-500"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Go Home
                </a>
            </div>

            <!-- Additional Help -->
            <div class="mt-8 pt-8 border-t border-zinc-800">
                <p class="text-zinc-500 text-sm">
                    This can happen if you've been inactive for a while or opened multiple tabs.
                </p>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection() ?>
