<div class="space-y-6">

    <!-- Stats -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-xl bg-white dark:bg-gray-800 p-5 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700">
            <p class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Totalt</p>
            <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white"><?= $stats['total'] ?></p>
        </div>
        <div class="rounded-xl bg-white dark:bg-gray-800 p-5 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700">
            <p class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Aktiva</p>
            <p class="mt-1 text-2xl font-bold text-green-600"><?= $stats['active'] ?></p>
        </div>
        <div class="rounded-xl bg-white dark:bg-gray-800 p-5 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700">
            <p class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Tjänstlediga</p>
            <p class="mt-1 text-2xl font-bold text-amber-500"><?= $stats['on_leave'] ?></p>
        </div>
    </div>

    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Personal</h1>
        <a href="/employees/create"
           class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Ny anställd
        </a>
    </div>

    <!-- Filters -->
    <form method="GET" action="/employees" class="flex flex-wrap gap-3">
        <input type="text" name="search" placeholder="Sök namn, nummer, e-post…"
               value="<?= htmlspecialchars($filters['search'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
               class="rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
        <select name="status" class="rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
            <option value="">Alla status</option>
            <option value="active"     <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Aktiv</option>
            <option value="on_leave"   <?= ($filters['status'] ?? '') === 'on_leave' ? 'selected' : '' ?>>Tjänstledig</option>
            <option value="terminated" <?= ($filters['status'] ?? '') === 'terminated' ? 'selected' : '' ?>>Avslutad</option>
        </select>
        <select name="department" class="rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
            <option value="">Alla avdelningar</option>
            <?php foreach ($departments as $d): ?>
                <option value="<?= (int) $d['id'] ?>" <?= (string) ($filters['department'] ?? '') === (string) $d['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($d['name'], ENT_QUOTES, 'UTF-8') ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="rounded-lg bg-gray-100 dark:bg-gray-700 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">Filtrera</button>
        <?php if (!empty($filters['search']) || !empty($filters['status']) || !empty($filters['department'])): ?>
            <a href="/employees" class="rounded-lg px-3 py-2 text-sm text-gray-500 hover:text-indigo-600">Rensa</a>
        <?php endif; ?>
    </form>

    <!-- Table -->
    <?php if (empty($employees)): ?>
        <div class="rounded-xl bg-white dark:bg-gray-800 p-10 text-center shadow-sm ring-1 ring-gray-200 dark:ring-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">Inga anställda hittades.
                <a href="/employees/create" class="text-indigo-600 dark:text-indigo-400 hover:underline">Lägg till den första.</a>
            </p>
        </div>
    <?php else: ?>
        <?php
        $typeLabels   = ['full_time'=>'Heltid','part_time'=>'Deltid','consultant'=>'Konsult','intern'=>'Praktikant'];
        $statusColors = ['active'=>'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400','on_leave'=>'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400','terminated'=>'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'];
        $statusLabels = ['active'=>'Aktiv','on_leave'=>'Tjänstledig','terminated'=>'Avslutad'];
        ?>
        <div class="overflow-x-auto rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Nr</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Namn</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Titel</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Avdelning</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Typ</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Status</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Startdatum</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php foreach ($employees as $emp): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-4 py-3 font-mono text-xs text-gray-500 dark:text-gray-400"><?= htmlspecialchars($emp['employee_number'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name'], ENT_QUOTES, 'UTF-8') ?></p>
                                <?php if ($emp['email']): ?>
                                    <p class="text-xs text-gray-500 dark:text-gray-400"><?= htmlspecialchars($emp['email'], ENT_QUOTES, 'UTF-8') ?></p>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($emp['title'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($emp['department_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= $typeLabels[$emp['employment_type']] ?? $emp['employment_type'] ?></td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium <?= $statusColors[$emp['status']] ?? '' ?>">
                                    <?= $statusLabels[$emp['status']] ?? $emp['status'] ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400"><?= $emp['hire_date'] ? date('Y-m-d', strtotime($emp['hire_date'])) : '—' ?></td>
                            <td class="px-4 py-3 text-right">
                                <a href="/employees/<?= (int) $emp['id'] ?>/edit" class="text-indigo-600 dark:text-indigo-400 hover:underline">Redigera</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
