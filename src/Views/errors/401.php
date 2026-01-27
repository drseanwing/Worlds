<?php $this->extends('layouts/base') ?>

<?php $this->section('title') ?>
401 Unauthorized - Worlds
<?php $this->endSection() ?>

<?php $this->section('sidebar', false) ?>

<?php $this->section('content') ?>
<div class="flex items-center justify-center min-h-[70vh]">
    <div class="text-center max-w-2xl mx-auto px-4">
        <!-- 401 Icon -->
        <div class="mb-8">
            <svg class="w-32 h-32 text-zinc-700 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
        </div>

        <!-- Error Code -->
        <h1 class="text-8xl font-bold text-amber-400 mb-4">401</h1>

        <!-- Error Message -->
        <h2 class="text-3xl font-bold text-zinc-100 mb-4">Authentication Required</h2>

        <p class="text-zinc-400 text-lg mb-8">
            <?php if (isset($message)): ?>
                <?= htmlspecialchars($message) ?>
            <?php else: ?>
                You need to log in to access this resource.
            <?php endif; ?>
        </p>

        <!-- Navigation Links -->
        <div class="space-y-4">
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a
                    href="/login"
                    class="inline-flex items-center justify-center px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-emerald-500"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                    </svg>
                    Log In
                </a>

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
        </div>
    </div>
</div>
<?php $this->endSection() ?>
