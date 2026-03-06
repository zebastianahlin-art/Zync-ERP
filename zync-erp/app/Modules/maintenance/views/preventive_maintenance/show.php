<!doctype html>
<html lang="sv">
<head>
    <meta charset="utf-8">
    <title>PM-schema <?= htmlspecialchars($schedule['title']) ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 24px; }
        .grid { display:grid; grid-template-columns: 1fr 1fr; gap:24px; }
        .card { border:1px solid #ddd; border-radius:8px; padding:18px; }
        table { width:100%; border-collapse:collapse; margin-top:12px; }
        th, td { border:1px solid #ddd; padding:10px; text-align:left; vertical-align:top; }
        th { background:#f5f5f5; }
        textarea { width:100%; box-sizing:border-box; padding:10px; }
    </style>
</head>
<body>
    <p><a href="/maintenance/preventive">← Till PM-scheman</a></p>

    <h1><?= htmlspecialchars($schedule['title']) ?></h1>

    <div class="grid">
        <div class="card">
            <h2>Översikt</h2>
            <p><strong>Asset:</strong> <?= htmlspecialchars($schedule['asset_name']) ?> (<?= htmlspecialchars($schedule['asset_type']) ?><?= !empty($schedule['asset_code']) ? ' / ' . htmlspecialchars($schedule['asset_code']) : '' ?>)</p>
            <p><strong>Beskrivning:</strong><br><?= nl2br(htmlspecialchars((string) ($schedule['description'] ?? ''))) ?></p>
            <p><strong>Intervall:</strong> <?= htmlspecialchars($schedule['interval_value'] . ' ' . $schedule['interval_type']) ?></p>
            <p><strong>Nästa förfallo:</strong> <?= htmlspecialchars($schedule['next_due_at']) ?></p>
            <p><strong>Senast genererad:</strong> <?= htmlspecialchars((string) ($schedule['last_generated_at'] ?? '')) ?></p>
            <p><strong>Senast slutförd:</strong> <?= htmlspecialchars((string) ($schedule['last_completed_at'] ?? '')) ?></p>
            <p><strong>Prioritet:</strong> <?= htmlspecialchars($schedule['priority']) ?></p>
            <p><strong>WO-typ:</strong> <?= htmlspecialchars($schedule['default_work_order_type']) ?></p>
            <p><strong>Automatisk WO:</strong> <?= (int) $schedule['auto_create_work_order'] === 1 ? 'Ja' : 'Nej' ?></p>
            <p><strong>Aktiv:</strong> <?= (int) $schedule['is_active'] === 1 ? 'Ja' : 'Nej' ?></p>
        </div>

        <div class="card">
            <h2>Markera körning som slutförd</h2>
            <form method="POST" action="/maintenance/preventive/complete-run">
                <label for="run_id">Körning</label>
                <select name="run_id" id="run_id" required>
                    <option value="">Välj körning</option>
                    <?php foreach ($runs as $run): ?>
                        <option value="<?= (int) $run['id'] ?>">
                            #<?= (int) $run['id'] ?> - <?= htmlspecialchars($run['run_status']) ?> - <?= htmlspecialchars($run['due_at']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="notes">Notering</label>
                <textarea name="notes" id="notes" rows="5"></textarea>

                <div style="margin-top:16px;">
                    <button type="submit">Markera slutförd</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card" style="margin-top:24px;">
        <h2>Körhistorik</h2>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Status</th>
                    <th>Förfallo</th>
                    <th>Genererad</th>
                    <th>WO</th>
                    <th>Notering</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($runs as $run): ?>
                    <tr>
                        <td><?= (int) $run['id'] ?></td>
                        <td><?= htmlspecialchars($run['run_status']) ?></td>
                        <td><?= htmlspecialchars($run['due_at']) ?></td>
                        <td><?= htmlspecialchars($run['generated_at']) ?></td>
                        <td>
                            <?php if (!empty($run['generated_work_order_id'])): ?>
                                <a href="/maintenance/work-orders/show?id=<?= (int) $run['generated_work_order_id'] ?>">
                                    <?= htmlspecialchars((string) $run['work_order_no']) ?>
                                </a>
                                <br>
                                <small><?= htmlspecialchars((string) $run['work_order_status']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td><?= nl2br(htmlspecialchars((string) ($run['notes'] ?? ''))) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
