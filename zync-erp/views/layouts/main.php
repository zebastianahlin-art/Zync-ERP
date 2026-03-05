<!DOCTYPE html>
<?php
$_layoutUser  = \App\Core\Auth::user();
$currentPath  = parse_url($_SERVER["REQUEST_URI"] ?? "/", PHP_URL_PATH) ?? "/";
$_dbTheme     = $_layoutUser['theme'] ?? 'light';
$_themeJs     = htmlspecialchars($_dbTheme, ENT_QUOTES, 'UTF-8');

function navActive(string $path, string $currentPath): string {
    return strpos($currentPath, $path) === 0
        ? 'text-indigo-600 dark:text-indigo-400 font-semibold'
        : 'text-gray-600 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400';
}
function mobileActive(string $path, string $currentPath): string {
    return strpos($currentPath, $path) === 0
        ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-400'
        : 'text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-gray-700 hover:text-indigo-600';
}
?>
<html lang="sv" class="h-full bg-gray-50 dark:bg-gray-900"
      x-data="{ darkMode: localStorage.getItem('theme') !== null ? localStorage.getItem('theme') === 'dark' : '<?= $_themeJs ?>' === 'dark' }"
      :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'ZYNC ERP', ENT_QUOTES, 'UTF-8') ?></title>
    <script>tailwind = { darkMode: 'class' };</script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <?php if (class_exists(\App\Core\Csrf::class)): ?>
    <meta name="csrf-token" content="<?= htmlspecialchars(\App\Core\Csrf::token(), ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>
</head>
<body class="h-full font-sans antialiased bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">

