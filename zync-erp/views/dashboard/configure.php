<?php
$categoryLabels = [
    'mandatory' => 'Obligatorisk',
    'kpi'       => 'KPI',
    'shortcut'  => 'Snabbknapp',
    'list'      => 'Lista',
    'chart'     => 'Diagram',
];
$categoryColors = [
    'mandatory' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
    'kpi'       => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
    'shortcut'  => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300',
    'list'      => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300',
    'chart'     => 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300',
];
?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Anpassa dashboard</h1>
        <a href="/dashboard" class="text-sm text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400">
            ← Tillbaka till dashboard
        </a>
    </div>

    <?php if (!empty($success)): ?>
        <div class="rounded-lg bg-green-50 dark:bg-green-900/30 p-4 text-green-800 dark:text-green-200">
            <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="rounded-lg bg-red-50 dark:bg-red-900/30 p-4 text-red-800 dark:text-red-200">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <p class="text-sm text-gray-500 dark:text-gray-400">
        Välj vilka widgets du vill visa på din dashboard. Obligatoriska widgets kan inte tas bort.
    </p>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php foreach ($available as $w):
            $isActive    = in_array($w['id'], $activeIds, false);
            $isMandatory = (int)$w['is_mandatory'] === 1;
            $catLabel    = $categoryLabels[$w['category']] ?? $w['category'];
            $catColor    = $categoryColors[$w['category']] ?? 'bg-gray-100 text-gray-700';
        ?>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 flex flex-col justify-between">
            <div>
                <div class="flex items-start justify-between mb-2">
                    <h3 class="font-semibold text-gray-800 dark:text-white text-sm">
                        <?= htmlspecialchars($w['name'], ENT_QUOTES, 'UTF-8') ?>
                    </h3>
                    <div class="flex items-center gap-1 ml-2">
                        <?php if ($isActive): ?>
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        <?php endif; ?>
                    </div>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                    <?= htmlspecialchars($w['description'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </p>
                <span class="inline-block text-xs font-medium px-2 py-0.5 rounded-full <?= $catColor ?>">
                    <?= htmlspecialchars($catLabel, ENT_QUOTES, 'UTF-8') ?>
                </span>
                <?php if (!empty($w['module'])): ?>
                    <span class="inline-block text-xs text-gray-400 ml-1">· <?= htmlspecialchars($w['module'], ENT_QUOTES, 'UTF-8') ?></span>
                <?php endif; ?>
            </div>

            <div class="mt-4">
                <?php if ($isActive): ?>
                    <?php if ($isMandatory): ?>
                        <button disabled
                                class="w-full px-4 py-2 rounded-lg text-sm font-medium bg-gray-100 dark:bg-gray-700 text-gray-400 cursor-not-allowed">
                            Obligatorisk – kan ej tas bort
                        </button>
                    <?php else: ?>
                        <form method="POST" action="/dashboard/widgets/remove">
                            <?= \App\Core\Csrf::field() ?>
                            <input type="hidden" name="widget_id" value="<?= (int)$w['id'] ?>">
                            <button type="submit"
                                    class="w-full px-4 py-2 rounded-lg text-sm font-medium bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300 hover:bg-red-100 dark:hover:bg-red-900/50 border border-red-200 dark:border-red-700 transition">
                                Ta bort från dashboard
                            </button>
                        </form>
                    <?php endif; ?>
                <?php else: ?>
                    <form method="POST" action="/dashboard/widgets/add">
                        <?= \App\Core\Csrf::field() ?>
                        <input type="hidden" name="widget_id" value="<?= (int)$w['id'] ?>">
                        <button type="submit"
                                class="w-full px-4 py-2 rounded-lg text-sm font-medium bg-indigo-600 hover:bg-indigo-700 text-white transition">
                            Lägg till på dashboard
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>

        <?php if (empty($available)): ?>
            <div class="col-span-full text-center py-12 text-gray-400">
                Inga widgets tillgängliga för din behörighetsnivå.
            </div>
        <?php endif; ?>
    </div>
</div>
