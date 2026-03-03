<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="/maintenance/work-orders" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Arbetsorderarkiv</h1>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Nummer</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Titel</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Maskin</th>
                        <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400">Timmar</th>
                        <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400">Kostnad</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Arkiverad</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($workOrders as $wo): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 font-mono text-xs text-indigo-600 dark:text-indigo-400">
                            <a href="/maintenance/work-orders/archive/<?= $wo['id'] ?>"><?= htmlspecialchars($wo['order_number'], ENT_QUOTES, 'UTF-8') ?></a>
                        </td>
                        <td class="px-4 py-3 text-gray-900 dark:text-white">
                            <a href="/maintenance/work-orders/archive/<?= $wo['id'] ?>" class="hover:underline"><?= htmlspecialchars($wo['title'], ENT_QUOTES, 'UTF-8') ?></a>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($wo['machine_name'] ?? $wo['equipment_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-right text-gray-900 dark:text-white"><?= htmlspecialchars($wo['total_hours'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-right text-gray-900 dark:text-white"><?= number_format((float)$wo['total_cost'], 0, ',', ' ') ?> kr</td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs"><?= htmlspecialchars(substr($wo['archived_at'] ?? '', 0, 10), ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-right">
                            <a href="/maintenance/work-orders/archive/<?= $wo['id'] ?>" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Visa</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($workOrders)): ?>
                    <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">Arkivet är tomt</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
