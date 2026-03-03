<?php
/** @var string $title */
/** @var array $session */
/** @var array $participants */
/** @var array $employees */
$sBadge = ['planned'=>'bg-indigo-100 text-indigo-700','ongoing'=>'bg-amber-100 text-amber-700','completed'=>'bg-green-100 text-green-700','cancelled'=>'bg-red-100 text-red-700'];
$sLabel = ['planned'=>'Planerad','ongoing'=>'Pågår','completed'=>'Genomförd','cancelled'=>'Avbruten'];
$pLabel = ['enrolled'=>'Anmäld','attended'=>'Närvarande','completed'=>'Genomförd','failed'=>'Underkänd','cancelled'=>'Avbruten'];
?>
<div class="space-y-6" x-data>
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white"><?= htmlspecialchars($session['course_name']) ?></h1>
            <div class="flex items-center gap-2 mt-1">
                <span class="text-sm text-gray-500 dark:text-gray-400"><?= date('Y-m-d H:i', strtotime($session['start_date'])) ?> – <?= date('H:i', strtotime($session['end_date'])) ?></span>
                <span class="inline-block rounded-full px-2.5 py-0.5 text-xs font-medium <?= $sBadge[$session['status']] ?? '' ?>"><?= $sLabel[$session['status']] ?? $session['status'] ?></span>
            </div>
        </div>
        <a href="/hr/training/<?= $session['course_id'] ?>" class="rounded-lg bg-gray-100 dark:bg-gray-700 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200 transition-colors">← Tillbaka till kurs</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Participants -->
        <div class="lg:col-span-2 overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-md">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Deltagare (<?= count($participants) ?>)</h2>
                <button @click="$refs.addPartDialog.showModal()" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">+ Lägg till</button>
            </div>
            <?php if (empty($participants)): ?>
                <p class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">Inga deltagare ännu.</p>
            <?php else: ?>
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Anställd</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Avdelning</th>
                            <th class="px-6 py-3 text-center font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Status</th>
                            <th class="px-6 py-3 text-center font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Poäng</th>
                            <th class="px-6 py-3 text-center font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Certifikat</th>
                            <th class="px-6 py-3 text-right font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <?php foreach ($participants as $p): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                            <td class="px-6 py-4">
                                <span class="font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($p['first_name'] . ' ' . $p['last_name']) ?></span><br>
                                <span class="text-xs text-gray-500"><?= htmlspecialchars($p['employee_number']) ?></span>
                            </td>
                            <td class="px-6 py-4 text-gray-500 dark:text-gray-400"><?= htmlspecialchars($p['department_name'] ?? '–') ?></td>
                            <td class="px-6 py-4 text-center">
                                <form method="post" action="/hr/training/<?= $session['course_id'] ?>/sessions/<?= $session['id'] ?>/participants/<?= $p['id'] ?>" class="inline">
                                    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(\App\Core\Csrf::token(), ENT_QUOTES) ?>">
                                    <input type="hidden" name="score" value="<?= $p['score'] ?? '' ?>">
                                    <input type="hidden" name="certificate_issued" value="<?= $p['certificate_issued'] ?? 0 ?>">
                                    <select name="status" onchange="this.form.submit()" class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-2 py-1 text-xs text-gray-900 dark:text-white">
                                        <?php foreach ($pLabel as $v => $l): ?>
                                            <option value="<?= $v ?>" <?= $p['status'] === $v ? 'selected' : '' ?>><?= $l ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </form>
                            </td>
                            <td class="px-6 py-4 text-center text-gray-700 dark:text-gray-300"><?= $p['score'] !== null ? $p['score'] : '–' ?></td>
                            <td class="px-6 py-4 text-center">
                                <?php if ($p['certificate_issued']): ?>
                                    <span class="text-green-500">🏅</span>
                                <?php else: ?>–<?php endif ?>
                            </td>
                            <td class="px-6 py-4 text-right text-xs text-gray-400"><?= $p['completed_at'] ? date('Y-m-d', strtotime($p['completed_at'])) : '' ?></td>
                        </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            <?php endif ?>
        </div>

        <!-- Info -->
        <div class="rounded-2xl bg-white dark:bg-gray-800 shadow-md p-6">
            <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">Detaljer</h3>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-gray-500">Instruktör</dt><dd class="text-gray-900 dark:text-white"><?= htmlspecialchars($session['instructor'] ?? '–') ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Plats</dt><dd class="text-gray-900 dark:text-white"><?= htmlspecialchars($session['location'] ?? '–') ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Max platser</dt><dd class="text-gray-900 dark:text-white"><?= $session['max_participants'] ?? 'Obegränsat' ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Start</dt><dd class="text-gray-900 dark:text-white"><?= date('Y-m-d H:i', strtotime($session['start_date'])) ?></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Slut</dt><dd class="text-gray-900 dark:text-white"><?= date('Y-m-d H:i', strtotime($session['end_date'])) ?></dd></div>
            </dl>
            <?php if ($session['notes']): ?>
                <hr class="my-4 border-gray-200 dark:border-gray-700">
                <p class="text-sm text-gray-600 dark:text-gray-400 whitespace-pre-line"><?= htmlspecialchars($session['notes']) ?></p>
            <?php endif ?>
        </div>
    </div>

    <!-- Add Participant Dialog -->
    <dialog x-ref="addPartDialog" class="rounded-2xl shadow-xl p-0 backdrop:bg-black/50 max-w-sm w-full dark:bg-gray-800">
        <form method="post" action="/hr/training/<?= $session['course_id'] ?>/sessions/<?= $session['id'] ?>/participants" class="p-6 space-y-4">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars(\App\Core\Csrf::token(), ENT_QUOTES) ?>">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Lägg till deltagare</h3>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Anställd</label>
                <select name="employee_id" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white" required>
                    <option value="">Välj anställd...</option>
                    <?php foreach ($employees as $emp):
                        if (($emp['status'] ?? '') !== 'active' || !empty($emp['is_deleted'])) continue;
                    ?>
                        <option value="<?= $emp['id'] ?>"><?= htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']) ?> (<?= $emp['employee_number'] ?>)</option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="this.closest('dialog').close()" class="rounded-lg bg-gray-100 dark:bg-gray-700 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200">Avbryt</button>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Lägg till</button>
            </div>
        </form>
    </dialog>
</div>
