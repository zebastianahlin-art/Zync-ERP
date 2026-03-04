<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Rekrytering</h1>
        <div class="flex gap-3">
            <a href="/hr/recruitment/applicants" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-sm font-medium rounded-lg transition">Alla sökande</a>
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
                    <?php foreach ($positions as $pos): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                            <a href="/hr/recruitment/positions/<?= $pos['id'] ?>" class="hover:text-indigo-600 dark:hover:text-indigo-400">
                                <?= htmlspecialchars($pos['title'], ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($pos['department_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3"><?= htmlspecialchars($pos['status'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-right"><?= (int) ($pos['applicant_count'] ?? 0) ?></td>
                        <td class="px-4 py-3 text-right">
                            <a href="/hr/recruitment/positions/<?= $pos['id'] ?>" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Visa</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($positions)): ?>
                    <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">Inga tjänster registrerade ännu</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
