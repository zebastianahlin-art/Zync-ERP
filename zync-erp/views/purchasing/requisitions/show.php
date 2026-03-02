<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="/purchasing/requisitions" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($requisition['requisition_number']) ?></h1>
            <?= reqShowStatus($requisition['status']) ?>
        </div>
        <div class="flex gap-2">
            <?php if (in_array($requisition['status'], ['draft'])): ?>
                <a href="/purchasing/requisitions/<?= $requisition['id'] ?>/edit" class="px-3 py-2 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">Redigera</a>
                <form method="POST" action="/purchasing/requisitions/<?= $requisition['id'] ?>/submit" class="inline">
                    <?= \App\Core\Csrf::field() ?>
                    <button type="submit" class="px-3 py-2 text-sm bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition">Skicka för godkännande</button>
                </form>
            <?php endif; ?>
            <?php if ($requisition['status'] === 'pending_approval'): ?>
                <form method="POST" action="/purchasing/requisitions/<?= $requisition['id'] ?>/approve" class="inline">
                    <?= \App\Core\Csrf::field() ?>
                    <button type="submit" class="px-3 py-2 text-sm bg-green-600 hover:bg-green-700 text-white rounded-lg transition">Godkänn</button>
                </form>
                <form method="POST" action="/purchasing/requisitions/<?= $requisition['id'] ?>/reject" class="inline" x-data="{ open: false }">
                    <?= \App\Core\Csrf::field() ?>
                    <button type="button" @click="open = !open" class="px-3 py-2 text-sm bg-red-600 hover:bg-red-700 text-white rounded-lg transition">Avvisa</button>
                    <div x-show="open" class="absolute mt-2 bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4 z-10">
                        <textarea name="reason" rows="2" placeholder="Anledning..." class="w-64 rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"></textarea>
                        <button type="submit" class="mt-2 w-full bg-red-600 text-white py-1 rounded text-sm">Bekräfta avvisning</button>
                    </div>
                </form>
            <?php endif; ?>
            <?php if ($requisition['status'] === 'approved'): ?>
                <form method="POST" action="/purchasing/requisitions/<?= $requisition['id'] ?>/convert" class="inline">
                    <?= \App\Core\Csrf::field() ?>
                    <button type="submit" class="px-3 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">Skapa inköpsorder →</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($success)): ?>
        <div class="rounded-lg bg-green-50 dark:bg-green-900/30 p-4 text-green-800 dark:text-green-200"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="rounded-lg bg-red-50 dark:bg-red-900/30 p-4 text-red-800 dark:text-red-200"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Info -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4"><?= htmlspecialchars($requisition['title']) ?></h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
            <div><span class="text-gray-500 dark:text-gray-400">Begärd av:</span> <span class="text-gray-900 dark:text-white ml-1"><?= htmlspecialchars($requisition['requested_by_name'] ?? '') ?></span></div>
            <div><span class="text-gray-500 dark:text-gray-400">Avdelning:</span> <span class="text-gray-900 dark:text-white ml-1"><?= htmlspecialchars($requisition['department_name'] ?? '—') ?></span></div>
            <div><span class="text-gray-500 dark:text-gray-400">Prioritet:</span> <span class="ml-1"><?= prioBadge($requisition['priority']) ?></span></div>
            <div><span class="text-gray-500 dark:text-gray-400">Behövs senast:</span> <span class="text-gray-900 dark:text-white ml-1"><?= $requisition['needed_by'] ?: '—' ?></span></div>
            <div><span class="text-gray-500 dark:text-gray-400">Totalt belopp:</span> <span class="text-gray-900 dark:text-white ml-1 font-semibold"><?= number_format((float)$requisition['total_amount'], 0, ',', ' ') ?> kr</span></div>
            <div><span class="text-gray-500 dark:text-gray-400">Skapad:</span> <span class="text-gray-900 dark:text-white ml-1"><?= $requisition['created_at'] ?></span></div>
        </div>
        <?php if (!empty($requisition['description'])): ?>
        <div class="mt-4 text-sm text-gray-700 dark:text-gray-300"><?= nl2br(htmlspecialchars($requisition['description'])) ?></div>
        <?php endif; ?>
        <?php if ($requisition['status'] === 'rejected' && !empty($requisition['rejected_reason'])): ?>
        <div class="mt-4 p-3 bg-red-50 dark:bg-red-900/20 rounded-lg text-sm text-red-800 dark:text-red-200">
            <strong>Avvisad av <?= htmlspecialchars($requisition['approved_by_name'] ?? '') ?>:</strong> <?= htmlspecialchars($requisition['rejected_reason']) ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Rader -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow">
        <div class="p-5 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Artikelrader</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Artikel</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Beskrivning</th>
                        <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400">Antal</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Enhet</th>
                        <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400">Pris (est.)</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Leverantör</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Konto</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">KS</th>
                        <?php if ($requisition['status'] === 'draft'): ?><th class="px-4 py-3"></th><?php endif; ?>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($lines as $l): ?>
                    <tr>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400 font-mono text-xs"><?= htmlspecialchars($l['article_number'] ?? '—') ?></td>
                        <td class="px-4 py-3 text-gray-900 dark:text-white"><?= htmlspecialchars($l['description']) ?></td>
                        <td class="px-4 py-3 text-right text-gray-900 dark:text-white"><?= rtrim(rtrim(number_format((float)$l['quantity'], 3, ',', ' '), '0'), ',') ?></td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($l['unit']) ?></td>
                        <td class="px-4 py-3 text-right text-gray-900 dark:text-white"><?= number_format((float)$l['estimated_price'], 2, ',', ' ') ?> kr</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($l['supplier_name'] ?? '—') ?></td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400 font-mono text-xs"><?= htmlspecialchars(($l['account_number'] ?? '') ? $l['account_number'] . ' ' . $l['account_name'] : '—') ?></td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400 font-mono text-xs"><?= htmlspecialchars(($l['cost_center_code'] ?? '') ? $l['cost_center_code'] . ' ' . $l['cost_center_name'] : '—') ?></td>
                        <?php if ($requisition['status'] === 'draft'): ?>
                        <td class="px-4 py-3">
                            <form method="POST" action="/purchasing/requisitions/<?= $requisition['id'] ?>/lines/<?= $l['id'] ?>/delete" class="inline">
                                <?= \App\Core\Csrf::field() ?>
                                <button type="submit" class="text-red-500 hover:text-red-700 text-xs">Ta bort</button>
                            </form>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($lines)): ?>
                    <tr><td colspan="9" class="px-4 py-6 text-center text-gray-400">Inga rader tillagda ännu</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Lägg till rad -->
        <?php if ($requisition['status'] === 'draft'): ?>
        <div class="p-5 border-t border-gray-200 dark:border-gray-700">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Lägg till rad</h3>
            <form method="POST" action="/purchasing/requisitions/<?= $requisition['id'] ?>/lines" class="grid grid-cols-1 sm:grid-cols-8 gap-3 items-end">
                <?= \App\Core\Csrf::field() ?>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Artikel</label>
                    <select name="article_id" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm" onchange="fillArticle(this)">
                        <option value="">— Fritext —</option>
                        <?php foreach ($articles as $a): ?>
                        <option value="<?= $a['id'] ?>" data-name="<?= htmlspecialchars($a['name']) ?>" data-unit="<?= $a['unit'] ?>" data-price="<?= $a['purchase_price'] ?>"><?= htmlspecialchars($a['article_number'] . ' — ' . $a['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Beskrivning *</label>
                    <input type="text" name="description" id="line-desc" required class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Antal</label>
                    <input type="number" name="quantity" value="1" step="0.001" min="0" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Pris (est.)</label>
                    <input type="number" name="estimated_price" id="line-price" value="0" step="0.01" min="0" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Leverantör</label>
                    <select name="supplier_id" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        <option value="">—</option>
                        <?php foreach ($suppliers as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Konto</label>
                    <select name="account_id" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        <option value="">—</option>
                        <?php foreach ($accounts as $acc): ?>
                        <option value="<?= $acc['id'] ?>"><?= htmlspecialchars($acc['account_number'] . ' ' . $acc['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">KS</label>
                    <select name="cost_center_id" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        <option value="">—</option>
                        <?php foreach ($costCenters as $cc): ?>
                        <option value="<?= $cc['id'] ?>"><?= htmlspecialchars($cc['code'] . ' ' . $cc['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-3 rounded text-sm font-medium transition">Lägg till</button>
                </div>
            </form>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function fillArticle(sel) {
    const opt = sel.options[sel.selectedIndex];
    if (opt.value) {
        document.getElementById('line-desc').value = opt.dataset.name || '';
        document.getElementById('line-price').value = opt.dataset.price || '0';
    }
}
</script>

<?php
function reqShowStatus(string $s): string {
    $m = [
        'draft' => ['Utkast','bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300'],
        'pending_approval' => ['Väntar godkännande','bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300'],
        'approved' => ['Godkänd','bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'],
        'rejected' => ['Avvisad','bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'],
        'ordered' => ['Beställd','bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300'],
        'closed' => ['Stängd','bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'],
    ];
    return '<span class="inline-flex px-2 py-1 rounded-full text-xs font-medium '.($m[$s][1]??'bg-gray-100 text-gray-700').'">'.($m[$s][0]??$s).'</span>';
}
function prioBadge(string $p): string {
    $m = ['low'=>['Låg','text-gray-600'],'normal'=>['Normal','text-blue-600'],'high'=>['Hög','text-orange-600'],'urgent'=>['Brådskande','text-red-600']];
    return '<span class="font-medium '.($m[$p][1]??'text-gray-600').'">'.($m[$p][0]??$p).'</span>';
}
?>
