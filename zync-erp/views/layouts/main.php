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

                <a href="/dashboard" class="px-3 py-2 rounded-md transition-colors <?= navActive('/dashboard', $currentPath) ?>">Dashboard</a>

                <!-- Dropdown: Kontakter -->
                <div class="relative" x-data="{ show: false }" @mouseenter="show=true" @mouseleave="show=false">
                    <button class="px-3 py-2 rounded-md transition-colors text-gray-600 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 flex items-center gap-1">
                        Kontakter <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="show" x-transition class="absolute left-0 mt-1 w-44 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-50">
                        <a href="/customers" class="block px-4 py-2 text-sm <?= mobileActive('/customers', $currentPath) ?>">Kunder</a>
                        <a href="/suppliers" class="block px-4 py-2 text-sm <?= mobileActive('/suppliers', $currentPath) ?>">Leverantörer</a>
                    </div>
                </div>

                <!-- Dropdown: Organisation -->
                <div class="relative" x-data="{ show: false }" @mouseenter="show=true" @mouseleave="show=false">
                    <button class="px-3 py-2 rounded-md transition-colors text-gray-600 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 flex items-center gap-1">
                        Organisation <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="show" x-transition class="absolute left-0 mt-1 w-44 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-50">
                        <a href="/departments" class="block px-4 py-2 text-sm <?= mobileActive('/departments', $currentPath) ?>">Avdelningar</a>
                        <a href="/employees" class="block px-4 py-2 text-sm <?= mobileActive('/employees', $currentPath) ?>">Personal</a>
                        <a href="/certificates" class="block px-4 py-2 text-sm <?= mobileActive('/certificates', $currentPath) ?>">Certifikat</a>
                    </div>
                </div>

                <!-- Dropdown: Drift -->
                <div class="relative" x-data="{ show: false }" @mouseenter="show=true" @mouseleave="show=false">
                    <button class="px-3 py-2 rounded-md transition-colors text-gray-600 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 flex items-center gap-1">
                        Drift <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="show" x-transition class="absolute left-0 mt-1 w-52 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-50">
                        <a href="/equipment" class="block px-4 py-2 text-sm <?= mobileActive('/equipment', $currentPath) ?>">Utrustning</a>
                        <a href="/machines" class="block px-4 py-2 text-sm <?= mobileActive('/machines', $currentPath) ?>">Maskiner</a>
                        <a href="/maintenance/faults" class="block px-4 py-2 text-sm <?= mobileActive('/maintenance', $currentPath) ?>">Underhåll</a>
                        <hr class="my-1 border-gray-200 dark:border-gray-700">
                        <a href="/maintenance/work-orders" class="block px-4 py-2 text-sm <?= mobileActive('/maintenance/work-orders', $currentPath) ?>">Arbetsordrar</a>
                        <a href="/maintenance/inspections" class="block px-4 py-2 text-sm <?= mobileActive('/maintenance/inspections', $currentPath) ?>">Besiktningar</a>
                        <?php if (($_layoutUser['role_level'] ?? 0) >= 5): ?><a href="/maintenance/supervisor" class="block px-4 py-2 text-sm <?= mobileActive('/maintenance/supervisor', $currentPath) ?>">Arbetsledare</a><?php endif; ?>
                    </div>
                </div>

                <!-- Dropdown: Lager & Inköp -->
                <div class="relative" x-data="{ show: false }" @mouseenter="show=true" @mouseleave="show=false">
                    <button class="px-3 py-2 rounded-md transition-colors text-gray-600 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 flex items-center gap-1">
                        Lager & Inköp <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="show" x-transition class="absolute left-0 mt-1 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-50">
                        <a href="/articles" class="block px-4 py-2 text-sm <?= mobileActive('/articles', $currentPath) ?>">Artiklar</a>
                        <a href="/inventory" class="block px-4 py-2 text-sm <?= mobileActive('/inventory', $currentPath) ?>">Lager</a>
                        <a href="/purchasing" class="block px-4 py-2 text-sm <?= mobileActive('/purchasing', $currentPath) ?>">Inköp</a>
                    </div>
                </div>

                <!-- Dropdown: Ekonomi -->
                <div class="relative" x-data="{ show: false }" @mouseenter="show=true" @mouseleave="show=false">
                    <button class="px-3 py-2 rounded-md transition-colors text-gray-600 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 flex items-center gap-1">
                        Ekonomi <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="show" x-transition class="absolute left-0 mt-1 w-52 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-50">
                        <a href="/finance" class="block px-4 py-2 text-sm <?= mobileActive('/finance', $currentPath) ?>">Översikt</a>
                        <hr class="my-1 border-gray-200 dark:border-gray-700">
                        <a href="/finance/invoices-out" class="block px-4 py-2 text-sm <?= mobileActive('/finance/invoices-out', $currentPath) ?>">Kundfakturor</a>
                        <a href="/finance/invoices-in" class="block px-4 py-2 text-sm <?= mobileActive('/finance/invoices-in', $currentPath) ?>">Lev.fakturor</a>
                        <hr class="my-1 border-gray-200 dark:border-gray-700">
                        <a href="/finance/journal" class="block px-4 py-2 text-sm <?= mobileActive('/finance/journal', $currentPath) ?>">Bokföring</a>
                        <a href="/finance/accounts" class="block px-4 py-2 text-sm <?= mobileActive('/finance/accounts', $currentPath) ?>">Kontoplan</a>
                        <a href="/finance/cost-centers" class="block px-4 py-2 text-sm <?= mobileActive('/finance/cost-centers', $currentPath) ?>">Kostnadsställen</a>
                        <hr class="my-1 border-gray-200 dark:border-gray-700">
                        <a href="/finance/reports/ledger" class="block px-4 py-2 text-sm <?= mobileActive('/finance/reports/ledger', $currentPath) ?>">Huvudbok</a>
                        <a href="/finance/reports/trial-balance" class="block px-4 py-2 text-sm <?= mobileActive('/finance/reports/trial-balance', $currentPath) ?>">Resultaträkning</a>
                    </div>
                </div>

                <!-- Dropdown: Hälsa & Säkerhet -->
                <div class="relative" x-data="{ show: false }" @mouseenter="show=true" @mouseleave="show=false">
                    <button class="px-3 py-2 rounded-md transition-colors text-gray-600 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 flex items-center gap-1">
                        H&S <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="show" x-transition class="absolute left-0 mt-1 w-56 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-50">
                        <a href="/safety" class="block px-4 py-2 text-sm <?= mobileActive('/safety', $currentPath) ?>">Översikt</a>
                        <hr class="my-1 border-gray-200 dark:border-gray-700">
                        <a href="/safety/risks" class="block px-4 py-2 text-sm <?= mobileActive('/safety/risks', $currentPath) ?>">Risker &amp; Faror</a>
                        <a href="/safety/reports" class="block px-4 py-2 text-sm <?= mobileActive('/safety/reports', $currentPath) ?>">Riskrapporter</a>
                        <hr class="my-1 border-gray-200 dark:border-gray-700">
                        <a href="/safety/audits" class="block px-4 py-2 text-sm <?= mobileActive('/safety/audits', $currentPath) ?>">Audits</a>
                        <a href="/safety/audit-templates" class="block px-4 py-2 text-sm <?= mobileActive('/safety/audit-templates', $currentPath) ?>">Audit-mallar</a>
                        <hr class="my-1 border-gray-200 dark:border-gray-700">
                        <a href="/safety/emergency" class="block px-4 py-2 text-sm <?= mobileActive('/safety/emergency', $currentPath) ?>">Krishantering</a>
                        <hr class="my-1 border-gray-200 dark:border-gray-700">
                        <a href="/safety/resources" class="block px-4 py-2 text-sm <?= mobileActive('/safety/resources', $currentPath) ?>">Nödresurser</a>
                        <a href="/safety/resources/overdue" class="block px-4 py-2 text-sm <?= mobileActive('/safety/resources/overdue', $currentPath) ?>">Förfallna kontroller</a>
                    </div>
                </div>

                <!-- Separator -->
                <div class="w-px h-6 bg-gray-200 dark:bg-gray-700 mx-1"></div>

                <?php if (($_layoutUser['role_level'] ?? 0) >= 7): ?>
                <a href="/admin" class="px-3 py-2 rounded-md transition-colors <?= navActive('/admin', $currentPath) ?>">Admin</a>
                <?php endif; ?>

                <!-- User menu -->
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
        <a href="/dashboard" class="block rounded px-3 py-2 text-sm <?= mobileActive('/dashboard', $currentPath) ?>">Dashboard</a>

        <p class="px-3 pt-3 pb-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Kontakter</p>
        <a href="/customers" class="block rounded px-3 py-2 text-sm <?= mobileActive('/customers', $currentPath) ?>">Kunder</a>
        <a href="/suppliers" class="block rounded px-3 py-2 text-sm <?= mobileActive('/suppliers', $currentPath) ?>">Leverantörer</a>

        <p class="px-3 pt-3 pb-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Organisation</p>
        <a href="/departments" class="block rounded px-3 py-2 text-sm <?= mobileActive('/departments', $currentPath) ?>">Avdelningar</a>
        <a href="/employees" class="block rounded px-3 py-2 text-sm <?= mobileActive('/employees', $currentPath) ?>">Personal</a>
        <a href="/certificates" class="block rounded px-3 py-2 text-sm <?= mobileActive('/certificates', $currentPath) ?>">Certifikat</a>

        <p class="px-3 pt-3 pb-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Drift</p>
        <a href="/equipment" class="block rounded px-3 py-2 text-sm <?= mobileActive('/equipment', $currentPath) ?>">Utrustning</a>
        <a href="/machines" class="block rounded px-3 py-2 text-sm <?= mobileActive('/machines', $currentPath) ?>">Maskiner</a>
        <a href="/maintenance/faults" class="block rounded px-3 py-2 text-sm <?= mobileActive('/maintenance', $currentPath) ?>">Underhåll</a>
        <a href="/maintenance/work-orders" class="block rounded px-3 py-2 text-sm <?= mobileActive('/maintenance/work-orders', $currentPath) ?>">Arbetsordrar</a>
        <a href="/maintenance/inspections" class="block rounded px-3 py-2 text-sm <?= mobileActive('/maintenance/inspections', $currentPath) ?>">Besiktningar</a>
        <?php if (($_layoutUser['role_level'] ?? 0) >= 5): ?><a href="/maintenance/supervisor" class="block rounded px-3 py-2 text-sm <?= mobileActive('/maintenance/supervisor', $currentPath) ?>">Arbetsledare</a><?php endif; ?>

        <p class="px-3 pt-3 pb-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Lager & Inköp</p>
        <a href="/articles" class="block rounded px-3 py-2 text-sm <?= mobileActive('/articles', $currentPath) ?>">Artiklar</a>
        <a href="/inventory" class="block rounded px-3 py-2 text-sm <?= mobileActive('/inventory', $currentPath) ?>">Lager</a>
        <a href="/purchasing" class="block rounded px-3 py-2 text-sm <?= mobileActive('/purchasing', $currentPath) ?>">Inköp</a>

        <p class="px-3 pt-3 pb-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Ekonomi</p>
        <a href="/finance" class="block rounded px-3 py-2 text-sm <?= mobileActive('/finance', $currentPath) ?>">Översikt</a>
        <a href="/finance/invoices-out" class="block rounded px-3 py-2 text-sm <?= mobileActive('/finance/invoices-out', $currentPath) ?>">Kundfakturor</a>
        <a href="/finance/invoices-in" class="block rounded px-3 py-2 text-sm <?= mobileActive('/finance/invoices-in', $currentPath) ?>">Lev.fakturor</a>
        <a href="/finance/journal" class="block rounded px-3 py-2 text-sm <?= mobileActive('/finance/journal', $currentPath) ?>">Bokföring</a>
        <a href="/finance/accounts" class="block rounded px-3 py-2 text-sm <?= mobileActive('/finance/accounts', $currentPath) ?>">Kontoplan</a>

        <p class="px-3 pt-3 pb-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Hälsa &amp; Säkerhet</p>
        <a href="/safety" class="block rounded px-3 py-2 text-sm <?= mobileActive('/safety', $currentPath) ?>">Översikt</a>
        <a href="/safety/risks" class="block rounded px-3 py-2 text-sm <?= mobileActive('/safety/risks', $currentPath) ?>">Risker &amp; Faror</a>
        <a href="/safety/reports" class="block rounded px-3 py-2 text-sm <?= mobileActive('/safety/reports', $currentPath) ?>">Riskrapporter</a>
        <a href="/safety/audits" class="block rounded px-3 py-2 text-sm <?= mobileActive('/safety/audits', $currentPath) ?>">Audits</a>
        <a href="/safety/audit-templates" class="block rounded px-3 py-2 text-sm <?= mobileActive('/safety/audit-templates', $currentPath) ?>">Audit-mallar</a>
        <a href="/safety/emergency" class="block rounded px-3 py-2 text-sm <?= mobileActive('/safety/emergency', $currentPath) ?>">Krishantering</a>
        <a href="/safety/resources" class="block rounded px-3 py-2 text-sm <?= mobileActive('/safety/resources', $currentPath) ?>">Nödresurser</a>
        <a href="/safety/resources/overdue" class="block rounded px-3 py-2 text-sm <?= mobileActive('/safety/resources/overdue', $currentPath) ?>">Förfallna kontroller</a>

        <hr class="my-2 border-gray-200 dark:border-gray-700">
        <?php if (($_layoutUser['role_level'] ?? 0) >= 7): ?>
        <a href="/admin" class="block rounded px-3 py-2 text-sm <?= mobileActive('/admin', $currentPath) ?>">Admin</a>
        <?php endif; ?>
        <a href="/my-page" class="block rounded px-3 py-2 text-sm <?= mobileActive('/my-page', $currentPath) ?>">Min Sida</a>
        <a href="/2fa/setup" class="block rounded px-3 py-2 text-sm <?= mobileActive('/2fa', $currentPath) ?>">2FA</a>
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
    ?>
    <?php if ($flashError !== null): ?>
        <div class="mb-6 rounded-lg bg-red-50 dark:bg-red-900/30 px-4 py-3 text-sm text-red-700 dark:text-red-400">
            <?= htmlspecialchars($flashError, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>
    <?php if ($flashSuccess !== null): ?>
        <div class="mb-6 rounded-lg bg-green-50 dark:bg-green-900/30 px-4 py-3 text-sm text-green-700 dark:text-green-400">
            <?= htmlspecialchars($flashSuccess, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <?= $content ?>
</main>

<!-- Footer -->
<footer class="border-t border-gray-200 dark:border-gray-700 mt-16 py-6 text-center text-xs text-gray-400 dark:text-gray-500">
    &copy; <?= date('Y') ?> ZYNC ERP. All rights reserved.
</footer>

</body>
</html>
