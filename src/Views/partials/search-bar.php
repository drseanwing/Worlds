<?php
/**
 * Search Bar Component Partial
 * Header search input with autocomplete suggestions
 *
 * Props:
 * - $placeholder: Search placeholder text (default: "Search entities...")
 * - $value: Current search value
 * - $action: Form action URL (default: /search)
 */

$placeholder = $placeholder ?? 'Search entities...';
$value = $value ?? '';
$action = $action ?? url('/search');
?>

<div
    x-data="{
        open: false,
        query: '<?= e($value) ?>',
        results: [],
        loading: false,
        selectedIndex: -1,
        async search() {
            if (this.query.length < 2) {
                this.results = [];
                this.open = false;
                return;
            }

            this.loading = true;

            try {
                const response = await fetch('<?= url('/api/search') ?>?q=' + encodeURIComponent(this.query));
                const data = await response.json();
                this.results = data.results || [];
                this.open = this.results.length > 0;
            } catch (error) {
                console.error('Search error:', error);
                this.results = [];
            } finally {
                this.loading = false;
            }
        },
        debounce: null,
        handleInput() {
            clearTimeout(this.debounce);
            this.debounce = setTimeout(() => this.search(), 300);
        },
        selectResult(index) {
            if (this.results[index]) {
                window.location.href = this.results[index].url;
            }
        },
        handleKeydown(event) {
            if (!this.open) return;

            if (event.key === 'ArrowDown') {
                event.preventDefault();
                this.selectedIndex = Math.min(this.selectedIndex + 1, this.results.length - 1);
            } else if (event.key === 'ArrowUp') {
                event.preventDefault();
                this.selectedIndex = Math.max(this.selectedIndex - 1, -1);
            } else if (event.key === 'Enter' && this.selectedIndex >= 0) {
                event.preventDefault();
                this.selectResult(this.selectedIndex);
            } else if (event.key === 'Escape') {
                this.open = false;
                this.selectedIndex = -1;
            }
        }
    }"
    @click.away="open = false"
    class="relative"
>
    <form action="<?= e($action) ?>" method="GET" class="relative">
        <input
            type="text"
            name="q"
            x-model="query"
            @input="handleInput()"
            @focus="if(results.length > 0) open = true"
            @keydown="handleKeydown($event)"
            placeholder="<?= e($placeholder) ?>"
            autocomplete="off"
            class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-emerald-400 transition-colors pr-10"
        >

        <!-- Search Icon / Spinner -->
        <div class="absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none">
            <svg
                x-show="!loading"
                class="w-5 h-5 text-zinc-500"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <svg
                x-show="loading"
                class="w-5 h-5 text-emerald-400 animate-spin"
                fill="none"
                viewBox="0 0 24 24"
                style="display: none;"
            >
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
    </form>

    <!-- Autocomplete Dropdown -->
    <div
        x-show="open"
        x-transition
        class="absolute z-50 w-full mt-2 bg-zinc-900 border border-zinc-700 shadow-2xl max-h-96 overflow-y-auto"
        style="display: none;"
    >
        <template x-if="results.length === 0 && !loading && query.length >= 2">
            <div class="p-4 text-center text-zinc-500 text-sm">
                No results found for "<?= e($value) ?>"
            </div>
        </template>

        <template x-for="(result, index) in results" :key="result.id">
            <a
                :href="result.url"
                @mouseenter="selectedIndex = index"
                class="block hover:bg-zinc-800 transition-colors"
                :class="{ 'bg-zinc-800': selectedIndex === index }"
            >
                <div class="p-3 flex items-start gap-3">
                    <!-- Icon -->
                    <div class="flex-shrink-0 w-10 h-10 bg-zinc-800 border flex items-center justify-center"
                         :class="selectedIndex === index ? 'border-emerald-400' : 'border-zinc-700'">
                        <span class="text-zinc-500 font-bold text-xs uppercase" x-text="result.type.substring(0, 2)"></span>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <h4 class="text-zinc-100 font-semibold text-sm" x-text="result.name"></h4>
                            <span class="text-xs px-2 py-0.5 bg-zinc-800 border border-zinc-700 text-zinc-500 uppercase whitespace-nowrap"
                                  x-text="result.type"></span>
                        </div>
                        <p
                            x-show="result.excerpt"
                            class="text-zinc-500 text-xs mt-1 line-clamp-1"
                            x-text="result.excerpt"
                        ></p>
                    </div>
                </div>
            </a>
        </template>

        <!-- View All Results -->
        <template x-if="results.length > 0">
            <div class="border-t border-zinc-800 p-2">
                <button
                    type="submit"
                    class="w-full px-3 py-2 text-emerald-400 hover:bg-zinc-800 text-sm font-medium transition-colors text-left"
                >
                    View all results for "<span x-text="query"></span>" →
                </button>
            </div>
        </template>
    </div>
</div>

<script>
// Mock autocomplete API endpoint
// In production, this would be handled by a real backend endpoint
if (!window.searchApiMocked) {
    window.searchApiMocked = true;

    // Intercept fetch calls to /api/search for demo purposes
    const originalFetch = window.fetch;
    window.fetch = function(...args) {
        if (args[0] && args[0].includes('/api/search')) {
            // Return mock data for demonstration
            return Promise.resolve({
                ok: true,
                json: () => Promise.resolve({
                    results: [] // Backend would return actual search results
                })
            });
        }
        return originalFetch.apply(this, args);
    };
}
</script>
