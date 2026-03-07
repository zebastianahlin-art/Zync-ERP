<!doctype html>
<html lang="sv">
<head>
    <meta charset="utf-8">
    <title>Arbetsorder <?= htmlspecialchars($workOrder['work_order_no']) ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 24px; }
        .grid { display:grid; grid-template-columns: 1fr 1fr; gap:24px; }
        .card { border:1px solid #ddd; border-radius:8px; padding:18px; }
        h1, h2, h3 { margin-top:0; }
        label { display:block; margin-top:12px; font-weight:bold; }
        input, select, textarea {
            width:100%; padding:10px; margin-top:6px; box-sizing:border-box;
        }
        .log { border-top:1px solid #eee; padding:10px 0; }
        .muted { color:#666; font-size:13px; }
        .full { margin-top:24px; }
    </style>
</head>
<body>
    <p><a href="/maintenance/work-orders">← Till arbetsorder</a></p>

    <h1><?= htmlspecialchars($workOrder['work_order_no']) ?> — <?= htmlspecialchars($workOrder['title']) ?></h1>

    <div class="grid">
        <div class="card">
            <h2>Översikt</h2>
            <p><strong>Asset:</strong> <?= htmlspecialchars($workOrder['asset_name']) ?> (<?= htmlspecialchars($workOrder['asset_type']) ?><?= !empty($workOrder['asset_code']) ? ' / ' . htmlspecialchars($workOrder['asset_code']) : '' ?>)</p>
            <p><strong>Typ:</strong> <?= htmlspecialchars($workOrder['type']) ?></p>
            <p><strong>Prioritet:</strong> <?= htmlspecialchars($workOrder['priority']) ?></p>
            <p><strong>Status:</strong> <?= htmlspecialchars($workOrder['status']) ?></p>
            <p><strong>Källa:</strong> <?= htmlspecialchars($workOrder['source']) ?></p>

            <?php if (!empty($workOrder['pm_schedule_id'])): ?>
                <p>
                    <strong>PM-schema:</strong>
                    <a href="/maintenance/preventive/show?id=<?= (int) $workOrder['pm_schedule_id'] ?>">
                        #<?= (int) $workOrder['pm_schedule_id'] ?>
                        <?php if (!empty($workOrder['pm_schedule_title'])): ?>
                            — <?= htmlspecialchars($workOrder['pm_schedule_title']) ?>
                        <?php endif; ?>
                    </a>
                </p>
            <?php endif; ?>

            <p><strong>Planerad start:</strong> <?= htmlspecialchars((string) ($workOrder['planned_start_at'] ?? '')) ?></p>
            <p><strong>Förfallodatum:</strong> <?= htmlspecialchars((string) ($workOrder['due_at'] ?? '')) ?></p>
            <p><strong>Estimerade timmar:</strong> <?= htmlspecialchars((string) ($workOrder['estimated_hours'] ?? '')) ?></p>
            <p><strong>Faktiska timmar:</strong> <?= htmlspecialchars((string) $workOrder['actual_hours']) ?></p>
            <p><strong>Beskrivning:</strong><br><?= nl2br(htmlspecialchars((string) ($workOrder['description'] ?? ''))) ?></p>
        </div>

        <div class="card">
            <h2>Byt status</h2>
            <form method="POST" action="/maintenance/work-orders/status">
                <input type="hidden" name="id" value="<?= (int) $workOrder['id'] ?>">

                <label for="status">Ny status</label>
                <select name="status" id="status">
                    <option value="approved">approved</option>
                    <option value="planned">planned</option>
                    <option value="in_progress">in_progress</option>
                    <option value="completed">completed</option>
                    <option value="closed">closed</option>
                    <option value="cancelled">cancelled</option>
                </select>

                <div style="margin-top:16px;">
                    <button type="submit">Uppdatera status</button>
                </div>
            </form>

            <hr style="margin:24px 0;">

            <h2>Lägg till logg</h2>
            <form method="POST" action="/maintenance/work-orders/add-log">
                <input type="hidden" name="work_order_id" value="<?= (int) $workOrder['id'] ?>">

                <label for="log_type">Typ</label>
                <select name="log_type" id="log_type">
                    <option value="comment">Kommentar</option>
                    <option value="work">Arbete</option>
                </select>

                <label for="message">Meddelande</label>
                <textarea name="message" id="message" rows="5" required></textarea>

                <label for="hours_spent">Timmar</label>
                <input type="number" step="0.01" name="hours_spent" id="hours_spent" value="0">

                <div style="margin-top:16px;">
                    <button type="submit">Spara logg</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card full">
        <h2>Loggar</h2>

        <?php if (empty($logs)): ?>
            <p>Inga loggar ännu.</p>
        <?php else: ?>
            <?php foreach ($logs as $log): ?>
                <div class="log">
                    <div><strong><?= htmlspecialchars($log['log_type']) ?></strong></div>
                    <div><?= nl2br(htmlspecialchars($log['message'])) ?></div>
                    <?php if ((float) $log['hours_spent'] > 0): ?>
                        <div><strong>Timmar:</strong> <?= htmlspecialchars((string) $log['hours_spent']) ?></div>
                    <?php endif; ?>
                    <div class="muted"><?= htmlspecialchars($log['created_at']) ?></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
