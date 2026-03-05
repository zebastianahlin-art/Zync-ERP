<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Historiska Avtal</h1>
        <a href="/purchasing/agreements" class="text-sm text-gray-500 hover:text-indigo-600 dark:text-gray-400">← Tillbaka</a>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-gray-500">Nummer</th>
                    <th class="px-4 py-3 text-left text-gray-500">Namn</th>
                    <th class="px-4 py-3 text-left text-gray-500">Leverantör</th>
                    <th class="px-4 py-3 text-left text-gray-500">Ansvarig</th>
                    <th class="px-4 py-3 text-left text-gray-500">Status</th>
                    <th class="px-4 py-3 text-left text-gray-500">Slutdatum</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                <?php foreach ($agreements as $a): ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-4 py-3 font-mono text-xs text-gray-600 dark:text-gray-400">
                        <a href="/purchasing/agreements/<?= (int)$a['id'] ?>" class="text-indigo-600 hover:underline">
                            <?= htmlspecialchars($a['agreement_number'] ?? $a['id'], ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    </td>
                    <td class="px-4 py-3 text-gray-800 dark:text-gray-200"><?= htmlspecialchars($a['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($a['supplier_name'] ?? '–', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($a['responsible_name'] ?? '–', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                            <?= htmlspecialchars($a['status'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-500 text-xs"><?= htmlspecialchars(substr($a['end_date'] ?? '–', 0, 10), ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($agreements)): ?>
                <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Inga historiska avtal hittades.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
