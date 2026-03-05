<div class="mx-auto max-w-2xl">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Redigera sökande</h1>
        <a href="/hr/recruitment/applicants/<?= (int)$applicant['id'] ?>" class="text-sm text-gray-500 dark:text-gray-400 hover:text-indigo-600">&larr; Tillbaka</a>
    </div>

    <div class="rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-md">
        <form method="POST" action="/hr/recruitment/applicants/<?= (int)$applicant['id'] ?>" class="space-y-5">
            <?= \App\Core\Csrf::field() ?>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Förnamn <span class="text-red-500">*</span></label>
                    <input id="first_name" name="first_name" type="text" required
                           value="<?= htmlspecialchars($applicant['first_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Efternamn <span class="text-red-500">*</span></label>
                    <input id="last_name" name="last_name" type="text" required
                           value="<?= htmlspecialchars($applicant['last_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">E-post</label>
                    <input id="email" name="email" type="email"
                           value="<?= htmlspecialchars($applicant['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Telefon</label>
                    <input id="phone" name="phone" type="text"
                           value="<?= htmlspecialchars($applicant['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="applied_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ansökningsdatum</label>
                    <input id="applied_at" name="applied_at" type="date"
                           value="<?= htmlspecialchars($applicant['applied_at'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                    <select id="status" name="status"
                            class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        <option value="new" <?= ($applicant['status'] ?? '') === 'new' ? 'selected' : '' ?>>Ny</option>
                        <option value="screening" <?= ($applicant['status'] ?? '') === 'screening' ? 'selected' : '' ?>>Granskning</option>
                        <option value="interview" <?= ($applicant['status'] ?? '') === 'interview' ? 'selected' : '' ?>>Intervju</option>
                        <option value="offer" <?= ($applicant['status'] ?? '') === 'offer' ? 'selected' : '' ?>>Erbjudande</option>
                        <option value="hired" <?= ($applicant['status'] ?? '') === 'hired' ? 'selected' : '' ?>>Anställd</option>
                        <option value="rejected" <?= ($applicant['status'] ?? '') === 'rejected' ? 'selected' : '' ?>>Avvisad</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Anteckningar</label>
                <textarea id="notes" name="notes" rows="3"
                          class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"><?= htmlspecialchars($applicant['notes'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-2">
                <a href="/hr/recruitment/applicants/<?= (int)$applicant['id'] ?>" class="rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Avbryt</a>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">Spara ändringar</button>
            </div>
        </form>
    </div>
</div>
