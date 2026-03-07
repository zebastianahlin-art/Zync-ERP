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
        table { width:100%; border-collapse:collapse; margin-top:12px; }
        th, td { border:1px solid #ddd; padding:10px; text-align:left; vertical-align:top; }
        th { background:#f5f5f5; }
        .two-col { display:grid; grid-template-columns: 1fr 1fr; gap:24px; }
        .actions-inline { display:flex; gap:8px; align-items:center; }
        .actions-inline form { display:inline; }
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
        <h2>Material / reservdelar</h2>

        <p>
            <strong>Planerad materialkostnad:</strong>
            <?= htmlspecialchars(number_format((float) ($materialTotals['planned_total_cost'] ?? 0), 2, ',', ' ')) ?>
        </p>
        <p>
            <strong>Uttagen materialkostnad:</strong>
            <?= htmlspecialchars(number_format((float) ($materialTotals['issued_total_cost'] ?? 0), 2, ',', ' ')) ?>
        </p>

        <table>
            <thead>
                <tr>
                    <th>Artikel</th>
                    <th>Planerat</th>
                    <th>Uttaget</th>
                    <th>Styckkostnad</th>
                    <th>Kostnad uttag</th>
                    <th>Notering</th>
                    <th>Åtgärder</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($materials)): ?>
                    <tr>
                        <td colspan="7">Inga materialrader ännu.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($materials as $material): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($material['article_name']) ?></strong><br>
                                <span class="muted">
                                    <?= htmlspecialchars((string) ($material['article_number'] ?? '')) ?>
                                    <?php if (!empty($material['unit'])): ?>
                                        / <?= htmlspecialchars((string) $material['unit']) ?>
                                    <?php endif; ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars((string) $material['planned_quantity']) ?></td>
                            <td><?= htmlspecialchars((string) $material['issued_quantity']) ?></td>
                            <td><?= htmlspecialchars(number_format((float) $material['unit_cost'], 2, ',', ' ')) ?></td>
                            <td><?= htmlspecialchars(number_format((float) $material['issued_quantity'] * (float) $material['unit_cost'], 2, ',', ' ')) ?></td>
                            <td><?= nl2br(htmlspecialchars((string) ($material['notes'] ?? ''))) ?></td>
                            <td>
                                <form method="POST" action="/maintenance/work-orders/update-material" style="margin-bottom:12px;">
                                    <input type="hidden" name="material_id" value="<?= (int) $material['id'] ?>">

                                    <label>Planerat</label>
                                    <input type="number" step="0.01" name="planned_quantity" value="<?= htmlspecialchars((string) $material['planned_quantity']) ?>">

                                    <label>Uttaget</label>
                                    <input type="number" step="0.01" name="issued_quantity" value="<?= htmlspecialchars((string) $material['issued_quantity']) ?>">

                                    <label>Styckkostnad</label>
                                    <input type="number" step="0.01" name="unit_cost" value="<?= htmlspecialchars((string) $material['unit_cost']) ?>">

                                    <label>Notering</label>
                                    <textarea name="notes" rows="3"><?= htmlspecialchars((string) ($material['notes'] ?? '')) ?></textarea>

                                    <div style="margin-top:8px;">
                                        <button type="submit">Spara</button>
                                    </div>
                                </form>

                                <form method="POST" action="/maintenance/work-orders/delete-material" onsubmit="return confirm('Ta bort materialraden?');">
                                    <input type="hidden" name="material_id" value="<?= (int) $material['id'] ?>">
                                    <button type="submit">Ta bort</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <hr style="margin:24px 0;">

        <h3>Lägg till material</h3>
        <form method="POST" action="/maintenance/work-orders/add-material">
            <input type="hidden" name="work_order_id" value="<?= (int) $workOrder['id'] ?>">

            <div class="two-col">
                <div>
                    <label for="article_id">Artikel</label>
                    <select name="article_id" id="article_id" required>
                        <option value="">Välj artikel</option>
                        <?php foreach ($articleOptions as $article): ?>
                            <option value="<?= (int) $article['id'] ?>">
                                <?= htmlspecialchars($article['name']) ?>
                                <?php if (!empty($article['article_number'])): ?>
                                    (<?= htmlspecialchars((string) $article['article_number']) ?>)
                                <?php endif; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="unit_cost">Styckkostnad</label>
                    <input type="number" step="0.01" name="unit_cost" id="unit_cost" value="0">
                </div>

                <div>
                    <label for="planned_quantity">Planerad kvantitet</label>
                    <input type="number" step="0.01" name="planned_quantity" id="planned_quantity" value="0">
                </div>

                <div>
                    <label for="issued_quantity">Uttagen kvantitet</label>
                    <input type="number" step="0.01" name="issued_quantity" id="issued_quantity" value="0">
                </div>
            </div>

            <label for="material_notes">Notering</label>
            <textarea name="notes" id="material_notes" rows="4"></textarea>

            <div style="margin-top:16px;">
                <button type="submit">Lägg till material</button>
            </div>
        </form>
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
