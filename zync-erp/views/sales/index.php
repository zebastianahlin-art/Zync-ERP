<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Försäljning</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Översikt kunder, offerter och ordrar</p>
        </div>
        <div class="flex gap-2">
            <a href="/sales/quotes/create" class="inline-flex items-center gap-2 rounded-lg bg-white dark:bg-gray-800 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 shadow-sm border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Ny offert
            </a>
            <a href="/sales/orders/create" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Ny order
            </a>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aktiva kunder</p>
            <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white"><?= $stats['customers_active'] ?></p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Öppna offerter</p>
            <p class="mt-2 text-2xl font-bold text-blue-600 dark:text-blue-400"><?= $stats['quotes_open'] ?></p>
            <p class="text-xs text-gray-400 mt-1"><?= number_format($stats['quotes_value'], 0, ',', ' ') ?> SEK</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aktiva ordrar</p>
            <p class="mt-2 text-2xl font-bold text-indigo-600 dark:text-indigo-400"><?= $stats['orders_active'] ?></p>
            <p class="text-xs text-gray-400 mt-1"><?= number_format($stats['orders_value'], 0, ',', ' ') ?> SEK</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Hit rate</p>
            <p class="mt-2 text-2xl font-bold text-emerald-600 dark:text-emerald-400"><?= $stats['hit_rate'] ?>%</p>
        </div>
    </div>

    <!-- Denna månad -->
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl p-6 text-white">
        <h3 class="text-sm font-medium opacity-80 uppercase tracking-wider">Denna månad</h3>
        <div class="mt-2 flex items-baseline gap-4">
            <span class="text-3xl font-bold"><?= number_format($stats['orders_month_value'], 0, ',', ' ') ?> SEK</span>
            <span class="text-sm opacity-80"><?= $stats['orders_month'] ?> ordrar</span>
        </div>
    </div>

    <!-- Snabblänkar -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
        <a href="/sales/customers" class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 text-center hover:shadow-md transition-shadow">
            <svg class="w-8 h-8 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <p class="mt-2 text-sm font-medium text-gray-700 dark:text-gray-300">Kunder</p>
        </a>
        <a href="/sales/quotes" class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 text-center hover:shadow-md transition-shadow">
            <svg class="w-8 h-8 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <p class="mt-2 text-sm font-medium text-gray-700 dark:text-gray-300">Offerter</p>
        </a>
        <a href="/sales/orders" class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 text-center hover:shadow-md transition-shadow">
            <svg class="w-8 h-8 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <p class="mt-2 text-sm font-medium text-gray-700 dark:text-gray-300">Ordrar</p>
        </a>
        <a href="/sales/pricelists" class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 text-center hover:shadow-md transition-shadow">
            <svg class="w-8 h-8 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
            <p class="mt-2 text-sm font-medium text-gray-700 dark:text-gray-300">Prislistor</p>
        </a>
        <a href="/sales/customers/create" class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 text-center hover:shadow-md transition-shadow">
            <svg class="w-8 h-8 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            <p class="mt-2 text-sm font-medium text-gray-700 dark:text-gray-300">Ny kund</p>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Senaste offerter -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <h3 class="font-semibold text-gray-900 dark:text-white">Senaste offerter</h3>
                <a href="/sales/quotes" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Visa alla →</a>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                <?php if (empty($recentQuotes)): ?>
                    <p class="p-5 text-sm text-gray-400">Inga offerter ännu</p>
                <?php else: foreach ($recentQuotes as $q): ?>
                    <a href="/sales/quotes/<?= $q['id'] ?>" class="flex items-center justify-between px-5 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <div>
                            <span class="text-sm font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($q['quote_number']) ?></span>
                            <span class="text-sm text-gray-500 dark:text-gray-400 ml-2"><?= htmlspecialchars($q['customer_name']) ?></span>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-medium text-gray-900 dark:text-white"><?= number_format((float)$q['total_amount'], 0, ',', ' ') ?> SEK</span>
                            <?php
                                $sc = match($q['status']) { 'draft'=>'gray','sent'=>'blue','accepted'=>'green','rejected'=>'red','expired'=>'yellow', default=>'gray' };
                                $sl = match($q['status']) { 'draft'=>'Utkast','sent'=>'Skickad','accepted'=>'Accepterad','rejected'=>'Nekad','expired'=>'Utgången','revised'=>'Reviderad', default=>$q['status'] };
                            ?>
                            <span class="ml-2 inline-flex items-center rounded-full bg-<?= $sc ?>-100 dark:bg-<?= $sc ?>-900/30 px-2 py-0.5 text-xs font-medium text-<?= $sc ?>-700 dark:text-<?= $sc ?>-400"><?= $sl ?></span>
                        </div>
                    </a>
                <?php endforeach; endif; ?>
            </div>
        </div>

        <!-- Senaste ordrar -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <h3 class="font-semibold text-gray-900 dark:text-white">Senaste ordrar</h3>
                <a href="/sales/orders" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Visa alla →</a>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                <?php if (empty($recentOrders)): ?>
                    <p class="p-5 text-sm text-gray-400">Inga ordrar ännu</p>
                <?php else: foreach ($recentOrders as $o): ?>
                    <a href="/sales/orders/<?= $o['id'] ?>" class="flex items-center justify-between px-5 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <div>
                            <span class="text-sm font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($o['order_number']) ?></span>
                            <span class="text-sm text-gray-500 dark:text-gray-400 ml-2"><?= htmlspecialchars($o['customer_name']) ?></span>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-medium text-gray-900 dark:text-white"><?= number_format((float)$o['total_amount'], 0, ',', ' ') ?> SEK</span>
                            <?php
                                $sc = match($o['status']) { 'draft'=>'gray','confirmed'=>'blue','in_production'=>'yellow','partially_delivered'=>'orange','delivered'=>'green','invoiced'=>'emerald','cancelled'=>'red', default=>'gray' };
                                $sl = match($o['status']) { 'draft'=>'Utkast','confirmed'=>'Bekräftad','in_production'=>'I produktion','partially_delivered'=>'Dellevererad','delivered'=>'Levererad','invoiced'=>'Fakturerad','cancelled'=>'Avbruten', default=>$o['status'] };
                            ?>
                            <span class="ml-2 inline-flex items-center rounded-full bg-<?= $sc ?>-100 dark:bg-<?= $sc ?>-900/30 px-2 py-0.5 text-xs font-medium text-<?= $sc ?>-700 dark:text-<?= $sc ?>-400"><?= $sl ?></span>
                        </div>
                    </a>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </div>
</div>
