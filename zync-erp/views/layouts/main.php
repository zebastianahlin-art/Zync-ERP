<!DOCTYPE html>
<?php
$_layoutUser  = \App\Core\Auth::user();
$currentPath = parse_url($_SERVER["REQUEST_URI"] ?? "/", PHP_URL_PATH) ?? "/";
$_dbTheme     = $_layoutUser['theme'] ?? 'light';
$_themeJs     = htmlspecialchars($_dbTheme, ENT_QUOTES, 'UTF-8');
?>
<html lang="sv" class="h-full bg-gray-50 dark:bg-gray-900"
      x-data="{ darkMode: localStorage.getItem('theme') !== null ? localStorage.getItem('theme') === 'dark' : '<?= $_themeJs ?>' === 'dark' }"
      :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'ZYNC ERP', ENT_QUOTES, 'UTF-8') ?></title>

    <!-- Tailwind CSS dark mode config -->
    <script>tailwind = { darkMode: 'class' };</script>

    <!-- Tailwind CSS (CDN – no build step required) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js (CDN) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="h-full font-sans antialiased bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">

    <!-- Navigation -->
    <nav class="bg-white dark:bg-gray-800 shadow-sm" x-data="{ open: false }">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <div class="flex items-center space-x-2">
                    <a href="/" class="text-xl font-bold tracking-tight text-indigo-600 dark:text-indigo-400">ZYNC ERP</a>
                </div>
                <div class="hidden sm:flex sm:items-center sm:space-x-6 text-sm text-gray-600 dark:text-gray-300">
                    <?php $currentUser = $_layoutUser; ?>
                    <?php if ($currentUser): ?>
                        <a href="/dashboard" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Dashboard</a>
                        <a href="/customers" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Kunder</a>
                        <a href="/suppliers" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Leverantörer</a>
                        <a href="/departments" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Avdelningar</a>
                        <a href="/employees" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Personal</a>
                        <a href="/certificates" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Certifikat</a>
                        <a href="/equipment" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Utrustning</a>
                        <a href="/maintenance/faults" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Underhåll</a>
                <a href="/inventory" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium <?= strpos($currentPath, '/inventory') === 0 ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' ?> transition-colors">
                <a href="/machines" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium <?= strpos($currentPath, '/machines') === 0 ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' ?> transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3"/></svg>
                    Maskiner
                </a>
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    Lager
                </a>
                        <a href="/articles" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Artiklar</a>
                        <?php if (($currentUser['role_level'] ?? 0) >= 7): ?>
                            <a href="/admin" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Admin</a>
                        <?php endif; ?>
                        <a href="/2fa/setup" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">2FA</a>
                        <a href="/my-page" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Min Sida</a>
                        <a href="/logout" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Logga ut</a>
                    <?php else: ?>
                        <a href="/login" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Logga in</a>
                    <?php endif; ?>

                    <!-- Dark mode toggle -->
                    <button
                        @click="
                            darkMode = !darkMode;
                            localStorage.setItem('theme', darkMode ? 'dark' : 'light');
                            <?php if ($currentUser): ?>
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
                        <svg x-show="!darkMode" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                        <svg x-show="darkMode" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M12 7a5 5 0 100 10A5 5 0 0012 7z"/>
                        </svg>
                    </button>
                </div>
                <!-- Mobile menu toggle -->
                <button
                    @click="open = !open"
                    class="sm:hidden rounded-md p-2 text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700"
                    aria-label="Toggle menu"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>
        <!-- Mobile menu -->
        <div x-show="open" class="sm:hidden border-t border-gray-100 dark:border-gray-700 px-4 py-2 space-y-1">
            <?php if ($currentUser): ?>
                <a href="/dashboard" class="block rounded px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-gray-700 hover:text-indigo-600">Dashboard</a>
                <a href="/customers" class="block rounded px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-gray-700 hover:text-indigo-600">Kunder</a>
                <a href="/suppliers" class="block rounded px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-gray-700 hover:text-indigo-600">Leverantörer</a>
                <a href="/departments" class="block rounded px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-gray-700 hover:text-indigo-600">Avdelningar</a>
                <a href="/employees" class="block rounded px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-gray-700 hover:text-indigo-600">Personal</a>
                <a href="/certificates" class="block rounded px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-gray-700 hover:text-indigo-600">Certifikat</a>
                <a href="/equipment" class="block rounded px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-gray-700 hover:text-indigo-600">Utrustning</a>
                <a href="/maintenance/faults" class="block rounded px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-gray-700 hover:text-indigo-600">Underhåll</a>
                <a href="/inventory" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium <?= strpos($currentPath, '/inventory') === 0 ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' ?> transition-colors">
                <a href="/machines" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium <?= strpos($currentPath, '/machines') === 0 ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' ?> transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3"/></svg>
                    Maskiner
                </a>
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    Lager
                </a>
                <a href="/articles" class="block rounded px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-gray-700 hover:text-indigo-600">Artiklar</a>
                <?php if (($currentUser['role_level'] ?? 0) >= 7): ?>
                    <a href="/admin" class="block rounded px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-gray-700 hover:text-indigo-600">Admin</a>
                <?php endif; ?>
                <a href="/2fa/setup" class="block rounded px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-gray-700 hover:text-indigo-600">2FA</a>
                <a href="/my-page" class="block rounded px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-gray-700 hover:text-indigo-600">Min Sida</a>
                <a href="/logout" class="block rounded px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-gray-700 hover:text-indigo-600">Logga ut</a>
            <?php else: ?>
                <a href="/login" class="block rounded px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-gray-700 hover:text-indigo-600">Logga in</a>
            <?php endif; ?>
        </div>
    </nav>

    <!-- CSRF meta tag for JS fetch requests -->
    <?php if (class_exists(\App\Core\Csrf::class)): ?>
        <meta name="csrf-token" content="<?= htmlspecialchars(\App\Core\Csrf::token(), ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>

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
