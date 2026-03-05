<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="/hr/training" class="text-sm text-gray-500 dark:text-gray-400 hover:text-indigo-600">&larr; Utbildningar</a>
            <h1 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($course['name'], ENT_QUOTES, 'UTF-8') ?></h1>
            <?php if (!empty($course['category'])): ?>
            <p class="text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars($course['category'], ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
        </div>
        <div class="flex items-center gap-3">
            <?php if ($course['is_mandatory']): ?>
            <span class="px-3 py-1 rounded-full text-xs font-medium bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300">Obligatorisk</span>
            <?php endif; ?>
            <a href="/hr/training/courses/<?= (int)$course['id'] ?>/edit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">Redigera</a>
            <form method="POST" action="/hr/training/courses/<?= (int)$course['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort utbildningen?')">
                <?= \App\Core\Csrf::field() ?>
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition">Ta bort</button>
            </form>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
        <?php
        $fields = [
            'Leverantör'       => $course['provider'] ?? null,
            'Varaktighet (h)'  => $course['duration_h'] ?? null,
            'Kategori'         => $course['category'] ?? null,
            'Antal tillfällen' => (string)($course['session_count'] ?? 0),
        ];
        foreach ($fields as $label => $value): ?>
        <div>
            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></dt>
            <dd class="mt-0.5 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($value ?? '—', ENT_QUOTES, 'UTF-8') ?></dd>
        </div>
        <?php endforeach; ?>
        <?php if (!empty($course['description'])): ?>
        <div class="md:col-span-2">
            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Beskrivning</dt>
            <dd class="mt-0.5 text-sm text-gray-900 dark:text-white whitespace-pre-wrap"><?= htmlspecialchars($course['description'], ENT_QUOTES, 'UTF-8') ?></dd>
        </div>
        <?php endif; ?>
    </div>

    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Kurstillfällen</h2>
        <a href="/hr/training/sessions/create" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">+ Nytt tillfälle</a>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <?php $courseSessions = array_filter($sessions, fn($s) => $s['course_id'] == $course['id']); ?>
        <?php if (empty($courseSessions)): ?>
        <p class="px-4 py-8 text-center text-gray-400 dark:text-gray-500 text-sm">Inga kurstillfällen registrerade</p>
        <?php else: ?>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-xs text-gray-500 dark:text-gray-400 uppercase">Datum</th>
                    <th class="px-4 py-3 text-left text-xs text-gray-500 dark:text-gray-400 uppercase">Plats</th>
                    <th class="px-4 py-3 text-left text-xs text-gray-500 dark:text-gray-400 uppercase">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($courseSessions as $s): ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <td class="px-4 py-3 text-gray-900 dark:text-white"><?= htmlspecialchars($s['start_date'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($s['location'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($s['status'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-right">
                        <a href="/hr/training/sessions/<?= (int)$s['id'] ?>" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Visa</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>
