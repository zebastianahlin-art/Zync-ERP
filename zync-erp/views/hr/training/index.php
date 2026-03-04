<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Utbildningar</h1>
        <a href="/hr/training/courses/create" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">+ Ny utbildning</a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <a href="/hr/training/sessions" class="block bg-white dark:bg-gray-800 rounded-xl shadow p-5 hover:shadow-md transition">
            <h2 class="font-semibold text-gray-900 dark:text-white">Kurstillfällen</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Schemalägg och hantera tillfällen</p>
        </a>
        <a href="/hr/training/participants" class="block bg-white dark:bg-gray-800 rounded-xl shadow p-5 hover:shadow-md transition">
            <h2 class="font-semibold text-gray-900 dark:text-white">Deltagare</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Registrera och följ deltagare</p>
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 font-semibold text-gray-900 dark:text-white">Kurser</div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Namn</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Kategori</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Tid (h)</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Obligatorisk</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($courses as $course): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($course['name'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($course['category'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3"><?= htmlspecialchars((string) ($course['duration_h'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3"><?= $course['is_mandatory'] ? '<span class="text-green-600 dark:text-green-400">Ja</span>' : 'Nej' ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($courses)): ?>
                    <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">Inga utbildningar registrerade ännu</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
