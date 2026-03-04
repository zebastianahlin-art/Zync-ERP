<?php
$asset = $asset ?? [];
$depreciations = $depreciations ?? [];
$suggestedDepreciation = $suggestedDepreciation ?? 0;
$statusLabels = ['active'=>'Aktiv','disposed'=>'Avyttrad','written_off'=>'Utskriven'];
$statusClasses = ['active'=>'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400','disposed'=>'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400','written_off'=>'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'];
$methodLabels = ['linear'=>'Linjär','declining'=>'Degressiv'];
$sc = $statusClasses[$asset['status'] ?? ''] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400';
?>
<div class="space-y-6">
    <?php if (!empty($success)): ?>
    <div class="rounded-lg bg-green-50 dark:bg-green-900/30 px-4 py-3 text-sm text-green-700 dark:text-green-400"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($asset['name'] ?? '') ?></h1>
            <p class="text-sm text-gray-500 font-mono"><?= htmlspecialchars($asset['asset_number'] ?? '') ?></p>
            <span class="px-3 py-1 rounded-full text-xs font-medium <?= $sc ?>"><?= $statusLabels[$asset['status'] ?? ''] ?? htmlspecialchars($asset['status'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
        </div>
        <div class="flex gap-2">
            <a href="/finance/assets" class="text-sm text-gray-500 hover:text-indigo-600">← Tillbaka</a>
            <a href="/finance/assets/<?= $asset['id'] ?>/edit" class="bg-gray-100 dark:bg-gray-700 px-3 py-2 rounded text-sm hover:bg-gray-200 transition">Redigera</a>
        </div>
    </div>

    <!-- Detaljer -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
        <div><span class="text-gray-500">Inköpsdatum</span><p class="font-medium text-gray-900 dark:text-white"><?= $asset['purchase_date'] ?? '—' ?></p></div>
        <div><span class="text-gray-500">Inköpspris</span><p class="font-mono font-bold text-gray-900 dark:text-white"><?= number_format((float)($asset['purchase_price'] ?? 0), 2, ',', ' ') ?> kr</p></div>
        <div><span class="text-gray-500">Bokfört värde</span><p class="font-mono font-bold text-indigo-600"><?= number_format((float)($asset['current_value'] ?? 0), 2, ',', ' ') ?> kr</p></div>
        <div><span class="text-gray-500">Avskrivningsmetod</span><p class="font-medium"><?= $methodLabels[$asset['depreciation_method'] ?? ''] ?? ($asset['depreciation_method'] ?? '—') ?></p></div>
        <div><span class="text-gray-500">Avskrivningstid</span><p class="font-medium"><?= (int)($asset['depreciation_years'] ?? 0) ?> år</p></div>
        <div><span class="text-gray-500">Avdelning</span><p class="font-medium"><?= htmlspecialchars($asset['department_name'] ?? '—') ?></p></div>
        <div><span class="text-gray-500">Konto</span><p class="font-medium font-mono"><?= htmlspecialchars(($asset['account_number'] ?? '') . ($asset['account_name'] ? ' — ' . $asset['account_name'] : '')) ?></p></div>
        <div><span class="text-gray-500">Skapad av</span><p class="font-medium"><?= htmlspecialchars($asset['created_by_name'] ?? '—') ?></p></div>
    </div>

    <?php if (!empty($asset['description'])): ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Beskrivning</h2>
        <p class="text-gray-600 dark:text-gray-400 text-sm"><?= nl2br(htmlspecialchars($asset['description'])) ?></p>
    </div>
    <?php endif; ?>

    <!-- Avskrivning -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Beräkna avskrivning</h2>
        <form method="POST" action="/finance/assets/<?= $asset['id'] ?>/depreciate" class="flex flex-wrap gap-4 items-end">
            <?= \App\Core\Csrf::field() ?>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Belopp (kr)</label>
                <input type="number" name="amount" step="0.01" value="<?= number_format($suggestedDepreciation, 2, '.', '') ?>" class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm w-40">
                <p class="text-xs text-gray-400 mt-1">Föreslagen årsavskrivning: <?= number_format($suggestedDepreciation, 2, ',', ' ') ?> kr</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Datum</label>
                <input type="date" name="depreciation_date" value="<?= date('Y-m-d') ?>" class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
            </div>
            <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white px-5 py-2 rounded text-sm transition">Bokför avskrivning</button>
        </form>
    </div>

    <!-- Avskrivningshistorik -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Avskrivningshistorik</h2>
        </div>
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-gray-500">Datum</th>
                    <th class="px-4 py-3 text-right text-gray-500">Belopp</th>
                    <th class="px-4 py-3 text-right text-gray-500">Ackumulerat</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php if (empty($depreciations)): ?>
                <tr><td colspan="3" class="px-4 py-8 text-center text-gray-400">Inga avskrivningar bokförda</td></tr>
                <?php else: ?>
                <?php foreach ($depreciations as $dep): ?>
                <tr>
                    <td class="px-4 py-3 text-gray-700 dark:text-gray-300"><?= $dep['depreciation_date'] ?></td>
                    <td class="px-4 py-3 text-right font-mono"><?= number_format((float)$dep['amount'], 2, ',', ' ') ?> kr</td>
                    <td class="px-4 py-3 text-right font-mono text-gray-500"><?= number_format((float)$dep['accumulated'], 2, ',', ' ') ?> kr</td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
