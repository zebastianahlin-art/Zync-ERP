<div class="flex min-h-[60vh] flex-col items-center justify-center text-center">
    <p class="text-6xl font-bold text-indigo-600 dark:text-indigo-400">404</p>
    <h1 class="mt-4 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
        <?= htmlspecialchars($message ?? 'Sidan hittades inte', ENT_QUOTES, 'UTF-8') ?>
    </h1>
    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
        Den sida du letar efter finns inte eller har flyttats.
    </p>
    <a href="/dashboard"
       class="mt-6 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">
        &larr; Tillbaka till Dashboard
    </a>
</div>
