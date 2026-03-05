<?php
$categories = [
    'general'       => 'Allmänt',
    'security'      => 'Säkerhet',
    'system'        => 'System',
    'notifications' => 'Notifieringar',
    'inventory'     => 'Lager',
    'finance'       => 'Ekonomi',
];
?>
<div class="space-y-8">

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Systeminställningar</h1>
        <a href="/admin" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">← Admin</a>
    </div>

    <div x-data="{ tab: '<?= htmlspecialchars(array_key_first($settings ?? ['general' => []]), ENT_QUOTES, 'UTF-8') ?>' }">

        <!-- Category tabs -->
        <div class="flex flex-wrap gap-1 border-b border-gray-200 dark:border-gray-700 mb-6">
            <?php foreach (array_keys($settings ?? []) as $cat): ?>
                <button
                    @click="tab = '<?= htmlspecialchars($cat, ENT_QUOTES, 'UTF-8') ?>'"
                    :class="tab === '<?= htmlspecialchars($cat, ENT_QUOTES, 'UTF-8') ?>' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 dark:border-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'"
                    class="px-4 py-2 text-sm font-medium border-b-2 -mb-px transition-colors">
                    <?= htmlspecialchars($categories[$cat] ?? ucfirst($cat), ENT_QUOTES, 'UTF-8') ?>
                </button>
            <?php endforeach; ?>
        </div>

        <?php foreach ($settings ?? [] as $cat => $rows): ?>
        <div x-show="tab === '<?= htmlspecialchars($cat, ENT_QUOTES, 'UTF-8') ?>'" x-cloak>
            <form method="POST" action="/admin/settings" class="space-y-6">
                <?= \App\Core\Csrf::field() ?>

                <div class="rounded-2xl bg-white dark:bg-gray-800 shadow-md divide-y divide-gray-100 dark:divide-gray-700">
                    <?php foreach ($rows as $row): ?>
                        <div class="flex items-start gap-4 p-4">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">
                                    <?= htmlspecialchars((string) $row['setting_key'], ENT_QUOTES, 'UTF-8') ?>
                                </label>
                                <p class="text-xs text-gray-500 dark:text-gray-400"><?= htmlspecialchars((string) ($row['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
                            </div>
                            <div class="w-56 flex-shrink-0">
                                <?php if ($row['data_type'] === 'boolean'): ?>
                                    <select name="<?= htmlspecialchars((string) $row['setting_key'], ENT_QUOTES, 'UTF-8') ?>"
                                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                        <option value="1" <?= ($row['setting_value'] ?? '0') === '1' ? 'selected' : '' ?>>Ja</option>
                                        <option value="0" <?= ($row['setting_value'] ?? '0') !== '1' ? 'selected' : '' ?>>Nej</option>
                                    </select>
                                <?php elseif ($row['data_type'] === 'text'): ?>
                                    <textarea name="<?= htmlspecialchars((string) $row['setting_key'], ENT_QUOTES, 'UTF-8') ?>"
                                              rows="3"
                                              class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent"><?= htmlspecialchars((string) ($row['setting_value'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                                <?php else: ?>
                                    <input type="<?= $row['data_type'] === 'integer' ? 'number' : 'text' ?>"
                                           name="<?= htmlspecialchars((string) $row['setting_key'], ENT_QUOTES, 'UTF-8') ?>"
                                           value="<?= htmlspecialchars((string) ($row['setting_value'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">
                        Spara inställningar
                    </button>
                </div>
            </form>
        </div>
        <?php endforeach; ?>

    </div>
</div>
