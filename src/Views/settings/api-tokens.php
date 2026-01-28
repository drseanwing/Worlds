<?php
/**
 * API Token Management View
 *
 * Displays user's API tokens and provides forms to create new tokens
 * and revoke existing ones.
 */

use Worlds\Config\Config;

$appName = Config::getAppName();
$newToken = $data['newToken'] ?? null;
$tokens = $data['tokens'] ?? [];
$success = $data['success'] ?? null;
$errors = $data['errors'] ?? [];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Tokens - <?= htmlspecialchars($appName) ?></title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="bg-gray-100 min-h-screen">
    <?php include __DIR__ . '/../partials/header.php'; ?>

    <div class="container mx-auto px-4 py-8">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800">API Tokens</h1>
            <p class="text-gray-600 mt-2">
                Manage your API tokens for programmatic access to the Worlds API.
            </p>
        </div>

        <?php if ($success): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
            <p><?= htmlspecialchars($success) ?></p>
        </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
            <ul class="list-disc list-inside">
                <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <?php if ($newToken): ?>
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6" role="alert">
            <p class="font-bold">Your new API token has been created!</p>
            <p class="mt-2">Copy this token now. You won't be able to see it again.</p>
            <div class="mt-3 p-3 bg-white rounded border border-yellow-400 font-mono text-sm break-all">
                <?= htmlspecialchars($newToken['token']) ?>
            </div>
            <p class="mt-2 text-sm">Token name: <strong><?= htmlspecialchars($newToken['name']) ?></strong></p>
        </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Create New Token Form -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Create New Token</h2>

                    <form method="POST" action="/settings/api-tokens">
                        <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">

                        <div class="mb-4">
                            <label for="name" class="block text-gray-700 font-medium mb-2">Token Name</label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="e.g., Mobile App"
                                required
                                minlength="3"
                            >
                            <p class="text-sm text-gray-600 mt-1">
                                A descriptive name to help you identify this token.
                            </p>
                        </div>

                        <div class="mb-6">
                            <label for="expires_in" class="block text-gray-700 font-medium mb-2">Expires</label>
                            <select
                                id="expires_in"
                                name="expires_in"
                                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                                <option value="never">Never</option>
                                <option value="30d">30 days</option>
                                <option value="90d">90 days</option>
                                <option value="1y">1 year</option>
                            </select>
                        </div>

                        <button
                            type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded transition duration-200"
                        >
                            Generate Token
                        </button>
                    </form>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-6">
                    <h3 class="font-semibold text-blue-900 mb-2">API Documentation</h3>
                    <p class="text-sm text-blue-800 mb-2">
                        Use your API token in the Authorization header:
                    </p>
                    <code class="block bg-white p-2 rounded text-xs font-mono break-all">
                        Authorization: Bearer YOUR_TOKEN_HERE
                    </code>
                    <p class="text-sm text-blue-800 mt-3">
                        <strong>Base URL:</strong> <code class="bg-white px-1 py-0.5 rounded">/api/v1</code>
                    </p>
                </div>
            </div>

            <!-- Existing Tokens List -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Your API Tokens</h2>

                    <?php if (empty($tokens)): ?>
                    <p class="text-gray-600">You don't have any API tokens yet. Create one to get started.</p>
                    <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($tokens as $token): ?>
                        <div class="border border-gray-300 rounded-lg p-4 <?= $token['is_expired'] ? 'bg-gray-50 opacity-75' : '' ?>">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-800 flex items-center">
                                        <?= htmlspecialchars($token['name']) ?>
                                        <?php if ($token['is_expired']): ?>
                                        <span class="ml-2 px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded">
                                            Expired
                                        </span>
                                        <?php endif; ?>
                                    </h3>

                                    <div class="text-sm text-gray-600 mt-2 space-y-1">
                                        <p>
                                            <strong>Created:</strong>
                                            <?= date('M j, Y g:i A', strtotime($token['created_at'])) ?>
                                        </p>

                                        <?php if ($token['last_used_at']): ?>
                                        <p>
                                            <strong>Last used:</strong>
                                            <?= date('M j, Y g:i A', strtotime($token['last_used_at'])) ?>
                                        </p>
                                        <?php else: ?>
                                        <p>
                                            <strong>Last used:</strong>
                                            <span class="text-gray-500">Never</span>
                                        </p>
                                        <?php endif; ?>

                                        <?php if ($token['expires_at']): ?>
                                        <p>
                                            <strong>Expires:</strong>
                                            <?= date('M j, Y g:i A', strtotime($token['expires_at'])) ?>
                                        </p>
                                        <?php else: ?>
                                        <p>
                                            <strong>Expires:</strong>
                                            <span class="text-gray-500">Never</span>
                                        </p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <form method="POST" action="/settings/api-tokens/<?= $token['id'] ?>" class="ml-4">
                                    <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button
                                        type="submit"
                                        class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded transition duration-200"
                                        onclick="return confirm('Are you sure you want to revoke this token? This action cannot be undone.')"
                                    >
                                        Revoke
                                    </button>
                                </form>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mt-6">
                    <h3 class="font-semibold text-yellow-900 mb-2">Security Best Practices</h3>
                    <ul class="text-sm text-yellow-800 space-y-1 list-disc list-inside">
                        <li>Never share your API tokens with anyone</li>
                        <li>Store tokens securely (e.g., environment variables)</li>
                        <li>Use different tokens for different applications</li>
                        <li>Revoke tokens immediately if compromised</li>
                        <li>Set expiration dates for tokens when possible</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script src="/assets/js/app.js"></script>
</body>
</html>
