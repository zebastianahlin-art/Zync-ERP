<?php
$budgets = $budgets ?? [];
$year = $year ?? date('Y');
$monthNames = ['1'=>'Januari','2'=>'Februari','3'=>'Mars','4'=>'April','5'=>'Maj','6'=>'Juni','7'=>'Juli','8'=>'Augusti','9'=>'September','10'=>'Oktober','11'=>'November','12'=>'December'];
?>
<div class="space-y-6">
    <?php if (!empty($success)): ?>
    <div class="rounded-lg bg-green-50 dark:bg-green-900/30 px-4 py-3 text-sm text-green-700 dark:text-green-400"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <div class="flex items-center justify-between flex-wrap gap-3">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Budgetar</h1>
        <div class="flex items-center gap-3">
            <form class="flex items-center gap-2">
                <label class="text-sm text-gray-600 dark:text-gray-400">År:</label>
                <input type="number" name="year" value="<?= (int)$year ?>" class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm w-24">
                <button type="submit" class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 px-3 py-1.5 rounded text-sm">Visa</button>
            </form>
            <a href="/finance/budgets/create" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm transition">+ Ny budgetrad</a>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-gray-500">Konto</th>
                    <th class="px-4 py-3 text-left text-gray-500">Månad</th>
                    <th class="px-4 py-3 text-right text-gray-500">Budgeterat belopp</th>
                    <th class="px-4 py-3 text-right text-gray-500">Åtgärder</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php if (empty($budgets)): ?>
                <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">Inga budgetrader för <?= (int)$year ?></td></tr>
                <?php else: ?>
                <?php foreach ($budgets as $b): ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                    <td class="px-4 py-3"><span class="font-mono text-xs text-gray-500"><?= htmlspecialchars($b['account_number']) ?></span> <?= htmlspecialchars($b['account_name']) ?></td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= $monthNames[(string)(int)$b['month']] ?? $b['month'] ?></td>
                    <td class="px-4 py-3 text-right font-mono font-medium"><?= number_format((float)$b['amount'], 2, ',', ' ') ?></td>
                    <td class="px-4 py-3 text-right flex justify-end gap-2">
                        <a href="/finance/budgets/<?= $b['id'] ?>/edit" class="text-indigo-600 hover:underline text-xs">Redigera</a>
                        <form method="POST" action="/finance/budgets/<?= $b['id'] ?>/delete" onsubmit="return confirm('Ta bort budgetrad?')">
                            <?= \App\Core\Csrf::field() ?>
                            <button class="text-red-500 hover:underline text-xs">Ta bort</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
