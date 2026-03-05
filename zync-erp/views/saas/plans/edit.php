<?php $e = fn(string $v) => htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); ?>
<div class="space-y-6">

    <!-- Breadcrumbs -->
    <nav class="flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400">
        <a href="/saas-admin" class="hover:text-indigo-600 dark:hover:text-indigo-400">SaaS Admin</a>
        <span>/</span>
        <a href="/saas-admin/plans" class="hover:text-indigo-600 dark:hover:text-indigo-400">Planer</a>
        <span>/</span>
        <span class="text-gray-900 dark:text-white font-medium">Redigera <?= $e((string) $plan['name']) ?></span>
    </nav>

    <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Redigera plan: <?= $e((string) $plan['name']) ?></h1>

    <form method="POST" action="/saas-admin/plans/<?= (int) $plan['id'] ?>" class="space-y-6">
        <?= \App\Core\Csrf::field() ?>

        <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md space-y-5">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-3">Planinformation</h2>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Plannamn *</label>
                    <input type="text" name="name" value="<?= $e((string) $plan['name']) ?>"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 <?= !empty($errors['name']) ? 'border-red-500' : '' ?>">
                    <?php if (!empty($errors['name'])): ?>
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400"><?= $e($errors['name']) ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Slug *</label>
                    <input type="text" name="slug" value="<?= $e((string) $plan['slug']) ?>"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 <?= !empty($errors['slug']) ? 'border-red-500' : '' ?>">
                    <?php if (!empty($errors['slug'])): ?>
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400"><?= $e($errors['slug']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Beskrivning</label>
                <textarea name="description" rows="2"
                          class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"><?= $e((string) ($plan['description'] ?? '')) ?></textarea>
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pris/månad (SEK) *</label>
                    <input type="number" name="price_monthly" value="<?= $e((string) $plan['price_monthly']) ?>" min="0" step="1"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 <?= !empty($errors['price_monthly']) ? 'border-red-500' : '' ?>">
                    <?php if (!empty($errors['price_monthly'])): ?>
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400"><?= $e($errors['price_monthly']) ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pris/år (SEK)</label>
                    <input type="number" name="price_yearly" value="<?= $e((string) $plan['price_yearly']) ?>" min="0" step="1"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Max användare</label>
                    <input type="number" name="max_users" value="<?= $e((string) $plan['max_users']) ?>" min="1"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Max lagring (GB)</label>
                    <input type="number" name="max_storage_gb" value="<?= $e((string) $plan['max_storage_gb']) ?>" min="1"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sorteringsordning</label>
                    <input type="number" name="sort_order" value="<?= $e((string) $plan['sort_order']) ?>" min="0"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Inkluderade moduler (JSON array)</label>
                <input type="text" name="included_modules"
                       value="<?= $e(is_string($plan['included_modules']) ? $plan['included_modules'] : json_encode($plan['included_modules'] ?? [])) ?>"
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm font-mono text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">JSON-array med module slugs.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Features (JSON array)</label>
                <textarea name="features" rows="3"
                          class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm font-mono text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"><?= $e(is_string($plan['features']) ? $plan['features'] : json_encode($plan['features'] ?? [], JSON_UNESCAPED_UNICODE)) ?></textarea>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">JSON-array med feature-beskrivningar.</p>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" id="is_active" value="1" <?= $plan['is_active'] ? 'checked' : '' ?>
                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <label for="is_active" class="text-sm text-gray-700 dark:text-gray-300">Aktiv (synlig för kunder)</label>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="rounded-lg bg-indigo-600 px-6 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">Spara ändringar</button>
            <a href="/saas-admin/plans" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">Avbryt</a>
        </div>
    </form>
</div>
