<?php $data = $data ?? []; $from = $from ?? ''; $to = $to ?? ''; $classNames = ['1'=>'Tillgångar','2'=>'Skulder & Eget kapital','3'=>'Intäkter','4'=>'Material/varuinköp','5'=>'Lokalkostnader','6'=>'Övriga externa','7'=>'Personal/avskrivning','8'=>'Finansiellt']; ?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Resultat- & balansräkning</h1>
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
                    <th class="px-4 py-3 text-left text-gray-500">Konto</th>
                    <th class="px-4 py-3 text-left text-gray-500">Namn</th>
                    <th class="px-4 py-3 text-right text-gray-500">Debet</th>
                    <th class="px-4 py-3 text-right text-gray-500">Kredit</th>
                    <th class="px-4 py-3 text-right text-gray-500">Saldo</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php $currentClass = ''; $classDebit = 0; $classCredit = 0; $classSaldo = 0; foreach ($data as $row): ?>
                <?php if ($row['account_class'] !== $currentClass): ?>
                    <?php if ($currentClass !== ''): ?>
                    <tr class="bg-gray-100 dark:bg-gray-700 font-bold text-xs">
                        <td colspan="2" class="px-4 py-2">Summa klass <?= $currentClass ?></td>
                        <td class="px-4 py-2 text-right font-mono"><?= number_format($classDebit, 2, ',', ' ') ?></td>
                        <td class="px-4 py-2 text-right font-mono"><?= number_format($classCredit, 2, ',', ' ') ?></td>
                        <td class="px-4 py-2 text-right font-mono"><?= number_format($classSaldo, 2, ',', ' ') ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php $currentClass = $row['account_class']; $classDebit = 0; $classCredit = 0; $classSaldo = 0; ?>
                    <tr class="bg-indigo-50 dark:bg-indigo-900/20"><td colspan="5" class="px-4 py-2 font-bold text-indigo-700 dark:text-indigo-400"><?= $currentClass ?> — <?= $classNames[$currentClass] ?? '' ?></td></tr>
                <?php endif; ?>
                <?php $classDebit += (float)$row['total_debit']; $classCredit += (float)$row['total_credit']; $classSaldo += (float)$row['balance']; ?>
                <tr>
                    <td class="px-4 py-2 font-mono"><?= $row['account_number'] ?></td>
                    <td class="px-4 py-2 text-gray-700 dark:text-gray-300"><?= htmlspecialchars($row['account_name']) ?></td>
                    <td class="px-4 py-2 text-right font-mono"><?= number_format((float)$row['total_debit'], 2, ',', ' ') ?></td>
                    <td class="px-4 py-2 text-right font-mono"><?= number_format((float)$row['total_credit'], 2, ',', ' ') ?></td>
                    <td class="px-4 py-2 text-right font-mono font-bold <?= (float)$row['balance'] < 0 ? 'text-red-600' : '' ?>"><?= number_format((float)$row['balance'], 2, ',', ' ') ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if ($currentClass !== ''): ?>
                <tr class="bg-gray-100 dark:bg-gray-700 font-bold text-xs">
                    <td colspan="2" class="px-4 py-2">Summa klass <?= $currentClass ?></td>
                    <td class="px-4 py-2 text-right font-mono"><?= number_format($classDebit, 2, ',', ' ') ?></td>
                    <td class="px-4 py-2 text-right font-mono"><?= number_format($classCredit, 2, ',', ' ') ?></td>
                    <td class="px-4 py-2 text-right font-mono"><?= number_format($classSaldo, 2, ',', ' ') ?></td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
