<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="/certificates" class="text-sm text-gray-500 dark:text-gray-400 hover:text-indigo-600">&larr; Alla certifikat</a>
            <h1 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">Utgångna certifikat</h1>
        </div>
    </div>

    <?php if (!empty($certificates)): ?>
    <div class="rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-4 text-red-800 dark:text-red-300 text-sm">
        🚨 <?= count($certificates) ?> certifikat har gått ut och behöver förnyas.
    </div>
    <?php endif; ?>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <?php if (empty($certificates)): ?>
        <p class="px-4 py-8 text-center text-gray-400 dark:text-gray-500 text-sm">Inga utgångna certifikat</p>
        <?php else: ?>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-xs text-gray-500 dark:text-gray-400 uppercase">Anställd</th>
                    <th class="px-4 py-3 text-left text-xs text-gray-500 dark:text-gray-400 uppercase">Certifikattyp</th>
                    <th class="px-4 py-3 text-left text-xs text-gray-500 dark:text-gray-400 uppercase">Utfärdat</th>
                    <th class="px-4 py-3 text-left text-xs text-gray-500 dark:text-gray-400 uppercase">Gick ut</th>
                    <th class="px-4 py-3 text-left text-xs text-gray-500 dark:text-gray-400 uppercase">Dagar sedan</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($certificates as $cert):
                    $daysSince = $cert['expiry_date'] ? (int) ceil((time() - strtotime($cert['expiry_date'])) / 86400) : null;
                ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($cert['employee_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($cert['certificate_type_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($cert['issued_date'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-red-600 dark:text-red-400"><?= htmlspecialchars($cert['expiry_date'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-red-600 dark:text-red-400 font-medium"><?= $daysSince !== null ? $daysSince . ' d' : '—' ?></td>
                    <td class="px-4 py-3 text-right">
                        <a href="/certificates/<?= (int)$cert['id'] ?>/edit" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Redigera</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>
