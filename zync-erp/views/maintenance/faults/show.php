<?php
function faultStatusBadgeShow(string $s): string {
    $m = [
        'reported'     => ['Rapporterad','bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300'],
        'acknowledged' => ['Bekräftad','bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300'],
        'assigned'     => ['Tilldelad','bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300'],
        'in_progress'  => ['Pågående','bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300'],
        'resolved'     => ['Löst','bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'],
        'closed'       => ['Stängd','bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'],
    ];
    return '<span class="inline-flex px-2 py-1 rounded-full text-xs font-medium '.($m[$s][1]??'bg-gray-100 text-gray-700').'">'.($m[$s][0]??$s).'</span>';
}
?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="/maintenance/faults" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($fault['fault_number'], ENT_QUOTES, 'UTF-8') ?></h1>
            <?= faultStatusBadgeShow($fault['status']) ?>
        </div>
        <div class="flex gap-2 flex-wrap">
            <?php if ($fault['status'] === 'reported'): ?>
            <form method="POST" action="/maintenance/faults/<?= $fault['id'] ?>/acknowledge" class="inline">
                <?= \App\Core\Csrf::field() ?>
                <button type="submit" class="px-3 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">Bekräfta</button>
            </form>
            <?php endif; ?>

            <?php if (in_array($fault['status'], ['reported','acknowledged'])): ?>
            <div x-data="{ open: false }" class="relative">
                <button @click="open=!open" class="px-3 py-2 text-sm bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">Tilldela</button>
                <div x-show="open" @click.outside="open=false" class="absolute right-0 mt-2 bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4 z-10 min-w-64">
                    <form method="POST" action="/maintenance/faults/<?= $fault['id'] ?>/assign">
                        <?= \App\Core\Csrf::field() ?>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Välj tekniker</label>
                        <select name="assigned_to" required class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm mb-3">
                            <option value="">—</option>
                            <?php foreach ($users as $u): ?>
                            <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['full_name'], ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded text-sm">Tilldela</button>
                    </form>
                </div>
            </div>
            <?php endif; ?>

            <?php if (in_array($fault['status'], ['reported','acknowledged','assigned']) && !$fault['work_order_id']): ?>
            <form method="POST" action="/maintenance/faults/<?= $fault['id'] ?>/convert" class="inline">
                <?= \App\Core\Csrf::field() ?>
                <button type="submit" class="px-3 py-2 text-sm bg-green-600 hover:bg-green-700 text-white rounded-lg transition">→ Skapa arbetsorder</button>
            </form>
            <?php endif; ?>

            <a href="/maintenance/faults/<?= $fault['id'] ?>/edit" class="px-3 py-2 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">Redigera</a>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4"><?= htmlspecialchars($fault['title'], ENT_QUOTES, 'UTF-8') ?></h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
            <div><span class="text-gray-500 dark:text-gray-400">Maskin:</span> <span class="ml-1 text-gray-900 dark:text-white"><?= htmlspecialchars($fault['machine_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></div>
            <div><span class="text-gray-500 dark:text-gray-400">Utrustning:</span> <span class="ml-1 text-gray-900 dark:text-white"><?= htmlspecialchars($fault['equipment_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></div>
            <div><span class="text-gray-500 dark:text-gray-400">Feltyp:</span> <span class="ml-1 text-gray-900 dark:text-white"><?= htmlspecialchars($fault['fault_type'], ENT_QUOTES, 'UTF-8') ?></span></div>
            <div><span class="text-gray-500 dark:text-gray-400">Prioritet:</span> <span class="ml-1 text-gray-900 dark:text-white"><?= htmlspecialchars($fault['priority'], ENT_QUOTES, 'UTF-8') ?></span></div>
            <div><span class="text-gray-500 dark:text-gray-400">Rapporterad av:</span> <span class="ml-1 text-gray-900 dark:text-white"><?= htmlspecialchars($fault['reported_by_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></div>
            <div><span class="text-gray-500 dark:text-gray-400">Tilldelad:</span> <span class="ml-1 text-gray-900 dark:text-white"><?= htmlspecialchars($fault['assigned_to_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></div>
            <div><span class="text-gray-500 dark:text-gray-400">Avdelning:</span> <span class="ml-1 text-gray-900 dark:text-white"><?= htmlspecialchars($fault['department_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></div>
            <div><span class="text-gray-500 dark:text-gray-400">Plats:</span> <span class="ml-1 text-gray-900 dark:text-white"><?= htmlspecialchars($fault['location'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></div>
            <div><span class="text-gray-500 dark:text-gray-400">Skapad:</span> <span class="ml-1 text-gray-900 dark:text-white"><?= htmlspecialchars(substr($fault['created_at'], 0, 10), ENT_QUOTES, 'UTF-8') ?></span></div>
            <?php if ($fault['work_order_id']): ?>
            <div class="sm:col-span-3"><span class="text-gray-500 dark:text-gray-400">Arbetsorder:</span> <a href="/maintenance/work-orders/<?= $fault['work_order_id'] ?>" class="ml-1 text-indigo-600 dark:text-indigo-400 hover:underline">Visa arbetsorder →</a></div>
            <?php endif; ?>
        </div>
        <?php if (!empty($fault['description'])): ?>
        <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg text-sm text-gray-700 dark:text-gray-300">
            <?= nl2br(htmlspecialchars($fault['description'], ENT_QUOTES, 'UTF-8')) ?>
        </div>
        <?php endif; ?>
    </div>
</div>
