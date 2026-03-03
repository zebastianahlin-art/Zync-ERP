<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Offerter</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1"><?= count($quotes) ?> offerter</p>
        </div>
        <a href="/sales/quotes/create" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Ny offert
        </a>
    </div>

    <form method="get" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <input type="text" name="search" value="<?= htmlspecialchars($filters['search'] ?? '') ?>" placeholder="Sök offertnr, kund, referens..." class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
            <select name="status" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                <option value="">Alla statusar</option>
                <?php foreach (['draft'=>'Utkast','sent'=>'Skickad','accepted'=>'Accepterad','rejected'=>'Nekad','expired'=>'Utgången','revised'=>'Reviderad'] as $k=>$v): ?>
                    <option value="<?= $k ?>" <?= ($filters['status'] ?? '') === $k ? 'selected' : '' ?>><?= $v ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="rounded-lg bg-gray-100 dark:bg-gray-700 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200">Filtrera</button>
        </div>
    </form>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Offertnr</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Kund</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Datum</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Giltig till</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Belopp</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php if (empty($quotes)): ?>
                        <tr><td colspan="6" class="px-4 py-8 text-center text-sm text-gray-400">Inga offerter hittades</td></tr>
                    <?php else: foreach ($quotes as $q):
                        $sc = match($q['status']) { 'draft'=>'gray','sent'=>'blue','accepted'=>'green','rejected'=>'red','expired'=>'yellow',default=>'gray' };
                        $sl = match($q['status']) { 'draft'=>'Utkast','sent'=>'Skickad','accepted'=>'Accepterad','rejected'=>'Nekad','expired'=>'Utgången','revised'=>'Reviderad',default=>$q['status'] };
                        $isExpired = $q['status'] === 'sent' && $q['valid_until'] < date('Y-m-d');
                    ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer" onclick="location.href='/sales/quotes/<?= $q['id'] ?>'">
                            <td class="px-4 py-3 text-sm font-medium text-indigo-600 dark:text-indigo-400"><?= $q['quote_number'] ?></td>
                            <td class="px-4 py-3">
                                <div class="text-sm font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($q['customer_name']) ?></div>
                                <div class="text-xs text-gray-400"><?= $q['customer_number'] ?></div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400"><?= $q['quote_date'] ?></td>
                            <td class="px-4 py-3 text-sm <?= $isExpired ? 'text-red-500 font-medium' : 'text-gray-500 dark:text-gray-400' ?>"><?= $q['valid_until'] ?><?= $isExpired ? ' ⚠️' : '' ?></td>
                            <td class="px-4 py-3 text-sm text-right font-medium text-gray-900 dark:text-white"><?= number_format((float)$q['total_amount'], 0, ',', ' ') ?> <?= $q['currency'] ?></td>
                            <td class="px-4 py-3 text-center"><span class="inline-flex items-center rounded-full bg-<?= $sc ?>-100 dark:bg-<?= $sc ?>-900/30 px-2 py-0.5 text-xs font-medium text-<?= $sc ?>-700 dark:text-<?= $sc ?>-400"><?= $sl ?></span></td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
