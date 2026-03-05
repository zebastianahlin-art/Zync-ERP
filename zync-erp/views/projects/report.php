<?php
    $planned = (float) ($project['planned_budget'] ?? $project['budget'] ?? 0);
    $actual  = (float) ($project['actual_cost'] ?? 0);
    $pctRaw  = $planned > 0 ? ($actual / $planned) * 100 : 0;
    $pctBar  = min($pctRaw, 100);
    $overBudget = $actual > $planned;
    $nearBudget = !$overBudget && $planned > 0 && $actual > $planned * 0.8;
    $barClass   = $overBudget ? 'bar-red' : ($nearBudget ? 'bar-yellow' : 'bar-green');
?>
<h1><?= htmlspecialchars($project['name'], ENT_QUOTES, 'UTF-8') ?></h1>
<p class="meta">
    Projektnr: <strong><?= htmlspecialchars($project['project_number'], ENT_QUOTES, 'UTF-8') ?></strong>
    &nbsp;&bull;&nbsp;
    <span class="badge <?= ($project['project_type'] ?? 'internal') === 'external' ? 'badge-green' : 'badge-blue' ?>">
        <?= ($project['project_type'] ?? 'internal') === 'external' ? 'Externt' : 'Internt' ?>
    </span>
    &nbsp;&bull;&nbsp; Rapport genererad: <?= date('Y-m-d H:i') ?>
</p>

