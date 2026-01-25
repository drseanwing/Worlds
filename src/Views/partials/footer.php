<footer class="bg-zinc-900 border-t border-zinc-800 mt-auto">
    <div class="px-6 py-6">
        <div class="flex items-center justify-between text-xs">
            <div class="flex items-center gap-4">
                <span class="text-zinc-500">
                    <span class="text-emerald-400 font-bold">WORLDS</span> v1.0.0
                </span>
                <span class="text-zinc-700">|</span>
                <span class="text-zinc-500">
                    &copy; <?= date('Y') ?> - Worldbuilding Tool
                </span>
            </div>

            <div class="flex items-center gap-3">
                <a href="<?= url('/about') ?>" class="text-zinc-500 hover:text-emerald-400 transition-colors">About</a>
                <span class="text-zinc-700">·</span>
                <a href="<?= url('/docs') ?>" class="text-zinc-500 hover:text-emerald-400 transition-colors">Documentation</a>
                <span class="text-zinc-700">·</span>
                <a href="https://github.com/worlds" target="_blank" rel="noopener" class="text-zinc-500 hover:text-emerald-400 transition-colors">
                    GitHub
                </a>
            </div>
        </div>
    </div>
</footer>
