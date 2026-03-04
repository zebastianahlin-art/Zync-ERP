<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Lönehantering</h1>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <a href="/hr/payroll/periods" class="rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-5 shadow-sm hover:shadow-md transition-shadow">
            <p class="text-sm text-gray-500 dark:text-gray-400">Löneperioder</p>
            <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-white"><?= count($periods) ?></p>
        </a>
        <a href="/hr/payroll/payslips" class="rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-5 shadow-sm hover:shadow-md transition-shadow">
            <p class="text-sm text-gray-500 dark:text-gray-400">Lönebesked</p>
            <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-white"><?= count($payslips) ?></p>
        </a>
    </div>
</div>
