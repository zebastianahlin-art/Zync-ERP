<!doctype html>
<html lang="sv">
<head>
    <meta charset="utf-8">
    <title>Arbetsorder</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 24px; }
        .topbar, .filters { display:flex; justify-content:space-between; align-items:end; gap:12px; margin-bottom:20px; flex-wrap:wrap; }
        .filters form { display:grid; grid-template-columns: repeat(4, minmax(180px, 1fr)); gap:12px; width:100%; }
        .filters .full { grid-column: span 4; }
        table { width:100%; border-collapse:collapse; }
        th, td { border:1px solid #ddd; padding:10px; text-align:left; vertical-align:top; }
        th { background:#f5f5f5; }
        a.button, button.button {
            display:inline-block; padding:10px 14px; background:#111; color:#fff;
            text-decoration:none; border-radius:6px; border:none; cursor:pointer;
        }
        input, select { width:100%; padding:10px; box-sizing:border-box; }
        .status { font-weight:bold; }
        .muted { color:#666; font-size:13px; }
    </style>
</head>
<body>
    <div class="topbar">
        <h1>Arbetsorder</h1>
        <a class="button" href="/maintenance/work-orders/create">Ny arbetsorder</a>
    </div>

    <div class="filters">
        <form method="GET" action="/maintenance/work-orders">
            <div>
                <label for="q">Sök</label>
                <input type="text" name="q" id="q" value="<?= htmlspecialchars($filters['q'] ?? '') ?>">
            </div>

            <div>
                <label for="status">Status</label>
                <select name="status" id="status">
                    <option value="">Alla</option>
                    <option value="reported" <?= (($filters['status'] ?? '') === 'reported') ? 'selected' : '' ?>>reported</option>
                    <option value="approved" <?= (($filters['status'] ?? '') === 'approved') ? 'selected' : '' ?>>approved</option>
                    <option value="planned" <?= (($filters['status'] ?? '') === 'planned') ? 'selected' : '' ?>>planned</option>
                    <option value="in_progress" <?= (($filters['status'] ?? '') === 'in_progress') ? 'selected' : '' ?>>in_progress</option>
                    <option value="completed" <?= (($filters['status'] ?? '') === 'completed') ? 'selected' : '' ?>>completed</option>
                    <option value="closed" <?= (($filters['status'] ?? '') === 'closed') ? 'selected' : '' ?>>closed</option>
                    <option value="cancelled" <?= (($filters['status'] ?? '') === 'cancelled') ? 'selected' : '' ?>>cancelled</option>
                </select>
            </div>

            <div>
                <label for="priority">Prioritet</label>
                <select name="priority" id="priority">
                    <option value="">Alla</option>
                    <option value="low" <?= (($filters['priority'] ?? '') === 'low') ? 'selected' : '' ?>>low</option>
                    <option value="medium" <?= (($filters['priority'] ?? '') === 'medium') ? 'selected' : '' ?>>medium</option>
                    <option value="high" <?= (($filters['priority'] ?? '') === 'high') ? 'selected' : '' ?>>high</option>
                    <option value="critical" <?= (($filters['priority'] ?? '') === 'critical') ? 'selected' : '' ?>>critical</option>
                </select>
            </div>

            <div>
                <label for="type">Typ</label>
                <select name="type" id="type">
                    <option value="">Alla</option>
                    <option value="corrective" <?= (($filters['type'] ?? '') === 'corrective') ? 'selected' : '' ?>>corrective</option>
                    <option value="preventive" <?= (($filters['type'] ?? '') === 'preventive') ? 'selected' : '' ?>>preventive</option>
                    <option value="inspection" <?= (($filters['type'] ?? '') === 'inspection') ? 'selected' : '' ?>>inspection</option>
                    <option value="emergency" <?= (($filters['type'] ?? '') === 'emergency') ? 'selected' : '' ?>>emergency</option>
                </select>
            </div>

            <div>
                <label for="source">Källa</label>
                <select name="source" id="source">
                    <option value="">Alla</option>
                    <option value="manual" <?= (($filters['source'] ?? '') === 'manual') ? 'selected' : '' ?>>manual</option>
                    <option value="pm_schedule" <?= (($filters['source'] ?? '') === 'pm_schedule') ? 'selected' : '' ?>>pm_schedule</option>
                    <option value="inspection" <?= (($filters['source'] ?? '') === 'inspection') ? 'selected' : '' ?>>inspection</option>
                    <option value="fault_report" <?= (($filters['source'] ?? '') === 'fault_report') ? 'selected' : '' ?>>fault_report</option>
                    <option value="system" <?= (($filters['source'] ?? '') === 'system') ? 'selected' : '' ?>>system</option>
                </select>
            </div>

            <div>
                <label for="asset_node_id">Asset</label>
                <select name="asset_node_id" id="asset_node_id">
                    <option value="">Alla</option>
                    <?php foreach ($assetOptions as $asset): ?>
                        <option value="<?= (int) $asset['id'] ?>" <?= ((string) ($filters['asset_node_id'] ?? '') === (string) $asset['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($asset['name']) ?> (<?= htmlspecialchars($asset['node_type']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="only_open">Endast öppna</label>
                <select name="only_open" id="only_open">
                    <option value="">Nej</option>
                    <option value="1" <?= (($filters['only_open'] ?? '') === '1') ? 'selected' : '' ?>>Ja</option>
                </select>
            </div>

            <div>
                <label for="overdue">Endast förfallna</label>
                <select name="overdue" id="overdue">
                    <option value="">Nej</option>
                    <option value="1" <?= (($filters['overdue'] ?? '') === '1') ? 'selected' : '' ?>>Ja</option>
                </select>
            </div>

            <div class="full">
                <button class="button" type="submit">Filtrera</button>
                <a class="button" href="/maintenance/work-orders" style="background:#666;">Rensa</a>
            </div>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>WO-nr</th>
                <th>Titel</th>
                <th>Asset</th>
                <th>Typ</th>
                <th>Källa</th>
                <th>Status</th>
                <th>Förfallodatum</th>
                <th>Timmar</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($workOrders as $wo): ?>
                <tr>
                    <td>
                        <a href="/maintenance/work-orders/show?id=<?= (int) $wo['id'] ?>">
                            <?= htmlspecialchars($wo['work_order_no']) ?>
                        </a>
                    </td>
                    <td>
                        <?= htmlspecialchars($wo['title']) ?>
                        <?php if (!empty($wo['pm_schedule_id'])): ?>
                            <div class="muted">
                                PM-schema: #<?= (int) $wo['pm_schedule_id'] ?>
                                <?php if (!empty($wo['pm_schedule_title'])): ?>
                                    — <?= htmlspecialchars($wo['pm_schedule_title']) ?>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?= htmlspecialchars($wo['asset_name']) ?><br>
                        <small><?= htmlspecialchars((string) $wo['asset_type']) ?> / <?= htmlspecialchars((string) $wo['asset_code']) ?></small>
                    </td>
                    <td><?= htmlspecialchars($wo['type']) ?></td>
                    <td><?= htmlspecialchars($wo['source']) ?></td>
                    <td class="status"><?= htmlspecialchars($wo['status']) ?></td>
                    <td><?= htmlspecialchars((string) ($wo['due_at'] ?? '')) ?></td>
                    <td><?= htmlspecialchars((string) $wo['actual_hours']) ?> / <?= htmlspecialchars((string) ($wo['estimated_hours'] ?? '')) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
