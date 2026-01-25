<?php
/**
 * Breadcrumb Navigation Component
 *
 * Displays page hierarchy with links to parent pages.
 *
 * Expected data structure:
 * $breadcrumbs = [
 *     ['label' => 'Home', 'url' => '/'],
 *     ['label' => 'Characters', 'url' => '/entities/character'],
 *     ['label' => 'Frodo Baggins', 'url' => null] // Current page (no link)
 * ];
 */

$breadcrumbs = $breadcrumbs ?? [];

if (empty($breadcrumbs)) {
    return;
}
?>

<nav class="mb-6" aria-label="Breadcrumb">
    <ol class="flex items-center gap-2 text-sm">
        <?php foreach ($breadcrumbs as $index => $crumb): ?>
            <?php $isLast = $index === count($breadcrumbs) - 1; ?>

            <li class="flex items-center gap-2">
                <?php if (!$isLast): ?>
                    <a
                        href="<?= url($crumb['url'] ?? '/') ?>"
                        class="text-zinc-500 hover:text-emerald-400 transition-colors font-medium"
                    >
                        <?= e($crumb['label']) ?>
                    </a>
                    <svg class="w-4 h-4 text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                <?php else: ?>
                    <span class="text-zinc-300 font-semibold">
                        <?= e($crumb['label']) ?>
                    </span>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ol>
</nav>
