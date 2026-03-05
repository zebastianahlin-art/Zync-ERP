<?php
    $statusLabels = ['todo' => 'Att göra', 'in_progress' => 'Pågår', 'done' => 'Klar'];
    $priorityLabels = ['low' => 'Låg', 'normal' => 'Normal', 'high' => 'Hög', 'urgent' => 'Brådskande'];
    $priorityColors = [
        'low'    => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300',
        'normal' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
        'high'   => 'bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-300',
        'urgent' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
    ];
?>
<div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                Kanban &mdash; <?= htmlspecialchars($project['name'], ENT_QUOTES, 'UTF-8') ?>
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                Projektnr: <span class="font-mono text-indigo-600 dark:text-indigo-400"><?= htmlspecialchars($project['project_number'], ENT_QUOTES, 'UTF-8') ?></span>
            </p>
        </div>
        <a href="/projects/<?= $project['id'] ?>" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">&larr; Projektdetaljer</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <?php
        $columnConfig = [
            'todo'        => ['label' => 'Att göra',   'header' => 'bg-gray-200 dark:bg-gray-700', 'border' => 'border-gray-300 dark:border-gray-600'],
            'in_progress' => ['label' => 'Pågår',      'header' => 'bg-yellow-100 dark:bg-yellow-900/30', 'border' => 'border-yellow-300 dark:border-yellow-600'],
            'done'        => ['label' => 'Klar',        'header' => 'bg-green-100 dark:bg-green-900/30', 'border' => 'border-green-300 dark:border-green-600'],
        ];
        foreach ($columnConfig as $colStatus => $colConfig):
            $colTasks = $columns[$colStatus] ?? [];
        ?>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden flex flex-col border <?= $colConfig['border'] ?>">
            <div class="px-4 py-3 <?= $colConfig['header'] ?> flex items-center justify-between">
                <span class="font-semibold text-gray-900 dark:text-white"><?= $colConfig['label'] ?></span>
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 bg-white/60 dark:bg-black/20 rounded-full px-2 py-0.5"><?= count($colTasks) ?></span>
            </div>
            <div class="flex-1 p-3 space-y-3 min-h-48">
                <?php foreach ($colTasks as $task): ?>
                <div class="bg-gray-50 dark:bg-gray-700/60 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-600">
                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-1"><?= htmlspecialchars($task['title'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php if (!empty($task['assigned_name'])): ?>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">&#128100; <?= htmlspecialchars($task['assigned_name'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                    <?php if (!empty($task['due_date'])): ?>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">&#128197; <?= htmlspecialchars($task['due_date'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                    <span class="inline-block text-xs px-2 py-0.5 rounded-full <?= $priorityColors[$task['priority']] ?? '' ?>"><?= $priorityLabels[$task['priority']] ?? $task['priority'] ?></span>

                    <!-- Status change buttons -->
                    <div class="mt-2 flex flex-wrap gap-1">
                        <?php foreach ($columnConfig as $targetStatus => $targetConfig): ?>
                        <?php if ($targetStatus !== $colStatus): ?>
                        <form method="POST" action="/projects/<?= (int) $project['id'] ?>/tasks/<?= (int) $task['id'] ?>/status">
                            <?= \App\Core\Csrf::field() ?>
                            <input type="hidden" name="status" value="<?= $targetStatus ?>">
                            <button type="submit" class="text-xs px-2 py-0.5 rounded bg-indigo-100 hover:bg-indigo-200 dark:bg-indigo-900/40 dark:hover:bg-indigo-800/60 text-indigo-700 dark:text-indigo-300 transition">&rarr; <?= $targetConfig['label'] ?></button>
                        </form>
                        <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if (empty($colTasks)): ?>
                <p class="text-xs text-gray-400 dark:text-gray-500 text-center py-8">Inga uppgifter</p>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
