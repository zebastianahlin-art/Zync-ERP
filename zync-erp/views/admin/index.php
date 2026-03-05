<div class="space-y-8">

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Admin – Översikt</h1>
        <div class="flex gap-2">
            <a href="/admin/settings" class="rounded-lg bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">Systeminställningar</a>
            <a href="/admin/modules" class="rounded-lg bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">Moduler</a>
        </div>
    </div>

    <!-- KPI cards -->
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-5 shadow-md">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Totalt användare</p>
            <p class="mt-2 text-3xl font-bold text-indigo-600 dark:text-indigo-400"><?= htmlspecialchars((string) ($stats['total_users'] ?? 0), ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-5 shadow-md">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Aktiva användare</p>
            <p class="mt-2 text-3xl font-bold text-green-600 dark:text-green-400"><?= htmlspecialchars((string) ($stats['active_users'] ?? 0), ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-5 shadow-md">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Aktiva moduler</p>
            <p class="mt-2 text-3xl font-bold text-blue-600 dark:text-blue-400"><?= htmlspecialchars((string) ($sys_info['module_count'] ?? 0), ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-5 shadow-md">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Audit-poster</p>
            <p class="mt-2 text-3xl font-bold text-yellow-600 dark:text-yellow-400"><?= htmlspecialchars((string) ($sys_info['audit_count'] ?? 0), ENT_QUOTES, 'UTF-8') ?></p>
        </div>
    </div>

    <!-- System info + Quick links -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

        <!-- System Info -->
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md">
            <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Systeminformation</h2>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">PHP-version</dt>
                    <dd class="font-medium text-gray-900 dark:text-gray-100"><?= htmlspecialchars((string) ($sys_info['php_version'] ?? ''), ENT_QUOTES, 'UTF-8') ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">Databas-version</dt>
                    <dd class="font-medium text-gray-900 dark:text-gray-100"><?= htmlspecialchars((string) ($sys_info['db_version'] ?? ''), ENT_QUOTES, 'UTF-8') ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">Minnesanvändning</dt>
                    <dd class="font-medium text-gray-900 dark:text-gray-100"><?= htmlspecialchars((string) ($sys_info['memory_usage'] ?? ''), ENT_QUOTES, 'UTF-8') ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">Minnesgräns</dt>
                    <dd class="font-medium text-gray-900 dark:text-gray-100"><?= htmlspecialchars((string) ($sys_info['memory_limit'] ?? ''), ENT_QUOTES, 'UTF-8') ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">Ledigt diskutrymme</dt>
                    <dd class="font-medium text-gray-900 dark:text-gray-100"><?= htmlspecialchars((string) ($sys_info['disk_free'] ?? ''), ENT_QUOTES, 'UTF-8') ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">Servertid</dt>
                    <dd class="font-medium text-gray-900 dark:text-gray-100"><?= htmlspecialchars((string) ($sys_info['server_time'] ?? ''), ENT_QUOTES, 'UTF-8') ?></dd>
                </div>
            </dl>
        </div>

        <!-- Quick Links -->
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md">
            <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Snabbnavigering</h2>
            <div class="grid grid-cols-2 gap-3">
                <a href="/admin/settings" class="flex items-center gap-2 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 p-3 text-sm font-medium text-indigo-700 dark:text-indigo-300 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition-colors">
                    ⚙️ Systeminställningar
                </a>
                <a href="/admin/modules" class="flex items-center gap-2 rounded-xl bg-blue-50 dark:bg-blue-900/30 p-3 text-sm font-medium text-blue-700 dark:text-blue-300 hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors">
                    📦 Moduladministration
                </a>
                <a href="/admin/site" class="flex items-center gap-2 rounded-xl bg-green-50 dark:bg-green-900/30 p-3 text-sm font-medium text-green-700 dark:text-green-300 hover:bg-green-100 dark:hover:bg-green-900/50 transition-colors">
                    🏢 Site-inställningar
                </a>
                <a href="/admin/users" class="flex items-center gap-2 rounded-xl bg-yellow-50 dark:bg-yellow-900/30 p-3 text-sm font-medium text-yellow-700 dark:text-yellow-300 hover:bg-yellow-100 dark:hover:bg-yellow-900/50 transition-colors">
                    👤 Användarhantering
                </a>
                <a href="/admin/roles" class="flex items-center gap-2 rounded-xl bg-purple-50 dark:bg-purple-900/30 p-3 text-sm font-medium text-purple-700 dark:text-purple-300 hover:bg-purple-100 dark:hover:bg-purple-900/50 transition-colors">
                    🔑 Rollhantering
                </a>
                <a href="/admin/audit-log" class="flex items-center gap-2 rounded-xl bg-red-50 dark:bg-red-900/30 p-3 text-sm font-medium text-red-700 dark:text-red-300 hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors">
                    📋 Audit-logg
                </a>
            </div>
        </div>
    </div>

    <!-- Roles breakdown + Recent audit log -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

        <!-- Rollfördelning -->
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md">
            <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Rollfördelning</h2>
            <div class="overflow-hidden rounded-xl">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Roll</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Användare</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        <?php foreach ($stats['roles'] ?? [] as $role): ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-4 py-2 text-gray-900 dark:text-gray-100"><?= htmlspecialchars((string) $role['name'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="px-4 py-2 text-right text-gray-600 dark:text-gray-300"><?= htmlspecialchars((string) $role['user_count'], ENT_QUOTES, 'UTF-8') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Senaste audit-logg -->
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Senaste audit-logg</h2>
                <a href="/admin/audit-log" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Visa alla →</a>
            </div>
            <?php if (empty($recent_audit)): ?>
                <p class="text-sm text-gray-500 dark:text-gray-400">Inga loggposter hittades.</p>
            <?php else: ?>
                <div class="space-y-2">
                    <?php foreach ($recent_audit as $entry): ?>
                        <div class="flex items-start gap-3 rounded-lg bg-gray-50 dark:bg-gray-700/50 p-3">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                    <?= htmlspecialchars((string) ($entry['action'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                                    <span class="text-gray-500 dark:text-gray-400">— <?= htmlspecialchars((string) ($entry['module'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    <?= htmlspecialchars((string) ($entry['username'] ?? 'System'), ENT_QUOTES, 'UTF-8') ?>
                                    &middot; <?= htmlspecialchars((string) ($entry['created_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>
