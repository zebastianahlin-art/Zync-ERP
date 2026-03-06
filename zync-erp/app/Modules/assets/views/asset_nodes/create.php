<!doctype html>
<html lang="sv">
<head>
    <meta charset="utf-8">
    <title>Skapa asset-nod</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 24px; }
        form { max-width: 700px; }
        label { display:block; margin-top:14px; font-weight:bold; }
        input, select, textarea {
            width:100%; padding:10px; margin-top:6px; box-sizing:border-box;
        }
        .actions { margin-top:20px; }
        .error { background:#ffe5e5; color:#900; padding:10px; margin-bottom:14px; border-radius:6px; }
    </style>
</head>
<body>
    <h1>Skapa asset-nod</h1>

    <?php foreach ($errors as $error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endforeach; ?>

    <form method="POST" action="/assets">
        <label for="node_type">Typ</label>
        <select name="node_type" id="node_type" required>
            <option value="">Välj typ</option>
            <option value="site" <?= (($assetNode['node_type'] ?? '') === 'site') ? 'selected' : '' ?>>Site</option>
            <option value="area" <?= (($assetNode['node_type'] ?? '') === 'area') ? 'selected' : '' ?>>Area</option>
            <option value="line" <?= (($assetNode['node_type'] ?? '') === 'line') ? 'selected' : '' ?>>Line</option>
            <option value="machine" <?= (($assetNode['node_type'] ?? '') === 'machine') ? 'selected' : '' ?>>Machine</option>
            <option value="component" <?= (($assetNode['node_type'] ?? '') === 'component') ? 'selected' : '' ?>>Component</option>
        </select>

        <label for="parent_id">Parent</label>
        <select name="parent_id" id="parent_id">
            <option value="">Ingen</option>
            <?php foreach ($parents as $parent): ?>
                <option value="<?= (int) $parent['id'] ?>"
                    <?= ((string) ($assetNode['parent_id'] ?? '') === (string) $parent['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($parent['name']) ?> (<?= htmlspecialchars($parent['node_type']) ?>)
                </option>
            <?php endforeach; ?>
        </select>

        <label for="name">Namn</label>
        <input type="text" name="name" id="name" required value="<?= htmlspecialchars($assetNode['name'] ?? '') ?>">

        <label for="code">Kod</label>
        <input type="text" name="code" id="code" value="<?= htmlspecialchars($assetNode['code'] ?? '') ?>">

        <label for="description">Beskrivning</label>
        <textarea name="description" id="description" rows="5"><?= htmlspecialchars($assetNode['description'] ?? '') ?></textarea>

        <label for="status">Status</label>
        <select name="status" id="status">
            <option value="active" <?= (($assetNode['status'] ?? 'active') === 'active') ? 'selected' : '' ?>>Aktiv</option>
            <option value="inactive" <?= (($assetNode['status'] ?? '') === 'inactive') ? 'selected' : '' ?>>Inaktiv</option>
            <option value="archived" <?= (($assetNode['status'] ?? '') === 'archived') ? 'selected' : '' ?>>Arkiverad</option>
        </select>

        <label for="sort_order">Sortering</label>
        <input type="number" name="sort_order" id="sort_order" value="<?= htmlspecialchars((string) ($assetNode['sort_order'] ?? 0)) ?>">

        <div class="actions">
            <button type="submit">Spara</button>
            <a href="/assets">Tillbaka</a>
        </div>
    </form>
</body>
</html>
