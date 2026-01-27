<?php $this->extends('layouts/base') ?>

<?php $this->section('title') ?>
503 Service Unavailable - Worlds
<?php $this->endSection() ?>

<?php $this->section('sidebar', false) ?>

<?php $this->section('content') ?>
<div class="flex items-center justify-center min-h-[70vh]">
    <div class="text-center max-w-2xl mx-auto px-4">
        <!-- 503 Icon -->
        <div class="mb-8">
            <svg class="w-32 h-32 text-zinc-700 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
        </div>

        <!-- Error Code -->
        <h1 class="text-8xl font-bold text-purple-400 mb-4">503</h1>

        <!-- Error Message -->
        <h2 class="text-3xl font-bold text-zinc-100 mb-4">Service Unavailable</h2>

        <p class="text-zinc-400 text-lg mb-8">
            <?php if (isset($message)): ?>
                <?= htmlspecialchars($message) ?>
            <?php else: ?>
                We're currently performing maintenance or experiencing high traffic. Please try again shortly.
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
                    Try Again
                </button>
            </div>

            <!-- Status Information -->
            <div class="mt-8 pt-8 border-t border-zinc-800">
                <p class="text-zinc-500 text-sm mb-4">
                    The service should be back online shortly. We apologize for the inconvenience.
                </p>
                <p class="text-zinc-600 text-xs">
                    If this persists, please check back in a few minutes.
                </p>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection() ?>