<!-- Navigation -->
<nav class="bg-white dark:bg-gray-800 shadow-sm sticky top-0 z-50" x-data="{ open: false, dropdowns: {} }">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-14 items-center justify-between">

            <!-- Logo -->
            <a href="/" class="text-xl font-bold tracking-tight text-indigo-600 dark:text-indigo-400 shrink-0">ZYNC ERP</a>

            <!-- Desktop nav -->
            <div class="hidden lg:flex lg:items-center lg:gap-1 text-sm">
                <?php if ($_layoutUser): ?>

                <!-- 1. Dashboard -->
                <a href="/dashboard" class="px-3 py-2 rounded-md transition-colors <?= navActive('/dashboard', $currentPath) ?>">Dashboard</a>

                <!-- 2. Underhåll -->
                <div class="relative" x-data="{ show: false }" @mouseenter="show=true" @mouseleave="show=false">
                    <button class="px-3 py-2 rounded-md transition-colors text-gray-600 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 flex items-center gap-1">
                        Underhåll <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="show" x-transition class="absolute left-0 mt-1 w-56 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-50">
                        <a href="/maintenance" class="block px-4 py-2 text-sm <?= mobileActive('/maintenance', $currentPath) ?>">Dashboard</a>
                        <a href="/maintenance/faults" class="block px-4 py-2 text-sm <?= mobileActive('/maintenance/faults', $currentPath) ?>">Felanmälan</a>
                        <a href="/maintenance/work-orders" class="block px-4 py-2 text-sm <?= mobileActive('/maintenance/work-orders', $currentPath) ?>">Aktiva arbetsordrar</a>
                        <?php if (($_layoutUser['role_level'] ?? 0) >= 5): ?>
                        <a href="/maintenance/supervisor" class="block px-4 py-2 text-sm <?= mobileActive('/maintenance/supervisor', $currentPath) ?>">Arbetsorderhantering</a>
                        <?php endif; ?>
                        <a href="/maintenance/work-orders" class="block px-4 py-2 text-sm <?= mobileActive('/maintenance/work-orders', $currentPath) ?>">Avrapportering</a>
                        <a href="/maintenance/work-orders/archive" class="block px-4 py-2 text-sm <?= mobileActive('/maintenance/work-orders/archive', $currentPath) ?>">Historiska arbetsordrar</a>
                        <hr class="my-1 border-gray-200 dark:border-gray-700">
                        <a href="/maintenance/preventive" class="block px-4 py-2 text-sm <?= mobileActive('/maintenance/preventive', $currentPath) ?>">Förebyggande underhåll</a>
                        <a href="/maintenance/preventive/create" class="block px-4 py-2 text-sm <?= mobileActive('/maintenance/preventive/create', $currentPath) ?>">Nytt FU-schema</a>
                        <a href="/maintenance/preventive/calendar" class="block px-4 py-2 text-sm <?= mobileActive('/maintenance/preventive/calendar', $currentPath) ?>">FU-kalender</a>
                        <hr class="my-1 border-gray-200 dark:border-gray-700">
                        <a href="/maintenance/ai" class="block px-4 py-2 text-sm <?= mobileActive('/maintenance/ai', $currentPath) ?>">AI-ingenjör</a>
                        <a href="/maintenance/ai/recommendations" class="block px-4 py-2 text-sm <?= mobileActive('/maintenance/ai/recommendations', $currentPath) ?>">Rekommendationer</a>
                        <hr class="my-1 border-gray-200 dark:border-gray-700">
                        <a href="/maintenance/inspections" class="block px-4 py-2 text-sm <?= mobileActive('/maintenance/inspections', $currentPath) ?>">Besiktningar</a>
                    </div>
                </div>

                <!-- 3. Anläggningsregister -->
                <div class="relative" x-data="{ show: false }" @mouseenter="show=true" @mouseleave="show=false">
                    <button class="px-3 py-2 rounded-md transition-colors text-gray-600 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 flex items-center gap-1">
                        Anläggningsregister <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="show" x-transition class="absolute left-0 mt-1 w-56 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-50">
                        <a href="/objects" class="block px-4 py-2 text-sm <?= mobileActive('/objects', $currentPath) ?>">Navigator</a>
                        <a href="/objects/tree" class="block px-4 py-2 text-sm <?= mobileActive('/objects/tree', $currentPath) ?>">Objektträd</a>
                        <hr class="my-1 border-gray-200 dark:border-gray-700">
                        <a href="/equipment" class="block px-4 py-2 text-sm <?= mobileActive('/equipment', $currentPath) ?>">Utrustning</a>
                        <a href="/machines" class="block px-4 py-2 text-sm <?= mobileActive('/machines', $currentPath) ?>">Maskiner</a>
                        <a href="/maintenance/inspections" class="block px-4 py-2 text-sm <?= mobileActive('/maintenance/inspections', $currentPath) ?>">Besiktningspliktig utrustning</a>
                    </div>
                </div>

                <!-- 4. Lager -->
                <div class="relative" x-data="{ show: false }" @mouseenter="show=true" @mouseleave="show=false">
                    <button class="px-3 py-2 rounded-md transition-colors text-gray-600 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 flex items-center gap-1">
                        Lager <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="show" x-transition class="absolute left-0 mt-1 w-56 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-50">
                        <a href="/inventory" class="block px-4 py-2 text-sm <?= mobileActive('/inventory', $currentPath) ?>">Lageröversikt</a>
                        <a href="/inventory/warehouses" class="block px-4 py-2 text-sm <?= mobileActive('/inventory/warehouses', $currentPath) ?>">Lagerställen</a>
                        <a href="/inventory/transactions" class="block px-4 py-2 text-sm <?= mobileActive('/inventory/transactions', $currentPath) ?>">Transaktioner</a>
                        <a href="/inventory/receiving" class="block px-4 py-2 text-sm <?= mobileActive('/inventory/receiving', $currentPath) ?>">Inleverans</a>
                        <a href="/inventory/issues" class="block px-4 py-2 text-sm <?= mobileActive('/inventory/issues', $currentPath) ?>">Uttag</a>
                        <a href="/inventory/stocktaking" class="block px-4 py-2 text-sm <?= mobileActive('/inventory/stocktaking', $currentPath) ?>">Inventering</a>
                    </div>
                </div>

                <!-- 5. Inköp -->
                <div class="relative" x-data="{ show: false }" @mouseenter="show=true" @mouseleave="show=false">
                    <button class="px-3 py-2 rounded-md transition-colors text-gray-600 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 flex items-center gap-1">
                        Inköp <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="show" x-transition class="absolute left-0 mt-1 w-56 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-50">
                        <a href="/purchasing" class="block px-4 py-2 text-sm <?= mobileActive('/purchasing', $currentPath) ?>">Dashboard</a>
                        <hr class="my-1 border-gray-200 dark:border-gray-700">
                        <p class="px-4 py-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Leverantörer</p>
                        <a href="/suppliers" class="block px-4 py-2 text-sm <?= mobileActive('/suppliers', $currentPath) ?>">Leverantörsregister</a>
                        <a href="/purchasing/supplier-audits" class="block px-4 py-2 text-sm <?= mobileActive('/purchasing/supplier-audits', $currentPath) ?>">Leverantörsaudit</a>
                        <hr class="my-1 border-gray-200 dark:border-gray-700">
                        <p class="px-4 py-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Inköpsanmodan</p>
                        <a href="/purchasing/requisitions/create" class="block px-4 py-2 text-sm <?= mobileActive('/purchasing/requisitions/create', $currentPath) ?>">Skapa inköpsanmodan</a>
                        <a href="/purchasing/requisitions" class="block px-4 py-2 text-sm <?= mobileActive('/purchasing/requisitions', $currentPath) ?>">Ej hanterade anmodan</a>
                        <a href="/purchasing/requisitions/history" class="block px-4 py-2 text-sm <?= mobileActive('/purchasing/requisitions/history', $currentPath) ?>">Historiska anmodan</a>
                        <hr class="my-1 border-gray-200 dark:border-gray-700">
                        <p class="px-4 py-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Inköpsorder</p>
                        <a href="/purchasing/orders/create" class="block px-4 py-2 text-sm <?= mobileActive('/purchasing/orders/create', $currentPath) ?>">Skapa inköpsorder</a>
                        <a href="/purchasing/orders" class="block px-4 py-2 text-sm <?= mobileActive('/purchasing/orders', $currentPath) ?>">Aktiva inköpsordrar</a>
                        <a href="/purchasing/orders/history" class="block px-4 py-2 text-sm <?= mobileActive('/purchasing/orders/history', $currentPath) ?>">Historiska inköpsordrar</a>
                        <hr class="my-1 border-gray-200 dark:border-gray-700">
                        <p class="px-4 py-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Avtal</p>
                        <a href="/purchasing/agreements/create" class="block px-4 py-2 text-sm <?= mobileActive('/purchasing/agreements/create', $currentPath) ?>">Skapa avtal</a>
                        <a href="/purchasing/agreements" class="block px-4 py-2 text-sm <?= mobileActive('/purchasing/agreements', $currentPath) ?>">Aktiva avtal</a>
                        <a href="/purchasing/agreements/history" class="block px-4 py-2 text-sm <?= mobileActive('/purchasing/agreements/history', $currentPath) ?>">Historiska avtal</a>
                        <a href="/purchasing/agreement-templates" class="block px-4 py-2 text-sm <?= mobileActive('/purchasing/agreement-templates', $currentPath) ?>">Avtalsmallar</a>
                    </div>
                </div>

                <!-- 6. Ekonomi -->
                <div class="relative" x-data="{ show: false }" @mouseenter="show=true" @mouseleave="show=false">
                    <button class="px-3 py-2 rounded-md transition-colors text-gray-600 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 flex items-center gap-1">
                        Ekonomi <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="show" x-transition class="absolute left-0 mt-1 w-56 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-50">
                        <a href="/finance" class="block px-4 py-2 text-sm <?= mobileActive('/finance', $currentPath) ?>">Dashboard</a>
                        <hr class="my-1 border-gray-200 dark:border-gray-700">
                        <p class="px-4 py-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Kunder &amp; Leverantörer</p>
                        <a href="/customers" class="block px-4 py-2 text-sm <?= mobileActive('/customers', $currentPath) ?>">Kunder</a>
                        <a href="/suppliers" class="block px-4 py-2 text-sm <?= mobileActive('/suppliers', $currentPath) ?>">Leverantörer</a>
                        <hr class="my-1 border-gray-200 dark:border-gray-700">
                        <p class="px-4 py-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Fakturahantering</p>
                        <a href="/finance/invoices-in" class="block px-4 py-2 text-sm <?= mobileActive('/finance/invoices-in', $currentPath) ?>">Leverantörsfakturor</a>
                        <a href="/finance/invoices-out/create" class="block px-4 py-2 text-sm <?= mobileActive('/finance/invoices-out/create', $currentPath) ?>">Skapa kundfaktura</a>
                        <hr class="my-1 border-gray-200 dark:border-gray-700">
                        <p class="px-4 py-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Bokföring</p>
                        <a href="/finance/accounts" class="block px-4 py-2 text-sm <?= mobileActive('/finance/accounts', $currentPath) ?>">Kontoplan</a>
                        <a href="/finance/cost-centers" class="block px-4 py-2 text-sm <?= mobileActive('/finance/cost-centers', $currentPath) ?>">Kostnadsställen</a>
                        <a href="/finance/journal" class="block px-4 py-2 text-sm <?= mobileActive('/finance/journal', $currentPath) ?>">Bokföring/Verifikationer</a>
                        <hr class="my-1 border-gray-200 dark:border-gray-700">
                        <p class="px-4 py-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Rapporter</p>
                        <a href="/finance/reports/ledger" class="block px-4 py-2 text-sm <?= mobileActive('/finance/reports/ledger', $currentPath) ?>">Huvudbok</a>
                        <a href="/finance/reports/trial-balance" class="block px-4 py-2 text-sm <?= mobileActive('/finance/reports/trial-balance', $currentPath) ?>">Resultaträkning</a>
                        <a href="/finance/reports/balance-sheet" class="block px-4 py-2 text-sm <?= mobileActive('/finance/reports/balance-sheet', $currentPath) ?>">Balansräkning</a>
                        <a href="/finance/reports/kpi" class="block px-4 py-2 text-sm <?= mobileActive('/finance/reports/kpi', $currentPath) ?>">KPI från avdelningar</a>
                        <a href="/finance/reports/stocktaking" class="block px-4 py-2 text-sm <?= mobileActive('/finance/reports/stocktaking', $currentPath) ?>">Inventering</a>
                        <hr class="my-1 border-gray-200 dark:border-gray-700">
                        <p class="px-4 py-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Tillgångar &amp; Budget</p>
                        <a href="/finance/account-groups" class="block px-4 py-2 text-sm <?= mobileActive('/finance/account-groups', $currentPath) ?>">Kontoplansgrupper</a>
                        <a href="/finance/assets" class="block px-4 py-2 text-sm <?= mobileActive('/finance/assets', $currentPath) ?>">Anläggningstillgångar</a>
                        <a href="/finance/budgets" class="block px-4 py-2 text-sm <?= mobileActive('/finance/budgets', $currentPath) ?>">Budgetar</a>
                    </div>
                </div>

                <!-- 7. Health & Safety -->
                <div class="relative" x-data="{ show: false }" @mouseenter="show=true" @mouseleave="show=false">
                    <button class="px-3 py-2 rounded-md transition-colors text-gray-600 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 flex items-center gap-1">
                        Health &amp; Safety <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="show" x-transition class="absolute left-0 mt-1 w-60 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-50">
                        <a href="/safety" class="block px-4 py-2 text-sm <?= mobileActive('/safety', $currentPath) ?>">Dashboard</a>
                        <hr class="my-1 border-gray-200 dark:border-gray-700">
                        <a href="/safety/risks" class="block px-4 py-2 text-sm <?= mobileActive('/safety/risks', $currentPath) ?>">Hantera risker &amp; faror</a>
                        <hr class="my-1 border-gray-200 dark:border-gray-700">
                        <p class="px-4 py-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Safety Audits</p>
                        <a href="/safety/audits/create" class="block px-4 py-2 text-sm <?= mobileActive('/safety/audits/create', $currentPath) ?>">Skapa safety audit</a>
                        <a href="/safety/audit-templates" class="block px-4 py-2 text-sm <?= mobileActive('/safety/audit-templates', $currentPath) ?>">Hantera audit-mallar</a>
                        <a href="/safety/audits/pending" class="block px-4 py-2 text-sm <?= mobileActive('/safety/audits/pending', $currentPath) ?>">Ej slutförda åtgärder</a>
                        <a href="/safety/audits/completed" class="block px-4 py-2 text-sm <?= mobileActive('/safety/audits/completed', $currentPath) ?>">Slutförda åtgärder</a>
                        <hr class="my-1 border-gray-200 dark:border-gray-700">
                        <p class="px-4 py-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Krishantering</p>
                        <a href="/safety/emergency" class="block px-4 py-2 text-sm <?= mobileActive('/safety/emergency', $currentPath) ?>">Aktiv krisplan</a>
                        <a href="/safety/emergency/procedures" class="block px-4 py-2 text-sm <?= mobileActive('/safety/emergency/procedures', $currentPath) ?>">Hantera krisplan</a>
                        <hr class="my-1 border-gray-200 dark:border-gray-700">
                        <p class="px-4 py-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Nödlägesövningar</p>
                        <a href="/safety/emergency/drills/create" class="block px-4 py-2 text-sm <?= mobileActive('/safety/emergency/drills/create', $currentPath) ?>">Skapa nödlägesövning</a>
                        <a href="/safety/emergency/drills" class="block px-4 py-2 text-sm <?= mobileActive('/safety/emergency/drills', $currentPath) ?>">Lista nödlägesövningar</a>
                        <a href="/safety/emergency/drills/templates" class="block px-4 py-2 text-sm <?= mobileActive('/safety/emergency/drills/templates', $currentPath) ?>">Hantera mallar</a>
                        <hr class="my-1 border-gray-200 dark:border-gray-700">
                        <p class="px-4 py-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Nödresurser</p>
                        <a href="/safety/resources" class="block px-4 py-2 text-sm <?= mobileActive('/safety/resources', $currentPath) ?>">Lista nödresurser</a>
                        <a href="/safety/resources/create" class="block px-4 py-2 text-sm <?= mobileActive('/safety/resources/create', $currentPath) ?>">Hantera nödresurser</a>
                        <a href="/safety/resources/overdue" class="block px-4 py-2 text-sm <?= mobileActive('/safety/resources/overdue', $currentPath) ?>">Förfallna kontroller</a>
                    </div>
                </div>

                <!-- 8. Produktion -->
                <div class="relative" x-data="{ show: false }" @mouseenter="show=true" @mouseleave="show=false">
                    <button class="px-3 py-2 rounded-md transition-colors text-gray-600 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 flex items-center gap-1">
                        Produktion <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="show" x-transition class="absolute left-0 mt-1 w-52 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-50">
                        <a href="/production" class="block px-4 py-2 text-sm <?= mobileActive('/production', $currentPath) ?>">Dashboard</a>
                        <a href="/production/lines" class="block px-4 py-2 text-sm <?= mobileActive('/production/lines', $currentPath) ?>">Produktionslinjer</a>
                        <a href="/production/orders" class="block px-4 py-2 text-sm <?= mobileActive('/production/orders', $currentPath) ?>">Produktionsplanering</a>
                        <a href="/production/products" class="block px-4 py-2 text-sm <?= mobileActive('/production/products', $currentPath) ?>">Skapa produkt</a>
                        <hr class="my-1 border-gray-200 dark:border-gray-700">
                        <a href="/production/stock" class="block px-4 py-2 text-sm <?= mobileActive('/production/stock', $currentPath) ?>">Produktionslager</a>
                        <a href="/production/stock/manage" class="block px-4 py-2 text-sm <?= mobileActive('/production/stock/manage', $currentPath) ?>">Hantera produktionslager</a>
                    </div>
                </div>

                <!-- 9. Försäljning -->
                <div class="relative" x-data="{ show: false }" @mouseenter="show=true" @mouseleave="show=false">
                    <button class="px-3 py-2 rounded-md transition-colors text-gray-600 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 flex items-center gap-1">
                        Försäljning <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="show" x-transition class="absolute left-0 mt-1 w-56 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-50">
                        <a href="/sales" class="block px-4 py-2 text-sm <?= mobileActive('/sales', $currentPath) ?>">Dashboard</a>
                        <hr class="my-1 border-gray-200 dark:border-gray-700">
                        <p class="px-4 py-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Kunder</p>
                        <a href="/customers" class="block px-4 py-2 text-sm <?= mobileActive('/customers', $currentPath) ?>">Kundregister</a>
                        <hr class="my-1 border-gray-200 dark:border-gray-700">
                        <p class="px-4 py-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Offerter</p>
                        <a href="/sales/quotes/create" class="block px-4 py-2 text-sm <?= mobileActive('/sales/quotes/create', $currentPath) ?>">Skapa offert</a>
                        <a href="/sales/quotes/accepted" class="block px-4 py-2 text-sm <?= mobileActive('/sales/quotes/accepted', $currentPath) ?>">Accepterade offerter</a>
                        <a href="/sales/quotes" class="block px-4 py-2 text-sm <?= mobileActive('/sales/quotes', $currentPath) ?>">Aktiva offerter</a>
                        <a href="/sales/quotes/history" class="block px-4 py-2 text-sm <?= mobileActive('/sales/quotes/history', $currentPath) ?>">Historiska offerter</a>
                        <a href="/sales/quotes/templates" class="block px-4 py-2 text-sm <?= mobileActive('/sales/quotes/templates', $currentPath) ?>">Offertmallar</a>
                        <hr class="my-1 border-gray-200 dark:border-gray-700">
                        <a href="/finance/invoices-out" class="block px-4 py-2 text-sm <?= mobileActive('/finance/invoices-out', $currentPath) ?>">Fakturering</a>
                        <hr class="my-1 border-gray-200 dark:border-gray-700">
                        <p class="px-4 py-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Prislistor</p>
                        <a href="/sales/pricing" class="block px-4 py-2 text-sm <?= mobileActive('/sales/pricing', $currentPath) ?>">Lista prislistor</a>
                        <a href="/sales/pricing/manage" class="block px-4 py-2 text-sm <?= mobileActive('/sales/pricing/manage', $currentPath) ?>">Hantera prislistor</a>
                    </div>
                </div>

                <!-- 10. CS & Transport -->
                <div class="relative" x-data="{ show: false }" @mouseenter="show=true" @mouseleave="show=false">
                    <button class="px-3 py-2 rounded-md transition-colors text-gray-600 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 flex items-center gap-1">
                        CS &amp; Transport <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="show" x-transition class="absolute left-0 mt-1 w-56 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-50">
                        <p class="px-4 py-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Customer Service</p>
                        <a href="/cs" class="block px-4 py-2 text-sm <?= mobileActive('/cs', $currentPath) ?>">CS Dashboard</a>
                        <a href="/cs/tickets" class="block px-4 py-2 text-sm <?= mobileActive('/cs/tickets', $currentPath) ?>">Alla ärenden</a>
                        <a href="/cs/tickets/create" class="block px-4 py-2 text-sm <?= mobileActive('/cs/tickets/create', $currentPath) ?>">Nytt ärende</a>
                        <a href="/cs/tickets/my" class="block px-4 py-2 text-sm <?= mobileActive('/cs/tickets/my', $currentPath) ?>">Mina ärenden</a>
                        <hr class="my-1 border-gray-200 dark:border-gray-700">
                        <p class="px-4 py-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Transport</p>
                        <a href="/transport" class="block px-4 py-2 text-sm <?= mobileActive('/transport', $currentPath) ?>">Transport Dashboard</a>
                        <a href="/transport/orders" class="block px-4 py-2 text-sm <?= mobileActive('/transport/orders', $currentPath) ?>">Transportordrar</a>
                        <a href="/transport/orders/create" class="block px-4 py-2 text-sm <?= mobileActive('/transport/orders/create', $currentPath) ?>">Ny transportorder</a>
                        <a href="/transport/carriers" class="block px-4 py-2 text-sm <?= mobileActive('/transport/carriers', $currentPath) ?>">Transportörer</a>
                    </div>
                </div>

                <!-- 11. Projekt -->
                <div class="relative" x-data="{ show: false }" @mouseenter="show=true" @mouseleave="show=false">
                    <button class="px-3 py-2 rounded-md transition-colors text-gray-600 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 flex items-center gap-1">
                        Projekt <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="show" x-transition class="absolute left-0 mt-1 w-44 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-50">
                        <a href="/projects" class="block px-4 py-2 text-sm <?= mobileActive('/projects', $currentPath) ?>">Dashboard</a>
                        <a href="/projects" class="block px-4 py-2 text-sm <?= mobileActive('/projects', $currentPath) ?>">Alla projekt</a>
                        <a href="/projects/create" class="block px-4 py-2 text-sm <?= mobileActive('/projects/create', $currentPath) ?>">Skapa projekt</a>
                    </div>
                </div>

                <!-- 12. HR -->
                <div class="relative" x-data="{ show: false }" @mouseenter="show=true" @mouseleave="show=false">
                    <button class="px-3 py-2 rounded-md transition-colors text-gray-600 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 flex items-center gap-1">
                        HR <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="show" x-transition class="absolute left-0 mt-1 w-52 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-50">
                        <a href="/hr" class="block px-4 py-2 text-sm font-semibold <?= mobileActive('/hr', $currentPath) ?>">🏠 HR Dashboard</a>
                        <hr class="my-1 border-gray-200 dark:border-gray-700">
                        <a href="/departments" class="block px-4 py-2 text-sm <?= mobileActive('/departments', $currentPath) ?>">Avdelningar</a>
                        <a href="/employees" class="block px-4 py-2 text-sm <?= mobileActive('/employees', $currentPath) ?>">Personal</a>
                        <a href="/certificates" class="block px-4 py-2 text-sm <?= mobileActive('/certificates', $currentPath) ?>">Certifikat</a>
                        <a href="/certificates/expiring" class="block px-4 py-2 text-sm <?= mobileActive('/certificates/expiring', $currentPath) ?>">⚠️ Certifikat utgår snart</a>
                        <a href="/certificates/expired" class="block px-4 py-2 text-sm <?= mobileActive('/certificates/expired', $currentPath) ?>">🚨 Utgångna certifikat</a>
                        <hr class="my-1 border-gray-200 dark:border-gray-700">
                        <a href="/hr/payroll" class="block px-4 py-2 text-sm <?= mobileActive('/hr/payroll', $currentPath) ?>">Lönehantering</a>
                        <a href="/hr/attendance" class="block px-4 py-2 text-sm <?= mobileActive('/hr/attendance', $currentPath) ?>">Närvaro &amp; Frånvaro</a>
                        <a href="/hr/training" class="block px-4 py-2 text-sm <?= mobileActive('/hr/training', $currentPath) ?>">Utbildningar</a>
                        <a href="/hr/recruitment" class="block px-4 py-2 text-sm <?= mobileActive('/hr/recruitment', $currentPath) ?>">Rekrytering</a>
                        <hr class="my-1 border-gray-200 dark:border-gray-700">
                        <a href="/hr/expenses" class="block px-4 py-2 text-sm <?= mobileActive('/hr/expenses', $currentPath) ?>">Reseräkningar</a>
                    </div>
                </div>

                <!-- 13. Rapporter -->
                <a href="/reports" class="px-3 py-2 rounded-md transition-colors <?= navActive('/reports', $currentPath) ?>">Rapporter</a>

                <!-- 14. Separator + Admin -->
                <div class="w-px h-6 bg-gray-200 dark:bg-gray-700 mx-1"></div>

                <?php if (($_layoutUser['role_level'] ?? 0) >= 7): ?>
                <!-- Admin dropdown -->
                <div class="relative" x-data="{ show: false }" @mouseenter="show=true" @mouseleave="show=false">
                    <button class="px-3 py-2 rounded-md transition-colors text-gray-600 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 flex items-center gap-1">
                        Admin <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="show" x-transition class="absolute right-0 mt-1 w-52 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-50">
                        <a href="/admin" class="block px-4 py-2 text-sm <?= mobileActive('/admin', $currentPath) ?>">Dashboard (Admin)</a>
                        <a href="/admin/settings" class="block px-4 py-2 text-sm <?= mobileActive('/admin/settings', $currentPath) ?>">Systeminställningar</a>
                        <a href="/admin/modules" class="block px-4 py-2 text-sm <?= mobileActive('/admin/modules', $currentPath) ?>">Moduladministration</a>
                        <a href="/admin/site" class="block px-4 py-2 text-sm <?= mobileActive('/admin/site', $currentPath) ?>">Site-inställningar</a>
                        <hr class="my-1 border-gray-200 dark:border-gray-700">
                        <a href="/admin/users" class="block px-4 py-2 text-sm <?= mobileActive('/admin/users', $currentPath) ?>">Användarhantering</a>
                        <a href="/admin/roles" class="block px-4 py-2 text-sm <?= mobileActive('/admin/roles', $currentPath) ?>">Rollhantering</a>
                        <hr class="my-1 border-gray-200 dark:border-gray-700">
                        <a href="/admin/audit-log" class="block px-4 py-2 text-sm <?= mobileActive('/admin/audit-log', $currentPath) ?>">Audit-logg</a>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (($_layoutUser['role_level'] ?? 0) >= 9): ?>
                <!-- SaaS Admin dropdown -->
                <div class="relative" x-data="{ show: false }" @mouseenter="show=true" @mouseleave="show=false">
                    <button class="px-3 py-2 rounded-md transition-colors text-gray-600 dark:text-gray-300 hover:text-purple-600 dark:hover:text-purple-400 flex items-center gap-1">
                        SaaS Admin <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="show" x-transition class="absolute right-0 mt-1 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-50">
                        <a href="/saas-admin" class="block px-4 py-2 text-sm <?= mobileActive('/saas-admin', $currentPath) ?>">Dashboard</a>
                        <a href="/saas-admin/tenants" class="block px-4 py-2 text-sm <?= mobileActive('/saas-admin/tenants', $currentPath) ?>">Kunder</a>
                        <a href="/saas-admin/invoices" class="block px-4 py-2 text-sm <?= mobileActive('/saas-admin/invoices', $currentPath) ?>">Fakturering</a>
                        <a href="/saas-admin/support" class="block px-4 py-2 text-sm <?= mobileActive('/saas-admin/support', $currentPath) ?>">Support</a>
                    </div>
                </div>
                <?php endif; ?>

                <!-- 15. User menu -->
                <div class="relative" x-data="{ show: false }" @mouseenter="show=true" @mouseleave="show=false">
                    <button class="px-3 py-2 rounded-md transition-colors text-gray-600 dark:text-gray-300 hover:text-indigo-600 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <?= htmlspecialchars($_layoutUser['full_name'] ?? $_layoutUser['username'] ?? '') ?>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="show" x-transition class="absolute right-0 mt-1 w-44 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-50">
                        <a href="/my-page" class="block px-4 py-2 text-sm <?= mobileActive('/my-page', $currentPath) ?>">Min Sida</a>
                        <a href="/2fa/setup" class="block px-4 py-2 text-sm <?= mobileActive('/2fa', $currentPath) ?>">2FA-inställningar</a>
                        <hr class="my-1 border-gray-200 dark:border-gray-700">
                        <a href="/logout" class="block px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20">Logga ut</a>
                    </div>
                </div>

                <?php else: ?>
                <a href="/login" class="px-3 py-2 rounded-md transition-colors <?= navActive('/login', $currentPath) ?>">Logga in</a>
                <?php endif; ?>

                <!-- Notification bell -->
                <?php if ($_layoutUser): ?>
                <div class="relative" x-data="{
                    open: false,
                    unread: 0,
                    notifications: [],
                    async fetchUnread() {
                        try {
                            const r = await fetch('/api/notifications/unread-count');
                            const d = await r.json();
                            this.unread = d.count ?? 0;
                        } catch(e) {}
                    },
                    async fetchNotifications() {
                        try {
                            const r = await fetch('/api/notifications/recent');
                            const d = await r.json();
                            this.notifications = d.notifications ?? [];
                        } catch(e) {}
                    },
                    async markAllRead() {
                        await fetch('/notifications/read-all', {
                            method: 'POST',
                            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                            body: '_csrf=' + encodeURIComponent(document.querySelector('meta[name=csrf-token]')?.content ?? '')
                        });
                        this.unread = 0;
                        this.notifications = this.notifications.map(n => ({...n, is_read: 1}));
                    }
                }" x-init="fetchUnread(); setInterval(() => fetchUnread(), 60000)">
                    <button @click="open = !open; if(open) fetchNotifications()"
                            class="relative rounded-lg p-2 text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                            aria-label="Notifikationer">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span x-show="unread > 0"
                              class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white"
                              x-text="unread > 9 ? '9+' : unread"></span>
                    </button>
                    <div x-show="open" x-transition @click.outside="open = false"
                         class="absolute right-0 mt-1 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 z-50">
                        <div class="flex items-center justify-between px-4 py-2 border-b border-gray-200 dark:border-gray-700">
                            <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">Notifikationer</span>
                            <button @click="markAllRead()" x-show="unread > 0"
                                    class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Markera alla som lästa</button>
                        </div>
                        <div class="max-h-72 overflow-y-auto divide-y divide-gray-100 dark:divide-gray-700">
                            <template x-if="notifications.length === 0">
                                <p class="px-4 py-6 text-center text-sm text-gray-400">Inga notifikationer</p>
                            </template>
                            <template x-for="n in notifications" :key="n.id">
                                <div :class="n.is_read ? 'bg-white dark:bg-gray-800' : 'bg-indigo-50 dark:bg-indigo-900/20'"
                                     class="px-4 py-2.5">
                                    <p class="text-sm font-medium text-gray-800 dark:text-gray-100" x-text="n.title"></p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5" x-text="n.message"></p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5" x-text="n.created_at"></p>
                                </div>
                            </template>
                        </div>
                        <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-2">
                            <a href="/notifications" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Visa alla notifikationer</a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Dark mode toggle -->
                <button
                    @click="
                        darkMode = !darkMode;
                        localStorage.setItem('theme', darkMode ? 'dark' : 'light');
                        <?php if ($_layoutUser): ?>
                        fetch('/settings/theme', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: 'theme=' + (darkMode ? 'dark' : 'light') + '&_csrf=' + encodeURIComponent(document.querySelector('meta[name=csrf-token]')?.content ?? '')
                        });
                        <?php endif; ?>
                    "
                    class="rounded-lg p-2 text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                    aria-label="Växla tema"
                >
                    <svg x-show="!darkMode" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    <svg x-show="darkMode" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M12 7a5 5 0 100 10A5 5 0 0012 7z"/></svg>
                </button>
            </div>

            <!-- Mobile hamburger -->
            <button @click="open = !open" class="lg:hidden rounded-md p-2 text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700" aria-label="Meny">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
    </div>

    <!-- Mobile menu -->
    <div x-show="open" x-transition class="lg:hidden border-t border-gray-100 dark:border-gray-700 px-4 py-3 space-y-1 bg-white dark:bg-gray-800 shadow-lg">
        <?php if ($_layoutUser): ?>
        <!-- 1. Dashboard -->
        <a href="/dashboard" class="block rounded px-3 py-2 text-sm <?= mobileActive('/dashboard', $currentPath) ?>">Dashboard</a>

        <!-- 2. Underhåll -->
        <p class="px-3 pt-3 pb-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Underhåll</p>
        <a href="/maintenance" class="block rounded px-3 py-2 text-sm <?= mobileActive('/maintenance', $currentPath) ?>">Dashboard</a>
        <a href="/maintenance/faults" class="block rounded px-3 py-2 text-sm <?= mobileActive('/maintenance/faults', $currentPath) ?>">Felanmälan</a>
        <a href="/maintenance/work-orders" class="block rounded px-3 py-2 text-sm <?= mobileActive('/maintenance/work-orders', $currentPath) ?>">Aktiva arbetsordrar</a>
        <?php if (($_layoutUser['role_level'] ?? 0) >= 5): ?>
        <a href="/maintenance/supervisor" class="block rounded px-3 py-2 text-sm <?= mobileActive('/maintenance/supervisor', $currentPath) ?>">Arbetsorderhantering</a>
        <?php endif; ?>
        <a href="/maintenance/work-orders" class="block rounded px-3 py-2 text-sm <?= mobileActive('/maintenance/work-orders', $currentPath) ?>">Avrapportering</a>
        <a href="/maintenance/work-orders/archive" class="block rounded px-3 py-2 text-sm <?= mobileActive('/maintenance/work-orders/archive', $currentPath) ?>">Historiska arbetsordrar</a>
        <a href="/maintenance/preventive" class="block rounded px-3 py-2 text-sm <?= mobileActive('/maintenance/preventive', $currentPath) ?>">Förebyggande underhåll</a>
        <a href="/maintenance/preventive/calendar" class="block rounded px-3 py-2 text-sm <?= mobileActive('/maintenance/preventive/calendar', $currentPath) ?>">FU-kalender</a>
        <a href="/maintenance/ai" class="block rounded px-3 py-2 text-sm <?= mobileActive('/maintenance/ai', $currentPath) ?>">AI-ingenjör</a>
        <a href="/maintenance/ai/recommendations" class="block rounded px-3 py-2 text-sm <?= mobileActive('/maintenance/ai/recommendations', $currentPath) ?>">Rekommendationer</a>
        <a href="/maintenance/inspections" class="block rounded px-3 py-2 text-sm <?= mobileActive('/maintenance/inspections', $currentPath) ?>">Besiktningar</a>

        <!-- 3. Anläggningsregister -->
        <p class="px-3 pt-3 pb-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Anläggningsregister</p>
        <a href="/objects" class="block rounded px-3 py-2 text-sm <?= mobileActive('/objects', $currentPath) ?>">Navigator</a>
        <a href="/objects/tree" class="block rounded px-3 py-2 text-sm <?= mobileActive('/objects/tree', $currentPath) ?>">Objektträd</a>
        <a href="/equipment" class="block rounded px-3 py-2 text-sm <?= mobileActive('/equipment', $currentPath) ?>">Utrustning</a>
        <a href="/machines" class="block rounded px-3 py-2 text-sm <?= mobileActive('/machines', $currentPath) ?>">Maskiner</a>
        <a href="/maintenance/inspections" class="block rounded px-3 py-2 text-sm <?= mobileActive('/maintenance/inspections', $currentPath) ?>">Besiktningspliktig utrustning</a>

        <!-- 4. Lager -->
        <p class="px-3 pt-3 pb-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Lager</p>
        <a href="/inventory" class="block rounded px-3 py-2 text-sm <?= mobileActive('/inventory', $currentPath) ?>">Lageröversikt</a>
        <a href="/inventory/warehouses" class="block rounded px-3 py-2 text-sm <?= mobileActive('/inventory/warehouses', $currentPath) ?>">Lagerställen</a>
        <a href="/inventory/transactions" class="block rounded px-3 py-2 text-sm <?= mobileActive('/inventory/transactions', $currentPath) ?>">Transaktioner</a>
        <a href="/inventory/receiving" class="block rounded px-3 py-2 text-sm <?= mobileActive('/inventory/receiving', $currentPath) ?>">Inleverans</a>
        <a href="/inventory/issues" class="block rounded px-3 py-2 text-sm <?= mobileActive('/inventory/issues', $currentPath) ?>">Uttag</a>
        <a href="/inventory/stocktaking" class="block rounded px-3 py-2 text-sm <?= mobileActive('/inventory/stocktaking', $currentPath) ?>">Inventering</a>

        <!-- 5. Inköp -->
        <p class="px-3 pt-3 pb-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Inköp</p>
        <a href="/purchasing" class="block rounded px-3 py-2 text-sm <?= mobileActive('/purchasing', $currentPath) ?>">Dashboard</a>
        <a href="/suppliers" class="block rounded px-3 py-2 text-sm <?= mobileActive('/suppliers', $currentPath) ?>">Leverantörsregister</a>
        <a href="/purchasing/supplier-audits" class="block rounded px-3 py-2 text-sm <?= mobileActive('/purchasing/supplier-audits', $currentPath) ?>">Leverantörsaudit</a>
        <a href="/purchasing/requisitions/create" class="block rounded px-3 py-2 text-sm <?= mobileActive('/purchasing/requisitions/create', $currentPath) ?>">Skapa inköpsanmodan</a>
        <a href="/purchasing/requisitions" class="block rounded px-3 py-2 text-sm <?= mobileActive('/purchasing/requisitions', $currentPath) ?>">Ej hanterade anmodan</a>
        <a href="/purchasing/requisitions/history" class="block rounded px-3 py-2 text-sm <?= mobileActive('/purchasing/requisitions/history', $currentPath) ?>">Historiska anmodan</a>
        <a href="/purchasing/orders/create" class="block rounded px-3 py-2 text-sm <?= mobileActive('/purchasing/orders/create', $currentPath) ?>">Skapa inköpsorder</a>
        <a href="/purchasing/orders" class="block rounded px-3 py-2 text-sm <?= mobileActive('/purchasing/orders', $currentPath) ?>">Aktiva inköpsordrar</a>
        <a href="/purchasing/orders/history" class="block rounded px-3 py-2 text-sm <?= mobileActive('/purchasing/orders/history', $currentPath) ?>">Historiska inköpsordrar</a>
        <a href="/purchasing/agreements/create" class="block rounded px-3 py-2 text-sm <?= mobileActive('/purchasing/agreements/create', $currentPath) ?>">Skapa avtal</a>
        <a href="/purchasing/agreements" class="block rounded px-3 py-2 text-sm <?= mobileActive('/purchasing/agreements', $currentPath) ?>">Aktiva avtal</a>
        <a href="/purchasing/agreements/history" class="block rounded px-3 py-2 text-sm <?= mobileActive('/purchasing/agreements/history', $currentPath) ?>">Historiska avtal</a>
        <a href="/purchasing/agreement-templates" class="block rounded px-3 py-2 text-sm <?= mobileActive('/purchasing/agreement-templates', $currentPath) ?>">Avtalsmallar</a>

        <!-- 6. Ekonomi -->
        <p class="px-3 pt-3 pb-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Ekonomi</p>
        <a href="/finance" class="block rounded px-3 py-2 text-sm <?= mobileActive('/finance', $currentPath) ?>">Dashboard</a>
        <a href="/customers" class="block rounded px-3 py-2 text-sm <?= mobileActive('/customers', $currentPath) ?>">Kunder</a>
        <a href="/suppliers" class="block rounded px-3 py-2 text-sm <?= mobileActive('/suppliers', $currentPath) ?>">Leverantörer</a>
        <a href="/finance/invoices-in" class="block rounded px-3 py-2 text-sm <?= mobileActive('/finance/invoices-in', $currentPath) ?>">Leverantörsfakturor</a>
        <a href="/finance/invoices-out/create" class="block rounded px-3 py-2 text-sm <?= mobileActive('/finance/invoices-out/create', $currentPath) ?>">Skapa kundfaktura</a>
        <a href="/finance/accounts" class="block rounded px-3 py-2 text-sm <?= mobileActive('/finance/accounts', $currentPath) ?>">Kontoplan</a>
        <a href="/finance/cost-centers" class="block rounded px-3 py-2 text-sm <?= mobileActive('/finance/cost-centers', $currentPath) ?>">Kostnadsställen</a>
        <a href="/finance/journal" class="block rounded px-3 py-2 text-sm <?= mobileActive('/finance/journal', $currentPath) ?>">Bokföring/Verifikationer</a>
        <a href="/finance/reports/ledger" class="block rounded px-3 py-2 text-sm <?= mobileActive('/finance/reports/ledger', $currentPath) ?>">Huvudbok</a>
        <a href="/finance/reports/trial-balance" class="block rounded px-3 py-2 text-sm <?= mobileActive('/finance/reports/trial-balance', $currentPath) ?>">Resultaträkning</a>
        <a href="/finance/reports/balance-sheet" class="block rounded px-3 py-2 text-sm <?= mobileActive('/finance/reports/balance-sheet', $currentPath) ?>">Balansräkning</a>
        <a href="/finance/reports/kpi" class="block rounded px-3 py-2 text-sm <?= mobileActive('/finance/reports/kpi', $currentPath) ?>">KPI från avdelningar</a>
        <a href="/finance/reports/stocktaking" class="block rounded px-3 py-2 text-sm <?= mobileActive('/finance/reports/stocktaking', $currentPath) ?>">Inventering</a>
        <a href="/finance/account-groups" class="block rounded px-3 py-2 text-sm <?= mobileActive('/finance/account-groups', $currentPath) ?>">Kontoplansgrupper</a>
        <a href="/finance/assets" class="block rounded px-3 py-2 text-sm <?= mobileActive('/finance/assets', $currentPath) ?>">Anläggningstillgångar</a>
        <a href="/finance/budgets" class="block rounded px-3 py-2 text-sm <?= mobileActive('/finance/budgets', $currentPath) ?>">Budgetar</a>

        <!-- 7. Health & Safety -->
        <p class="px-3 pt-3 pb-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Health &amp; Safety</p>
        <a href="/safety" class="block rounded px-3 py-2 text-sm <?= mobileActive('/safety', $currentPath) ?>">Dashboard</a>
        <a href="/safety/risks" class="block rounded px-3 py-2 text-sm <?= mobileActive('/safety/risks', $currentPath) ?>">Hantera risker &amp; faror</a>
        <a href="/safety/audits/create" class="block rounded px-3 py-2 text-sm <?= mobileActive('/safety/audits/create', $currentPath) ?>">Skapa safety audit</a>
        <a href="/safety/audit-templates" class="block rounded px-3 py-2 text-sm <?= mobileActive('/safety/audit-templates', $currentPath) ?>">Hantera audit-mallar</a>
        <a href="/safety/audits/pending" class="block rounded px-3 py-2 text-sm <?= mobileActive('/safety/audits/pending', $currentPath) ?>">Ej slutförda åtgärder</a>
        <a href="/safety/audits/completed" class="block rounded px-3 py-2 text-sm <?= mobileActive('/safety/audits/completed', $currentPath) ?>">Slutförda åtgärder</a>
        <a href="/safety/emergency" class="block rounded px-3 py-2 text-sm <?= mobileActive('/safety/emergency', $currentPath) ?>">Aktiv krisplan</a>
        <a href="/safety/emergency/procedures" class="block rounded px-3 py-2 text-sm <?= mobileActive('/safety/emergency/procedures', $currentPath) ?>">Hantera krisplan</a>
        <a href="/safety/emergency/drills/create" class="block rounded px-3 py-2 text-sm <?= mobileActive('/safety/emergency/drills/create', $currentPath) ?>">Skapa nödlägesövning</a>
        <a href="/safety/emergency/drills" class="block rounded px-3 py-2 text-sm <?= mobileActive('/safety/emergency/drills', $currentPath) ?>">Lista nödlägesövningar</a>
        <a href="/safety/emergency/drills/templates" class="block rounded px-3 py-2 text-sm <?= mobileActive('/safety/emergency/drills/templates', $currentPath) ?>">Hantera mallar</a>
        <a href="/safety/resources" class="block rounded px-3 py-2 text-sm <?= mobileActive('/safety/resources', $currentPath) ?>">Lista nödresurser</a>
        <a href="/safety/resources/create" class="block rounded px-3 py-2 text-sm <?= mobileActive('/safety/resources/create', $currentPath) ?>">Hantera nödresurser</a>
        <a href="/safety/resources/overdue" class="block rounded px-3 py-2 text-sm <?= mobileActive('/safety/resources/overdue', $currentPath) ?>">Förfallna kontroller</a>

        <!-- 8. Produktion -->
        <p class="px-3 pt-3 pb-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Produktion</p>
        <a href="/production" class="block rounded px-3 py-2 text-sm <?= mobileActive('/production', $currentPath) ?>">Dashboard</a>
        <a href="/production/lines" class="block rounded px-3 py-2 text-sm <?= mobileActive('/production/lines', $currentPath) ?>">Produktionslinjer</a>
        <a href="/production/orders" class="block rounded px-3 py-2 text-sm <?= mobileActive('/production/orders', $currentPath) ?>">Produktionsplanering</a>
        <a href="/production/products" class="block rounded px-3 py-2 text-sm <?= mobileActive('/production/products', $currentPath) ?>">Skapa produkt</a>
        <a href="/production/stock" class="block rounded px-3 py-2 text-sm <?= mobileActive('/production/stock', $currentPath) ?>">Produktionslager</a>
        <a href="/production/stock/manage" class="block rounded px-3 py-2 text-sm <?= mobileActive('/production/stock/manage', $currentPath) ?>">Hantera produktionslager</a>

        <!-- 9. Försäljning -->
        <p class="px-3 pt-3 pb-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Försäljning</p>
        <a href="/sales" class="block rounded px-3 py-2 text-sm <?= mobileActive('/sales', $currentPath) ?>">Dashboard</a>
        <a href="/customers" class="block rounded px-3 py-2 text-sm <?= mobileActive('/customers', $currentPath) ?>">Kundregister</a>
        <a href="/sales/quotes/create" class="block rounded px-3 py-2 text-sm <?= mobileActive('/sales/quotes/create', $currentPath) ?>">Skapa offert</a>
        <a href="/sales/quotes/accepted" class="block rounded px-3 py-2 text-sm <?= mobileActive('/sales/quotes/accepted', $currentPath) ?>">Accepterade offerter</a>
        <a href="/sales/quotes" class="block rounded px-3 py-2 text-sm <?= mobileActive('/sales/quotes', $currentPath) ?>">Aktiva offerter</a>
        <a href="/sales/quotes/history" class="block rounded px-3 py-2 text-sm <?= mobileActive('/sales/quotes/history', $currentPath) ?>">Historiska offerter</a>
        <a href="/sales/quotes/templates" class="block rounded px-3 py-2 text-sm <?= mobileActive('/sales/quotes/templates', $currentPath) ?>">Offertmallar</a>
        <a href="/finance/invoices-out" class="block rounded px-3 py-2 text-sm <?= mobileActive('/finance/invoices-out', $currentPath) ?>">Fakturering</a>
        <a href="/sales/pricing" class="block rounded px-3 py-2 text-sm <?= mobileActive('/sales/pricing', $currentPath) ?>">Lista prislistor</a>
        <a href="/sales/pricing/manage" class="block rounded px-3 py-2 text-sm <?= mobileActive('/sales/pricing/manage', $currentPath) ?>">Hantera prislistor</a>

        <!-- 10. CS & Transport -->
        <p class="px-3 pt-3 pb-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">CS &amp; Transport</p>
        <a href="/cs" class="block rounded px-3 py-2 text-sm <?= mobileActive('/cs', $currentPath) ?>">CS Dashboard</a>
        <a href="/cs/tickets" class="block rounded px-3 py-2 text-sm <?= mobileActive('/cs/tickets', $currentPath) ?>">Alla ärenden</a>
        <a href="/cs/tickets/create" class="block rounded px-3 py-2 text-sm <?= mobileActive('/cs/tickets/create', $currentPath) ?>">Nytt ärende</a>
        <a href="/cs/tickets/my" class="block rounded px-3 py-2 text-sm <?= mobileActive('/cs/tickets/my', $currentPath) ?>">Mina ärenden</a>
        <a href="/transport" class="block rounded px-3 py-2 text-sm <?= mobileActive('/transport', $currentPath) ?>">Transport Dashboard</a>
        <a href="/transport/orders" class="block rounded px-3 py-2 text-sm <?= mobileActive('/transport/orders', $currentPath) ?>">Transportordrar</a>
        <a href="/transport/orders/create" class="block rounded px-3 py-2 text-sm <?= mobileActive('/transport/orders/create', $currentPath) ?>">Ny transportorder</a>
        <a href="/transport/carriers" class="block rounded px-3 py-2 text-sm <?= mobileActive('/transport/carriers', $currentPath) ?>">Transportörer</a>

        <!-- 11. Projekt -->
        <p class="px-3 pt-3 pb-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Projekt</p>
        <a href="/projects" class="block rounded px-3 py-2 text-sm <?= mobileActive('/projects', $currentPath) ?>">Dashboard</a>
        <a href="/projects" class="block rounded px-3 py-2 text-sm <?= mobileActive('/projects', $currentPath) ?>">Alla projekt</a>
        <a href="/projects/create" class="block rounded px-3 py-2 text-sm <?= mobileActive('/projects/create', $currentPath) ?>">Skapa projekt</a>

        <!-- 12. HR -->
        <p class="px-3 pt-3 pb-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">HR</p>
        <a href="/hr" class="block rounded px-3 py-2 text-sm font-semibold <?= mobileActive('/hr', $currentPath) ?>">🏠 HR Dashboard</a>
        <a href="/departments" class="block rounded px-3 py-2 text-sm <?= mobileActive('/departments', $currentPath) ?>">Avdelningar</a>
        <a href="/employees" class="block rounded px-3 py-2 text-sm <?= mobileActive('/employees', $currentPath) ?>">Personal</a>
        <a href="/certificates" class="block rounded px-3 py-2 text-sm <?= mobileActive('/certificates', $currentPath) ?>">Certifikat</a>
        <a href="/certificates/expiring" class="block rounded px-3 py-2 text-sm <?= mobileActive('/certificates/expiring', $currentPath) ?>">⚠️ Certifikat utgår snart</a>
        <a href="/certificates/expired" class="block rounded px-3 py-2 text-sm <?= mobileActive('/certificates/expired', $currentPath) ?>">🚨 Utgångna certifikat</a>
        <a href="/hr/payroll" class="block rounded px-3 py-2 text-sm <?= mobileActive('/hr/payroll', $currentPath) ?>">Lönehantering</a>
        <a href="/hr/attendance" class="block rounded px-3 py-2 text-sm <?= mobileActive('/hr/attendance', $currentPath) ?>">Närvaro &amp; Frånvaro</a>
        <a href="/hr/training" class="block rounded px-3 py-2 text-sm <?= mobileActive('/hr/training', $currentPath) ?>">Utbildningar</a>
        <a href="/hr/recruitment" class="block rounded px-3 py-2 text-sm <?= mobileActive('/hr/recruitment', $currentPath) ?>">Rekrytering</a>
        <a href="/hr/expenses" class="block rounded px-3 py-2 text-sm <?= mobileActive('/hr/expenses', $currentPath) ?>">Reseräkningar</a>

        <!-- 13. Rapporter -->
        <p class="px-3 pt-3 pb-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Rapporter</p>
        <a href="/reports" class="block rounded px-3 py-2 text-sm <?= mobileActive('/reports', $currentPath) ?>">Rapportöversikt</a>

        <hr class="my-2 border-gray-200 dark:border-gray-700">
        <!-- 14. Admin -->
        <?php if (($_layoutUser['role_level'] ?? 0) >= 7): ?>
        <p class="px-3 pt-2 pb-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Admin</p>
        <a href="/admin" class="block rounded px-3 py-2 text-sm <?= mobileActive('/admin', $currentPath) ?>">Dashboard (Admin)</a>
        <a href="/admin/settings" class="block rounded px-3 py-2 text-sm <?= mobileActive('/admin/settings', $currentPath) ?>">Systeminställningar</a>
        <a href="/admin/modules" class="block rounded px-3 py-2 text-sm <?= mobileActive('/admin/modules', $currentPath) ?>">Moduladministration</a>
        <a href="/admin/site" class="block rounded px-3 py-2 text-sm <?= mobileActive('/admin/site', $currentPath) ?>">Site-inställningar</a>
        <a href="/admin/users" class="block rounded px-3 py-2 text-sm <?= mobileActive('/admin/users', $currentPath) ?>">Användarhantering</a>
        <a href="/admin/roles" class="block rounded px-3 py-2 text-sm <?= mobileActive('/admin/roles', $currentPath) ?>">Rollhantering</a>
        <a href="/admin/audit-log" class="block rounded px-3 py-2 text-sm <?= mobileActive('/admin/audit-log', $currentPath) ?>">Audit-logg</a>
        <?php endif; ?>
        <?php if (($_layoutUser['role_level'] ?? 0) >= 9): ?>
        <p class="px-3 pt-2 pb-1 text-xs font-semibold text-purple-500 uppercase tracking-wider">SaaS Admin</p>
        <a href="/saas-admin" class="block rounded px-3 py-2 text-sm <?= mobileActive('/saas-admin', $currentPath) ?>">Dashboard</a>
        <a href="/saas-admin/tenants" class="block rounded px-3 py-2 text-sm <?= mobileActive('/saas-admin/tenants', $currentPath) ?>">Kunder</a>
        <a href="/saas-admin/invoices" class="block rounded px-3 py-2 text-sm <?= mobileActive('/saas-admin/invoices', $currentPath) ?>">Fakturering</a>
        <a href="/saas-admin/support" class="block rounded px-3 py-2 text-sm <?= mobileActive('/saas-admin/support', $currentPath) ?>">Support</a>
        <?php endif; ?>
        <!-- 15. User menu -->
        <a href="/my-page" class="block rounded px-3 py-2 text-sm <?= mobileActive('/my-page', $currentPath) ?>">Min Sida</a>
        <a href="/2fa/setup" class="block rounded px-3 py-2 text-sm <?= mobileActive('/2fa', $currentPath) ?>">2FA-inställningar</a>
        <a href="/logout" class="block rounded px-3 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20">Logga ut</a>
        <?php else: ?>
        <a href="/login" class="block rounded px-3 py-2 text-sm <?= mobileActive('/login', $currentPath) ?>">Logga in</a>
        <?php endif; ?>
    </div>
