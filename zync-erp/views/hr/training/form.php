<?php
/** @var string $title */
/** @var bool $isNew */
/** @var array $course */
/** @var array $errors */
?>
<div class="mx-auto max-w-xl space-y-6">
    <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white"><?= $isNew ? 'Ny utbildning' : 'Redigera utbildning' ?></h1>

    <div class="rounded-2xl bg-white dark:bg-gray-800 shadow-md p-6">
        <form method="post" action="<?= $isNew ? '/hr/training' : '/hr/training/' . ($course['id'] ?? '') ?>" class="space-y-4">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars(\App\Core\Csrf::token(), ENT_QUOTES) ?>">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Namn *</label>
                    <input type="text" name="name" class="w-full rounded-lg border <?= isset($errors['name']) ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' ?> bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white" value="<?= htmlspecialchars($course['name'] ?? '') ?>" required>
                    <?php if (isset($errors['name'])): ?><p class="text-xs text-red-500 mt-1"><?= $errors['name'] ?></p><?php endif ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Leverantör</label>
                    <input type="text" name="provider" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white" value="<?= htmlspecialchars($course['provider'] ?? '') ?>">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select name="status" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white">
                        <option value="active" <?= ($course['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Aktiv</option>
                        <option value="inactive" <?= ($course['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inaktiv</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Timmar</label>
                    <input type="number" name="duration_hours" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white" value="<?= $course['duration_hours'] ?? '' ?>" step="0.5">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kostnad (kr)</label>
                    <input type="number" name="cost" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white" value="<?= $course['cost'] ?? '' ?>" step="1">
                </div>
                <div class="flex items-center gap-2 pt-6">
                    <input type="hidden" name="is_mandatory" value="0">
                    <input type="checkbox" name="is_mandatory" value="1" id="mandatory" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" <?= !empty($course['is_mandatory']) ? 'checked' : '' ?>>
                    <label for="mandatory" class="text-sm font-medium text-gray-700 dark:text-gray-300">Obligatorisk utbildning</label>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Återkommande (månader)</label>
                    <input type="number" name="recurrence_months" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white" value="<?= $course['recurrence_months'] ?? '' ?>" placeholder="T.ex. 12">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Beskrivning</label>
                    <textarea name="description" rows="4" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($course['description'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="flex justify-between pt-2">
                <a href="/hr/training" class="rounded-lg bg-gray-100 dark:bg-gray-700 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200 transition-colors">Avbryt</a>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors"><?= $isNew ? 'Skapa' : 'Spara' ?></button>
            </div>
        </form>
    </div>
</div>
