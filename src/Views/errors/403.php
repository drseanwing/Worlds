<?php $this->extends('layouts/base') ?>

<?php $this->section('title') ?>
403 Forbidden - Worlds
<?php $this->endSection() ?>

<?php $this->section('sidebar', false) ?>

<?php $this->section('content') ?>
<div class="flex items-center justify-center min-h-[70vh]">
    <div class="text-center max-w-2xl mx-auto px-4">
        <!-- 403 Icon -->
        <div class="mb-8">
            <svg class="w-32 h-32 text-zinc-700 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
            </svg>
        </div>

        <!-- Error Code -->
        <h1 class="text-8xl font-bold text-red-400 mb-4">403</h1>

        <!-- Error Message -->
        <h2 class="text-3xl font-bold text-zinc-100 mb-4">Access Forbidden</h2>

        <p class="text-zinc-400 text-lg mb-8">
            <?php if (isset($message)): ?>
                <?= htmlspecialchars($message) ?>
            <?php else: ?>
                You don't have permission to access this resource.
            <?php endif; ?>
        </p>

        <!-- Navigation Links -->
        <div class="space-y-4">
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a
                    href="/"
                    class="inline-flex items-center justify-center px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-emerald-500"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Go Home
                </a>

                <?php if (\Worlds\Config\Auth::check()): ?>
                    <a
                        href="/dashboard"
                        class="inline-flex items-center justify-center px-6 py-3 bg-zinc-800 hover:bg-zinc-700 text-zinc-100 font-medium rounded-lg border border-zinc-700 transition-colors focus:outline-none focus:ring-2 focus:ring-zinc-500"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Dashboard
                    </a>
                <?php endif; ?>
            </div>

            <!-- Additional Help -->
            <div class="mt-8 pt-8 border-t border-zinc-800">
                <p class="text-zinc-500 text-sm">
                    If you believe this is an error, please contact an administrator.
                </p>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection() ?>
