<div class="space-y-8">

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Admin – Översikt</h1>
    </div>

    <!-- Stats cards -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">

        <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Totalt användare</p>
            <p class="mt-2 text-4xl font-bold text-indigo-600 dark:text-indigo-400">
                <?= htmlspecialchars((string) ($stats['total_users'] ?? 0), ENT_QUOTES, 'UTF-8') ?>
            </p>
        </div>

        <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Aktiva användare</p>
            <p class="mt-2 text-4xl font-bold text-green-600 dark:text-green-400">
                <?= htmlspecialchars((string) ($stats['active_users'] ?? 0), ENT_QUOTES, 'UTF-8') ?>
            </p>
        </div>

        <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Inaktiva användare</p>
            <p class="mt-2 text-4xl font-bold text-red-500 dark:text-red-400">
                <?= htmlspecialchars((string) (($stats['total_users'] ?? 0) - ($stats['active_users'] ?? 0)), ENT_QUOTES, 'UTF-8') ?>
            </p>
        </div>

    </div>

    <!-- Roles breakdown -->
    <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md">
        <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Rollfördelning</h2>
        <div class="overflow-hidden rounded-xl">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Roll</th>
                        <th class="px-6 py-3 text-right font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Antal användare</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                    <?php foreach ($stats['roles'] ?? [] as $role): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-3 text-gray-900 dark:text-gray-100">
                                <?= htmlspecialchars((string) $role['name'], ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td class="px-6 py-3 text-right text-gray-600 dark:text-gray-300">
                                <?= htmlspecialchars((string) $role['user_count'], ENT_QUOTES, 'UTF-8') ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="flex space-x-4">
        <a href="/admin/users"
           class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">
            Hantera användare
        </a>
    </div>

</div>
