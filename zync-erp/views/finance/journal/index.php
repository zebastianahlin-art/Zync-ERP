<?php $entries = $entries ?? []; $year = $year ?? date('Y'); $period = $period ?? null; ?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Bokföring — Verifikationer</h1>
        <div class="flex gap-3">
            <a href="/finance" class="text-sm text-gray-500 hover:text-indigo-600">← Ekonomi</a>
            <a href="/finance/journal/create" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">+ Ny verifikation</a>
        </div>
    </div>
    <?php if (!empty($success)): ?><div class="rounded-lg bg-green-50 dark:bg-green-900/30 px-4 py-3 text-sm text-green-700 dark:text-green-400"><?= htmlspecialchars($success) ?></div><?php endif; ?>
    <form class="flex gap-3 items-end">
        <div><label class="block text-xs text-gray-500 mb-1">År</label><input type="number" name="year" value="<?= $year ?>" class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm w-24"></div>
        <div><label class="block text-xs text-gray-500 mb-1">Period</label><select name="period" class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"><option value="">Alla</option><?php for($m=1;$m<=12;$m++): ?><option value="<?= $m ?>" <?= $period == $m ? 'selected' : '' ?>><?= $m ?></option><?php endfor; ?></select></div>
        <button type="submit" class="bg-gray-200 dark:bg-gray-600 px-3 py-2 rounded text-sm hover:bg-gray-300 transition">Filtrera</button>
    </form>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Verifikation</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Serie</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Datum</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Beskrivning</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500">Debet</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500">Kredit</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500">Balanserad</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($entries as $e): ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer" onclick="location.href='/finance/journal/<?= $e['id'] ?>'">
                    <td class="px-4 py-3 font-mono text-sm text-indigo-600 font-medium"><?= htmlspecialchars($e['voucher_number']) ?></td>
                    <td class="px-4 py-3 text-sm text-gray-600"><?= htmlspecialchars($e['voucher_series']) ?></td>
                    <td class="px-4 py-3 text-sm text-gray-600"><?= $e['entry_date'] ?></td>
                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300"><?= htmlspecialchars($e['description']) ?></td>
                    <td class="px-4 py-3 text-sm text-right font-mono"><?= number_format((float)$e['total_debit'], 2, ',', ' ') ?></td>
                    <td class="px-4 py-3 text-sm text-right font-mono"><?= number_format((float)$e['total_credit'], 2, ',', ' ') ?></td>
                    <td class="px-4 py-3 text-center"><?= $e['is_balanced'] ? '✅' : '⚠️' ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($entries)): ?>
                <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">Inga verifikationer</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
