<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Lagerställen</h1>
        <a href="/inventory/warehouses/create"
           class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nytt lagerställe
        </a>
    </div>

    <!-- Flash messages -->
    <?php if (!empty($success)): ?>
    <div class="rounded-lg bg-green-50 dark:bg-green-900/30 p-4 text-green-800 dark:text-green-200">
        <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
    </div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
    <div class="rounded-lg bg-red-50 dark:bg-red-900/30 p-4 text-red-800 dark:text-red-200">
        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
    </div>
    <?php endif; ?>

    <!-- Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Namn</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Kod</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Adress</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Ansvarig</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Status</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($warehouses as $wh): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                            <?= htmlspecialchars($wh['name'], ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <td class="px-4 py-3 font-mono text-xs text-indigo-600 dark:text-indigo-400">
                            <?= htmlspecialchars($wh['code'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                            <?= htmlspecialchars($wh['address'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                            <?= htmlspecialchars($wh['responsible_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <td class="px-4 py-3">
                            <?php if (!empty($wh['is_active'])): ?>
                            <span class="inline-flex items-center rounded-full bg-green-100 dark:bg-green-900/30 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:text-green-200">
                                Aktiv
                            </span>
                            <?php else: ?>
                            <span class="inline-flex items-center rounded-full bg-gray-100 dark:bg-gray-700 px-2.5 py-0.5 text-xs font-medium text-gray-600 dark:text-gray-400">
                                Inaktiv
                            </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-right flex items-center justify-end gap-3">
                            <a href="/inventory/warehouses/<?= htmlspecialchars((string) $wh['id'], ENT_QUOTES, 'UTF-8') ?>/edit"
                               class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">Redigera</a>
                            <form method="POST" action="/inventory/warehouses/<?= htmlspecialchars((string) $wh['id'], ENT_QUOTES, 'UTF-8') ?>/delete"
                                  onsubmit="return confirm('Är du säker på att du vill ta bort detta lagerställe?')">
                                <?= \App\Core\Csrf::field() ?>
                                <button type="submit" class="text-sm text-red-600 dark:text-red-400 hover:underline">Ta bort</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($warehouses)): ?>
                    <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Inga lagerställen registrerade</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