</nav>

<!-- Main content -->
<main class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
    <?php
    $flashError   = App\Core\Flash::get('error');
    $flashSuccess = App\Core\Flash::get('success');
    $flashWarning = App\Core\Flash::get('warning');
    $flashInfo    = App\Core\Flash::get('info');
    $toasts = [];
    if ($flashSuccess !== null) {
        $toasts[] = ['type' => 'success', 'message' => $flashSuccess];
    }
    if ($flashError !== null) {
        $toasts[] = ['type' => 'error', 'message' => $flashError];
    }
    if ($flashWarning !== null) {
        $toasts[] = ['type' => 'warning', 'message' => $flashWarning];
    }
    if ($flashInfo !== null) {
        $toasts[] = ['type' => 'info', 'message' => $flashInfo];
    }
    ?>
    <!-- Toast container (B4) -->
    <div id="toast-container" class="fixed top-4 right-4 z-[100] flex flex-col gap-2 w-80"
         x-data="{
             toasts: <?= json_encode($toasts, JSON_UNESCAPED_UNICODE) ?>,
             remove(i) { this.toasts.splice(i, 1); }
         }"
         x-init="toasts.forEach((t, i) => setTimeout(() => remove(i), 5000))">
        <template x-for="(toast, i) in toasts" :key="i">
            <div x-show="true" x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-x-4"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 :class="{
                     'bg-green-50 dark:bg-green-900/30 border-green-400 text-green-800 dark:text-green-300': toast.type === 'success',
                     'bg-red-50 dark:bg-red-900/30 border-red-400 text-red-800 dark:text-red-300': toast.type === 'error',
                     'bg-yellow-50 dark:bg-yellow-900/30 border-yellow-400 text-yellow-800 dark:text-yellow-300': toast.type === 'warning',
                     'bg-blue-50 dark:bg-blue-900/30 border-blue-400 text-blue-800 dark:text-blue-300': toast.type === 'info'
                 }"
                 class="flex items-start gap-3 rounded-lg border px-4 py-3 shadow-lg text-sm">
                <!-- Icon -->
                <svg x-show="toast.type === 'success'" class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <svg x-show="toast.type === 'error'" class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                <svg x-show="toast.type === 'warning'" class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <svg x-show="toast.type === 'info'" class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="flex-1" x-text="toast.message"></span>
                <button @click="remove(i)" class="opacity-60 hover:opacity-100 transition-opacity" aria-label="Stäng">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </template>
    </div>

    <!-- Loading spinner (B5) -->
    <div id="loading-overlay" class="hidden fixed inset-0 z-[200] flex items-center justify-center bg-black/20 dark:bg-black/40">
        <div class="flex flex-col items-center gap-3 bg-white dark:bg-gray-800 rounded-xl shadow-xl px-8 py-6">
            <svg class="animate-spin h-8 w-8 text-indigo-600 dark:text-indigo-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <span class="text-sm text-gray-600 dark:text-gray-300">Bearbetar...</span>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('form[data-loading]').forEach(function (form) {
            form.addEventListener('submit', function () {
                document.getElementById('loading-overlay').classList.remove('hidden');
            });
        });
    });
    </script>

    <?= $content ?>
</main>

<!-- Footer -->
<footer class="border-t border-gray-200 dark:border-gray-700 mt-16 py-6 text-center text-xs text-gray-400 dark:text-gray-500">
    &copy; <?= date('Y') ?> ZYNC ERP. All rights reserved.
</footer>

</body>
</html>
