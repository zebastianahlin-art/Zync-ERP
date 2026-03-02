<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Avtalshantering</h1>
        <a href="/purchasing/agreements/create" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nytt avtal
        </a>
    </div>

    <?php if (!empty($success)): ?>
        <div class="rounded-lg bg-green-50 dark:bg-green-900/30 p-4 text-green-800 dark:text-green-200"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <!-- Utgående avtal varning -->
    <?php if (!empty($expiring)): ?>
    <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-xl p-5">
        <h3 class="font-semibold text-orange-800 dark:text-orange-200 mb-3">⚠️ Avtal som går ut inom 60 dagar</h3>
        <div class="space-y-2">
            <?php foreach ($expiring as $a): ?>
            <a href="/purchasing/agreements/<?= $a['id'] ?>" class="flex justify-between items-center p-2 rounded hover:bg-orange-100 dark:hover:bg-orange-900/40">
                <span class="text-gray-900 dark:text-white"><?= htmlspecialchars($a['title']) ?> — <span class="text-gray-500"><?= htmlspecialchars($a['supplier_name']) ?></span></span>
                <span class="text-sm text-orange-600 font-medium"><?= $a['end_date'] ?></span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Avtalsnr</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Titel</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Leverantör</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Typ</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Status</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Startdatum</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Slutdatum</th>
                    <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400">Värde</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Ansvarig</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($agreements as $a): ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <td class="px-4 py-3"><a href="/purchasing/agreements/<?= $a['id'] ?>" class="text-blue-600 hover:underline font-mono text-xs"><?= htmlspecialchars($a['agreement_number']) ?></a></td>
                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($a['title']) ?></td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($a['supplier_name'] ?? '') ?></td>
                    <td class="px-4 py-3"><?= agrTypeBadge($a['agreement_type']) ?></td>
                    <td class="px-4 py-3"><?= agrStatusBadge($a['status']) ?></td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= $a['start_date'] ?></td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= $a['end_date'] ?: '—' ?></td>
                    <td class="px-4 py-3 text-right font-mono text-gray-900 dark:text-white"><?= $a['value'] ? number_format((float)$a['value'], 0, ',', ' ') . ' ' . $a['currency'] : '—' ?></td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($a['responsible_name'] ?? '—') ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($agreements)): ?>
                <tr><td colspan="9" class="px-4 py-8 text-center text-gray-400">Inga avtal ännu. <a href="/purchasing/agreements/create" class="text-blue-600 hover:underline">Skapa det första →</a></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
function agrTypeBadge(string $t): string {
    $m = ['framework'=>'Ramavtal','project'=>'Projekt','service'=>'Service','standard'=>'Standard',
          'AB04'=>'AB04','ABT06'=>'ABT06','NL10'=>'NL10','NL17'=>'NL17','ABA99'=>'ABA99','other'=>'Övrigt'];
    return '<span class="inline-flex px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">'.($m[$t]??$t).'</span>';
}
function agrStatusBadge(string $s): string {
    $m = [
        'draft' => ['Utkast','bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300'],
        'active' => ['Aktivt','bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'],
        'expired' => ['Utgånget','bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'],
        'terminated' => ['Avslutat','bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'],
    ];
    return '<span class="inline-flex px-2 py-1 rounded-full text-xs font-medium '.($m[$s][1]??'bg-gray-100 text-gray-700').'">'.($m[$s][0]??$s).'</span>';
}
?>
