<header class="sticky top-0 z-50 bg-zinc-900 border-b border-zinc-800 backdrop-blur-sm bg-opacity-95">
    <div class="px-6 py-4">
        <div class="flex items-center justify-between gap-4">
            <!-- Logo/Brand -->
            <div class="flex items-center gap-6">
                <a href="<?= url('/') ?>" class="text-2xl font-bold tracking-tighter text-zinc-100 hover:text-emerald-400 transition-colors">
                    <span class="inline-block border-2 border-emerald-400 px-3 py-1">WORLDS</span>
                </a>

                <!-- Campaign Switcher -->
                <?php if (isset($currentCampaign)): ?>
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center gap-2 px-3 py-1 bg-zinc-800 border border-zinc-700 hover:border-emerald-400 transition-colors text-sm">
                            <span class="text-zinc-400 uppercase tracking-wide">Campaign:</span>
                            <span class="text-zinc-100 font-semibold"><?= e($currentCampaign['name'] ?? 'None') ?></span>
                            <svg class="w-4 h-4 text-zinc-400 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="open" @click.away="open = false" x-transition class="absolute top-full mt-2 left-0 w-64 bg-zinc-900 border border-zinc-700 shadow-xl">
                            <?php if (isset($campaigns) && count($campaigns) > 0): ?>
                                <div class="py-2">
                                    <?php foreach ($campaigns as $campaign): ?>
                                        <a href="<?= url('/campaigns/switch/' . $campaign['id']) ?>"
                                           class="block px-4 py-2 hover:bg-zinc-800 transition-colors <?= ($currentCampaign['id'] ?? null) === $campaign['id'] ? 'text-emerald-400' : 'text-zinc-300' ?>">
                                            <?= e($campaign['name']) ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                                <div class="border-t border-zinc-700"></div>
                            <?php endif; ?>
                            <a href="<?= url('/campaigns/create') ?>" class="block px-4 py-2 text-emerald-400 hover:bg-zinc-800 transition-colors">
                                + New Campaign
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Search Bar -->
            <div class="flex-1 max-w-2xl">
                <form action="<?= url('/search') ?>" method="GET" class="relative">
                    <input
                        type="text"
                        name="q"
                        placeholder="Search entities..."
                        class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-emerald-400 transition-colors"
                        value="<?= e($_GET['q'] ?? '') ?>"
                    >
                    <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 text-zinc-500 hover:text-emerald-400 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </form>
            </div>

            <!-- User Menu -->
            <div x-data="{ open: false }" class="relative">
                <?php if (session('user_id')): ?>
                    <button @click="open = !open" class="flex items-center gap-2 px-3 py-2 border border-zinc-700 hover:border-emerald-400 transition-colors">
                        <div class="w-6 h-6 bg-gradient-to-br from-emerald-400 to-cyan-500 flex items-center justify-center text-zinc-900 font-bold text-xs">
                            <?= strtoupper(substr(session('username') ?? 'U', 0, 1)) ?>
                        </div>
                        <span class="text-zinc-100 text-sm"><?= e(session('username') ?? 'User') ?></span>
                        <svg class="w-4 h-4 text-zinc-400" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div x-show="open" @click.away="open = false" x-transition class="absolute top-full mt-2 right-0 w-48 bg-zinc-900 border border-zinc-700 shadow-xl">
                        <div class="py-2">
                            <a href="<?= url('/profile') ?>" class="block px-4 py-2 text-zinc-300 hover:bg-zinc-800 transition-colors">Profile</a>
                            <a href="<?= url('/settings') ?>" class="block px-4 py-2 text-zinc-300 hover:bg-zinc-800 transition-colors">Settings</a>
                            <a href="<?= url('/settings/api-tokens') ?>" class="block px-4 py-2 text-zinc-300 hover:bg-zinc-800 transition-colors">API Tokens</a>
                            <div class="border-t border-zinc-700 my-2"></div>
                            <form action="<?= url('/auth/logout') ?>" method="POST">
                                <?= csrf_field() ?>
                                <button type="submit" class="w-full text-left px-4 py-2 text-red-400 hover:bg-zinc-800 transition-colors">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="flex items-center gap-2">
                        <a href="<?= url('/auth/login') ?>" class="px-4 py-2 text-zinc-300 hover:text-emerald-400 transition-colors">Login</a>
                        <a href="<?= url('/auth/register') ?>" class="px-4 py-2 bg-emerald-500 text-zinc-900 font-semibold hover:bg-emerald-400 transition-colors">
                            Register
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>
