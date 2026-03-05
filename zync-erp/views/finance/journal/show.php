<?php $entry = $entry ?? []; $lines = $lines ?? []; $accounts = $accounts ?? []; $costCenters = $costCenters ?? []; ?>
<div class="space-y-6">
    <?php if (!empty($success)): ?><div class="rounded-lg bg-green-50 dark:bg-green-900/30 px-4 py-3 text-sm text-green-700 dark:text-green-400"><?= htmlspecialchars($success) ?></div><?php endif; ?>
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($entry['voucher_number']) ?></h1>
            <p class="text-sm text-gray-500"><?= htmlspecialchars($entry['description']) ?></p>
            <?php if (!$entry['is_balanced']): ?><span class="px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">⚠️ Ej balanserad</span><?php else: ?><span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">✅ Balanserad</span><?php endif; ?>
            <?php if ($entry['is_locked']): ?><span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-200 text-gray-600">🔒 Låst</span><?php endif; ?>
        </div>
        <a href="/finance/journal" class="text-sm text-gray-500 hover:text-indigo-600">← Tillbaka</a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
        <div><span class="text-gray-500">Datum</span><p class="font-medium"><?= $entry['entry_date'] ?></p></div>
        <div><span class="text-gray-500">Serie</span><p class="font-medium"><?= $entry['voucher_series'] ?></p></div>
        <div><span class="text-gray-500">Period</span><p class="font-medium"><?= $entry['fiscal_year'] ?>-<?= str_pad((string)$entry['fiscal_period'], 2, '0', STR_PAD_LEFT) ?></p></div>
        <div><span class="text-gray-500">Skapad av</span><p class="font-medium"><?= htmlspecialchars($entry['created_by_name'] ?? '—') ?></p></div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 grid grid-cols-2 gap-4 text-sm">
        <div><span class="text-gray-500">Total debet</span><p class="text-xl font-bold font-mono"><?= number_format((float)$entry['total_debit'], 2, ',', ' ') ?></p></div>
        <div><span class="text-gray-500">Total kredit</span><p class="text-xl font-bold font-mono"><?= number_format((float)$entry['total_credit'], 2, ',', ' ') ?></p></div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700"><h2 class="text-lg font-semibold">Konteringsrader</h2></div>
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-gray-500">Konto</th>
                    <th class="px-4 py-3 text-left text-gray-500">KS</th>
                    <th class="px-4 py-3 text-left text-gray-500">Beskrivning</th>
                    <th class="px-4 py-3 text-right text-gray-500">Debet</th>
                    <th class="px-4 py-3 text-right text-gray-500">Kredit</th>
                    <?php if (!$entry['is_locked']): ?><th class="px-4 py-3"></th><?php endif; ?>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($lines as $l): ?>
                <tr>
                    <td class="px-4 py-3 font-mono text-sm"><?= htmlspecialchars($l['account_number'] . ' ' . $l['account_name']) ?></td>
                    <td class="px-4 py-3 text-xs text-gray-500"><?= htmlspecialchars(($l['cost_center_code'] ?? '') ? $l['cost_center_code'] . ' ' . $l['cost_center_name'] : '—') ?></td>
                    <td class="px-4 py-3 text-gray-700 dark:text-gray-300"><?= htmlspecialchars($l['description'] ?? '') ?></td>
                    <td class="px-4 py-3 text-right font-mono <?= (float)$l['debit'] > 0 ? 'font-bold' : 'text-gray-400' ?>"><?= number_format((float)$l['debit'], 2, ',', ' ') ?></td>
                    <td class="px-4 py-3 text-right font-mono <?= (float)$l['credit'] > 0 ? 'font-bold' : 'text-gray-400' ?>"><?= number_format((float)$l['credit'], 2, ',', ' ') ?></td>
                    <?php if (!$entry['is_locked']): ?>
                    <td class="px-4 py-3"><form method="POST" action="/finance/journal/<?= $entry['id'] ?>/lines/<?= $l['id'] ?>/delete" onsubmit="return confirm('Ta bort denna rad?')"><?= \App\Core\Csrf::field() ?><button class="text-red-500 hover:text-red-700 text-xs">Ta bort</button></form></td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if (!$entry['is_locked']): ?>
        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 border-t">
            <form method="POST" action="/finance/journal/<?= $entry['id'] ?>/lines" class="grid grid-cols-1 sm:grid-cols-6 gap-3 items-end">
                <?= \App\Core\Csrf::field() ?>
                <div><label class="block text-xs text-gray-500 mb-1">Konto *</label><select name="account_id" required class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"><option value="">Välj...</option><?php foreach ($accounts as $acc): ?><option value="<?= $acc['id'] ?>"><?= htmlspecialchars($acc['account_number'] . ' ' . $acc['name']) ?></option><?php endforeach; ?></select></div>
                <div><label class="block text-xs text-gray-500 mb-1">KS</label><select name="cost_center_id" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"><option value="">—</option><?php foreach ($costCenters as $cc): ?><option value="<?= $cc['id'] ?>"><?= htmlspecialchars($cc['code'] . ' ' . $cc['name']) ?></option><?php endforeach; ?></select></div>
                <div><label class="block text-xs text-gray-500 mb-1">Beskrivning</label><input type="text" name="description" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"></div>
                <div><label class="block text-xs text-gray-500 mb-1">Debet</label><input type="number" name="debit" step="0.01" value="0" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"></div>
                <div><label class="block text-xs text-gray-500 mb-1">Kredit</label><input type="number" name="credit" step="0.01" value="0" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"></div>
                <div><button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-3 rounded text-sm font-medium transition">Lägg till</button></div>
            </form>
        </div>
        <?php endif; ?>
    </div>
</div>
