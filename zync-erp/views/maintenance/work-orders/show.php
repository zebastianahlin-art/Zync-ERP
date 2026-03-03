<?php
function woStatusBadgeShow(string $s): string {
    $m = [
        'reported'         => ['Rapporterad','bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300'],
        'assigned'         => ['Tilldelad','bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300'],
        'in_progress'      => ['Pågående','bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300'],
        'work_completed'   => ['Arbete utfört','bg-teal-100 text-teal-800 dark:bg-teal-900/30 dark:text-teal-300'],
        'pending_approval' => ['Väntar attestering','bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300'],
        'approved'         => ['Attesterad','bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'],
        'rejected'         => ['Avvisad','bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'],
        'closed'           => ['Avslutad','bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'],
        'archived'         => ['Arkiverad','bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-500'],
    ];
    return '<span class="inline-flex px-2 py-1 rounded-full text-xs font-medium '.($m[$s][1]??'bg-gray-100 text-gray-700').'">'.($m[$s][0]??$s).'</span>';
}
$isSupervisor = ($currentUser['role_level'] ?? 0) >= 5;
$canEdit = in_array($wo['status'], ['reported','assigned','in_progress']);
$canLogTime = in_array($wo['status'], ['assigned','in_progress','work_completed']);
$canComplete = $wo['status'] === 'in_progress' && $wo['assigned_to'] == $currentUser['id'];
$canApprove = $isSupervisor && in_array($wo['status'], ['work_completed','pending_approval']);
?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-3">
            <a href="/maintenance/work-orders" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($wo['order_number'], ENT_QUOTES, 'UTF-8') ?></h1>
            <?= woStatusBadgeShow($wo['status']) ?>
        </div>
        <div class="flex gap-2 flex-wrap">
            <?php if ($wo['status'] === 'assigned' && ($isSupervisor || $wo['assigned_to'] == $currentUser['id'])): ?>
            <form method="POST" action="/maintenance/work-orders/<?= $wo['id'] ?>/start" class="inline">
                <?= \App\Core\Csrf::field() ?>
                <button type="submit" class="px-3 py-2 text-sm bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition">▶ Starta</button>
            </form>
            <?php endif; ?>

            <?php if ($canComplete): ?>
            <div x-data="{ open: false }" class="relative">
                <button @click="open=!open" class="px-3 py-2 text-sm bg-teal-600 hover:bg-teal-700 text-white rounded-lg transition">✓ Arbete utfört</button>
                <div x-show="open" @click.outside="open=false" class="absolute right-0 mt-2 bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4 z-10 min-w-72">
                    <form method="POST" action="/maintenance/work-orders/<?= $wo['id'] ?>/complete">
                        <?= \App\Core\Csrf::field() ?>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Slutkommentar</label>
                        <textarea name="completion_notes" rows="3" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm mb-3"></textarea>
                        <button type="submit" class="w-full bg-teal-600 text-white py-2 rounded text-sm">Markera som utfört</button>
                    </form>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($canApprove): ?>
            <div x-data="{ showApprove: false, showReject: false }" class="flex gap-2">
                <button @click="showApprove=!showApprove" class="px-3 py-2 text-sm bg-green-600 hover:bg-green-700 text-white rounded-lg transition">✓ Godkänn</button>
                <div x-show="showApprove" @click.outside="showApprove=false" class="absolute mt-10 bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4 z-20 min-w-72">
                    <form method="POST" action="/maintenance/work-orders/<?= $wo['id'] ?>/approve">
                        <?= \App\Core\Csrf::field() ?>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Kommentar (valfri)</label>
                        <textarea name="approval_notes" rows="2" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm mb-3"></textarea>
                        <button type="submit" class="w-full bg-green-600 text-white py-2 rounded text-sm">Bekräfta godkännande</button>
                    </form>
                </div>
                <button @click="showReject=!showReject" class="px-3 py-2 text-sm bg-red-600 hover:bg-red-700 text-white rounded-lg transition">✗ Avvisa</button>
                <div x-show="showReject" @click.outside="showReject=false" class="absolute mt-10 bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4 z-20 min-w-72">
                    <form method="POST" action="/maintenance/work-orders/<?= $wo['id'] ?>/reject">
                        <?= \App\Core\Csrf::field() ?>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Anledning *</label>
                        <textarea name="rejected_reason" rows="2" required class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm mb-3"></textarea>
                        <button type="submit" class="w-full bg-red-600 text-white py-2 rounded text-sm">Bekräfta avvisning</button>
                    </form>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($isSupervisor && $wo['status'] === 'approved'): ?>
            <form method="POST" action="/maintenance/work-orders/<?= $wo['id'] ?>/close" class="inline">
                <?= \App\Core\Csrf::field() ?>
                <button type="submit" class="px-3 py-2 text-sm bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">Stäng order</button>
            </form>
            <?php endif; ?>

            <?php if ($canEdit): ?>
            <a href="/maintenance/work-orders/<?= $wo['id'] ?>/edit" class="px-3 py-2 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">Redigera</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Details card -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4"><?= htmlspecialchars($wo['title'], ENT_QUOTES, 'UTF-8') ?></h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
            <div><span class="text-gray-500 dark:text-gray-400">Typ:</span> <span class="ml-1 text-gray-900 dark:text-white"><?= htmlspecialchars($wo['work_type'], ENT_QUOTES, 'UTF-8') ?></span></div>
            <div><span class="text-gray-500 dark:text-gray-400">Prioritet:</span> <span class="ml-1 text-gray-900 dark:text-white"><?= htmlspecialchars($wo['priority'], ENT_QUOTES, 'UTF-8') ?></span></div>
            <div><span class="text-gray-500 dark:text-gray-400">Maskin:</span> <span class="ml-1 text-gray-900 dark:text-white"><?= htmlspecialchars($wo['machine_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></div>
            <div><span class="text-gray-500 dark:text-gray-400">Utrustning:</span> <span class="ml-1 text-gray-900 dark:text-white"><?= htmlspecialchars($wo['equipment_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></div>
            <div><span class="text-gray-500 dark:text-gray-400">Tilldelad:</span> <span class="ml-1 text-gray-900 dark:text-white"><?= htmlspecialchars($wo['assigned_to_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></div>
            <div><span class="text-gray-500 dark:text-gray-400">Avdelning:</span> <span class="ml-1 text-gray-900 dark:text-white"><?= htmlspecialchars($wo['department_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></div>
            <div><span class="text-gray-500 dark:text-gray-400">Est. timmar:</span> <span class="ml-1 text-gray-900 dark:text-white"><?= htmlspecialchars($wo['estimated_hours'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></div>
            <div><span class="text-gray-500 dark:text-gray-400">Tot. timmar:</span> <span class="ml-1 font-semibold text-gray-900 dark:text-white"><?= htmlspecialchars($wo['total_hours'], ENT_QUOTES, 'UTF-8') ?></span></div>
            <div><span class="text-gray-500 dark:text-gray-400">Materialkostnad:</span> <span class="ml-1 font-semibold text-gray-900 dark:text-white"><?= number_format((float)$wo['total_material_cost'], 0, ',', ' ') ?> kr</span></div>
        </div>
        <?php if (!empty($wo['description'])): ?>
        <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg text-sm text-gray-700 dark:text-gray-300">
            <?= nl2br(htmlspecialchars($wo['description'], ENT_QUOTES, 'UTF-8')) ?>
        </div>
        <?php endif; ?>
        <?php if (!empty($wo['completion_notes'])): ?>
        <div class="mt-3 p-3 bg-teal-50 dark:bg-teal-900/20 rounded-lg text-sm text-teal-800 dark:text-teal-200">
            <strong>Slutkommentar:</strong> <?= nl2br(htmlspecialchars($wo['completion_notes'], ENT_QUOTES, 'UTF-8')) ?>
        </div>
        <?php endif; ?>
        <?php if ($wo['status'] === 'rejected' && !empty($wo['rejected_reason'])): ?>
        <div class="mt-3 p-3 bg-red-50 dark:bg-red-900/20 rounded-lg text-sm text-red-800 dark:text-red-200">
            <strong>Avvisad:</strong> <?= nl2br(htmlspecialchars($wo['rejected_reason'], ENT_QUOTES, 'UTF-8')) ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Time entries -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow">
        <div class="p-5 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h2 class="font-semibold text-gray-900 dark:text-white">Tidsregistrering</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Datum</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Tekniker</th>
                        <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400">Timmar</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Beskrivning</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Status</th>
                        <?php if ($isSupervisor): ?><th class="px-4 py-3"></th><?php endif; ?>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($timeEntries as $te): ?>
                    <tr>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($te['work_date'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-gray-900 dark:text-white"><?= htmlspecialchars($te['user_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-right text-gray-900 dark:text-white font-medium"><?= htmlspecialchars($te['hours'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($te['description'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3">
                            <?php if ($te['is_approved']): ?>
                            <span class="text-xs text-green-600 dark:text-green-400">✓ Godkänd</span>
                            <?php else: ?>
                            <span class="text-xs text-gray-400">Ej godkänd</span>
                            <?php endif; ?>
                        </td>
                        <?php if ($isSupervisor): ?>
                        <td class="px-4 py-3">
                            <?php if (!$te['is_approved']): ?>
                            <form method="POST" action="/maintenance/work-orders/<?= $wo['id'] ?>/approve-time/<?= $te['id'] ?>" class="inline">
                                <?= \App\Core\Csrf::field() ?>
                                <button type="submit" class="text-xs text-green-600 hover:text-green-800">Godkänn</button>
                            </form>
                            <?php endif; ?>
                        </td>
                        <?php endif; ?>
                        <td class="px-4 py-3">
                            <?php if (!$te['is_approved'] && ($te['user_id'] == $currentUser['id'] || $isSupervisor)): ?>
                            <form method="POST" action="/maintenance/work-orders/<?= $wo['id'] ?>/time/<?= $te['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort?')">
                                <?= \App\Core\Csrf::field() ?>
                                <button type="submit" class="text-xs text-red-500 hover:text-red-700">Ta bort</button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($timeEntries)): ?>
                    <tr><td colspan="<?= $isSupervisor ? 7 : 6 ?>" class="px-4 py-4 text-center text-gray-400 text-sm">Inga tidsposter</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Log time form -->
        <?php if ($canLogTime): ?>
        <div class="p-5 border-t border-gray-200 dark:border-gray-700">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Logga tid</h3>
            <form method="POST" action="/maintenance/work-orders/<?= $wo['id'] ?>/time" class="grid grid-cols-1 sm:grid-cols-5 gap-3 items-end">
                <?= \App\Core\Csrf::field() ?>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Datum *</label>
                    <input type="date" name="work_date" value="<?= date('Y-m-d') ?>" required class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Timmar *</label>
                    <input type="number" name="hours" step="0.5" min="0.5" max="24" required class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs text-gray-500 mb-1">Beskrivning</label>
                    <input type="text" name="description" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-3 rounded text-sm font-medium transition">Logga tid</button>
                </div>
            </form>
        </div>
        <?php endif; ?>
    </div>

    <!-- Parts -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow">
        <div class="p-5 border-b border-gray-200 dark:border-gray-700">
            <h2 class="font-semibold text-gray-900 dark:text-white">Material & Reservdelar</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Artikel</th>
                        <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400">Antal</th>
                        <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400">À-pris</th>
                        <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400">Totalt</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Status</th>
                        <?php if ($isSupervisor): ?><th class="px-4 py-3"></th><?php endif; ?>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($parts as $p): ?>
                    <tr>
                        <td class="px-4 py-3 text-gray-900 dark:text-white"><?= htmlspecialchars($p['article_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-right text-gray-900 dark:text-white"><?= htmlspecialchars($p['quantity'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-right text-gray-900 dark:text-white"><?= number_format((float)$p['unit_price'], 2, ',', ' ') ?> kr</td>
                        <td class="px-4 py-3 text-right font-medium text-gray-900 dark:text-white"><?= number_format((float)$p['total_price'], 2, ',', ' ') ?> kr</td>
                        <td class="px-4 py-3">
                            <?php if ($p['is_approved']): ?>
                            <span class="text-xs text-green-600 dark:text-green-400">✓ Godkänd</span>
                            <?php else: ?>
                            <span class="text-xs text-gray-400">Ej godkänd</span>
                            <?php endif; ?>
                        </td>
                        <?php if ($isSupervisor): ?>
                        <td class="px-4 py-3">
                            <?php if (!$p['is_approved']): ?>
                            <form method="POST" action="/maintenance/work-orders/<?= $wo['id'] ?>/approve-part/<?= $p['id'] ?>" class="inline">
                                <?= \App\Core\Csrf::field() ?>
                                <button type="submit" class="text-xs text-green-600 hover:text-green-800">Godkänn</button>
                            </form>
                            <?php endif; ?>
                        </td>
                        <?php endif; ?>
                        <td class="px-4 py-3">
                            <?php if (!$p['is_approved'] && $isSupervisor): ?>
                            <form method="POST" action="/maintenance/work-orders/<?= $wo['id'] ?>/parts/<?= $p['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort?')">
                                <?= \App\Core\Csrf::field() ?>
                                <button type="submit" class="text-xs text-red-500 hover:text-red-700">Ta bort</button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($parts)): ?>
                    <tr><td colspan="<?= $isSupervisor ? 7 : 6 ?>" class="px-4 py-4 text-center text-gray-400 text-sm">Inget material registrerat</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Add part form -->
        <?php if ($canLogTime): ?>
        <div class="p-5 border-t border-gray-200 dark:border-gray-700">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Lägg till material</h3>
            <form method="POST" action="/maintenance/work-orders/<?= $wo['id'] ?>/parts" class="grid grid-cols-1 sm:grid-cols-5 gap-3 items-end">
                <?= \App\Core\Csrf::field() ?>
                <div class="sm:col-span-2">
                    <label class="block text-xs text-gray-500 mb-1">Artikel</label>
                    <select name="article_id" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm" onchange="fillPartPrice(this)">
                        <option value="">— Fritext —</option>
                        <?php foreach ($articles as $a): ?>
                        <option value="<?= $a['id'] ?>" data-price="<?= $a['purchase_price'] ?>"><?= htmlspecialchars($a['article_number'] . ' — ' . $a['name'], ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Antal *</label>
                    <input type="number" name="quantity" value="1" step="0.001" min="0.001" required class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">À-pris</label>
                    <input type="number" name="unit_price" id="part-price" value="0" step="0.01" min="0" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-3 rounded text-sm font-medium transition">Lägg till</button>
                </div>
            </form>
        </div>
        <?php endif; ?>

        <!-- Approve all button for supervisors -->
        <?php
        $hasUnapprovedTime = count(array_filter($timeEntries, fn($t) => !$t['is_approved'])) > 0;
        $hasUnapprovedParts = count(array_filter($parts, fn($p) => !$p['is_approved'])) > 0;
        if ($isSupervisor && ($hasUnapprovedTime || $hasUnapprovedParts)):
        ?>
        <div class="p-5 border-t border-gray-200 dark:border-gray-700">
            <form method="POST" action="/maintenance/work-orders/<?= $wo['id'] ?>/approve-all" class="inline">
                <?= \App\Core\Csrf::field() ?>
                <button type="submit" class="px-4 py-2 text-sm bg-green-600 hover:bg-green-700 text-white rounded-lg transition">✓ Godkänn alla rader</button>
            </form>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function fillPartPrice(sel) {
    const opt = sel.options[sel.selectedIndex];
    const priceInput = document.getElementById('part-price');
    if (opt.value && priceInput) {
        priceInput.value = opt.dataset.price || '0';
    }
}
</script>
