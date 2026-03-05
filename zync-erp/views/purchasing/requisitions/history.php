<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Historiska Anmodan</h1>
        <a href="/purchasing/requisitions" class="text-sm text-gray-500 hover:text-indigo-600 dark:text-gray-400">← Tillbaka</a>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-gray-500">Nummer</th>
                    <th class="px-4 py-3 text-left text-gray-500">Titel</th>
                    <th class="px-4 py-3 text-left text-gray-500">Avdelning</th>
                    <th class="px-4 py-3 text-left text-gray-500">Begärd av</th>
                    <th class="px-4 py-3 text-left text-gray-500">Status</th>
                    <th class="px-4 py-3 text-right text-gray-500">Belopp</th>
                    <th class="px-4 py-3 text-left text-gray-500">Skapad</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                <?php foreach ($requisitions as $r): ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-4 py-3 font-mono text-xs text-gray-600 dark:text-gray-400">
                        <a href="/purchasing/requisitions/<?= (int)$r['id'] ?>" class="text-indigo-600 hover:underline">
                            <?= htmlspecialchars($r['requisition_number'] ?? $r['id'], ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    </td>
                    <td class="px-4 py-3 text-gray-800 dark:text-gray-200"><?= htmlspecialchars($r['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($r['department_name'] ?? '–', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($r['requested_by_name'] ?? '–', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs <?= ($r['status'] ?? '') === 'rejected' ? 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' ?>">
                            <?= htmlspecialchars($r['status'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right font-mono"><?= number_format((float)($r['total_amount'] ?? 0), 2, ',', ' ') ?> kr</td>
                    <td class="px-4 py-3 text-gray-500 text-xs"><?= htmlspecialchars(substr($r['created_at'] ?? '', 0, 10), ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($requisitions)): ?>
                <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">Inga historiska anmodan hittades.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
