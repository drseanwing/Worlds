<?php
/**
 * Entity Mention Tooltip Partial
 * Shows entity preview on hover
 *
 * Props:
 * - $entity: Entity data (name, entity_type, entry)
 */
?>

<div
    x-data="{ show: false }"
    @mouseenter="show = true"
    @mouseleave="show = false"
    class="relative inline-block"
>
    <span class="text-emerald-400 hover:text-emerald-300 cursor-pointer border-b border-dotted border-emerald-400/50">
        <?= e($entity['name'] ?? 'Unknown Entity') ?>
    </span>

    <div
        x-show="show"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform scale-95"
        x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        class="absolute z-50 bottom-full left-1/2 -translate-x-1/2 mb-2 w-80 pointer-events-none"
        style="display: none;"
    >
        <div class="bg-zinc-900 border border-zinc-700 shadow-2xl">
            <div class="p-4">
                <div class="flex items-start gap-3 mb-3">
                    <div class="flex-shrink-0 w-10 h-10 bg-zinc-800 border border-zinc-700 flex items-center justify-center">
                        <span class="text-zinc-500 font-bold text-xs uppercase">
                            <?= strtoupper(substr($entity['entity_type'] ?? 'EN', 0, 2)) ?>
                        </span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-zinc-100 font-bold mb-1"><?= e($entity['name'] ?? 'Unknown') ?></h4>
                        <p class="text-zinc-500 text-xs capitalize"><?= e($entity['entity_type'] ?? 'entity') ?></p>
                    </div>
                </div>

                <?php if (!empty($entity['entry'])): ?>
                    <p class="text-zinc-400 text-sm line-clamp-3">
                        <?= e(substr($entity['entry'], 0, 150)) ?><?= strlen($entity['entry']) > 150 ? '...' : '' ?>
                    </p>
                <?php endif; ?>
            </div>

            <div class="border-t border-zinc-800 px-4 py-2 bg-zinc-950/50">
                <p class="text-zinc-600 text-xs">Click to view details</p>
            </div>
        </div>

        <!-- Arrow -->
        <div class="absolute top-full left-1/2 -translate-x-1/2 -mt-px">
            <div class="border-4 border-transparent border-t-zinc-700"></div>
        </div>
    </div>
</div>
