<?php
/**
 * Calendar Entity Type Partial
 * Displays calendar-specific fields with celestial styling
 *
 * Props:
 * - $entity: Entity data including calendar fields
 * - $calendarData: Parsed JSON calendar data
 */

// Parse calendar data if it exists
$calendarData = $entity['calendar_data'] ?? null;
if (is_string($calendarData)) {
    $calendarData = json_decode($calendarData, true);
}

// Extract calendar components
$months = $calendarData['months'] ?? [];
$weekdays = $calendarData['weekdays'] ?? [];
$currentDate = $calendarData['current_date'] ?? null;
$yearZero = $calendarData['year_zero'] ?? 0;
$suffix = $calendarData['suffix'] ?? '';
$moons = $calendarData['moons'] ?? [];

// Calculate current month details for calendar view
$currentMonth = null;
$currentMonthIndex = 0;
if ($currentDate && !empty($months)) {
    $currentMonthIndex = ($currentDate['month'] ?? 1) - 1;
    $currentMonth = $months[$currentMonthIndex] ?? null;
}
?>

<div x-data="calendarViewer()" class="space-y-6">

    <!-- Current Date Display - Hero Section -->
    <?php if ($currentDate): ?>
    <div class="relative overflow-hidden bg-gradient-to-br from-amber-950/40 via-orange-950/30 to-amber-900/20 border border-amber-900/30 p-8 backdrop-blur-sm">
        <!-- Decorative Sun Rays -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-amber-400/10 to-transparent rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-gradient-to-tr from-orange-500/10 to-transparent rounded-full blur-2xl"></div>

        <div class="relative z-10">
            <div class="flex items-center gap-3 mb-4">
                <!-- Sun Icon -->
                <svg class="w-8 h-8 text-amber-400 animate-pulse" fill="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="5"></circle>
                    <path d="M12 1v6m0 6v6m11-11h-6m-6 0H1m3.343-3.343l4.242 4.242m6.364 0l4.242-4.242M3.343 20.657l4.242-4.242m6.364 0l4.242 4.242" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                </svg>
                <h3 class="text-sm font-bold text-amber-300 uppercase tracking-widest">Current Date</h3>
            </div>

            <div class="flex items-baseline gap-4">
                <div class="text-6xl font-black text-transparent bg-gradient-to-br from-amber-200 via-amber-300 to-orange-400 bg-clip-text">
                    <?= e($currentDate['day'] ?? 1) ?>
                </div>
                <div class="flex flex-col">
                    <div class="text-2xl font-bold text-zinc-100">
                        <?= e($months[$currentMonthIndex]['name'] ?? 'Unknown Month') ?>
                    </div>
                    <div class="text-lg font-semibold text-amber-400">
                        <?= e($currentDate['year'] ?? 0) ?> <?= e($suffix) ?>
                    </div>
                </div>
            </div>

            <!-- Weekday -->
            <?php if (!empty($weekdays) && $currentMonth):
                $dayOfWeek = (($currentDate['day'] ?? 1) - 1) % count($weekdays);
                $weekdayName = $weekdays[$dayOfWeek] ?? '';
            ?>
            <div class="mt-4 inline-block px-4 py-2 bg-amber-900/30 border border-amber-700/30 text-amber-300 font-medium">
                <?= e($weekdayName) ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Calendar Grid View -->
        <?php if ($currentMonth && !empty($weekdays)): ?>
        <div class="bg-zinc-900/80 border border-zinc-800 overflow-hidden">
            <div class="bg-zinc-950 border-b border-zinc-800 px-6 py-4">
                <div class="flex items-center justify-between">
                    <button @click="prevMonth()" class="p-2 text-zinc-500 hover:text-amber-400 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>

                    <h3 class="text-xl font-bold text-zinc-100">
                        <span x-text="currentMonthName"></span>
                    </h3>

                    <button @click="nextMonth()" class="p-2 text-zinc-500 hover:text-amber-400 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="p-6">
                <!-- Weekday Headers -->
                <div class="grid grid-cols-7 gap-2 mb-4">
                    <?php foreach ($weekdays as $weekday): ?>
                    <div class="text-center text-xs font-bold text-zinc-500 uppercase tracking-wider">
                        <?= e(substr($weekday, 0, 3)) ?>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Calendar Days Grid -->
                <div class="grid grid-cols-7 gap-2">
                    <template x-for="day in calendarDays" :key="day.number || day.empty">
                        <div
                            class="aspect-square flex items-center justify-center text-sm font-semibold transition-all"
                            :class="day.empty ? '' : (day.isToday ? 'bg-gradient-to-br from-amber-500 to-orange-500 text-white shadow-lg shadow-amber-900/50' : 'bg-zinc-800/50 border border-zinc-700/50 text-zinc-300 hover:border-amber-500/50 hover:text-amber-300')"
                        >
                            <span x-text="day.number || ''"></span>
                        </div>
                    </template>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Configuration Summary -->
        <div class="space-y-6">

            <!-- Year Configuration -->
            <div class="bg-zinc-900 border border-zinc-800 p-6">
                <h3 class="text-sm font-bold text-zinc-400 uppercase tracking-wider mb-4">Year Configuration</h3>

                <div class="space-y-3">
                    <div class="flex justify-between items-center pb-3 border-b border-zinc-800">
                        <span class="text-zinc-500 text-sm">Year Zero</span>
                        <span class="text-zinc-100 font-semibold"><?= e($yearZero) ?></span>
                    </div>
                    <div class="flex justify-between items-center pb-3 border-b border-zinc-800">
                        <span class="text-zinc-500 text-sm">Era Suffix</span>
                        <span class="text-amber-400 font-bold"><?= e($suffix ?: 'None') ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-zinc-500 text-sm">Total Months</span>
                        <span class="text-zinc-100 font-semibold"><?= count($months) ?></span>
                    </div>
                </div>
            </div>

            <!-- Moons Section -->
            <?php if (!empty($moons)): ?>
            <div class="relative overflow-hidden bg-gradient-to-br from-cyan-950/30 via-blue-950/20 to-cyan-900/10 border border-cyan-900/30 p-6">
                <!-- Decorative Moon Glow -->
                <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-cyan-400/20 to-transparent rounded-full blur-2xl"></div>

                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-4">
                        <svg class="w-6 h-6 text-cyan-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"></path>
                        </svg>
                        <h3 class="text-sm font-bold text-cyan-300 uppercase tracking-widest">Celestial Bodies</h3>
                    </div>

                    <div class="space-y-3">
                        <?php foreach ($moons as $moon): ?>
                        <div class="flex items-center justify-between p-3 bg-cyan-900/20 border border-cyan-800/30 hover:border-cyan-700/50 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-cyan-400/30 to-cyan-600/30 border border-cyan-500/40 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-cyan-300" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"></path>
                                    </svg>
                                </div>
                                <span class="text-zinc-100 font-semibold"><?= e($moon['name'] ?? 'Unknown') ?></span>
                            </div>
                            <div class="text-right">
                                <div class="text-xs text-cyan-500 uppercase tracking-wide">Cycle</div>
                                <div class="text-cyan-300 font-bold"><?= e($moon['cycle'] ?? 0) ?> days</div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>

    <!-- Months Table -->
    <?php if (!empty($months)): ?>
    <div class="bg-zinc-900 border border-zinc-800 overflow-hidden">
        <div class="bg-zinc-950 border-b border-zinc-800 px-6 py-4">
            <h3 class="text-lg font-bold text-zinc-200">Months of the Year</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-zinc-950 border-b border-zinc-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-zinc-400 uppercase tracking-wider">
                            Position
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-zinc-400 uppercase tracking-wider">
                            Month Name
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-zinc-400 uppercase tracking-wider">
                            Days
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800">
                    <?php foreach ($months as $index => $month): ?>
                    <tr
                        class="hover:bg-zinc-800/50 transition-colors"
                        :class="<?= $index ?> === currentMonthIndex ? 'bg-amber-950/20 border-l-4 border-amber-500' : ''"
                    >
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center justify-center w-8 h-8 bg-zinc-800 border border-zinc-700 text-zinc-400 text-sm font-bold">
                                <?= e($month['position'] ?? $index + 1) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-zinc-100 font-semibold text-lg">
                                <?= e($month['name'] ?? 'Unnamed') ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="inline-block px-3 py-1 bg-zinc-800 border border-zinc-700 text-amber-400 font-bold">
                                <?= e($month['days'] ?? 30) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="bg-zinc-950 border-t border-zinc-800">
                    <tr>
                        <td colspan="2" class="px-6 py-3 text-right text-sm font-bold text-zinc-400 uppercase">
                            Total Days in Year
                        </td>
                        <td class="px-6 py-3 text-right">
                            <span class="inline-block px-3 py-1 bg-amber-900/30 border border-amber-700/30 text-amber-300 font-bold">
                                <?= array_sum(array_column($months, 'days')) ?>
                            </span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- Weekdays Display -->
    <?php if (!empty($weekdays)): ?>
    <div class="bg-zinc-900 border border-zinc-800 p-6">
        <h3 class="text-sm font-bold text-zinc-400 uppercase tracking-wider mb-4">Days of the Week</h3>

        <div class="flex flex-wrap gap-2">
            <?php foreach ($weekdays as $index => $weekday): ?>
            <div class="flex-1 min-w-[80px] text-center p-3 bg-zinc-800/50 border border-zinc-700 hover:border-emerald-400 transition-colors">
                <div class="text-xs text-zinc-500 mb-1"><?= $index + 1 ?></div>
                <div class="text-zinc-100 font-semibold"><?= e($weekday) ?></div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="mt-4 text-center text-sm text-zinc-500">
            <?= count($weekdays) ?> days per week
        </div>
    </div>
    <?php endif; ?>

