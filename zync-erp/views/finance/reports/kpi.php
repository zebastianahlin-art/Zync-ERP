<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">KPI från Avdelningar</h1>
        <a href="/reports" class="text-sm text-gray-500 hover:text-indigo-600 dark:text-gray-400">← Rapporter</a>
    </div>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 text-center">
            <div class="text-3xl font-bold text-orange-500 dark:text-orange-400"><?= (int)($kpiData['unpaid_out'] ?? 0) ?></div>
            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">Obetalda utgående fakturor</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 text-center">
            <div class="text-3xl font-bold text-red-500"><?= (int)($kpiData['unpaid_in'] ?? 0) ?></div>
            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">Obetalda inkommande fakturor</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 text-center">
            <div class="text-3xl font-bold text-yellow-500"><?= (int)($kpiData['open_reqs'] ?? 0) ?></div>
            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">Väntande anmodan</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 text-center">
            <div class="text-3xl font-bold text-blue-500 dark:text-blue-400"><?= (int)($kpiData['active_orders'] ?? 0) ?></div>
            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">Aktiva inköpsordrar</div>
        </div>
    </div>
    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 text-sm text-blue-700 dark:text-blue-300">
        <strong>Tips:</strong> Dessa KPI-värden visar aktuell status för ekonomi- och inköpsmodulen. Klicka på respektive modul för detaljerade rapporter.
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
        <a href="/reports/finance" class="block bg-white dark:bg-gray-800 rounded-lg shadow p-4 hover:shadow-md transition text-center">
            <div class="text-sm font-medium text-gray-700 dark:text-gray-300">Ekonomirapport</div>
        </a>
        <a href="/reports/purchasing" class="block bg-white dark:bg-gray-800 rounded-lg shadow p-4 hover:shadow-md transition text-center">
            <div class="text-sm font-medium text-gray-700 dark:text-gray-300">Inköpsrapport</div>
        </a>
        <a href="/reports/maintenance" class="block bg-white dark:bg-gray-800 rounded-lg shadow p-4 hover:shadow-md transition text-center">
            <div class="text-sm font-medium text-gray-700 dark:text-gray-300">Underhållsrapport</div>
        </a>
        <a href="/reports/hr" class="block bg-white dark:bg-gray-800 rounded-lg shadow p-4 hover:shadow-md transition text-center">
            <div class="text-sm font-medium text-gray-700 dark:text-gray-300">HR-rapport</div>
        </a>
    </div>
</div>
