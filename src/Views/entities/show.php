<?php $this->extends('layouts/base') ?>

<?php $this->section('title') ?>
<?= htmlspecialchars($entity['name'] ?? 'Entity') ?> - Worlds
<?php $this->endSection() ?>

<?php $this->section('content') ?>
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                <?= htmlspecialchars($entity['name'] ?? 'Unnamed Entity') ?>
            </h1>
            <span class="inline-block bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                <?= htmlspecialchars($entity['entity_type'] ?? 'Unknown') ?>
            </span>
        </div>

        <?php if (!empty($entity['entry'])): ?>
        <div class="prose max-w-none mb-6">
            <?= nl2br(htmlspecialchars($entity['entry'])) ?>
        </div>
        <?php endif; ?>

        <div class="border-t pt-6 mt-6">
            <h2 class="text-xl font-semibold mb-4">Details</h2>
            <dl class="grid grid-cols-2 gap-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Created</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        <?= date('F j, Y', strtotime($entity['created_at'] ?? 'now')) ?>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Updated</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        <?= date('F j, Y', strtotime($entity['updated_at'] ?? 'now')) ?>
                    </dd>
                </div>
            </dl>
        </div>

        <div class="mt-6 flex space-x-4">
            <a href="/entities/<?= $entity['id'] ?? '' ?>/edit"
               class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Edit
            </a>
            <a href="/entities"
               class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300">
                Back to List
            </a>
        </div>
    </div>
</div>
<?php $this->endSection() ?>
