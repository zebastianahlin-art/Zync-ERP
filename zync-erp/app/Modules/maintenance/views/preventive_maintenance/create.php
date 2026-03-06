<!doctype html>
<html lang="sv">
<head>
    <meta charset="utf-8">
    <title>Nytt PM-schema</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 24px; }
        form { max-width: 850px; }
        label { display:block; margin-top:14px; font-weight:bold; }
        input, select, textarea {
            width:100%; padding:10px; margin-top:6px; box-sizing:border-box;
        }
        .actions { margin-top:20px; }
        .error { background:#ffe5e5; color:#900; padding:10px; margin-bottom:14px; border-radius:6px; }
        .checkbox-row { margin-top:16px; display:flex; align-items:center; gap:10px; }
        .checkbox-row input { width:auto; margin:0; }
    </style>
</head>
<body>
    <h1>Nytt PM-schema</h1>

    <?php foreach ($errors as $error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endforeach; ?>

    <form method="POST" action="/maintenance/preventive">
        <label for="asset_node_id">Asset</label>
        <select name="asset_node_id" id="asset_node_id" required>
            <option value="">Välj asset</option>
            <?php foreach ($assetOptions as $asset): ?>
                <option value="<?= (int) $asset['id'] ?>" <?= ((string) ($schedule['asset_node_id'] ?? '') === (string) $asset['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($asset['name']) ?> (<?= htmlspecialchars($asset['node_type']) ?><?= !empty($asset['code']) ? ' / ' . htmlspecialchars($asset['code']) : '' ?>)
                </option>
            <?php endforeach; ?>
        </select>

        <label for="title">Titel</label>
        <input type="text" name="title" id="title" required value="<?= htmlspecialchars($schedule['title'] ?? '') ?>">

        <label for="description">Beskrivning</label>
        <textarea name="description" id="description" rows="5"><?= htmlspecialchars($schedule['description'] ?? '') ?></textarea>

        <label for="interval_type">Intervalltyp</label>
        <select name="interval_type" id="interval_type">
            <option value="daily" <?= (($schedule['interval_type'] ?? '') === 'daily') ? 'selected' : '' ?>>Dagligen</option>
            <option value="weekly" <?= (($schedule['interval_type'] ?? '') === 'weekly') ? 'selected' : '' ?>>Veckovis</option>
            <option value="monthly" <?= (($schedule['interval_type'] ?? 'monthly') === 'monthly') ? 'selected' : '' ?>>Månadsvis</option>
            <option value="yearly" <?= (($schedule['interval_type'] ?? '') === 'yearly') ? 'selected' : '' ?>>Årligen</option>
        </select>

        <label for="interval_value">Varje X intervall</label>
        <input type="number" min="1" name="interval_value" id="interval_value" value="<?= htmlspecialchars((string) ($schedule['interval_value'] ?? 1)) ?>">

        <label for="next_due_at">Första förfallodatum</label>
        <input type="datetime-local" name="next_due_at" id="next_due_at" value="<?= htmlspecialchars($schedule['next_due_at'] ?? '') ?>">

        <label for="priority">Prioritet</label>
        <select name="priority" id="priority">
            <option value="low" <?= (($schedule['priority'] ?? '') === 'low') ? 'selected' : '' ?>>Låg</option>
            <option value="medium" <?= (($schedule['priority'] ?? 'medium') === 'medium') ? 'selected' : '' ?>>Medium</option>
            <option value="high" <?= (($schedule['priority'] ?? '') === 'high') ? 'selected' : '' ?>>Hög</option>
            <option value="critical" <?= (($schedule['priority'] ?? '') === 'critical') ? 'selected' : '' ?>>Kritisk</option>
        </select>

        <label for="estimated_hours">Estimerade timmar</label>
        <input type="number" step="0.01" name="estimated_hours" id="estimated_hours" value="<?= htmlspecialchars((string) ($schedule['estimated_hours'] ?? '')) ?>">

        <label for="default_work_order_type">Standard arbetsordertyp</label>
        <select name="default_work_order_type" id="default_work_order_type">
            <option value="preventive" <?= (($schedule['default_work_order_type'] ?? 'preventive') === 'preventive') ? 'selected' : '' ?>>Preventive</option>
            <option value="inspection" <?= (($schedule['default_work_order_type'] ?? '') === 'inspection') ? 'selected' : '' ?>>Inspection</option>
        </select>

        <div class="checkbox-row">
            <input
                type="checkbox"
                name="auto_create_work_order"
                id="auto_create_work_order"
                value="1"
                <?= (int) ($schedule['auto_create_work_order'] ?? 1) === 1 ? 'checked' : '' ?>
            >
            <label for="auto_create_work_order" style="margin:0; font-weight:normal;">Skapa arbetsorder automatiskt</label>
        </div>

        <div class="actions">
            <button type="submit">Spara</button>
            <a href="/maintenance/preventive">Tillbaka</a>
        </div>
    </form>
</body>
</html>
