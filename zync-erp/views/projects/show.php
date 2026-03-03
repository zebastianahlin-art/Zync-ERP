<?php
$csrf = \App\Core\Csrf::token();
$p = $project;
$statusLabels = ['planning'=>'Planering','active'=>'Aktiv','on_hold'=>'Pausad','completed'=>'Avslutad','cancelled'=>'Avbruten'];
$statusColors = ['planning'=>'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
    'active'=>'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
    'on_hold'=>'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
    'completed'=>'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
    'cancelled'=>'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'];
$taskStatusLabels = ['todo'=>'Att göra','in_progress'=>'Pågår','review'=>'Granskning','done'=>'Klar','cancelled'=>'Avbruten'];
$taskStatusColors = ['todo'=>'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
    'in_progress'=>'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
    'review'=>'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
    'done'=>'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
    'cancelled'=>'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'];
$priorityIcons = ['low'=>'🟢','normal'=>'🔵','high'=>'🟠','urgent'=>'🔴'];
$riskColors = ['low'=>'text-green-600','medium'=>'text-yellow-600','high'=>'text-red-600'];
$budgetPct = $p['budget_total'] > 0 ? round(($budgetSummary['actual'] / $p['budget_total']) * 100) : 0;
$timePct = $p['budget_hours'] > 0 ? round(($p['actual_hours'] / $p['budget_hours']) * 100) : 0;
?>

<!-- Header -->
<div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-3 mb-1">
            <h1 class="text-2xl font-bold"><?= htmlspecialchars($p['project_number']) ?></h1>
            <span class="px-2 py-1 rounded-full text-xs font-medium <?= $statusColors[$p['status']] ?>"><?= $statusLabels[$p['status']] ?></span>
            <span><?= $priorityIcons[$p['priority']] ?? '' ?></span>
        </div>
        <p class="text-lg text-gray-600 dark:text-gray-300"><?= htmlspecialchars($p['name']) ?></p>
        <?php if ($p['description']): ?><p class="text-sm text-gray-500 mt-1"><?= nl2br(htmlspecialchars($p['description'])) ?></p><?php endif; ?>
    </div>
    <div class="flex gap-2">
        <a href="/projects/<?= $p['id'] ?>/edit" class="px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">✏️ Redigera</a>
        <?php if ($p['status'] !== 'completed'): ?>
        <button onclick="document.getElementById('completeModal').classList.remove('hidden')" class="px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg">✅ Avsluta projekt</button>
        <?php endif; ?>
    </div>
</div>

<!-- KPI-kort -->
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border border-gray-200 dark:border-gray-700">
        <p class="text-xs text-gray-500 uppercase">Projektledare</p>
        <p class="text-sm font-semibold mt-1"><?= htmlspecialchars($p['manager_name'] ?? '—') ?></p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border border-gray-200 dark:border-gray-700">
        <p class="text-xs text-gray-500 uppercase">Kund</p>
        <p class="text-sm font-semibold mt-1"><?= htmlspecialchars($p['customer_name'] ?? '—') ?></p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border border-gray-200 dark:border-gray-700">
        <p class="text-xs text-gray-500 uppercase">Tid</p>
        <p class="text-sm font-semibold mt-1"><?= number_format($p['actual_hours'], 1) ?> / <?= number_format($p['budget_hours'], 0) ?>h</p>
        <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-1.5 mt-1"><div class="h-1.5 rounded-full <?= $timePct > 100 ? 'bg-red-500' : 'bg-indigo-600' ?>" style="width:<?= min($timePct, 100) ?>%"></div></div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border border-gray-200 dark:border-gray-700">
        <p class="text-xs text-gray-500 uppercase">Budget</p>
        <p class="text-sm font-semibold mt-1"><?= number_format($budgetSummary['actual'], 0, ',', ' ') ?> / <?= number_format($p['budget_total'], 0, ',', ' ') ?> kr</p>
        <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-1.5 mt-1"><div class="h-1.5 rounded-full <?= $budgetPct > 100 ? 'bg-red-500' : 'bg-green-600' ?>" style="width:<?= min($budgetPct, 100) ?>%"></div></div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border border-gray-200 dark:border-gray-700">
        <p class="text-xs text-gray-500 uppercase">Färdigställande</p>
        <p class="text-2xl font-bold mt-1"><?= $p['completion_pct'] ?>%</p>
        <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-1.5 mt-1"><div class="h-1.5 rounded-full bg-indigo-600" style="width:<?= $p['completion_pct'] ?>%"></div></div>
    </div>
