<?php $this->extends('layouts/base') ?>

<?php $this->section('title') ?>
500 Server Error - Worlds
<?php $this->endSection() ?>

<?php $this->section('sidebar', false) ?>

<?php $this->section('content') ?>
<div class="flex items-center justify-center min-h-[70vh]">
    <div class="text-center max-w-3xl mx-auto px-4">
        <!-- 500 Icon -->
        <div class="mb-8">
            <svg class="w-32 h-32 text-red-500/80 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
        </div>

        <!-- Error Code -->
        <h1 class="text-8xl font-bold text-red-500 mb-4">500</h1>

        <!-- Error Message -->
        <h2 class="text-3xl font-bold text-zinc-100 mb-4">Internal Server Error</h2>

        <p class="text-zinc-400 text-lg mb-8">
            Something went wrong on our end. We've been notified and will look into it.
        </p>

        <?php if (isset($debugMode) && $debugMode === true): ?>
            <!-- Debug Information (only shown in debug mode) -->
            <div class="bg-zinc-900 border-2 border-red-900/50 rounded-lg p-6 mb-8 text-left">
                <div class="flex items-center mb-4">
                    <svg class="w-5 h-5 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="text-xl font-bold text-red-400">Debug Information</h3>
                </div>

                <?php if (isset($exception)): ?>
                    <!-- Exception Details -->
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs font-semibold text-zinc-500 uppercase mb-1">Exception</p>
                            <p class="text-sm text-zinc-300 font-mono"><?= htmlspecialchars(get_class($exception)) ?></p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold text-zinc-500 uppercase mb-1">Message</p>
                            <p class="text-sm text-red-300 font-mono"><?= htmlspecialchars($exception->getMessage()) ?></p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold text-zinc-500 uppercase mb-1">Location</p>
                            <p class="text-sm text-zinc-300 font-mono">
                                <?= htmlspecialchars($exception->getFile()) ?>
                                <span class="text-red-400">line <?= $exception->getLine() ?></span>
                            </p>
                        </div>

                        <!-- Stack Trace -->
                        <div>
                            <p class="text-xs font-semibold text-zinc-500 uppercase mb-2">Stack Trace</p>
                            <div class="bg-zinc-950 border border-zinc-800 rounded p-4 overflow-x-auto">
                                <pre class="text-xs text-zinc-400 font-mono whitespace-pre-wrap"><?= htmlspecialchars($exception->getTraceAsString()) ?></pre>
                            </div>
                        </div>
                    </div>
                <?php elseif (isset($message)): ?>
                    <!-- Simple Error Message -->
                    <div>
                        <p class="text-xs font-semibold text-zinc-500 uppercase mb-1">Error</p>
                        <p class="text-sm text-red-300 font-mono"><?= htmlspecialchars($message) ?></p>
                    </div>

                    <?php if (isset($file) && isset($line)): ?>
                        <div class="mt-4">
                            <p class="text-xs font-semibold text-zinc-500 uppercase mb-1">Location</p>
                            <p class="text-sm text-zinc-300 font-mono">
                                <?= htmlspecialchars($file) ?>
                                <span class="text-red-400">line <?= $line ?></span>
                            </p>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($trace)): ?>
                        <div class="mt-4">
                            <p class="text-xs font-semibold text-zinc-500 uppercase mb-2">Stack Trace</p>
                            <div class="bg-zinc-950 border border-zinc-800 rounded p-4 overflow-x-auto">
                                <pre class="text-xs text-zinc-400 font-mono whitespace-pre-wrap"><?= htmlspecialchars($trace) ?></pre>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <!-- Debug Mode Notice -->
            <div class="bg-amber-900/20 border border-amber-700/50 rounded-lg p-4 mb-8">
                <div class="flex items-center text-amber-400">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <p class="text-sm">
                        <strong>Debug mode is enabled.</strong> Disable it in production to hide error details.
                    </p>
                </div>
            </div>
        <?php else: ?>
            <!-- Production Error Message (no technical details) -->
            <div class="bg-zinc-900 border border-zinc-800 rounded-lg p-6 mb-8">
                <p class="text-zinc-400">
                    Our team has been automatically notified of this issue.
                    Please try again later or contact support if the problem persists.
                </p>
            </div>
        <?php endif; ?>

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

                <button
                    onclick="window.location.reload()"
                    class="inline-flex items-center justify-center px-6 py-3 bg-zinc-800 hover:bg-zinc-700 text-zinc-100 font-medium rounded-lg border border-zinc-700 transition-colors focus:outline-none focus:ring-2 focus:ring-zinc-500"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Try Again
                </button>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection() ?>
