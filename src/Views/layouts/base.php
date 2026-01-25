<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= $this->getSection('description', 'Worlds - A worldbuilding tool for creative minds') ?>">
    <title><?= $this->getSection('title', 'Worlds - Worldbuilding Tool') ?></title>

    <!-- Tailwind CSS -->
    <link rel="stylesheet" href="<?= asset('assets/css/app.css') ?>">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            font-family: 'JetBrains Mono', monospace;
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Space Mono', monospace;
        }
    </style>
</head>
<body class="bg-zinc-950 text-zinc-100 antialiased">
    <!-- Background Pattern -->
    <div class="fixed inset-0 opacity-5 pointer-events-none" style="background-image: repeating-linear-gradient(45deg, transparent, transparent 35px, rgba(255,255,255,.03) 35px, rgba(255,255,255,.03) 70px);"></div>

    <div class="relative min-h-screen flex flex-col">
        <?php $this->include('partials/header') ?>

        <?php $this->include('partials/flash') ?>

        <div class="flex-1 flex">
            <?php if ($this->getSection('sidebar', true)): ?>
                <?php $this->include('partials/sidebar') ?>
            <?php endif; ?>

            <main class="flex-1 px-6 py-8">
                <?php if ($this->hasSection('breadcrumb')): ?>
                    <?php $this->yield('breadcrumb') ?>
                <?php endif; ?>

                <div class="max-w-7xl mx-auto">
                    <?php $this->yield('content') ?>
                </div>
            </main>
        </div>

        <?php $this->include('partials/footer') ?>
    </div>

    <!-- Application JavaScript -->
    <script src="<?= asset('assets/js/app.js') ?>"></script>

    <?php $this->yield('scripts') ?>
</body>
</html>