</div>

<!-- Datum-info -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 text-sm">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-gray-200 dark:border-gray-700">
        <span class="text-gray-500">Start:</span> <span class="font-medium"><?= $p['start_date'] ?? '—' ?></span>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-gray-200 dark:border-gray-700">
        <span class="text-gray-500">Planerat slut:</span> <span class="font-medium <?= ($p['end_date'] && $p['end_date'] < date('Y-m-d') && $p['status'] !== 'completed') ? 'text-red-600' : '' ?>"><?= $p['end_date'] ?? '—' ?></span>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-gray-200 dark:border-gray-700">
        <span class="text-gray-500">Avdelning:</span> <span class="font-medium"><?= htmlspecialchars($p['department_name'] ?? '—') ?></span>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-gray-200 dark:border-gray-700">
        <span class="text-gray-500">Skapad av:</span> <span class="font-medium"><?= htmlspecialchars($p['created_by_name'] ?? '—') ?></span>
    </div>
</div>

<!-- FLIKAR -->
<div x-data="{ tab: 'tasks' }" class="mb-8">
    <div class="flex gap-1 border-b border-gray-200 dark:border-gray-700 mb-4 overflow-x-auto">
        <?php foreach (['tasks'=>'📝 Uppgifter','phases'=>'📐 Faser','milestones'=>'🏁 Milstolpar','time'=>'⏱ Tid','budget'=>'💰 Budget','risks'=>'⚠️ Risker','members'=>'👥 Team','log'=>'📜 Logg'] as $k=>$v): ?>
        <button @click="tab='<?= $k ?>'" :class="tab==='<?= $k ?>' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
            class="px-4 py-2 text-sm font-medium border-b-2 whitespace-nowrap transition"><?= $v ?>
            <?php if ($k === 'tasks'): ?><span class="ml-1 text-xs bg-gray-200 dark:bg-gray-600 rounded-full px-1.5"><?= count($tasks) ?></span><?php endif; ?>
        </button>
        <?php endforeach; ?>
    </div>

    <!-- ═══ UPPGIFTER ═══ -->
    <div x-show="tab==='tasks'" x-cloak>
        <!-- Lägg till uppgift -->
        <form method="post" action="/projects/<?= $p['id'] ?>/tasks" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-4">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf) ?>">
            <h3 class="text-sm font-semibold mb-3">Lägg till uppgift</h3>
            <div class="grid grid-cols-1 md:grid-cols-6 gap-3">
                <input type="text" name="title" placeholder="Uppgift..." required class="md:col-span-2 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                <select name="assigned_to" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                    <option value="">Tilldela</option>
                    <?php foreach ($users as $u): ?><option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['full_name']) ?></option><?php endforeach; ?>
                </select>
                <select name="priority" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                    <option value="normal">🔵 Normal</option><option value="low">🟢 Låg</option><option value="high">🟠 Hög</option><option value="urgent">🔴 Brådskande</option>
                </select>
                <input type="number" name="estimated_hours" placeholder="Est. timmar" step="0.5" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm">+ Lägg till</button>
            </div>
        </form>

        <!-- Kanban-liknande lista -->
        <?php
        $grouped = ['todo'=>[],'in_progress'=>[],'review'=>[],'done'=>[]];
        foreach ($tasks as $t) { $grouped[$t['status']][] = $t; }
        ?>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <?php foreach ($grouped as $st => $items): ?>
            <div>
                <h4 class="text-sm font-semibold mb-2 flex items-center gap-2">
                    <span class="<?= $taskStatusColors[$st] ?> px-2 py-0.5 rounded text-xs"><?= $taskStatusLabels[$st] ?></span>
                    <span class="text-gray-400 text-xs">(<?= count($items) ?>)</span>
                </h4>
                <div class="space-y-2">
                    <?php foreach ($items as $t): ?>
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-3">
                        <div class="flex justify-between items-start">
                            <p class="text-sm font-medium"><?= $priorityIcons[$t['priority']] ?> <?= htmlspecialchars($t['title']) ?></p>
                        </div>
                        <?php if ($t['assigned_name']): ?><p class="text-xs text-gray-500 mt-1">👤 <?= htmlspecialchars($t['assigned_name']) ?></p><?php endif; ?>
                        <?php if ($t['estimated_hours']): ?><p class="text-xs text-gray-500"><?= $t['actual_hours'] ?>/<?= $t['estimated_hours'] ?>h</p><?php endif; ?>
                        <div class="flex gap-1 mt-2">
                            <?php foreach (['todo','in_progress','review','done'] as $ns): if ($ns !== $t['status']): ?>
                            <form method="post" action="/projects/<?= $p['id'] ?>/tasks/<?= $t['id'] ?>/status" class="inline">
                                <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf) ?>">
                                <input type="hidden" name="status" value="<?= $ns ?>">
                                <button class="text-xs px-1.5 py-0.5 rounded border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700"><?= $taskStatusLabels[$ns] ?></button>
                            </form>
                            <?php endif; endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- ═══ FASER ═══ -->
    <div x-show="tab==='phases'" x-cloak>
        <form method="post" action="/projects/<?= $p['id'] ?>/phases" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-4">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf) ?>">
            <h3 class="text-sm font-semibold mb-3">Lägg till fas</h3>
            <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                <input type="text" name="name" placeholder="Fas namn..." required class="md:col-span-2 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                <input type="date" name="start_date" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                <input type="date" name="end_date" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm">+ Lägg till</button>
            </div>
        </form>
        <div class="space-y-3">
            <?php foreach ($phases as $ph): ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 flex justify-between items-center">
                <div>
                    <p class="font-medium"><?= htmlspecialchars($ph['name']) ?></p>
                    <p class="text-sm text-gray-500"><?= $ph['start_date'] ?? '?' ?> → <?= $ph['end_date'] ?? '?' ?> · Budget: <?= $ph['budget_hours'] ?>h</p>
                </div>
                <div class="flex gap-2">
                    <?php foreach (['pending'=>'Väntande','active'=>'Aktiv','completed'=>'Klar'] as $s=>$l): if ($s !== $ph['status']): ?>
                    <form method="post" action="/projects/<?= $p['id'] ?>/phases/<?= $ph['id'] ?>/status" class="inline">
                        <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf) ?>"><input type="hidden" name="status" value="<?= $s ?>">
                        <button class="text-xs px-2 py-1 rounded border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700"><?= $l ?></button>
                    </form>
                    <?php endif; endforeach; ?>
                    <form method="post" action="/projects/<?= $p['id'] ?>/phases/<?= $ph['id'] ?>/delete" onsubmit="return confirm('Ta bort fas?')">
                        <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf) ?>">
                        <button class="text-xs px-2 py-1 text-red-600 rounded border border-red-300 hover:bg-red-50">🗑</button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($phases)): ?><p class="text-gray-500 text-sm">Inga faser tillagda.</p><?php endif; ?>
        </div>
    </div>

    <!-- ═══ MILSTOLPAR ═══ -->
    <div x-show="tab==='milestones'" x-cloak>
        <form method="post" action="/projects/<?= $p['id'] ?>/milestones" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-4">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf) ?>">
            <h3 class="text-sm font-semibold mb-3">Lägg till milstolpe</h3>
            <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                <input type="text" name="name" placeholder="Milstolpe..." required class="md:col-span-2 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                <input type="date" name="due_date" required class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                <select name="phase_id" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                    <option value="">Ingen fas</option>
                    <?php foreach ($phases as $ph): ?><option value="<?= $ph['id'] ?>"><?= htmlspecialchars($ph['name']) ?></option><?php endforeach; ?>
                </select>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm">+ Lägg till</button>
            </div>
        </form>
        <div class="space-y-3">
            <?php foreach ($milestones as $ms): ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <span class="text-lg"><?= $ms['completed_at'] ? '✅' : '🏁' ?></span>
                    <div>
                        <p class="font-medium <?= $ms['completed_at'] ? 'line-through text-gray-400' : '' ?>"><?= htmlspecialchars($ms['name']) ?></p>
                        <p class="text-sm text-gray-500">Deadline: <?= $ms['due_date'] ?><?= $ms['phase_name'] ? ' · Fas: ' . htmlspecialchars($ms['phase_name']) : '' ?></p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <?php if (!$ms['completed_at']): ?>
                    <form method="post" action="/projects/<?= $p['id'] ?>/milestones/<?= $ms['id'] ?>/complete">
                        <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf) ?>">
                        <button class="text-xs px-2 py-1 bg-green-600 text-white rounded hover:bg-green-700">Slutför</button>
                    </form>
                    <?php endif; ?>
                    <form method="post" action="/projects/<?= $p['id'] ?>/milestones/<?= $ms['id'] ?>/delete" onsubmit="return confirm('Ta bort?')">
                        <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf) ?>">
                        <button class="text-xs px-2 py-1 text-red-600 rounded border border-red-300 hover:bg-red-50">🗑</button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($milestones)): ?><p class="text-gray-500 text-sm">Inga milstolpar tillagda.</p><?php endif; ?>
        </div>
    </div>

    <!-- ═══ TIDRAPPORTER ═══ -->
    <div x-show="tab==='time'" x-cloak>
        <form method="post" action="/projects/<?= $p['id'] ?>/timesheets" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-4">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf) ?>">
            <h3 class="text-sm font-semibold mb-3">Registrera tid</h3>
            <div class="grid grid-cols-1 md:grid-cols-6 gap-3">
                <input type="date" name="work_date" value="<?= date('Y-m-d') ?>" required class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                <input type="number" name="hours" step="0.25" min="0.25" placeholder="Timmar" required class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                <select name="task_id" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                    <option value="">Ingen uppgift</option>
                    <?php foreach ($tasks as $t): ?><option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['title']) ?></option><?php endforeach; ?>
                </select>
                <input type="text" name="description" placeholder="Beskrivning..." class="md:col-span-2 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm">+ Registrera</button>
            </div>
        </form>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr><th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Datum</th><th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Person</th><th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Uppgift</th><th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Timmar</th><th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Beskrivning</th><th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($timesheets as $ts): ?>
                    <tr>
                        <td class="px-4 py-2"><?= $ts['work_date'] ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($ts['user_name']) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($ts['task_title'] ?? '—') ?></td>
                        <td class="px-4 py-2 font-medium"><?= $ts['hours'] ?>h</td>
                        <td class="px-4 py-2 text-gray-500"><?= htmlspecialchars($ts['description'] ?? '') ?></td>
                        <td class="px-4 py-2">
                            <?php if ($ts['approved']): ?>
                                <span class="text-green-600 text-xs">✅ Godkänd</span>
                            <?php else: ?>
                                <form method="post" action="/projects/timesheets/<?= $ts['id'] ?>/approve" class="inline">
                                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf) ?>">
                                    <button class="text-xs px-2 py-1 bg-green-600 text-white rounded hover:bg-green-700">Godkänn</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($timesheets)): ?><tr><td colspan="6" class="px-4 py-4 text-center text-gray-500">Inga tidrapporter.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ═══ BUDGET ═══ -->
    <div x-show="tab==='budget'" x-cloak>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                <p class="text-xs text-gray-500 uppercase">Tidkostnad</p>
                <p class="text-xl font-bold"><?= number_format($budgetSummary['time_cost'], 0, ',', ' ') ?> kr</p>
                <p class="text-xs text-gray-500"><?= number_format($p['actual_hours'], 1) ?>h × <?= number_format($p['hourly_rate'], 0) ?> kr/h</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                <p class="text-xs text-gray-500 uppercase">Total kostnad</p>
                <p class="text-xl font-bold <?= $budgetPct > 100 ? 'text-red-600' : '' ?>"><?= number_format($budgetSummary['actual'], 0, ',', ' ') ?> kr</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                <p class="text-xs text-gray-500 uppercase">Budget kvar</p>
                <p class="text-xl font-bold <?= ($p['budget_total'] - $budgetSummary['actual']) < 0 ? 'text-red-600' : 'text-green-600' ?>"><?= number_format($p['budget_total'] - $budgetSummary['actual'], 0, ',', ' ') ?> kr</p>
            </div>
        </div>
        <form method="post" action="/projects/<?= $p['id'] ?>/budget" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-4">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf) ?>">
            <h3 class="text-sm font-semibold mb-3">Lägg till budgetpost</h3>
            <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                <select name="category" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                    <option value="material">Material</option><option value="subcontractor">Underleverantör</option><option value="travel">Resor</option><option value="other">Övrigt</option>
                </select>
                <input type="text" name="description" placeholder="Beskrivning..." required class="md:col-span-2 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                <input type="number" name="budgeted_amount" placeholder="Belopp (kr)" step="1" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm">+ Lägg till</button>
            </div>
        </form>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-900/50"><tr><th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th><th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Beskrivning</th><th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Budget</th><th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Utfall</th><th class="px-4 py-2"></th></tr></thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php $cats = ['labor'=>'Arbete','material'=>'Material','subcontractor'=>'Underlev.','travel'=>'Resor','other'=>'Övrigt']; ?>
                    <?php foreach ($budget as $b): ?>
                    <tr><td class="px-4 py-2"><?= $cats[$b['category']] ?? $b['category'] ?></td><td class="px-4 py-2"><?= htmlspecialchars($b['description']) ?></td><td class="px-4 py-2 text-right"><?= number_format($b['budgeted_amount'], 0, ',', ' ') ?></td><td class="px-4 py-2 text-right"><?= number_format($b['actual_amount'], 0, ',', ' ') ?></td>
                    <td class="px-4 py-2 text-right"><form method="post" action="/projects/<?= $p['id'] ?>/budget/<?= $b['id'] ?>/delete" onsubmit="return confirm('Ta bort?')"><input type="hidden" name="_token" value="<?= htmlspecialchars($csrf) ?>"><button class="text-red-600 text-xs">🗑</button></form></td></tr>
                    <?php endforeach; ?>
                    <?php if (empty($budget)): ?><tr><td colspan="5" class="px-4 py-4 text-center text-gray-500">Inga budgetposter.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ═══ RISKER ═══ -->
    <div x-show="tab==='risks'" x-cloak>
        <form method="post" action="/projects/<?= $p['id'] ?>/risks" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-4">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf) ?>">
            <h3 class="text-sm font-semibold mb-3">Identifiera risk</h3>
            <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                <input type="text" name="title" placeholder="Risk..." required class="md:col-span-2 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                <select name="probability" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                    <option value="low">Låg sannolikhet</option><option value="medium" selected>Medel</option><option value="high">Hög</option>
                </select>
                <select name="impact" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                    <option value="low">Låg konsekvens</option><option value="medium" selected>Medel</option><option value="high">Hög</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm">+ Lägg till</button>
            </div>
        </form>
        <div class="space-y-3">
            <?php foreach ($risks as $r): ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="font-medium"><?= htmlspecialchars($r['title']) ?></p>
                        <p class="text-sm mt-1">
                            Sannolikhet: <span class="font-medium <?= $riskColors[$r['probability']] ?>"><?= $r['probability'] ?></span> ·
                            Konsekvens: <span class="font-medium <?= $riskColors[$r['impact']] ?>"><?= $r['impact'] ?></span>
                        </p>
                    </div>
                    <div class="flex gap-2">
                        <?php foreach (['mitigated'=>'Mitigerad','occurred'=>'Inträffad','closed'=>'Stängd'] as $s=>$l): if ($s !== $r['status']): ?>
                        <form method="post" action="/projects/<?= $p['id'] ?>/risks/<?= $r['id'] ?>/status" class="inline">
                            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf) ?>"><input type="hidden" name="status" value="<?= $s ?>">
                            <button class="text-xs px-2 py-1 rounded border border-gray-300 dark:border-gray-600 hover:bg-gray-100"><?= $l ?></button>
                        </form>
                        <?php endif; endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($risks)): ?><p class="text-gray-500 text-sm">Inga risker identifierade.</p><?php endif; ?>
        </div>
    </div>

    <!-- ═══ TEAM ═══ -->
    <div x-show="tab==='members'" x-cloak>
        <form method="post" action="/projects/<?= $p['id'] ?>/members" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-4">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf) ?>">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <select name="user_id" required class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                    <option value="">Välj person</option>
                    <?php foreach ($users as $u): ?><option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['full_name']) ?></option><?php endforeach; ?>
                </select>
                <select name="role" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                    <option value="member">Medlem</option><option value="manager">Projektledare</option><option value="viewer">Läsare</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm">+ Lägg till</button>
            </div>
        </form>
        <div class="space-y-2">
            <?php $roleLabels = ['manager'=>'Projektledare','member'=>'Medlem','viewer'=>'Läsare']; ?>
            <?php foreach ($members as $m): ?>
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-3 flex justify-between items-center">
                <div><span class="font-medium"><?= htmlspecialchars($m['full_name']) ?></span> <span class="text-xs text-gray-500 ml-2"><?= $roleLabels[$m['role']] ?? $m['role'] ?></span></div>
                <form method="post" action="/projects/<?= $p['id'] ?>/members/<?= $m['user_id'] ?>/delete" onsubmit="return confirm('Ta bort?')">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf) ?>">
                    <button class="text-red-600 text-xs">🗑</button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- ═══ LOGG ═══ -->
    <div x-show="tab==='log'" x-cloak>
        <form method="post" action="/projects/<?= $p['id'] ?>/comments" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-4">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf) ?>">
            <div class="flex gap-3">
                <input type="text" name="message" placeholder="Lägg till kommentar..." required class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm">Skicka</button>
            </div>
        </form>
        <div class="space-y-3">
            <?php foreach ($log as $l): ?>
            <div class="flex gap-3">
                <div class="w-8 h-8 bg-indigo-100 dark:bg-indigo-900/30 rounded-full flex items-center justify-center text-xs"><?= mb_substr($l['user_name'], 0, 1) ?></div>
                <div>
                    <p class="text-sm"><span class="font-medium"><?= htmlspecialchars($l['user_name']) ?></span> <span class="text-gray-500 text-xs"><?= date('Y-m-d H:i', strtotime($l['created_at'])) ?></span></p>
                    <p class="text-sm text-gray-600 dark:text-gray-300"><?= htmlspecialchars($l['message']) ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- ═══ AVSLUTA-MODAL ═══ -->
<div id="completeModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 w-full max-w-md shadow-xl">
        <h3 class="text-lg font-bold mb-4">✅ Avsluta projekt</h3>
        <form method="post" action="/projects/<?= $p['id'] ?>/complete" class="space-y-4">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf) ?>">
            <div>
                <label class="block text-sm font-medium mb-1">Utvärdering (1-5)</label>
                <select name="evaluation_score" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                    <option value="">—</option>
                    <option value="5">⭐⭐⭐⭐⭐ Utmärkt</option><option value="4">⭐⭐⭐⭐ Bra</option><option value="3">⭐⭐⭐ OK</option><option value="2">⭐⭐ Under förväntan</option><option value="1">⭐ Dåligt</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Kommentar</label>
                <textarea name="evaluation_notes" rows="3" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm" placeholder="Vad gick bra? Vad kan förbättras?"></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('completeModal').classList.add('hidden')" class="px-4 py-2 text-sm rounded-lg border border-gray-300">Avbryt</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg">Avsluta projekt</button>
            </div>
        </form>
    </div>
</div>
