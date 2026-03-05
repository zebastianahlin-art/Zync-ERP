<?php $e = fn(string $v) => htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); ?>
<div class="space-y-6">

    <!-- Breadcrumbs -->
    <nav class="flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400">
        <a href="/saas-admin" class="hover:text-indigo-600 dark:hover:text-indigo-400">SaaS Admin</a>
        <span>/</span>
        <a href="/saas-admin/invoices" class="hover:text-indigo-600 dark:hover:text-indigo-400">Fakturering</a>
        <span>/</span>
        <span class="text-gray-900 dark:text-white font-medium">Batch-generering</span>
    </nav>

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Batch-fakturering</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Generera fakturor för alla aktiva kunder under en vald månad</p>
        </div>
        <a href="/saas-admin/invoices" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">← Tillbaka till fakturor</a>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

        <!-- Form -->
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-3 mb-5">Välj period</h2>

            <form method="POST" action="/saas-admin/invoices/generate" class="space-y-5">
                <?= \App\Core\Csrf::field() ?>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Månad</label>
                    <select name="month" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <?php foreach ($months as $num => $name): ?>
                        <option value="<?= (int) $num ?>" <?= (int) $month === (int) $num ? 'selected' : '' ?>><?= $e($name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">År</label>
                    <select name="year" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <?php for ($y = date('Y') + 1; $y >= date('Y') - 2; $y--): ?>
                        <option value="<?= $y ?>" <?= (int) $year === $y ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 p-4">
                    <p class="text-sm font-medium text-amber-700 dark:text-amber-400 mb-1">⚠️ OBS!</p>
                    <p class="text-sm text-amber-700 dark:text-amber-400">
                        Fakturor genereras <strong>bara för kunder som saknar en faktura</strong> för vald period.
                        Befintliga fakturor berörs inte.
                    </p>
                </div>

                <button type="submit" class="w-full rounded-lg bg-green-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-green-700 transition-colors">
                    🚀 Generera fakturor
                </button>
            </form>
        </div>

        <!-- Info panel -->
        <div class="space-y-4">
            <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Hur fungerar det?</h2>
                <ol class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                    <li class="flex gap-3">
                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-xs font-bold text-indigo-600 dark:text-indigo-400">1</span>
                        Systemet hämtar alla kunder med status <strong class="text-gray-700 dark:text-gray-300">Aktiv</strong>.
                    </li>
                    <li class="flex gap-3">
                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-xs font-bold text-indigo-600 dark:text-indigo-400">2</span>
                        För varje kund kontrolleras om en faktura redan finns för vald period.
                    </li>
                    <li class="flex gap-3">
                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-xs font-bold text-indigo-600 dark:text-indigo-400">3</span>
                        Nya fakturor skapas med belopp från kundens plan (+ 25% moms) och förfallodag 14 dagar efter periodslutet.
                    </li>
                    <li class="flex gap-3">
                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-xs font-bold text-indigo-600 dark:text-indigo-400">4</span>
                        Fakturorna skapas med status <strong class="text-gray-700 dark:text-gray-300">Utkast</strong> — granska och skicka manuellt.
                    </li>
                </ol>
            </div>

            <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md">
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-3">Fakturastatusflöde</h2>
                <div class="flex flex-wrap items-center gap-2 text-xs">
                    <span class="rounded-full bg-gray-100 dark:bg-gray-700 px-3 py-1 text-gray-600 dark:text-gray-400">Utkast</span>
                    <span class="text-gray-400">→</span>
                    <span class="rounded-full bg-blue-100 dark:bg-blue-900/30 px-3 py-1 text-blue-700 dark:text-blue-300">Skickad</span>
                    <span class="text-gray-400">→</span>
                    <span class="rounded-full bg-green-100 dark:bg-green-900/30 px-3 py-1 text-green-700 dark:text-green-300">Betald</span>
                </div>
                <div class="mt-2 flex flex-wrap items-center gap-2 text-xs">
                    <span class="rounded-full bg-blue-100 dark:bg-blue-900/30 px-3 py-1 text-blue-700 dark:text-blue-300">Skickad</span>
                    <span class="text-gray-400">→</span>
                    <span class="rounded-full bg-red-100 dark:bg-red-900/30 px-3 py-1 text-red-700 dark:text-red-300">Förfallen</span>
                    <span class="text-gray-400">→</span>
                    <span class="rounded-full bg-gray-100 dark:bg-gray-700 px-3 py-1 text-gray-600 dark:text-gray-400">Makulerad</span>
                </div>
            </div>
        </div>
    </div>
</div>
