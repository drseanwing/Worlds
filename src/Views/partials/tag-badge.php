<?php
/**
 * Tag Badge Partial
 * Single tag badge with color
 *
 * Props:
 * - $tag: Tag data (name, color)
 * - $size: Badge size (sm, md, lg) default: md
 * - $removable: Show remove button (default: false)
 * - $onRemove: JavaScript function to call on remove
 */

$size = $size ?? 'md';
$removable = $removable ?? false;

$sizeClasses = [
    'sm' => 'px-2 py-0.5 text-xs',
    'md' => 'px-2 py-1 text-xs',
    'lg' => 'px-3 py-1.5 text-sm',
];

$paddingClass = $sizeClasses[$size] ?? $sizeClasses['md'];
?>

<span
    class="inline-flex items-center gap-1 <?= $paddingClass ?> font-medium border"
    style="
        background-color: <?= e($tag['color'] ?? '#6b7280') ?>33;
        border-color: <?= e($tag['color'] ?? '#6b7280') ?>;
        color: <?= e($tag['color'] ?? '#6b7280') ?>;
    "
>
    <span><?= e($tag['name'] ?? 'Tag') ?></span>

    <?php if ($removable): ?>
        <button
            type="button"
            onclick="<?= $onRemove ?? '' ?>"
            class="hover:opacity-75 transition-opacity"
            aria-label="Remove tag"
        >
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    <?php endif; ?>
</span>
