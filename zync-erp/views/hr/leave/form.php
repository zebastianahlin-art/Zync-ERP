<?php
/** @var string $title */
/** @var array $employees */
/** @var array $types */
/** @var array $leaveReq */
/** @var array $errors */
?>
<div class="mx-auto max-w-xl space-y-6">
    <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Ny frånvaroansökan</h1>

    <div class="rounded-2xl bg-white dark:bg-gray-800 shadow-md p-6">
        <form method="post" action="/hr/leave" class="space-y-4">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars(\App\Core\Csrf::token(), ENT_QUOTES) ?>">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Anställd *</label>
                    <select name="employee_id" class="w-full rounded-lg border <?= isset($errors['employee_id']) ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' ?> bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white" required>
                        <option value="">Välj anställd...</option>
                        <?php foreach ($employees as $e): ?>
                            <option value="<?= $e['id'] ?>" <?= ($leaveReq['employee_id'] ?? '') == $e['id'] ? 'selected' : '' ?>><?= htmlspecialchars($e['first_name'] . ' ' . $e['last_name']) ?> (<?= $e['employee_number'] ?>)</option>
                        <?php endforeach ?>
                    </select>
                    <?php if (isset($errors['employee_id'])): ?><p class="text-xs text-red-500 mt-1"><?= $errors['employee_id'] ?></p><?php endif ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Typ *</label>
                    <select name="leave_type_id" class="w-full rounded-lg border <?= isset($errors['leave_type_id']) ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' ?> bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white" required>
                        <option value="">Välj typ...</option>
                        <?php foreach ($types as $t): ?>
                            <option value="<?= $t['id'] ?>" <?= ($leaveReq['leave_type_id'] ?? '') == $t['id'] ? 'selected' : '' ?>><?= htmlspecialchars($t['name']) ?> (<?= $t['code'] ?>)</option>
                        <?php endforeach ?>
                    </select>
                    <?php if (isset($errors['leave_type_id'])): ?><p class="text-xs text-red-500 mt-1"><?= $errors['leave_type_id'] ?></p><?php endif ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Startdatum *</label>
                    <input type="date" name="start_date" class="w-full rounded-lg border <?= isset($errors['start_date']) ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' ?> bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white" value="<?= $leaveReq['start_date'] ?? '' ?>" required>
                    <?php if (isset($errors['start_date'])): ?><p class="text-xs text-red-500 mt-1"><?= $errors['start_date'] ?></p><?php endif ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Slutdatum *</label>
                    <input type="date" name="end_date" class="w-full rounded-lg border <?= isset($errors['end_date']) ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' ?> bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white" value="<?= $leaveReq['end_date'] ?? '' ?>" required>
                    <?php if (isset($errors['end_date'])): ?><p class="text-xs text-red-500 mt-1"><?= $errors['end_date'] ?></p><?php endif ?>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Anledning / kommentar</label>
                <textarea name="reason" rows="3" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($leaveReq['reason'] ?? '') ?></textarea>
            </div>
            <div class="flex justify-between pt-2">
                <a href="/hr/leave" class="rounded-lg bg-gray-100 dark:bg-gray-700 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200 transition-colors">Avbryt</a>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">Skicka ansökan</button>
            </div>
        </form>
    </div>
</div>
