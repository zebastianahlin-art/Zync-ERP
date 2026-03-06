<!doctype html>
<html lang="sv">
<head>
    <meta charset="utf-8">
    <title>Arbetsorder</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 24px; }
        .topbar { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; }
        table { width:100%; border-collapse:collapse; }
        th, td { border:1px solid #ddd; padding:10px; text-align:left; vertical-align:top; }
        th { background:#f5f5f5; }
        a.button {
            display:inline-block; padding:10px 14px; background:#111; color:#fff;
            text-decoration:none; border-radius:6px;
        }
        .status { font-weight:bold; }
    </style>
</head>
<body>
    <div class="topbar">
        <h1>Arbetsorder</h1>
        <a class="button" href="/maintenance/work-orders/create">Ny arbetsorder</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>WO-nr</th>
                <th>Titel</th>
                <th>Asset</th>
                <th>Typ</th>
                <th>Prioritet</th>
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
                    <td><?= htmlspecialchars($wo['title']) ?></td>
                    <td>
                        <?= htmlspecialchars($wo['asset_name']) ?><br>
                        <small><?= htmlspecialchars((string) $wo['asset_type']) ?> / <?= htmlspecialchars((string) $wo['asset_code']) ?></small>
                    </td>
                    <td><?= htmlspecialchars($wo['type']) ?></td>
                    <td><?= htmlspecialchars($wo['priority']) ?></td>
                    <td class="status"><?= htmlspecialchars($wo['status']) ?></td>
                    <td><?= htmlspecialchars((string) ($wo['due_at'] ?? '')) ?></td>
                    <td><?= htmlspecialchars((string) $wo['actual_hours']) ?> / <?= htmlspecialchars((string) ($wo['estimated_hours'] ?? '')) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
