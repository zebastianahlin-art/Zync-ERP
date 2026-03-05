<?php if (!empty($success)): ?>
<div class="mb-4 rounded-lg bg-green-50 dark:bg-green-900/20 p-4 text-green-800 dark:text-green-300 text-sm">
    <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
</div>
<?php endif; ?>

<div class="space-y-6" x-data="{ search: '', statusFilter: '' }">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Personal</h1>
        <a href="/employees/create" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">+ Ny anst&#228;lld</a>
    </div>

    <div class="flex gap-3">
        <input x-model="search" type="search" placeholder="S&#246;k p&#229; namn, e-post&#8230;"
               class="flex-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
        <select x-model="statusFilter" class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
            <option value="">Alla statusar</option>
            <option value="active">Aktiv</option>
            <option value="on_leave">Tj&#228;nstledig</option>
            <option value="terminated">Avslutad</option>
        </select>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Namn</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Anst.nr</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Avdelning</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Befattning</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Status</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($employees as $emp):
                        $statusLabels = ['active'=>'Aktiv','on_leave'=>'Tj&#228;nstledig','terminated'=>'Avslutad'];
                        $statusClasses = [
                            'active'     => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300',
                            'on_leave'   => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300',
                            'terminated' => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300',
                        ];
                        $empStatus = $emp['status'] ?? 'active';
                        $empStatusLabel = $statusLabels[$empStatus] ?? $empStatus;
                        $empStatusClass = $statusClasses[$empStatus] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300';
                    ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50"
                        x-show="
                            (search === '' || '<?= strtolower(htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name'] . ' ' . ($emp['email'] ?? ''), ENT_QUOTES, 'UTF-8')) ?>'.includes(search.toLowerCase())) &&
                            (statusFilter === '' || statusFilter === '<?= htmlspecialchars($empStatus, ENT_QUOTES, 'UTF-8') ?>')
                        ">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                            <a href="/employees/<?= (int)$emp['id'] ?>" class="hover:text-indigo-600 dark:hover:text-indigo-400">
                                <?= htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name'], ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        </td>
                        <td class="px-4 py-3 font-mono text-xs text-indigo-600 dark:text-indigo-400">
                            <?= htmlspecialchars($emp['employee_number'] ?? '&#8212;', ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                            <?= htmlspecialchars($emp['department_name'] ?? '&#8212;', ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                            <?= htmlspecialchars($emp['position'] ?? '&#8212;', ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium <?= $empStatusClass ?>">
                                <?= htmlspecialchars($empStatusLabel, ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <a href="/employees/<?= (int)$emp['id'] ?>" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline mr-3">Visa</a>
                            <a href="/employees/<?= (int)$emp['id'] ?>/edit" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline mr-3">Redigera</a>
                            <form method="POST" action="/employees/<?= (int)$emp['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort anst&#228;lld?')">
                                <?= \App\Core\Csrf::field() ?>
                                <button type="submit" class="text-xs text-red-500 hover:text-red-700">Ta bort</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($employees)): ?>
                    <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400 dark:text-gray-500">Ingen personal registrerad &#228;nnu</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
