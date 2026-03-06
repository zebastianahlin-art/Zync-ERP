<!doctype html>
<html lang="sv">
<head>
    <meta charset="utf-8">
    <title>Förebyggande underhåll</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 24px; }
        .topbar { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; gap:12px; }
        .actions { display:flex; gap:10px; }
        table { width:100%; border-collapse:collapse; }
        th, td { border:1px solid #ddd; padding:10px; text-align:left; vertical-align:top; }
        th { background:#f5f5f5; }
        a.button, button.button {
            display:inline-block; padding:10px 14px; background:#111; color:#fff;
            text-decoration:none; border-radius:6px; border:none; cursor:pointer;
        }
        form.inline { display:inline; }
    </style>
</head>
<body>
    <div class="topbar">
        <h1>Förebyggande underhåll</h1>
        <div class="actions">
            <form class="inline" method="POST" action="/maintenance/preventive/run-due">
                <button class="button" type="submit">Kör förfallna scheman</button>
            </form>
            <a class="button" href="/maintenance/preventive/create">Nytt PM-schema</a>
        </div>
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
