<div class="space-y-6">
    <div class="flex items-center gap-3">
        <a href="/maintenance/supervisor" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Väntar attestering</h1>
    </div>

    <?php if (!empty($success)): ?>
        <div class="rounded-lg bg-green-50 dark:bg-green-900/30 p-4 text-green-800 dark:text-green-200"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="rounded-lg bg-red-50 dark:bg-red-900/30 p-4 text-red-800 dark:text-red-200"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Nummer</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Titel</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Tilldelad till</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Slutförd</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Prioritet</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Attest</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($workOrders ?? [] as $wo): ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 align-top">
                    <td class="px-4 py-3">
                        <a href="/maintenance/work-orders/<?= (int)$wo['id'] ?>" class="text-blue-600 hover:underline font-mono text-xs"><?= htmlspecialchars($wo['order_number'] ?? '', ENT_QUOTES, 'UTF-8') ?></a>
                    </td>
                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($wo['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($wo['assigned_to_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400"><?= htmlspecialchars(!empty($wo['completed_at']) ? date('Y-m-d', strtotime($wo['completed_at'])) : '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3"><?= pendingApprovalPriorityBadge($wo['priority'] ?? 'normal') ?></td>
                    <td class="px-4 py-3">
                        <div class="flex flex-col gap-2" x-data="{ showReject: false }">
                            <!-- Attestera -->
                            <form method="POST" action="/maintenance/work-orders/<?= (int)$wo['id'] ?>/approve">
                                <?= \App\Core\Csrf::field() ?>
                                <button type="submit"
                                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-lg text-xs font-medium transition whitespace-nowrap">
                                    Attestera
                                </button>
                            </form>
                            <!-- Avvisa toggle -->
                            <div>
                                <button type="button" @click="showReject = !showReject"
                                    class="bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/40 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-700 px-3 py-1 rounded-lg text-xs font-medium transition whitespace-nowrap">
                                    Avvisa
                                </button>
                                <div x-show="showReject" x-cloak class="mt-2">
                                    <form method="POST" action="/maintenance/work-orders/<?= (int)$wo['id'] ?>/reject" class="flex flex-col gap-1">
                                        <?= \App\Core\Csrf::field() ?>
                                        <input type="text" name="rejected_reason" required placeholder="Ange orsak…"
                                            class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-red-500 w-48">
                                        <button type="submit"
                                            class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-lg text-xs font-medium transition self-start">
                                            Bekräfta avvisning
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($workOrders)): ?>
                <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400 dark:text-gray-500">Inga arbetsordrar väntar attestering</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
function pendingApprovalPriorityBadge(string $p): string {
    $m = [
        'low'      => ['Låg',        'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'],
        'normal'   => ['Normal',     'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'],
        'high'     => ['Hög',        'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300'],
        'urgent'   => ['Brådskande', 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'],
        'critical' => ['Kritisk',    'bg-red-200 text-red-900 dark:bg-red-900/50 dark:text-red-200 font-bold'],
    ];
    $label = $m[$p][0] ?? $p;
    $class = $m[$p][1] ?? 'bg-gray-100 text-gray-700';
    return "<span class=\"inline-flex px-2 py-1 rounded-full text-xs font-medium {$class}\">" . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . "</span>";
}
?>
