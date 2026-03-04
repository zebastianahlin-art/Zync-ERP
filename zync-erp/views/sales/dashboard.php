<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Sales</h1>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <p class="text-sm text-gray-500 dark:text-gray-400">Offerter</p>
            <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400 mt-1"><?= (int) ($stats['quotes'] ?? 0) ?></p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <p class="text-sm text-gray-500 dark:text-gray-400">Säljordrar</p>
            <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400 mt-1"><?= (int) ($stats['orders'] ?? 0) ?></p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <p class="text-sm text-gray-500 dark:text-gray-400">Prislistor</p>
            <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400 mt-1"><?= (int) ($stats['lists'] ?? 0) ?></p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <a href="/sales/quotes" class="block bg-white dark:bg-gray-800 rounded-xl shadow p-5 hover:shadow-md transition">
            <h2 class="font-semibold text-gray-900 dark:text-white">Offerter</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Hantera offerter till kunder</p>
        </a>
        <a href="/sales/orders" class="block bg-white dark:bg-gray-800 rounded-xl shadow p-5 hover:shadow-md transition">
            <h2 class="font-semibold text-gray-900 dark:text-white">Säljordrar</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kundorderingångar</p>
        </a>
        <a href="/sales/pricing" class="block bg-white dark:bg-gray-800 rounded-xl shadow p-5 hover:shadow-md transition">
            <h2 class="font-semibold text-gray-900 dark:text-white">Prissättning</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Prislistor och rabatter</p>
        </a>
    </div>
</div>
