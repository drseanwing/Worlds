<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->getSection('title', 'Worlds - Worldbuilding Tool') ?></title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <?php $this->include('partials/header') ?>

        <main class="container mx-auto px-4 py-8">
            <?php $this->yield('content') ?>
        </main>

        <?php $this->include('partials/footer') ?>
    </div>

    <script src="/assets/js/app.js"></script>
    <?php $this->yield('scripts') ?>
</body>
</html>
