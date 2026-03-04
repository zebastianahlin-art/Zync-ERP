<div class="flex flex-col items-center justify-center py-24 text-center">
    <!-- Construction icon -->
    <div class="mb-6 flex items-center justify-center w-20 h-20 rounded-full bg-indigo-100 dark:bg-indigo-900/30">
        <svg class="w-10 h-10 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l5.653-4.655m5.873-4.025a3.05 3.05 0 00-4.337 0l-1.514 1.514a3.05 3.05 0 000 4.337"/>
        </svg>
    </div>

    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-3">
        <?= $module ?>
    </h1>

    <p class="text-lg text-gray-500 dark:text-gray-400 mb-2">
        Den här modulen är under utveckling.
    </p>
    <p class="text-sm text-gray-400 dark:text-gray-500 mb-8">
        Funktionaliteten kommer att finnas tillgänglig i en kommande version av ZYNC ERP.
    </p>

    <a href="/dashboard"
       class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        Tillbaka till Dashboard
    </a>
</div>
