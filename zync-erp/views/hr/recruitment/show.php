<?php
/** @var string $title */
/** @var array $position */
/** @var array $candidates */
$statusBadge = ['draft'=>'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300','open'=>'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400','interviewing'=>'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400','offered'=>'bg-indigo-100 text-indigo-700','filled'=>'bg-gray-800 text-white','cancelled'=>'bg-red-100 text-red-700'];
$statusLabel = ['draft'=>'Utkast','open'=>'Öppen','interviewing'=>'Intervju','offered'=>'Erbjuden','filled'=>'Tillsatt','cancelled'=>'Avbruten'];
$candLabel = ['new'=>'Ny','screening'=>'Granskning','interview'=>'Intervju','assessment'=>'Bedömning','offered'=>'Erbjuden','hired'=>'Anställd','rejected'=>'Avslagen','withdrawn'=>'Tillbakadragen'];
$candBadge = ['new'=>'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400','screening'=>'bg-blue-100 text-blue-700','interview'=>'bg-amber-100 text-amber-700','assessment'=>'bg-cyan-100 text-cyan-700','offered'=>'bg-green-100 text-green-700','hired'=>'bg-gray-800 text-white','rejected'=>'bg-red-100 text-red-700','withdrawn'=>'bg-gray-100 text-gray-700'];
$typeLabel = ['full_time'=>'Heltid','part_time'=>'Deltid','temporary'=>'Tillfällig','internship'=>'Praktik'];
?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white"><?= htmlspecialchars($position['title']) ?></h1>
            <div class="flex items-center gap-2 mt-1">
                <span class="inline-block rounded-full px-2.5 py-0.5 text-xs font-medium <?= $statusBadge[$position['status']] ?? '' ?>"><?= $statusLabel[$position['status']] ?? $position['status'] ?></span>
                <?php if ($position['department_name']): ?>
                    <span class="text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars($position['department_name']) ?></span>
                <?php endif ?>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="/hr/recruitment" class="rounded-lg bg-gray-100 dark:bg-gray-700 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200 transition-colors">← Tillbaka</a>
            <a href="/hr/recruitment/<?= $position['id'] ?>/edit" class="rounded-lg bg-indigo-100 dark:bg-indigo-900/30 px-4 py-2 text-sm font-medium text-indigo-700 dark:text-indigo-400 hover:bg-indigo-200 transition-colors">Redigera</a>
            <form method="post" action="/hr/recruitment/<?= $position['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort tjänsten och alla kandidater?')">
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars(\App\Core\Csrf::token(), ENT_QUOTES) ?>">
                <button class="rounded-lg bg-red-50 dark:bg-red-900/20 px-4 py-2 text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-100 transition-colors">Ta bort</button>
            </form>
        </div>
    </div>

    <!-- Info row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <?php if ($position['description'] || $position['requirements']): ?>
        <div class="lg:col-span-2 space-y-4">
            <?php if ($position['description']): ?>
            <div class="rounded-2xl bg-white dark:bg-gray-800 shadow-md p-6">
                <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Beskrivning</h3>
                <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line"><?= htmlspecialchars($position['description']) ?></p>
            </div>
            <?php endif ?>
            <?php if ($position['requirements']): ?>
            <div class="rounded-2xl bg-white dark:bg-gray-800 shadow-md p-6">
                <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Krav</h3>
                <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line"><?= htmlspecialchars($position['requirements']) ?></p>
            </div>
            <?php endif ?>
        </div>
        <?php endif ?>
        <div>
            <div class="rounded-2xl bg-white dark:bg-gray-800 shadow-md p-6">
                <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">Detaljer</h3>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between"><dt class="text-gray-500">Platser</dt><dd class="text-gray-900 dark:text-white font-medium"><?= $position['positions_count'] ?></dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Typ</dt><dd class="text-gray-900 dark:text-white"><?= $typeLabel[$position['employment_type']] ?? $position['employment_type'] ?></dd></div>
                    <?php if ($position['salary_range_min'] || $position['salary_range_max']): ?>
                    <div class="flex justify-between"><dt class="text-gray-500">Lön</dt><dd class="text-gray-900 dark:text-white"><?= number_format((float)($position['salary_range_min'] ?? 0), 0, ',', ' ') ?> – <?= number_format((float)($position['salary_range_max'] ?? 0), 0, ',', ' ') ?> kr</dd></div>
                    <?php endif ?>
                    <div class="flex justify-between"><dt class="text-gray-500">Öppnad</dt><dd class="text-gray-900 dark:text-white"><?= $position['opening_date'] ?? '–' ?></dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Stängs</dt><dd class="text-gray-900 dark:text-white"><?= $position['closing_date'] ?? '–' ?></dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Skapad</dt><dd class="text-gray-900 dark:text-white"><?= date('Y-m-d', strtotime($position['created_at'])) ?></dd></div>
                </dl>
            </div>
        </div>
    </div>

    <!-- Candidates -->
    <div class="overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-md" x-data>
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Kandidater (<?= count($candidates) ?>)</h2>
            <button @click="$refs.addCandDialog.showModal()" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">+ Lägg till</button>
        </div>
        <?php if (empty($candidates)): ?>
            <p class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">Inga kandidater ännu.</p>
        <?php else: ?>
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Namn</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">E-post</th>
                        <th class="px-6 py-3 text-center font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Betyg</th>
                        <th class="px-6 py-3 text-center font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Status</th>
                        <th class="px-6 py-3 text-right font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php foreach ($candidates as $c): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($c['first_name'] . ' ' . $c['last_name']) ?></td>
                        <td class="px-6 py-4"><a href="mailto:<?= htmlspecialchars($c['email']) ?>" class="text-indigo-600 dark:text-indigo-400 hover:underline"><?= htmlspecialchars($c['email']) ?></a></td>
                        <td class="px-6 py-4 text-center">
                            <?php if ($c['rating']): ?>
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <span class="<?= $i <= $c['rating'] ? 'text-amber-400' : 'text-gray-300 dark:text-gray-600' ?>">★</span>
                                <?php endfor ?>
                            <?php else: ?>–<?php endif ?>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <form method="post" action="/hr/recruitment/<?= $position['id'] ?>/candidates/<?= $c['id'] ?>/status" class="inline">
                                <input type="hidden" name="_csrf" value="<?= htmlspecialchars(\App\Core\Csrf::token(), ENT_QUOTES) ?>">
                                <select name="status" onchange="this.form.submit()" class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-2 py-1 text-xs text-gray-900 dark:text-white">
                                    <?php foreach ($candLabel as $v => $l): ?>
                                        <option value="<?= $v ?>" <?= $c['status'] === $v ? 'selected' : '' ?>><?= $l ?></option>
                                    <?php endforeach ?>
                                </select>
                            </form>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="inline-block rounded-full bg-gray-100 dark:bg-gray-700 px-2 py-0.5 text-xs text-gray-500 dark:text-gray-400"><?= $c['interview_count'] ?> intervjuer</span>
                        </td>
                    </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        <?php endif ?>
    </div>

    <!-- Add Candidate Dialog -->
    <dialog x-ref="addCandDialog" class="rounded-2xl shadow-xl p-0 backdrop:bg-black/50 max-w-md w-full dark:bg-gray-800">
        <form method="post" action="/hr/recruitment/<?= $position['id'] ?>/candidates" class="p-6 space-y-4">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars(\App\Core\Csrf::token(), ENT_QUOTES) ?>">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Lägg till kandidat</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Förnamn *</label>
                    <input type="text" name="first_name" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Efternamn *</label>
                    <input type="text" name="last_name" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">E-post *</label>
                    <input type="email" name="email" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Telefon</label>
                    <input type="text" name="phone" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Betyg</label>
                    <select name="rating" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white">
                        <option value="">–</option>
                        <?php for ($i = 1; $i <= 5; $i++): ?><option value="<?= $i ?>"><?= $i ?></option><?php endfor ?>
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Anteckningar</label>
                    <textarea name="notes" rows="2" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="this.closest('dialog').close()" class="rounded-lg bg-gray-100 dark:bg-gray-700 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200">Avbryt</button>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Lägg till</button>
            </div>
        </form>
    </dialog>
</div>
