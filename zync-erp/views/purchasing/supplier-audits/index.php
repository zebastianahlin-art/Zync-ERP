<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Leverantörsaudit</h1>
        <a href="/purchasing/supplier-audits/create"
           class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Ny audit
        </a>
    </div>

    <?php if (!empty($success)): ?>
        <div class="rounded-lg bg-green-50 dark:bg-green-900/30 p-4 text-green-800 dark:text-green-200">
            <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="rounded-lg bg-red-50 dark:bg-red-900/30 p-4 text-red-800 dark:text-red-200">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Leverantör</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Datum</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Utförare</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Status</th>
                        <th class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">Leverans</th>
                        <th class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">Kvalitet</th>
                        <th class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">Pris</th>
                        <th class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">Kommunikation</th>
                        <th class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">Totalt</th>
                        <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400">Åtgärder</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php if (empty($audits)): ?>
                        <tr>
                            <td colspan="10" class="px-4 py-12 text-center text-gray-400 dark:text-gray-500">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="w-10 h-10 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    <span>Inga audits registrerade ännu</span>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($audits as $audit): ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                    <?= htmlspecialchars($audit['supplier_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                    <?= htmlspecialchars($audit['audit_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                    <?= htmlspecialchars($audit['auditor_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td class="px-4 py-3">
                                    <?php
                                    $statusMap = [
                                        'planned'     => ['Planerad',  'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300'],
                                        'in_progress' => ['Pågående',  'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300'],
                                        'completed'   => ['Slutförd',  'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'],
                                    ];
                                    $s = $audit['status'] ?? '';
                                    [$label, $cls] = $statusMap[$s] ?? [$s, 'bg-gray-100 text-gray-700'];
                                    ?>
                                    <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium <?= $cls ?>">
                                        <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                                <?php
                                $scoreFields = ['delivery_score', 'quality_score', 'price_score', 'communication_score'];
                                foreach ($scoreFields as $field):
                                    $val = $audit[$field] ?? null;
                                ?>
                                <td class="px-4 py-3 text-center text-gray-700 dark:text-gray-300">
                                    <?= $val !== null ? htmlspecialchars($val, ENT_QUOTES, 'UTF-8') . '/5' : '—' ?>
                                </td>
                                <?php endforeach; ?>
                                <td class="px-4 py-3 text-center font-semibold text-gray-900 dark:text-white">
                                    <?php
                                    $overall = $audit['overall_score'] ?? null;
                                    echo $overall !== null ? htmlspecialchars(number_format((float)$overall, 1), ENT_QUOTES, 'UTF-8') . '/5' : '—';
                                    ?>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="inline-flex gap-2">
                                        <a href="/purchasing/supplier-audits/<?= (int)$audit['id'] ?>"
                                           class="rounded px-2 py-1 text-xs font-medium text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition">
                                            Visa
                                        </a>
                                        <a href="/purchasing/supplier-audits/<?= (int)$audit['id'] ?>/edit"
                                           class="rounded px-2 py-1 text-xs font-medium text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition">
                                            Redigera
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
