<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Kunder</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1"><?= count($customers) ?> kunder</p>
        </div>
        <a href="/sales/customers/create" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Ny kund
        </a>
    </div>

    <!-- Filter -->
    <form method="get" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <input type="text" name="search" value="<?= htmlspecialchars($filters['search'] ?? '') ?>" placeholder="Sök namn, kundnr, org.nr..." class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
            <select name="status" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                <option value="">Alla statusar</option>
                <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Aktiv</option>
                <option value="inactive" <?= ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inaktiv</option>
                <option value="blocked" <?= ($filters['status'] ?? '') === 'blocked' ? 'selected' : '' ?>>Blockerad</option>
            </select>
            <select name="category" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                <option value="">Alla kategorier</option>
                <option value="standard" <?= ($filters['category'] ?? '') === 'standard' ? 'selected' : '' ?>>Standard</option>
                <option value="key_account" <?= ($filters['category'] ?? '') === 'key_account' ? 'selected' : '' ?>>Key Account</option>
                <option value="distributor" <?= ($filters['category'] ?? '') === 'distributor' ? 'selected' : '' ?>>Distributör</option>
                <option value="internal" <?= ($filters['category'] ?? '') === 'internal' ? 'selected' : '' ?>>Intern</option>
            </select>
            <button type="submit" class="rounded-lg bg-gray-100 dark:bg-gray-700 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">Filtrera</button>
        </div>
    </form>

    <!-- Tabell -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Kundnr</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Namn</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Org.nr</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Kategori</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Ordrar</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Total fsg</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Utestående</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php if (empty($customers)): ?>
                        <tr><td colspan="8" class="px-4 py-8 text-center text-sm text-gray-400">Inga kunder hittades</td></tr>
                    <?php else: foreach ($customers as $c):
                        $catLabel = match($c['category'] ?? 'standard') { 'key_account'=>'Key Account','distributor'=>'Distributör','internal'=>'Intern', default=>'Standard' };
                        $catColor = match($c['category'] ?? 'standard') { 'key_account'=>'purple','distributor'=>'blue','internal'=>'gray', default=>'green' };
                        $stColor = match($c['status'] ?? 'active') { 'active'=>'green','inactive'=>'gray','blocked'=>'red', default=>'gray' };
                        $stLabel = match($c['status'] ?? 'active') { 'active'=>'Aktiv','inactive'=>'Inaktiv','blocked'=>'Blockerad', default=>$c['status'] };
                    ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer" onclick="location.href='/sales/customers/<?= $c['id'] ?>'">
                            <td class="px-4 py-3 text-sm font-mono text-gray-500 dark:text-gray-400"><?= htmlspecialchars($c['customer_number']) ?></td>
                            <td class="px-4 py-3">
                                <div class="text-sm font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($c['name']) ?></div>
                                <div class="text-xs text-gray-400"><?= htmlspecialchars($c['email']) ?></div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars($c['org_number']) ?></td>
                            <td class="px-4 py-3"><span class="inline-flex items-center rounded-full bg-<?= $catColor ?>-100 dark:bg-<?= $catColor ?>-900/30 px-2 py-0.5 text-xs font-medium text-<?= $catColor ?>-700 dark:text-<?= $catColor ?>-400"><?= $catLabel ?></span></td>
                            <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white"><?= $c['order_count'] ?></td>
                            <td class="px-4 py-3 text-sm text-right font-medium text-gray-900 dark:text-white"><?= number_format((float)$c['total_sales'], 0, ',', ' ') ?></td>
                            <td class="px-4 py-3 text-sm text-right <?= (float)$c['outstanding'] > 0 ? 'text-red-600 font-medium' : 'text-gray-400' ?>"><?= number_format((float)$c['outstanding'], 0, ',', ' ') ?></td>
                            <td class="px-4 py-3 text-center"><span class="inline-flex items-center rounded-full bg-<?= $stColor ?>-100 dark:bg-<?= $stColor ?>-900/30 px-2 py-0.5 text-xs font-medium text-<?= $stColor ?>-700 dark:text-<?= $stColor ?>-400"><?= $stLabel ?></span></td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
