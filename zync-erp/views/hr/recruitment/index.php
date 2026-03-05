<?php if (!empty($success)): ?>
<div class="mb-4 rounded-lg bg-green-50 dark:bg-green-900/20 p-4 text-green-800 dark:text-green-300 text-sm">
    <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
</div>
<?php endif; ?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Rekrytering</h1>
        <div class="flex gap-3">
            <a href="/hr/recruitment/applicants" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg transition">Alla sökande</a>
            <a href="/hr/recruitment/positions/create" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">+ Ny tjänst</a>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Titel</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Avdelning</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Status</th>
                        <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Sökande</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($positions as $pos):
                        $statusLabels = ['draft'=>'Utkast','open'=>'Öppen','on_hold'=>'Pausad','closed'=>'Stängd','filled'=>'Tillsatt'];
                        $statusClasses = [
                            'draft'   => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300',
                            'open'    => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300',
                            'on_hold' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300',
                            'closed'  => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300',
                            'filled'  => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300',
                        ];
                        $st = $pos['status'] ?? 'draft';
                        $stLabel = $statusLabels[$st] ?? $st;
                        $stClass = $statusClasses[$st] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300';
                    ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                            <a href="/hr/recruitment/positions/<?= (int)$pos['id'] ?>" class="hover:text-indigo-600 dark:hover:text-indigo-400">
                                <?= htmlspecialchars($pos['title'], ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($pos['department_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium <?= $stClass ?>">
                                <?= htmlspecialchars($stLabel, ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-400"><?= (int)($pos['applicant_count'] ?? 0) ?></td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <a href="/hr/recruitment/positions/<?= (int)$pos['id'] ?>" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline mr-2">Visa</a>
                            <a href="/hr/recruitment/positions/<?= (int)$pos['id'] ?>/edit" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline mr-2">Redigera</a>
                            <form method="POST" action="/hr/recruitment/positions/<?= (int)$pos['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort tjänsten?')">
                                <?= \App\Core\Csrf::field() ?>
                                <button type="submit" class="text-xs text-red-500 hover:text-red-700">Ta bort</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($positions)): ?>
                    <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400 dark:text-gray-500">Inga tjänster registrerade ännu</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
