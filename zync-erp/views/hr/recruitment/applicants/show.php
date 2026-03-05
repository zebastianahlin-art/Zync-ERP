<?php if (!empty($success)): ?>
<div class="mb-4 rounded-lg bg-green-50 dark:bg-green-900/20 p-4 text-green-800 dark:text-green-300 text-sm">
    <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
</div>
<?php endif; ?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="/hr/recruitment/positions/<?= (int)$applicant['position_id'] ?>" class="text-sm text-gray-500 dark:text-gray-400 hover:text-indigo-600">&larr; <?= htmlspecialchars($applicant['position_title'] ?? 'Tjänst', ENT_QUOTES, 'UTF-8') ?></a>
            <h1 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                <?= htmlspecialchars(($applicant['first_name'] ?? '') . ' ' . ($applicant['last_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
            </h1>
        </div>
        <div class="flex items-center gap-3">
            <a href="/hr/recruitment/applicants/<?= (int)$applicant['id'] ?>/edit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">Redigera</a>
            <form method="POST" action="/hr/recruitment/applicants/<?= (int)$applicant['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort sökande?')">
                <?= \App\Core\Csrf::field() ?>
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition">Ta bort</button>
            </form>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
        <div>
            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Tjänst</dt>
            <dd class="mt-0.5 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($applicant['position_title'] ?? '—', ENT_QUOTES, 'UTF-8') ?></dd>
        </div>
        <div>
            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">E-post</dt>
            <dd class="mt-0.5 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($applicant['email'] ?? '—', ENT_QUOTES, 'UTF-8') ?></dd>
        </div>
        <div>
            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Telefon</dt>
            <dd class="mt-0.5 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($applicant['phone'] ?? '—', ENT_QUOTES, 'UTF-8') ?></dd>
        </div>
        <div>
            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Ansökningsdatum</dt>
            <dd class="mt-0.5 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($applicant['applied_at'] ?? '—', ENT_QUOTES, 'UTF-8') ?></dd>
        </div>
        <div>
            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Status</dt>
            <dd class="mt-0.5 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($applicant['status'] ?? '—', ENT_QUOTES, 'UTF-8') ?></dd>
        </div>
        <?php if (!empty($applicant['notes'])): ?>
        <div class="md:col-span-2">
            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Anteckningar</dt>
            <dd class="mt-0.5 text-sm text-gray-900 dark:text-white whitespace-pre-wrap"><?= htmlspecialchars($applicant['notes'], ENT_QUOTES, 'UTF-8') ?></dd>
        </div>
        <?php endif; ?>
    </div>

    <!-- Uppdatera status -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Uppdatera status</h3>
        <form method="POST" action="/hr/recruitment/applicants/<?= (int)$applicant['id'] ?>/status" class="flex items-end gap-3">
            <?= \App\Core\Csrf::field() ?>
            <div>
                <label for="status" class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Ny status</label>
                <select id="status" name="status"
                        class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                    <option value="new" <?= ($applicant['status'] ?? '') === 'new' ? 'selected' : '' ?>>Ny</option>
                    <option value="screening" <?= ($applicant['status'] ?? '') === 'screening' ? 'selected' : '' ?>>Granskning</option>
                    <option value="interview" <?= ($applicant['status'] ?? '') === 'interview' ? 'selected' : '' ?>>Intervju</option>
                    <option value="offer" <?= ($applicant['status'] ?? '') === 'offer' ? 'selected' : '' ?>>Erbjudande</option>
                    <option value="hired" <?= ($applicant['status'] ?? '') === 'hired' ? 'selected' : '' ?>>Anställd</option>
                    <option value="rejected" <?= ($applicant['status'] ?? '') === 'rejected' ? 'selected' : '' ?>>Avvisad</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">Uppdatera</button>
        </form>
    </div>
</div>
