<?php if (!empty($success)): ?>
<div class="mb-4 rounded-lg bg-green-50 dark:bg-green-900/20 p-4 text-green-800 dark:text-green-300 text-sm">
    <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
</div>
<?php endif; ?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Lönehantering</h1>
        <a href="/hr/payroll/periods/create" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">+ Ny löneperiod</a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Namn</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Från</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Till</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Status</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($periods as $period):
                        $statusLabels = ['open'=>'Öppen','locked'=>'Låst','closed'=>'Stängd'];
                        $statusClasses = [
                            'open'   => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300',
                            'locked' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300',
                            'closed' => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300',
                        ];
                        $st = $period['status'] ?? 'open';
                        $stLabel = $statusLabels[$st] ?? $st;
                        $stClass = $statusClasses[$st] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300';
                    ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                            <a href="/hr/payroll/periods/<?= (int)$period['id'] ?>" class="hover:text-indigo-600 dark:hover:text-indigo-400">
                                <?= htmlspecialchars($period['name'], ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($period['period_from'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($period['period_to'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium <?= $stClass ?>">
                                <?= htmlspecialchars($stLabel, ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <a href="/hr/payroll/periods/<?= (int)$period['id'] ?>" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline mr-3">Visa</a>
                            <a href="/hr/payroll/periods/<?= (int)$period['id'] ?>/edit" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline mr-3">Redigera</a>
                            <form method="POST" action="/hr/payroll/periods/<?= (int)$period['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort löneperioden?')">
                                <?= \App\Core\Csrf::field() ?>
                                <button type="submit" class="text-xs text-red-500 hover:text-red-700">Ta bort</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($periods)): ?>
                    <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400 dark:text-gray-500">Inga löneperioder registrerade ännu</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
