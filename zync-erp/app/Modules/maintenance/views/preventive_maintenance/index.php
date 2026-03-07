<!doctype html>
<html lang="sv">
<head>
    <meta charset="utf-8">
    <title>Förebyggande underhåll</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 24px; }
        .topbar, .filters { display:flex; justify-content:space-between; align-items:end; gap:12px; margin-bottom:20px; flex-wrap:wrap; }
        .filters form { display:grid; grid-template-columns: repeat(3, minmax(180px, 1fr)); gap:12px; width:100%; }
        .filters .full { grid-column: span 3; }
        .actions { display:flex; gap:10px; }
        table { width:100%; border-collapse:collapse; }
        th, td { border:1px solid #ddd; padding:10px; text-align:left; vertical-align:top; }
        th { background:#f5f5f5; }
        a.button, button.button {
            display:inline-block; padding:10px 14px; background:#111; color:#fff;
            text-decoration:none; border-radius:6px; border:none; cursor:pointer;
        }
        input, select { width:100%; padding:10px; box-sizing:border-box; }
    </style>
</head>
<body>
    <div class="topbar">
        <h1>Förebyggande underhåll</h1>
        <div class="actions">
            <form method="POST" action="/maintenance/preventive/run-due">
                <button class="button" type="submit">Kör förfallna scheman</button>
            </form>
            <a class="button" href="/maintenance/preventive/create">Nytt PM-schema</a>
        </div>
    </div>

    <div class="filters">
        <form method="GET" action="/maintenance/preventive">
            <div>
                <label for="q">Sök</label>
                <input type="text" name="q" id="q" value="<?= htmlspecialchars($filters['q'] ?? '') ?>">
            </div>

            <div>
                <label for="is_active">Aktiv</label>
                <select name="is_active" id="is_active">
                    <option value="">Alla</option>
                    <option value="1" <?= (($filters['is_active'] ?? '') === '1') ? 'selected' : '' ?>>Ja</option>
                    <option value="0" <?= (($filters['is_active'] ?? '') === '0') ? 'selected' : '' ?>>Nej</option>
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
                <label for="interval_type">Intervall</label>
                <select name="interval_type" id="interval_type">
                    <option value="">Alla</option>
                    <option value="daily" <?= (($filters['interval_type'] ?? '') === 'daily') ? 'selected' : '' ?>>daily</option>
                    <option value="weekly" <?= (($filters['interval_type'] ?? '') === 'weekly') ? 'selected' : '' ?>>weekly</option>
                    <option value="monthly" <?= (($filters['interval_type'] ?? '') === 'monthly') ? 'selected' : '' ?>>monthly</option>
                    <option value="yearly" <?= (($filters['interval_type'] ?? '') === 'yearly') ? 'selected' : '' ?>>yearly</option>
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
                <label for="due_only">Endast förfallna</label>
                <select name="due_only" id="due_only">
                    <option value="">Nej</option>
                    <option value="1" <?= (($filters['due_only'] ?? '') === '1') ? 'selected' : '' ?>>Ja</option>
                </select>
            </div>

            <div class="full">
                <button class="button" type="submit">Filtrera</button>
                <a class="button" href="/maintenance/preventive" style="background:#666;">Rensa</a>
            </div>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Titel</th>
                <th>Asset</th>
                <th>Intervall</th>
                <th>Nästa förfallo</th>
                <th>Prioritet</th>
                <th>Aktiv</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($schedules as $item): ?>
                <tr>
                    <td>
                        <a href="/maintenance/preventive/show?id=<?= (int) $item['id'] ?>">
                            <?= htmlspecialchars($item['title']) ?>
                        </a>
                    </td>
                    <td>
                        <?= htmlspecialchars($item['asset_name']) ?><br>
                        <small><?= htmlspecialchars((string) $item['asset_type']) ?> / <?= htmlspecialchars((string) $item['asset_code']) ?></small>
                    </td>
                    <td><?= htmlspecialchars($item['interval_value'] . ' ' . $item['interval_type']) ?></td>
                    <td><?= htmlspecialchars($item['next_due_at']) ?></td>
                    <td><?= htmlspecialchars($item['priority']) ?></td>
                    <td><?= (int) $item['is_active'] === 1 ? 'Ja' : 'Nej' ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
