<!doctype html>
<html lang="sv">
<head>
    <meta charset="utf-8">
    <title>Skapa arbetsorder</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 24px; }
        form { max-width: 850px; }
        label { display:block; margin-top:14px; font-weight:bold; }
        input, select, textarea {
            width:100%; padding:10px; margin-top:6px; box-sizing:border-box;
        }
        .actions { margin-top:20px; }
        .error { background:#ffe5e5; color:#900; padding:10px; margin-bottom:14px; border-radius:6px; }
    </style>
</head>
<body>
    <h1>Skapa arbetsorder</h1>

    <?php foreach ($errors as $error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endforeach; ?>

    <form method="POST" action="/maintenance/work-orders">
        <label for="asset_node_id">Asset</label>
        <select name="asset_node_id" id="asset_node_id" required>
            <option value="">Välj asset</option>
            <?php foreach ($assetOptions as $asset): ?>
                <option value="<?= (int) $asset['id'] ?>" <?= ((string) ($workOrder['asset_node_id'] ?? '') === (string) $asset['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($asset['name']) ?> (<?= htmlspecialchars($asset['node_type']) ?><?= !empty($asset['code']) ? ' / ' . htmlspecialchars($asset['code']) : '' ?>)
                </option>
            <?php endforeach; ?>
        </select>

        <label for="title">Titel</label>
        <input type="text" name="title" id="title" required value="<?= htmlspecialchars($workOrder['title'] ?? '') ?>">

        <label for="description">Beskrivning</label>
        <textarea name="description" id="description" rows="6"><?= htmlspecialchars($workOrder['description'] ?? '') ?></textarea>

        <label for="type">Typ</label>
        <select name="type" id="type">
            <option value="corrective" <?= (($workOrder['type'] ?? '') === 'corrective') ? 'selected' : '' ?>>Corrective</option>
            <option value="preventive" <?= (($workOrder['type'] ?? '') === 'preventive') ? 'selected' : '' ?>>Preventive</option>
            <option value="inspection" <?= (($workOrder['type'] ?? '') === 'inspection') ? 'selected' : '' ?>>Inspection</option>
            <option value="emergency" <?= (($workOrder['type'] ?? '') === 'emergency') ? 'selected' : '' ?>>Emergency</option>
        </select>

        <label for="priority">Prioritet</label>
        <select name="priority" id="priority">
            <option value="low" <?= (($workOrder['priority'] ?? '') === 'low') ? 'selected' : '' ?>>Låg</option>
            <option value="medium" <?= (($workOrder['priority'] ?? 'medium') === 'medium') ? 'selected' : '' ?>>Medium</option>
            <option value="high" <?= (($workOrder['priority'] ?? '') === 'high') ? 'selected' : '' ?>>Hög</option>
            <option value="critical" <?= (($workOrder['priority'] ?? '') === 'critical') ? 'selected' : '' ?>>Kritisk</option>
        </select>

        <label for="status">Startstatus</label>
        <select name="status" id="status">
            <option value="reported" <?= (($workOrder['status'] ?? 'reported') === 'reported') ? 'selected' : '' ?>>Reported</option>
            <option value="approved" <?= (($workOrder['status'] ?? '') === 'approved') ? 'selected' : '' ?>>Approved</option>
            <option value="planned" <?= (($workOrder['status'] ?? '') === 'planned') ? 'selected' : '' ?>>Planned</option>
        </select>

        <label for="planned_start_at">Planerad start</label>
        <input type="datetime-local" name="planned_start_at" id="planned_start_at" value="<?= htmlspecialchars($workOrder['planned_start_at'] ?? '') ?>">

        <label for="due_at">Förfallodatum</label>
        <input type="datetime-local" name="due_at" id="due_at" value="<?= htmlspecialchars($workOrder['due_at'] ?? '') ?>">

        <label for="estimated_hours">Estimerade timmar</label>
        <input type="number" step="0.01" name="estimated_hours" id="estimated_hours" value="<?= htmlspecialchars((string) ($workOrder['estimated_hours'] ?? '')) ?>">

        <div class="actions">
            <button type="submit">Spara</button>
            <a href="/maintenance/work-orders">Tillbaka</a>
        </div>
    </form>
</body>
</html>
