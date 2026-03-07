<!doctype html>
<html lang="sv">
<head>
    <meta charset="utf-8">
    <title>Underhåll Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 24px; }
        .topbar { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; }
        .cards { display:grid; grid-template-columns: repeat(4, 1fr); gap:16px; margin-bottom:24px; }
        .card {
            border:1px solid #ddd;
            border-radius:10px;
            padding:18px;
            background:#fff;
        }
        .metric { font-size:28px; font-weight:bold; margin-top:8px; }
        .grid { display:grid; grid-template-columns: 1fr 1fr; gap:24px; }
        table { width:100%; border-collapse:collapse; }
        th, td { border:1px solid #ddd; padding:10px; text-align:left; vertical-align:top; }
        th { background:#f5f5f5; }
        a.button {
            display:inline-block; padding:10px 14px; background:#111; color:#fff;
            text-decoration:none; border-radius:6px;
        }
        .muted { color:#666; font-size:13px; }
    </style>
</head>
<body>
    <div class="topbar">
        <h1>Underhåll Dashboard</h1>
        <div>
            <a class="button" href="/maintenance/work-orders">Arbetsorder</a>
            <a class="button" href="/maintenance/preventive">PM</a>
        </div>
    </div>

    <div class="cards">
        <div class="card">
            <div>Öppna arbetsorder</div>
            <div class="metric"><?= (int) ($workOrderCounts['open_count'] ?? 0) ?></div>
        </div>
        <div class="card">
            <div>Pågående arbetsorder</div>
            <div class="metric"><?= (int) ($workOrderCounts['in_progress_count'] ?? 0) ?></div>
        </div>
        <div class="card">
            <div>Förfallna arbetsorder</div>
            <div class="metric"><?= (int) ($workOrderCounts['overdue_count'] ?? 0) ?></div>
        </div>
        <div class="card">
            <div>Förfallna PM</div>
            <div class="metric"><?= (int) ($pmCounts['due_count'] ?? 0) ?></div>
        </div>
    </div>

    <div class="grid">
        <div class="card">
            <h2>Öppna arbetsorder</h2>

            <table>
                <thead>
                    <tr>
                        <th>WO</th>
                        <th>Titel</th>
                        <th>Asset</th>
                        <th>Status</th>
                        <th>Förfallo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentOpenWorkOrders as $wo): ?>
                        <tr>
                            <td>
                                <a href="/maintenance/work-orders/show?id=<?= (int) $wo['id'] ?>">
                                    <?= htmlspecialchars($wo['work_order_no']) ?>
                                </a>
                            </td>
                            <td><?= htmlspecialchars($wo['title']) ?></td>
                            <td>
                                <?= htmlspecialchars($wo['asset_name']) ?>
                                <div class="muted"><?= htmlspecialchars((string) ($wo['asset_code'] ?? '')) ?></div>
                            </td>
                            <td><?= htmlspecialchars($wo['status']) ?></td>
                            <td><?= htmlspecialchars((string) ($wo['due_at'] ?? '')) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="card">
            <h2>Kommande / förfallna PM</h2>

            <table>
                <thead>
                    <tr>
                        <th>Schema</th>
                        <th>Asset</th>
                        <th>Intervall</th>
                        <th>Nästa förfallo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dueSoonSchedules as $schedule): ?>
                        <tr>
                            <td>
                                <a href="/maintenance/preventive/show?id=<?= (int) $schedule['id'] ?>">
                                    <?= htmlspecialchars($schedule['title']) ?>
                                </a>
                            </td>
                            <td>
                                <?= htmlspecialchars($schedule['asset_name']) ?>
                                <div class="muted"><?= htmlspecialchars((string) ($schedule['asset_code'] ?? '')) ?></div>
                            </td>
                            <td><?= htmlspecialchars($schedule['interval_value'] . ' ' . $schedule['interval_type']) ?></td>
                            <td><?= htmlspecialchars($schedule['next_due_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