</div>

<script>
function calendarViewer() {
    return {
        currentMonthIndex: <?= $currentMonthIndex ?>,
        currentDay: <?= $currentDate['day'] ?? 1 ?>,
        months: <?= json_encode($months) ?>,
        weekdays: <?= json_encode($weekdays) ?>,

        get currentMonthName() {
            return this.months[this.currentMonthIndex]?.name || 'Unknown';
        },

        get currentMonthDays() {
            return this.months[this.currentMonthIndex]?.days || 30;
        },

        get calendarDays() {
            const days = [];
            const totalDays = this.currentMonthDays;
            const weekdayCount = this.weekdays.length || 7;

            // Add empty cells for alignment (starting on first day of week)
            const startOffset = 0; // Could calculate based on year_zero and month position
            for (let i = 0; i < startOffset; i++) {
                days.push({ empty: true });
            }

            // Add actual days
            for (let i = 1; i <= totalDays; i++) {
                days.push({
                    number: i,
                    isToday: i === this.currentDay && this.currentMonthIndex === <?= $currentMonthIndex ?>
                });
            }

            // Fill remaining cells to complete the grid
            const totalCells = Math.ceil(days.length / weekdayCount) * weekdayCount;
            while (days.length < totalCells) {
                days.push({ empty: true });
            }

            return days;
        },

        prevMonth() {
            this.currentMonthIndex = this.currentMonthIndex > 0
                ? this.currentMonthIndex - 1
                : this.months.length - 1;
        },

        nextMonth() {
            this.currentMonthIndex = this.currentMonthIndex < this.months.length - 1
                ? this.currentMonthIndex + 1
                : 0;
        }
    };
}
</script>