<h2>Projektinformation</h2>
<div class="info-grid">
    <div class="info-item"><div class="info-label">Status</div><div class="info-value"><?= htmlspecialchars($project['status'], ENT_QUOTES, 'UTF-8') ?></div></div>
    <div class="info-item"><div class="info-label">Kund</div><div class="info-value"><?= htmlspecialchars($project['customer_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></div></div>
    <div class="info-item"><div class="info-label">Projektledare</div><div class="info-value"><?= htmlspecialchars($project['manager_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></div></div>
    <div class="info-item"><div class="info-label">Startdatum</div><div class="info-value"><?= htmlspecialchars($project['start_date'] ?? '—', ENT_QUOTES, 'UTF-8') ?></div></div>
    <div class="info-item"><div class="info-label">Slutdatum</div><div class="info-value"><?= htmlspecialchars($project['end_date'] ?? '—', ENT_QUOTES, 'UTF-8') ?></div></div>
    <div class="info-item"><div class="info-label">Budget</div><div class="info-value"><?= number_format((float)$project['budget'], 0, ',', ' ') ?> kr</div></div>
</div>

<?php if (!empty($project['description'])): ?>
<p style="margin-bottom:12px; color:#374151;"><?= nl2br(htmlspecialchars($project['description'], ENT_QUOTES, 'UTF-8')) ?></p>
<?php endif; ?>

<h2>Budget-sammanfattning</h2>
<div style="margin-bottom:8px;">
    <div style="display:flex; justify-content:space-between; font-size:11px; margin-bottom:3px;">
        <span>Faktisk kostnad: <strong><?= number_format($actual, 0, ',', ' ') ?> kr</strong></span>
        <span>Planerat: <strong><?= number_format($planned, 0, ',', ' ') ?> kr</strong></span>
        <span><?= round($pctRaw, 1) ?>% förbrukat</span>
    </div>
    <div class="progress-bar-wrap">
        <div class="progress-bar <?= $barClass ?>" style="width:<?= $pctBar ?>%"></div>
    </div>
    <?php if ($overBudget): ?><p style="color:#dc2626;font-size:10px;margin-top:2px;">⚠ Budgetöverskridning!</p><?php endif; ?>
</div>

<?php if (!empty($stakeholders)): ?>
<h2>Intressenter</h2>
<table>
    <thead><tr><th>Namn</th><th>Roll</th><th>E-post</th><th>Telefon</th><th>Anteckningar</th></tr></thead>
    <tbody>
    <?php foreach ($stakeholders as $sh): ?>
    <tr>
        <td><?= htmlspecialchars($sh['name'], ENT_QUOTES, 'UTF-8') ?></td>
        <td><?= htmlspecialchars($sh['role'], ENT_QUOTES, 'UTF-8') ?></td>
        <td><?= htmlspecialchars($sh['email'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
        <td><?= htmlspecialchars($sh['phone'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
        <td><?= htmlspecialchars($sh['notes'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<?php if (!empty($tasks)): ?>
<h2>Uppgifter</h2>
<table>
    <thead><tr><th>Titel</th><th>Ansvarig</th><th>Deadline</th><th>Prioritet</th><th>Status</th></tr></thead>
    <tbody>
    <?php foreach ($tasks as $task): ?>
    <tr>
        <td><?= htmlspecialchars($task['title'], ENT_QUOTES, 'UTF-8') ?></td>
        <td><?= htmlspecialchars($task['assigned_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
        <td><?= htmlspecialchars($task['due_date'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
        <td><?= htmlspecialchars($task['priority'], ENT_QUOTES, 'UTF-8') ?></td>
        <td><?= htmlspecialchars($task['status'], ENT_QUOTES, 'UTF-8') ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<?php if (!empty($linkedPOs)): ?>
<h2>Kopplade inköpsordrar</h2>
<table>
    <thead><tr><th>Ordernr</th><th>Leverantör</th><th>Status</th><th class="text-right">Belopp</th><th>Anteckningar</th></tr></thead>
    <tbody>
    <?php foreach ($linkedPOs as $po): ?>
    <tr>
        <td><?= htmlspecialchars($po['order_number'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
        <td><?= htmlspecialchars($po['supplier_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
        <td><?= htmlspecialchars($po['status'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
        <td class="text-right"><?= number_format((float)$po['total_amount'], 0, ',', ' ') ?> kr</td>
        <td><?= htmlspecialchars($po['link_notes'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
    </tr>
    <?php endforeach; ?>
    <tr class="total-row">
        <td colspan="3">Totalt</td>
        <td class="text-right"><?= number_format(array_sum(array_column($linkedPOs, 'total_amount')), 0, ',', ' ') ?> kr</td>
        <td></td>
    </tr>
    </tbody>
</table>
<?php endif; ?>

<?php if (!empty($costs)): ?>
<h2>Kostnader</h2>
<table>
    <thead><tr><th>Beskrivning</th><th>Kategori</th><th>Datum</th><th class="text-right">Belopp</th></tr></thead>
    <tbody>
    <?php foreach ($costs as $cost): ?>
    <tr>
        <td><?= htmlspecialchars($cost['description'], ENT_QUOTES, 'UTF-8') ?></td>
        <td><?= htmlspecialchars($cost['category'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
        <td><?= htmlspecialchars($cost['cost_date'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
        <td class="text-right"><?= number_format((float)$cost['amount'], 0, ',', ' ') ?> kr</td>
    </tr>
    <?php endforeach; ?>
    <tr class="total-row">
        <td colspan="3">Totalt</td>
        <td class="text-right"><?= number_format(array_sum(array_column($costs, 'amount')), 0, ',', ' ') ?> kr</td>
    </tr>
    </tbody>
</table>
<?php endif; ?>

<?php if (!empty($budget)): ?>
<h2>Budgetrader</h2>
<table>
    <thead><tr><th>Beskrivning</th><th class="text-right">Budgeterat</th><th class="text-right">Faktiskt</th></tr></thead>
    <tbody>
    <?php foreach ($budget as $line): ?>
    <tr>
        <td><?= htmlspecialchars($line['description'], ENT_QUOTES, 'UTF-8') ?></td>
        <td class="text-right"><?= number_format((float)$line['budgeted'], 0, ',', ' ') ?> kr</td>
        <td class="text-right"><?= number_format((float)$line['actual'], 0, ',', ' ') ?> kr</td>
    </tr>
    <?php endforeach; ?>
    <tr class="total-row">
        <td>Totalt</td>
        <td class="text-right"><?= number_format(array_sum(array_column($budget, 'budgeted')), 0, ',', ' ') ?> kr</td>
        <td class="text-right"><?= number_format(array_sum(array_column($budget, 'actual')), 0, ',', ' ') ?> kr</td>
    </tr>
    </tbody>
</table>
<?php endif; ?>
