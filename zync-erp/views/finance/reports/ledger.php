<?php $entries = $entries ?? []; $from = $from ?? ''; $to = $to ?? ''; ?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Huvudbok</h1>
        <a href="/finance" class="text-sm text-gray-500 hover:text-indigo-600">← Ekonomi</a>
    </div>
    <form class="flex gap-3 items-end flex-wrap">
        <div><label class="block text-xs text-gray-500 mb-1">Från</label><input type="date" name="from" value="<?= $from ?>" class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"></div>
        <div><label class="block text-xs text-gray-500 mb-1">Till</label><input type="date" name="to" value="<?= $to ?>" class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"></div>
        <div><label class="block text-xs text-gray-500 mb-1">Konto från</label><input type="text" name="account_from" value="<?= htmlspecialchars($accountFrom ?? '') ?>" placeholder="1000" class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm w-24"></div>
        <div><label class="block text-xs text-gray-500 mb-1">Konto till</label><input type="text" name="account_to" value="<?= htmlspecialchars($accountTo ?? '') ?>" placeholder="9999" class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm w-24"></div>
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm transition">Visa</button>
    </form>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-gray-500">Konto</th>
                    <th class="px-4 py-3 text-left text-gray-500">Datum</th>
                    <th class="px-4 py-3 text-left text-gray-500">Verifikation</th>
                    <th class="px-4 py-3 text-left text-gray-500">Beskrivning</th>
                    <th class="px-4 py-3 text-left text-gray-500">KS</th>
                    <th class="px-4 py-3 text-right text-gray-500">Debet</th>
                    <th class="px-4 py-3 text-right text-gray-500">Kredit</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php $currentAccount = ''; $accountDebit = 0; $accountCredit = 0; foreach ($entries as $e): ?>
                <?php if ($e['account_number'] !== $currentAccount): ?>
                    <?php if ($currentAccount !== ''): ?>
                    <tr class="bg-gray-100 dark:bg-gray-700 font-bold">
                        <td colspan="5" class="px-4 py-2 text-right text-xs">Summa <?= $currentAccount ?></td>
                        <td class="px-4 py-2 text-right font-mono text-xs"><?= number_format($accountDebit, 2, ',', ' ') ?></td>
                        <td class="px-4 py-2 text-right font-mono text-xs"><?= number_format($accountCredit, 2, ',', ' ') ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php $currentAccount = $e['account_number']; $accountDebit = 0; $accountCredit = 0; ?>
                    <tr class="bg-indigo-50 dark:bg-indigo-900/20"><td colspan="7" class="px-4 py-2 font-bold text-indigo-700 dark:text-indigo-400"><?= htmlspecialchars($e['account_number'] . ' ' . $e['account_name']) ?></td></tr>
                <?php endif; ?>
                <?php $accountDebit += (float)$e['debit']; $accountCredit += (float)$e['credit']; ?>
                <tr>
                    <td class="px-4 py-2 font-mono text-xs text-gray-400"><?= $e['account_number'] ?></td>
                    <td class="px-4 py-2"><?= $e['entry_date'] ?></td>
                    <td class="px-4 py-2 font-mono text-indigo-600"><?= htmlspecialchars($e['voucher_number']) ?></td>
                    <td class="px-4 py-2 text-gray-700 dark:text-gray-300"><?= htmlspecialchars($e['description'] ?? '') ?></td>
                    <td class="px-4 py-2 text-xs text-gray-500"><?= htmlspecialchars($e['cost_center_code'] ?? '') ?></td>
                    <td class="px-4 py-2 text-right font-mono <?= (float)$e['debit'] > 0 ? '' : 'text-gray-300' ?>"><?= number_format((float)$e['debit'], 2, ',', ' ') ?></td>
                    <td class="px-4 py-2 text-right font-mono <?= (float)$e['credit'] > 0 ? '' : 'text-gray-300' ?>"><?= number_format((float)$e['credit'], 2, ',', ' ') ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if ($currentAccount !== ''): ?>
                <tr class="bg-gray-100 dark:bg-gray-700 font-bold">
                    <td colspan="5" class="px-4 py-2 text-right text-xs">Summa <?= $currentAccount ?></td>
                    <td class="px-4 py-2 text-right font-mono text-xs"><?= number_format($accountDebit, 2, ',', ' ') ?></td>
                    <td class="px-4 py-2 text-right font-mono text-xs"><?= number_format($accountCredit, 2, ',', ' ') ?></td>
                </tr>
                <?php endif; ?>
                <?php if (empty($entries)): ?>
                <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">Inga poster i valt intervall</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
