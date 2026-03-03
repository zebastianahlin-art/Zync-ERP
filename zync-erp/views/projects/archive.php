<?php
$statusLabels = ['completed'=>'Avslutad','cancelled'=>'Avbruten'];
$catLabels = ['internal'=>'Internt','customer'=>'Kund','maintenance'=>'Underhåll','development'=>'Utveckling','other'=>'Övrigt'];
?>

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <h1 class="text-2xl font-bold">📁 Projektarkiv — Avslutade projekt</h1>
    <a href="/projects" class="px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">← Tillbaka till projekt</a>
</div>

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-900/50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Projekt</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kund</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Period</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Timmar</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Budget</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Utfall</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Betyg</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            <?php if (empty($projects)): ?>
            <tr><td colspan="8" class="px-4 py-8 text-center text-gray-500">Inga avslutade projekt.</td></tr>
            <?php endif; ?>
            <?php foreach ($projects as $p): ?>
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                <td class="px-4 py-3">
                    <a href="/projects/<?= $p['id'] ?>" class="font-medium text-indigo-600 dark:text-indigo-400 hover:underline"><?= htmlspecialchars($p['project_number']) ?></a>
                    <p class="text-sm text-gray-500"><?= htmlspecialchars($p['name']) ?></p>
                </td>
                <td class="px-4 py-3 text-sm"><?= $catLabels[$p['category']] ?? $p['category'] ?></td>
                <td class="px-4 py-3 text-sm"><?= htmlspecialchars($p['customer_name'] ?? '—') ?></td>
                <td class="px-4 py-3 text-sm"><?= $p['start_date'] ?? '?' ?> → <?= $p['actual_end_date'] ?? $p['end_date'] ?? '?' ?></td>
                <td class="px-4 py-3 text-sm"><?= number_format($p['actual_hours'], 1) ?> / <?= number_format($p['budget_hours'], 0) ?>h</td>
                <td class="px-4 py-3 text-sm"><?= number_format($p['budget_total'], 0, ',', ' ') ?> kr</td>
                <td class="px-4 py-3 text-sm"><?= number_format($p['actual_cost'], 0, ',', ' ') ?> kr</td>
                <td class="px-4 py-3 text-sm">
                    <?php if ($p['evaluation_score']): ?>
                        <?= str_repeat('⭐', $p['evaluation_score']) ?>
                    <?php else: ?>
                        <span class="text-gray-400">—</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
