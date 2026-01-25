<?php
/**
 * Flash Message Component
 *
 * Displays success and error messages that auto-dismiss after a timeout.
 * Uses Alpine.js for interactivity.
 */

$success = get_flash('success');
$error = get_flash('error');
$warning = get_flash('warning');
$info = get_flash('info');
?>

<?php if ($success || $error || $warning || $info): ?>
    <div class="fixed top-20 right-6 z-50 space-y-3 pointer-events-none">
        <?php if ($success): ?>
            <div
                x-data="{ show: true }"
                x-init="setTimeout(() => show = false, 5000)"
                x-show="show"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-x-4"
                x-transition:enter-end="opacity-100 transform translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform translate-x-0"
                x-transition:leave-end="opacity-0 transform translate-x-4"
                class="pointer-events-auto w-96 bg-emerald-950 border-l-4 border-emerald-400 shadow-2xl"
            >
                <div class="flex items-start gap-3 p-4">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-emerald-400 mb-1">Success</p>
                        <p class="text-sm text-zinc-300"><?= e($success) ?></p>
                    </div>
                    <button @click="show = false" class="flex-shrink-0 text-zinc-500 hover:text-zinc-300 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div
                x-data="{ show: true }"
                x-init="setTimeout(() => show = false, 7000)"
                x-show="show"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-x-4"
                x-transition:enter-end="opacity-100 transform translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform translate-x-0"
                x-transition:leave-end="opacity-0 transform translate-x-4"
                class="pointer-events-auto w-96 bg-red-950 border-l-4 border-red-400 shadow-2xl"
            >
                <div class="flex items-start gap-3 p-4">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-red-400 mb-1">Error</p>
                        <p class="text-sm text-zinc-300"><?= e($error) ?></p>
                    </div>
                    <button @click="show = false" class="flex-shrink-0 text-zinc-500 hover:text-zinc-300 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($warning): ?>
            <div
                x-data="{ show: true }"
                x-init="setTimeout(() => show = false, 6000)"
                x-show="show"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-x-4"
                x-transition:enter-end="opacity-100 transform translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform translate-x-0"
                x-transition:leave-end="opacity-0 transform translate-x-4"
                class="pointer-events-auto w-96 bg-yellow-950 border-l-4 border-yellow-400 shadow-2xl"
            >
                <div class="flex items-start gap-3 p-4">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-yellow-400 mb-1">Warning</p>
                        <p class="text-sm text-zinc-300"><?= e($warning) ?></p>
                    </div>
                    <button @click="show = false" class="flex-shrink-0 text-zinc-500 hover:text-zinc-300 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($info): ?>
            <div
                x-data="{ show: true }"
                x-init="setTimeout(() => show = false, 5000)"
                x-show="show"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-x-4"
                x-transition:enter-end="opacity-100 transform translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform translate-x-0"
                x-transition:leave-end="opacity-0 transform translate-x-4"
                class="pointer-events-auto w-96 bg-blue-950 border-l-4 border-blue-400 shadow-2xl"
            >
                <div class="flex items-start gap-3 p-4">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-blue-400 mb-1">Info</p>
                        <p class="text-sm text-zinc-300"><?= e($info) ?></p>
                    </div>
                    <button @click="show = false" class="flex-shrink-0 text-zinc-500 hover:text-zinc-300 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>
