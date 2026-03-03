<?php
/** @var string $title */
/** @var array $stats */
/** @var array $requests */
/** @var array $filter */
$badge = ['pending'=>'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400','approved'=>'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400','rejected'=>'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400','cancelled'=>'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300'];
$label = ['pending'=>'Väntande','approved'=>'Godkänd','rejected'=>'Avslagen','cancelled'=>'Avbruten'];
?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Frånvaro</h1>
        <div class="flex gap-2">
            <a href="/hr" class="rounded-lg bg-gray-100 dark:bg-gray-700 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">← HR</a>
            <a href="/hr/leave/create" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">+ Ny ansökan</a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-2xl bg-white dark:bg-gray-800 shadow-md p-5 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">Väntande</p>
            <p class="text-2xl font-bold text-amber-600 dark:text-amber-400"><?= $stats['pending_requests'] ?></p>
        </div>
        <div class="rounded-2xl bg-white dark:bg-gray-800 shadow-md p-5 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">Aktiva ledigheter</p>
            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400"><?= $stats['active_leaves'] ?></p>
        </div>
        <div class="rounded-2xl bg-white dark:bg-gray-800 shadow-md p-5 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">Totalt i år</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white"><?= $stats['total_this_year'] ?></p>
        </div>
        <div class="rounded-2xl bg-white dark:bg-gray-800 shadow-md p-5 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">Närvarande idag</p>
            <p class="text-2xl font-bold text-green-600 dark:text-green-400"><?= $stats['present_today'] ?></p>
        </div>
    </div>

    <!-- Filter -->
    <form method="get" class="flex items-center gap-3">
        <select name="status" class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm px-3 py-2 text-gray-700 dark:text-gray-300">
            <option value="">Alla statusar</option>
            <?php foreach ($label as $v => $l): ?>
                <option value="<?= $v ?>" <?= ($filter['status'] ?? '') === $v ? 'selected' : '' ?>><?= $l ?></option>
            <?php endforeach ?>
        </select>
        <button class="rounded-lg bg-indigo-100 dark:bg-indigo-900/30 px-4 py-2 text-sm font-medium text-indigo-700 dark:text-indigo-400 hover:bg-indigo-200 transition-colors">Filtrera</button>
        <?php if (!empty($filter['status'])): ?>
            <a href="/hr/leave" class="text-sm text-gray-500 hover:text-gray-700">Rensa</a>
        <?php endif ?>
    </form>

    <!-- Requests Table -->
    <div class="overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-md">
        <?php if (empty($requests)): ?>
            <p class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">Inga frånvaroansökningar. <a href="/hr/leave/create" class="text-indigo-600 dark:text-indigo-400 hover:underline">Skapa den första.</a></p>
        <?php else: ?>
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Anställd</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Avdelning</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Typ</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Period</th>
                        <th class="px-6 py-3 text-center font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Dagar</th>
                        <th class="px-6 py-3 text-center font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Status</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Godkänd av</th>
                        <th class="px-6 py-3 text-right font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php foreach ($requests as $r): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                        <td class="px-6 py-4">
                            <span class="font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($r['first_name'] . ' ' . $r['last_name']) ?></span><br>
                            <span class="text-xs text-gray-500"><?= htmlspecialchars($r['employee_number']) ?></span>
                        </td>
                        <td class="px-6 py-4 text-gray-500 dark:text-gray-400"><?= htmlspecialchars($r['department_name'] ?? '–') ?></td>
                        <td class="px-6 py-4">
                            <span class="inline-block rounded-full px-2 py-0.5 text-xs font-medium text-white" style="background-color:<?= $r['type_color'] ?>"><?= htmlspecialchars($r['type_name']) ?></span>
                        </td>
                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300"><?= $r['start_date'] ?> – <?= $r['end_date'] ?></td>
                        <td class="px-6 py-4 text-center text-gray-700 dark:text-gray-300"><?= $r['days'] ?></td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-block rounded-full px-2.5 py-0.5 text-xs font-medium <?= $badge[$r['status']] ?? $badge['cancelled'] ?>"><?= $label[$r['status']] ?? $r['status'] ?></span>
                        </td>
                        <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                            <?= $r['approver_first'] ? htmlspecialchars($r['approver_first'] . ' ' . $r['approver_last']) : '–' ?>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <?php if ($r['status'] === 'pending'): ?>
                                <div class="flex justify-end gap-1" x-data>
                                    <form method="post" action="/hr/leave/<?= $r['id'] ?>/approve" class="inline">
                                        <input type="hidden" name="_csrf" value="<?= htmlspecialchars(\App\Core\Csrf::token(), ENT_QUOTES) ?>">
                                        <button class="rounded bg-green-100 dark:bg-green-900/30 px-2 py-1 text-xs font-medium text-green-700 dark:text-green-400 hover:bg-green-200 transition-colors" title="Godkänn">✓</button>
                                    </form>
                                    <button @click="$refs.rejectModal<?= $r['id'] ?>.showModal()" class="rounded bg-red-100 dark:bg-red-900/30 px-2 py-1 text-xs font-medium text-red-700 dark:text-red-400 hover:bg-red-200 transition-colors" title="Avslå">✕</button>
                                    <form method="post" action="/hr/leave/<?= $r['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort?')">
                                        <input type="hidden" name="_csrf" value="<?= htmlspecialchars(\App\Core\Csrf::token(), ENT_QUOTES) ?>">
                                        <button class="rounded bg-gray-100 dark:bg-gray-700 px-2 py-1 text-xs text-gray-500 hover:bg-gray-200 transition-colors" title="Ta bort">🗑</button>
                                    </form>
                                </div>

                                <!-- Reject Dialog -->
                                <dialog x-ref="rejectModal<?= $r['id'] ?>" class="rounded-2xl shadow-xl p-0 backdrop:bg-black/50 max-w-md w-full">
                                    <form method="post" action="/hr/leave/<?= $r['id'] ?>/reject" class="p-6 space-y-4">
                                        <input type="hidden" name="_csrf" value="<?= htmlspecialchars(\App\Core\Csrf::token(), ENT_QUOTES) ?>">
                                        <h3 class="text-lg font-semibold text-gray-900">Avslå ansökan</h3>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Anledning</label>
                                            <textarea name="rejection_reason" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" required></textarea>
                                        </div>
                                        <div class="flex justify-end gap-2">
                                            <button type="button" onclick="this.closest('dialog').close()" class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200">Avbryt</button>
                                            <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Avslå</button>
                                        </div>
                                    </form>
                                </dialog>
                            <?php endif ?>
                        </td>
                    </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        <?php endif ?>
    </div>
</div>
