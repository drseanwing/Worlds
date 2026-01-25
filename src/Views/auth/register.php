<?php $this->extends('layouts/base') ?>

<?php $this->section('title') ?>
Register - Worlds
<?php $this->endSection() ?>

<?php $this->section('content') ?>
<div class="min-h-screen flex items-center justify-center px-4 py-12 relative overflow-hidden">
    <!-- Atmospheric background elements -->
    <div class="absolute inset-0 bg-gradient-to-br from-slate-950 via-gray-900 to-slate-900"></div>
    <div class="absolute top-0 left-0 w-full h-full opacity-5">
        <div class="absolute top-40 right-20 w-96 h-96 bg-orange-500 rounded-full blur-3xl"></div>
        <div class="absolute bottom-40 left-20 w-96 h-96 bg-amber-600 rounded-full blur-3xl"></div>
    </div>

    <!-- Subtle noise texture overlay -->
    <div class="absolute inset-0 opacity-20 mix-blend-soft-light" style="background-image: url('data:image/svg+xml,%3Csvg viewBox=%220 0 400 400%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noiseFilter%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.9%22 numOctaves=%223%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noiseFilter)%22/%3E%3C/svg%3E');"></div>

    <div class="relative z-10 w-full max-w-md">
        <!-- Header -->
        <div class="text-center mb-10">
            <h1 class="text-5xl font-bold mb-3 bg-gradient-to-r from-orange-200 via-amber-400 to-amber-200 bg-clip-text text-transparent tracking-tight">
                Worlds
            </h1>
            <p class="text-gray-400 text-lg font-light tracking-wide">
                Forge your legend
            </p>
        </div>

        <!-- Registration Form Card -->
        <div class="relative group">
            <!-- Glow effect on hover -->
            <div class="absolute -inset-0.5 bg-gradient-to-r from-orange-600 to-amber-600 rounded-2xl opacity-0 group-hover:opacity-20 blur transition duration-500"></div>

            <div class="relative bg-slate-800/80 backdrop-blur-xl border border-slate-700/50 rounded-2xl shadow-2xl p-8">
                <!-- Flash Messages -->
                <?php if ($error = get_flash('error')): ?>
                <div class="mb-6 p-4 bg-red-900/30 border border-red-700/50 rounded-lg backdrop-blur-sm">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-red-400 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-red-200 text-sm leading-relaxed"><?= e($error) ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($errors = get_flash('errors')): ?>
                <div class="mb-6 p-4 bg-red-900/30 border border-red-700/50 rounded-lg backdrop-blur-sm">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-red-400 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <div class="flex-1">
                            <?php if (is_array($errors)): ?>
                                <ul class="text-red-200 text-sm space-y-1">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?= e($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p class="text-red-200 text-sm"><?= e($errors) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <form method="POST" action="<?= url('/register') ?>" class="space-y-6">
                    <?= csrf_field() ?>

                    <!-- Username Field -->
                    <div class="space-y-2">
                        <label for="username" class="block text-sm font-medium text-gray-300 tracking-wide">
                            Username
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <input
                                type="text"
                                name="username"
                                id="username"
                                value="<?= e(old('username')) ?>"
                                required
                                autocomplete="username"
                                class="block w-full pl-12 pr-4 py-3 bg-slate-900/50 border border-slate-600/50 rounded-lg text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500/50 transition duration-200"
                                placeholder="Choose a unique username"
                            >
                        </div>
                        <p class="text-xs text-gray-500 mt-1.5 pl-1">
                            This will be your identity across all worlds
                        </p>
                    </div>

                    <!-- Password Field -->
                    <div class="space-y-2">
                        <label for="password" class="block text-sm font-medium text-gray-300 tracking-wide">
                            Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <input
                                type="password"
                                name="password"
                                id="password"
                                required
                                autocomplete="new-password"
                                class="block w-full pl-12 pr-4 py-3 bg-slate-900/50 border border-slate-600/50 rounded-lg text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500/50 transition duration-200"
                                placeholder="Create a strong password"
                            >
                        </div>
                        <p class="text-xs text-gray-500 mt-1.5 pl-1">
                            Minimum 8 characters recommended
                        </p>
                    </div>

                    <!-- Password Confirmation Field -->
                    <div class="space-y-2">
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-300 tracking-wide">
                            Confirm Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            <input
                                type="password"
                                name="password_confirmation"
                                id="password_confirmation"
                                required
                                autocomplete="new-password"
                                class="block w-full pl-12 pr-4 py-3 bg-slate-900/50 border border-slate-600/50 rounded-lg text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500/50 transition duration-200"
                                placeholder="Confirm your password"
                            >
                        </div>
                    </div>

                    <!-- Terms Agreement -->
                    <div class="flex items-start pt-2">
                        <div class="flex items-center h-5">
                            <input
                                type="checkbox"
                                name="terms"
                                id="terms"
                                required
                                class="w-4 h-4 rounded border-slate-600 bg-slate-900/50 text-amber-500 focus:ring-2 focus:ring-amber-500/50 focus:ring-offset-0 transition"
                            >
                        </div>
                        <label for="terms" class="ml-3 text-sm text-gray-400 leading-relaxed">
                            I agree to embark on this journey and respect the realms I create
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        class="w-full relative group overflow-hidden bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-400 hover:to-amber-400 text-white font-semibold py-3.5 px-6 rounded-lg shadow-lg shadow-orange-900/30 hover:shadow-orange-900/50 focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:ring-offset-2 focus:ring-offset-slate-800 transition-all duration-300 transform hover:scale-[1.02]"
                    >
                        <span class="relative z-10 flex items-center justify-center tracking-wide">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Create Account
                        </span>
                        <div class="absolute inset-0 bg-gradient-to-r from-amber-400 to-orange-400 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </button>
                </form>

                <!-- Divider -->
                <div class="relative my-8">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-slate-700/50"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-slate-800 text-gray-500 tracking-wide">Already have an account?</span>
                    </div>
                </div>

                <!-- Login Link -->
                <div class="text-center">
                    <a
                        href="<?= url('/login') ?>"
                        class="inline-flex items-center text-amber-400 hover:text-amber-300 font-medium text-sm group transition"
                    >
                        Sign in instead
                        <svg class="w-4 h-4 ml-1.5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer Text -->
        <p class="mt-8 text-center text-gray-500 text-sm tracking-wide">
            Your saga begins here
        </p>
    </div>
</div>
<?php $this->endSection() ?>
