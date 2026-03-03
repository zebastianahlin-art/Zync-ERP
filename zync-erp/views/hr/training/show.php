<?php
/** @var string $title */
/** @var array $course */
/** @var array $sessions */
$sBadge = ['planned'=>'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400','ongoing'=>'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400','completed'=>'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400','cancelled'=>'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'];
$sLabel = ['planned'=>'Planerad','ongoing'=>'Pågår','completed'=>'Genomförd','cancelled'=>'Avbruten'];
?>
<div class="space-y-6" x-data>
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white"><?= htmlspecialchars($course['name']) ?></h1>
            <div class="flex items-center gap-2 mt-1">
                <?php if ($course['is_mandatory']): ?><span class="inline-block rounded-full bg-red-100 dark:bg-red-900/30 px-2.5 py-0.5 text-xs font-medium text-red-700 dark:text-red-400">Obligatorisk</span><?php endif ?>
                <span class="inline-block rounded-full px-2.5 py-0.5 text-xs font-medium <?= $course['status'] === 'active' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' ?>"><?= $course['status'] === 'active' ? 'Aktiv' : 'Inaktiv' ?></span>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="/hr/training" class="rounded-lg bg-gray-100 dark:bg-gray-700 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200 transition-colors">← Tillbaka</a>
            <a href="/hr/training/<?= $course['id'] ?>/edit" class="rounded-lg bg-indigo-100 dark:bg-indigo-900/30 px-4 py-2 text-sm font-medium text-indigo-700 dark:text-indigo-400 hover:bg-indigo-200 transition-colors">Redigera</a>
            <form method="post" action="/hr/training/<?= $course['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort utbildning och alla tillfällen?')">
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars(\App\Core\Csrf::token(), ENT_QUOTES) ?>">
                <button class="rounded-lg bg-red-50 dark:bg-red-900/20 px-4 py-2 text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-100 transition-colors">Ta bort</button>
            </form>
        </div>
    </div>

    <!-- Course Info -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <?php if ($course['description']): ?>
        <div class="lg:col-span-2 rounded-2xl bg-white dark:bg-gray-800 shadow-md p-6">
            <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Beskrivning</h3>
            <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line"><?= htmlspecialchars($course['description']) ?></p>
        </div>
        <?php endif ?>
        <div class="rounded-2xl bg-white dark:bg-gray-800 shadow-md p-6">
            <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">Detaljer</h3>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-gray-500">Leverantör</dt><dd class="text-gray-900 dark:text-white"><?= htmlspecialchars($course['provider'] ?? '–') ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Timmar</dt><dd class="text-gray-900 dark:text-white"><?= $course['duration_hours'] ? $course['duration_hours'] . 'h' : '–' ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Kostnad</dt><dd class="text-gray-900 dark:text-white"><?= $course['cost'] ? number_format((float)$course['cost'], 0, ',', ' ') . ' kr' : '–' ?></dd></div>
                <?php if ($course['recurrence_months']): ?>
                <div class="flex justify-between"><dt class="text-gray-500">Återkommande</dt><dd class="text-gray-900 dark:text-white">Var <?= $course['recurrence_months'] ?> mån</dd></div>
                <?php endif ?>
            </dl>
        </div>
    </div>

    <!-- Sessions -->
    <div class="overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-md">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Tillfällen (<?= count($sessions) ?>)</h2>
            <button @click="$refs.addSessionDialog.showModal()" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">+ Nytt tillfälle</button>
        </div>
        <?php if (empty($sessions)): ?>
            <p class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">Inga tillfällen skapade.</p>
        <?php else: ?>
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Datum</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Instruktör</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Plats</th>
                        <th class="px-6 py-3 text-center font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Deltagare</th>
                        <th class="px-6 py-3 text-center font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Status</th>
                        <th class="px-6 py-3 text-right font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php foreach ($sessions as $s): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300"><?= date('Y-m-d H:i', strtotime($s['start_date'])) ?> – <?= date('H:i', strtotime($s['end_date'])) ?></td>
                        <td class="px-6 py-4 text-gray-500 dark:text-gray-400"><?= htmlspecialchars($s['instructor'] ?? '–') ?></td>
                        <td class="px-6 py-4 text-gray-500 dark:text-gray-400"><?= htmlspecialchars($s['location'] ?? '–') ?></td>
                        <td class="px-6 py-4 text-center text-gray-700 dark:text-gray-300">
                            <?= $s['participant_count'] ?>
                            <?php if ($s['max_participants']): ?><span class="text-gray-400"> / <?= $s['max_participants'] ?></span><?php endif ?>
                        </td>
                        <td class="px-6 py-4 text-center"><span class="inline-block rounded-full px-2.5 py-0.5 text-xs font-medium <?= $sBadge[$s['status']] ?? 'bg-gray-100 text-gray-700' ?>"><?= $sLabel[$s['status']] ?? $s['status'] ?></span></td>
                        <td class="px-6 py-4 text-right"><a href="/hr/training/<?= $course['id'] ?>/sessions/<?= $s['id'] ?>" class="text-indigo-600 dark:text-indigo-400 hover:underline">Hantera</a></td>
                    </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        <?php endif ?>
    </div>

    <!-- Add Session Dialog -->
    <dialog x-ref="addSessionDialog" class="rounded-2xl shadow-xl p-0 backdrop:bg-black/50 max-w-md w-full dark:bg-gray-800">
        <form method="post" action="/hr/training/<?= $course['id'] ?>/sessions" class="p-6 space-y-4">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars(\App\Core\Csrf::token(), ENT_QUOTES) ?>">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Nytt tillfälle</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start *</label>
                    <input type="datetime-local" name="start_date" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Slut *</label>
                    <input type="datetime-local" name="end_date" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Instruktör</label>
                    <input type="text" name="instructor" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Plats</label>
                    <input type="text" name="location" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Max deltagare</label>
                    <input type="number" name="max_participants" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select name="status" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white">
                        <option value="planned">Planerad</option>
                        <option value="ongoing">Pågår</option>
                        <option value="completed">Genomförd</option>
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Anteckningar</label>
                    <textarea name="notes" rows="2" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="this.closest('dialog').close()" class="rounded-lg bg-gray-100 dark:bg-gray-700 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200">Avbryt</button>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Skapa</button>
            </div>
        </form>
    </dialog>
</div>
