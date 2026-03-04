<div class="max-w-2xl space-y-6">
    <div class="flex items-center gap-3">
        <a href="/hr/recruitment/positions/<?= $applicant['position_id'] ?>" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700">← Tillbaka</a>
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white"><?= htmlspecialchars($applicant['name']) ?></h1>
    </div>
    <div class="rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6 shadow-sm space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div><p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tjänst</p><p class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($applicant['position_title'] ?? '–') ?></p></div>
            <div><p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</p><p class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($applicant['status']) ?></p></div>
            <div><p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">E-post</p><p class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($applicant['email']) ?></p></div>
            <div><p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Telefon</p><p class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($applicant['phone'] ?? '–') ?></p></div>
        </div>
        <?php if ($applicant['cover_letter']): ?>
        <div class="pt-2 border-t border-gray-100 dark:border-gray-700">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Personligt brev</p>
            <p class="mt-1 text-sm text-gray-900 dark:text-white"><?= nl2br(htmlspecialchars($applicant['cover_letter'])) ?></p>
        </div>
        <?php endif; ?>
        <div class="pt-2 border-t border-gray-100 dark:border-gray-700">
            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Uppdatera status</p>
            <form method="post" action="/hr/recruitment/applicants/<?= $applicant['id'] ?>/status" class="flex gap-2">
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                <select name="status" class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none">
                    <option value="applied" <?= $applicant['status'] === 'applied' ? 'selected' : '' ?>>Applied</option>
                    <option value="screening" <?= $applicant['status'] === 'screening' ? 'selected' : '' ?>>Screening</option>
                    <option value="interview" <?= $applicant['status'] === 'interview' ? 'selected' : '' ?>>Intervju</option>
                    <option value="offer" <?= $applicant['status'] === 'offer' ? 'selected' : '' ?>>Erbjudande</option>
                    <option value="hired" <?= $applicant['status'] === 'hired' ? 'selected' : '' ?>>Anställd</option>
                    <option value="rejected" <?= $applicant['status'] === 'rejected' ? 'selected' : '' ?>>Avvisad</option>
                </select>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Spara</button>
            </form>
        </div>
    </div>
</div>
