<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'ZYNC ERP', ENT_QUOTES, 'UTF-8') ?></title>

    <!-- Tailwind CSS (CDN – no build step required) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js (CDN) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="h-full font-sans antialiased">

    <!-- Navigation -->
    <nav class="bg-white shadow-sm" x-data="{ open: false }">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <div class="flex items-center space-x-2">
                    <span class="text-xl font-bold tracking-tight text-indigo-600">ZYNC ERP</span>
                </div>
                <div class="hidden sm:flex sm:items-center sm:space-x-6 text-sm text-gray-600">
                    <a href="/" class="hover:text-indigo-600 transition-colors">Home</a>
                </div>
                <!-- Mobile menu toggle -->
                <button
                    @click="open = !open"
                    class="sm:hidden rounded-md p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-700"
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
        <div x-show="open" class="sm:hidden border-t border-gray-100 px-4 py-2 space-y-1">
            <a href="/" class="block rounded px-3 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">Home</a>
        </div>
    </nav>

    <!-- Main content -->
    <main class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <?= $content ?>
    </main>

    <!-- Footer -->
    <footer class="border-t border-gray-200 mt-16 py-6 text-center text-xs text-gray-400">
        &copy; <?= date('Y') ?> ZYNC ERP. All rights reserved.
    </footer>

</body>
</html>
