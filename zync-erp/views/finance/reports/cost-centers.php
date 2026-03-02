<?php $data = $data ?? []; $from = $from ?? ''; $to = $to ?? ''; ?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Kostnadsställerapport</h1>
        <a href="/finance" class="text-sm text-gray-500 hover:text-indigo-600">← Ekonomi</a>
    </div>
    <form class="flex gap-3 items-end">
        <div><label class="block text-xs text-gray-500 mb-1">Från</label><input type="date" name="from" value="<?= $from ?>" class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"></div>
        <div><label class="block text-xs text-gray-500 mb-1">Till</label><input type="date" name="to" value="<?= $to ?>" class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"></div>
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm transition">Visa</button>
    </form>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-gray-500">Kod</th>
                    <th class="px-4 py-3 text-left text-gray-500">Namn</th>
                    <th class="px-4 py-3 text-right text-gray-500">Budget</th>
                    <th class="px-4 py-3 text-right text-gray-500">Debet</th>
                    <th class="px-4 py-3 text-right text-gray-500">Kredit</th>
                    <th class="px-4 py-3 text-right text-gray-500">Netto</th>
                    <th class="px-4 py-3 text-right text-gray-500">Avvikelse</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($data as $row): $diff = (float)($row['budget'] ?? 0) - (float)$row['balance']; ?>
                <tr>
                    <td class="px-4 py-3 font-mono font-bold"><?= htmlspecialchars($row['code']) ?></td>
                    <td class="px-4 py-3 text-gray-700 dark:text-gray-300"><?= htmlspecialchars($row['name']) ?></td>
                    <td class="px-4 py-3 text-right font-mono"><?= number_format((float)($row['budget'] ?? 0), 0, ',', ' ') ?></td>
                    <td class="px-4 py-3 text-right font-mono"><?= number_format((float)$row['total_debit'], 2, ',', ' ') ?></td>
                    <td class="px-4 py-3 text-right font-mono"><?= number_format((float)$row['total_credit'], 2, ',', ' ') ?></td>
                    <td class="px-4 py-3 text-right font-mono font-bold"><?= number_format((float)$row['balance'], 2, ',', ' ') ?></td>
                    <td class="px-4 py-3 text-right font-mono font-bold <?= $diff < 0 ? 'text-red-600' : 'text-green-600' ?>"><?= number_format($diff, 0, ',', ' ') ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($data)): ?>
                <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">Inga data</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
