<?php
/**
 * Entity Card Partial
 * Grid view card for entity listing
 *
 * Props:
 * - $entity: Entity data (id, name, entity_type, entry, updated_at, image_url)
 */
?>

<a
    href="<?= url('/entities/' . ($entity['entity_type'] ?? 'entity') . '/' . ($entity['id'] ?? '')) ?>"
    class="group bg-zinc-900 border border-zinc-800 hover:border-emerald-400 transition-all duration-200 overflow-hidden flex flex-col"
>
    <!-- Image/Icon -->
    <div class="relative h-40 bg-zinc-800 overflow-hidden">
        <?php if (!empty($entity['image_url'])): ?>
            <img
                src="<?= e($entity['image_url']) ?>"
                alt="<?= e($entity['name'] ?? 'Entity') ?>"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
            >
        <?php else: ?>
            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-zinc-800 to-zinc-900">
                <svg class="w-16 h-16 text-zinc-700 group-hover:text-emerald-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                </svg>
            </div>
        <?php endif; ?>

        <!-- Type Badge -->
        <div class="absolute top-2 right-2">
            <span class="inline-block px-2 py-1 bg-zinc-900/90 backdrop-blur-sm border border-zinc-700 text-zinc-400 text-xs font-bold uppercase tracking-wide">
                <?= e($entity['entity_type'] ?? 'entity') ?>
            </span>
        </div>
    </div>

    <!-- Content -->
    <div class="p-4 flex-1 flex flex-col">
        <h3 class="text-zinc-100 font-bold text-lg mb-2 group-hover:text-emerald-400 transition-colors line-clamp-1">
            <?= e($entity['name'] ?? 'Unnamed Entity') ?>
        </h3>

        <?php if (!empty($entity['entry'])): ?>
            <p class="text-zinc-500 text-sm line-clamp-2 mb-3 flex-1">
                <?= e($entity['entry']) ?>
            </p>
        <?php else: ?>
            <p class="text-zinc-600 text-sm italic mb-3 flex-1">No description</p>
        <?php endif; ?>

        <!-- Footer -->
        <div class="flex items-center justify-between pt-3 border-t border-zinc-800">
            <span class="text-zinc-600 text-xs">
                Updated <?= date('M j, Y', strtotime($entity['updated_at'] ?? 'now')) ?>
            </span>

            <svg class="w-4 h-4 text-zinc-600 group-hover:text-emerald-400 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </div>
    </div>
</a>
