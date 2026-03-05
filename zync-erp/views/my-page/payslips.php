<?php
$statusMap = [
    'draft'    => ['bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400', 'Utkast'],
    'approved' => ['bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400', 'Godkänd'],
    'paid'     => ['bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400', 'Utbetald'],
];
?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Mina lönespecar</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Alla dina lönespecifikationer</p>
        </div>
        <a href="/my-page" class="text-sm text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">&larr; Min Sida</a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Period</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Från</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Till</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Bruttolön</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Nettolön</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Status</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($payslips as $ps): ?>
                    <?php $s = $statusMap[$ps['status']] ?? ['bg-gray-100 text-gray-600', $ps['status']]; ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($ps['period_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($ps['period_from'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($ps['period_to'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-right text-gray-700 dark:text-gray-300"><?= number_format((float)($ps['gross_pay'] ?? 0), 0, ',', ' ') ?> kr</td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-white"><?= number_format((float)($ps['net_pay'] ?? 0), 0, ',', ' ') ?> kr</td>
                        <td class="px-4 py-3"><span class="inline-flex px-2 py-0.5 rounded text-xs font-medium <?= $s[0] ?>"><?= $s[1] ?></span></td>
                        <td class="px-4 py-3 text-right">
                            <a href="/my-page/payslips/<?= (int)$ps['id'] ?>" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline font-medium">Visa</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($payslips)): ?>
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-gray-400 dark:text-gray-500">
                            Inga lönespecar hittades. Kontakta din HR-avdelning om du saknar information.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
