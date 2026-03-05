<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="/certificates" class="text-sm text-gray-500 dark:text-gray-400 hover:text-indigo-600">&larr; Alla certifikat</a>
            <h1 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">Certifikat som löper ut</h1>
        </div>
        <form method="GET" class="flex items-center gap-2">
            <label class="text-sm text-gray-600 dark:text-gray-400">Visa de som löper ut inom</label>
            <input type="number" name="days" min="1" max="365" value="<?= (int)$days ?>"
                   class="w-20 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-1 text-sm focus:border-indigo-500 focus:outline-none">
            <span class="text-sm text-gray-600 dark:text-gray-400">dagar</span>
            <button type="submit" class="px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white text-sm rounded-lg transition">Filtrera</button>
        </form>
    </div>

    <?php if (!empty($certificates)): ?>
    <div class="rounded-lg bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 p-4 text-yellow-800 dark:text-yellow-300 text-sm">
        ⚠️ <?= count($certificates) ?> certifikat löper ut inom <?= (int)$days ?> dagar.
    </div>
    <?php endif; ?>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <?php if (empty($certificates)): ?>
        <p class="px-4 py-8 text-center text-gray-400 dark:text-gray-500 text-sm">Inga certifikat löper ut inom <?= (int)$days ?> dagar</p>
        <?php else: ?>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-xs text-gray-500 dark:text-gray-400 uppercase">Anställd</th>
                    <th class="px-4 py-3 text-left text-xs text-gray-500 dark:text-gray-400 uppercase">Certifikattyp</th>
                    <th class="px-4 py-3 text-left text-xs text-gray-500 dark:text-gray-400 uppercase">Utfärdat</th>
                    <th class="px-4 py-3 text-left text-xs text-gray-500 dark:text-gray-400 uppercase">Utgår</th>
                    <th class="px-4 py-3 text-left text-xs text-gray-500 dark:text-gray-400 uppercase">Dagar kvar</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($certificates as $cert):
                    $daysLeft = $cert['expiry_date'] ? (int) ceil((strtotime($cert['expiry_date']) - time()) / 86400) : null;
                    $urgency = $daysLeft !== null && $daysLeft <= 7
                        ? 'text-red-600 dark:text-red-400 font-bold'
                        : 'text-yellow-600 dark:text-yellow-400 font-medium';
                ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($cert['employee_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($cert['certificate_type_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($cert['issued_date'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($cert['expiry_date'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 <?= $urgency ?>"><?= $daysLeft !== null ? $daysLeft . ' d' : '—' ?></td>
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
