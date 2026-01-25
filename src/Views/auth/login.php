<?php $this->extends('layouts/base') ?>

<?php $this->section('title') ?>
Login - Worlds
<?php $this->endSection() ?>

<?php $this->section('content') ?>
<div class="min-h-screen flex items-center justify-center px-4 py-12 relative overflow-hidden">
    <!-- Atmospheric background elements -->
    <div class="absolute inset-0 bg-gradient-to-br from-gray-900 via-slate-900 to-gray-950"></div>
    <div class="absolute top-0 left-0 w-full h-full opacity-5">
        <div class="absolute top-20 left-20 w-96 h-96 bg-amber-500 rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 right-20 w-96 h-96 bg-orange-600 rounded-full blur-3xl"></div>
    </div>

    <!-- Subtle noise texture overlay -->
    <div class="absolute inset-0 opacity-20 mix-blend-soft-light" style="background-image: url('data:image/svg+xml,%3Csvg viewBox=%220 0 400 400%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noiseFilter%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.9%22 numOctaves=%223%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noiseFilter)%22/%3E%3C/svg%3E');"></div>

    <div class="relative z-10 w-full max-w-md">
        <!-- Header -->
        <div class="text-center mb-10">
            <h1 class="text-5xl font-bold mb-3 bg-gradient-to-r from-amber-200 via-amber-400 to-orange-400 bg-clip-text text-transparent tracking-tight">
                Worlds
            </h1>
            <p class="text-gray-400 text-lg font-light tracking-wide">
                Enter your realm
            </p>
        </div>

        <!-- Login Form Card -->
        <div class="relative group">
            <!-- Glow effect on hover -->
            <div class="absolute -inset-0.5 bg-gradient-to-r from-amber-600 to-orange-600 rounded-2xl opacity-0 group-hover:opacity-20 blur transition duration-500"></div>

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

                <?php if ($success = get_flash('success')): ?>
                <div class="mb-6 p-4 bg-emerald-900/30 border border-emerald-700/50 rounded-lg backdrop-blur-sm">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-emerald-400 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-emerald-200 text-sm leading-relaxed"><?= e($success) ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <form method="POST" action="<?= url('/login') ?>" class="space-y-6">
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
                                placeholder="Enter your username"
                            >
                        </div>
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
                                autocomplete="current-password"
                                class="block w-full pl-12 pr-4 py-3 bg-slate-900/50 border border-slate-600/50 rounded-lg text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500/50 transition duration-200"
                                placeholder="Enter your password"
                            >
                        </div>
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between text-sm">
                        <label class="flex items-center cursor-pointer group">
                            <input
                                type="checkbox"
                                name="remember"
                                class="w-4 h-4 rounded border-slate-600 bg-slate-900/50 text-amber-500 focus:ring-2 focus:ring-amber-500/50 focus:ring-offset-0 transition"
                            >
                            <span class="ml-2 text-gray-400 group-hover:text-gray-300 transition">Remember me</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        class="w-full relative group overflow-hidden bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-400 hover:to-orange-400 text-white font-semibold py-3.5 px-6 rounded-lg shadow-lg shadow-amber-900/30 hover:shadow-amber-900/50 focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:ring-offset-2 focus:ring-offset-slate-800 transition-all duration-300 transform hover:scale-[1.02]"
                    >
                        <span class="relative z-10 flex items-center justify-center tracking-wide">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            Sign In
                        </span>
                        <div class="absolute inset-0 bg-gradient-to-r from-orange-400 to-amber-400 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </button>
                </form>

                <!-- Divider -->
                <div class="relative my-8">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-slate-700/50"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-slate-800 text-gray-500 tracking-wide">New to Worlds?</span>
                    </div>
                </div>

                <!-- Register Link -->
                <div class="text-center">
                    <a
                        href="<?= url('/register') ?>"
                        class="inline-flex items-center text-amber-400 hover:text-amber-300 font-medium text-sm group transition"
                    >
                        Create an account
                        <svg class="w-4 h-4 ml-1.5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer Text -->
        <p class="mt-8 text-center text-gray-500 text-sm tracking-wide">
            A gateway to infinite realms
        </p>
    </div>
</div>
<?php $this->endSection() ?>
