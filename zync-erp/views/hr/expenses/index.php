<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100">Reseräkningar</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Hantera anställdas reseräkningar</p>
        </div>
        <a href="/hr/expenses/create" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">+ Ny reseräkning</a>
    </div>

    <?php if ($success !== null): ?>
        <div class="rounded-lg bg-green-50 dark:bg-green-900/30 px-4 py-3 text-sm text-green-700 dark:text-green-400"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <div class="flex flex-wrap gap-2">
        <a href="/hr/expenses" class="px-3 py-1.5 rounded-lg text-sm <?= empty($filters['status']) ? 'bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-400 font-semibold' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' ?> transition-colors">Alla</a>
        <?php foreach (['draft' => 'Utkast', 'submitted' => 'Inskickade', 'approved' => 'Godkända', 'rejected' => 'Avslagna', 'paid' => 'Utbetalda'] as $s => $label): ?>
            <a href="/hr/expenses?status=<?= htmlspecialchars($s, ENT_QUOTES, 'UTF-8') ?>" class="px-3 py-1.5 rounded-lg text-sm <?= ($filters['status'] ?? '') === $s ? 'bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-400 font-semibold' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' ?> transition-colors"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></a>
        <?php endforeach; ?>
    </div>

    <div class="overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-md">
        <?php if (empty($reports)): ?>
            <p class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">Inga reseräkningar hittades. <a href="/hr/expenses/create" class="text-indigo-600 dark:text-indigo-400 hover:underline">Skapa den första.</a></p>
        <?php else: ?>
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Nr</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Anställd</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Titel</th>
                        <th class="px-6 py-3 text-right font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Belopp</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Status</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Datum</th>
                        <th class="px-6 py-3 text-right font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Åtgärder</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php foreach ($reports as $r): ?>
                        <?php
                            $sc = [
                                'draft'     => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400',
                                'submitted' => 'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400',
                                'approved'  => 'bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-400',
                                'rejected'  => 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-400',
                                'paid'      => 'bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-400',
                            ];
                        ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-6 py-4 font-mono text-xs text-gray-500 dark:text-gray-400"><?= htmlspecialchars($r['report_number'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-6 py-4 text-gray-900 dark:text-gray-100"><?= htmlspecialchars($r['employee_name'] ?? '–', ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-gray-100">
                                <a href="/hr/expenses/<?= (int) $r['id'] ?>" class="hover:text-indigo-600 dark:hover:text-indigo-400"><?= htmlspecialchars($r['title'], ENT_QUOTES, 'UTF-8') ?></a>
                            </td>
                            <td class="px-6 py-4 text-right text-gray-900 dark:text-gray-100 font-medium"><?= number_format((float) $r['total_amount'], 2, ',', ' ') ?> <?= htmlspecialchars($r['currency'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium <?= $sc[$r['status']] ?? '' ?>"><?= htmlspecialchars($r['status'], ENT_QUOTES, 'UTF-8') ?></span>
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400 text-xs"><?= htmlspecialchars(substr($r['created_at'], 0, 10), ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-6 py-4 text-right space-x-3">
                                <a href="/hr/expenses/<?= (int) $r['id'] ?>" class="text-indigo-600 dark:text-indigo-400 hover:underline">Visa</a>
                                <a href="/hr/expenses/<?= (int) $r['id'] ?>/edit" class="text-gray-600 dark:text-gray-400 hover:underline">Redigera</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
