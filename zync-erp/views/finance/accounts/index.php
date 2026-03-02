<?php $accounts = $accounts ?? []; $classNames = ['1'=>'Tillgångar','2'=>'Skulder','3'=>'Intäkter','4'=>'Material/varuinköp','5'=>'Lokalkostnader','6'=>'Övriga externa','7'=>'Personal/avskrivning','8'=>'Finansiellt']; ?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Kontoplan (BAS)</h1>
        <div class="flex gap-3">
            <a href="/finance" class="text-sm text-gray-500 hover:text-indigo-600">← Ekonomi</a>
            <a href="/finance/accounts/create" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">+ Nytt konto</a>
        </div>
    </div>
    <?php if (!empty($success)): ?><div class="rounded-lg bg-green-50 dark:bg-green-900/30 px-4 py-3 text-sm text-green-700 dark:text-green-400"><?= htmlspecialchars($success) ?></div><?php endif; ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Konto</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Namn</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Klass</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Moms</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500">Aktiv</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($accounts as $a): ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <td class="px-4 py-2 font-mono text-sm font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($a['account_number']) ?></td>
                    <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300"><?= htmlspecialchars($a['name']) ?></td>
                    <td class="px-4 py-2 text-xs text-gray-500"><?= $a['account_class'] ?> — <?= $classNames[$a['account_class']] ?? '' ?></td>
                    <td class="px-4 py-2 text-xs text-gray-500"><?= htmlspecialchars($a['vat_code'] ?? '—') ?></td>
                    <td class="px-4 py-2 text-center"><?= $a['is_active'] ? '✅' : '❌' ?></td>
                    <td class="px-4 py-2"><a href="/finance/accounts/<?= $a['id'] ?>/edit" class="text-indigo-600 hover:underline text-xs">Redigera</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

