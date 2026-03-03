<?php
/** @var string $title */
/** @var bool $isNew */
/** @var array $position */
/** @var array $departments */
/** @var array $errors */
?>
<div class="mx-auto max-w-2xl space-y-6">
    <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white"><?= $isNew ? 'Ny tjänst' : 'Redigera tjänst' ?></h1>

    <div class="rounded-2xl bg-white dark:bg-gray-800 shadow-md p-6">
        <form method="post" action="<?= $isNew ? '/hr/recruitment' : '/hr/recruitment/' . ($position['id'] ?? '') ?>" class="space-y-4">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars(\App\Core\Csrf::token(), ENT_QUOTES) ?>">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Titel *</label>
                    <input type="text" name="title" class="w-full rounded-lg border <?= isset($errors['title']) ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' ?> bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white" value="<?= htmlspecialchars($position['title'] ?? '') ?>" required>
                    <?php if (isset($errors['title'])): ?><p class="text-xs text-red-500 mt-1"><?= $errors['title'] ?></p><?php endif ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Avdelning</label>
                    <select name="department_id" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white">
                        <option value="">Välj...</option>
                        <?php foreach ($departments as $d): ?>
                            <option value="<?= $d['id'] ?>" <?= ($position['department_id'] ?? '') == $d['id'] ? 'selected' : '' ?>><?= htmlspecialchars($d['name']) ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Anställningsform</label>
                    <select name="employment_type" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white">
                        <?php foreach (['full_time'=>'Heltid','part_time'=>'Deltid','temporary'=>'Tillfällig','internship'=>'Praktik'] as $v => $l): ?>
                            <option value="<?= $v ?>" <?= ($position['employment_type'] ?? '') === $v ? 'selected' : '' ?>><?= $l ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select name="status" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white">
                        <?php foreach (['draft'=>'Utkast','open'=>'Öppen','interviewing'=>'Intervju','offered'=>'Erbjuden','filled'=>'Tillsatt','cancelled'=>'Avbruten'] as $v => $l): ?>
                            <option value="<?= $v ?>" <?= ($position['status'] ?? 'draft') === $v ? 'selected' : '' ?>><?= $l ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Antal platser</label>
                    <input type="number" name="positions_count" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white" value="<?= $position['positions_count'] ?? 1 ?>" min="1">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Lön från</label>
                    <input type="number" name="salary_range_min" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white" value="<?= $position['salary_range_min'] ?? '' ?>" step="100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Lön till</label>
                    <input type="number" name="salary_range_max" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white" value="<?= $position['salary_range_max'] ?? '' ?>" step="100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Öppningsdatum</label>
                    <input type="date" name="opening_date" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white" value="<?= $position['opening_date'] ?? '' ?>">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sista ansökan</label>
                    <input type="date" name="closing_date" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white" value="<?= $position['closing_date'] ?? '' ?>">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Beskrivning</label>
                    <textarea name="description" rows="4" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($position['description'] ?? '') ?></textarea>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Krav</label>
                    <textarea name="requirements" rows="3" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($position['requirements'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="flex justify-between pt-2">
                <a href="/hr/recruitment" class="rounded-lg bg-gray-100 dark:bg-gray-700 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200 transition-colors">Avbryt</a>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors"><?= $isNew ? 'Skapa' : 'Spara' ?></button>
            </div>
        </form>
    </div>
</div>
