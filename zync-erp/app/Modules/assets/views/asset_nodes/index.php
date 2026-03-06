<?php

declare(strict_types=1);

function renderAssetTree(array $nodes, int $depth = 0): void
{
    foreach ($nodes as $node) {
        $indent = str_repeat('&mdash; ', $depth);
        ?>
        <tr>
            <td><?= $indent . htmlspecialchars($node['name']) ?></td>
            <td><?= htmlspecialchars($node['node_type']) ?></td>
            <td><?= htmlspecialchars((string) ($node['code'] ?? '')) ?></td>
            <td><?= htmlspecialchars($node['status']) ?></td>
            <td>
                <a href="/assets/edit?id=<?= (int) $node['id'] ?>">Redigera</a>

                <form action="/assets/archive" method="POST" style="display:inline;">
                    <input type="hidden" name="id" value="<?= (int) $node['id'] ?>">
                    <button type="submit" onclick="return confirm('Arkivera noden?')">Arkivera</button>
                </form>
            </td>
        </tr>
        <?php

        if (!empty($node['children'])) {
            renderAssetTree($node['children'], $depth + 1);
        }
    }
}
?>
<!doctype html>
<html lang="sv">
<head>
    <meta charset="utf-8">
    <title>Assetstruktur</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 24px; }
        .topbar { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; }
        table { width:100%; border-collapse:collapse; }
        th, td { border:1px solid #ddd; padding:10px; text-align:left; }
        th { background:#f5f5f5; }
        a.button {
            display:inline-block; padding:10px 14px; background:#111; color:#fff;
            text-decoration:none; border-radius:6px;
        }
        button { padding:8px 12px; }
    </style>
</head>
<body>
    <div class="topbar">
        <h1>Assetstruktur</h1>
        <a class="button" href="/assets/create">Ny nod</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Namn</th>
                <th>Typ</th>
                <th>Kod</th>
                <th>Status</th>
                <th>Åtgärder</th>
            </tr>
        </thead>
        <tbody>
            <?php renderAssetTree($tree); ?>
        </tbody>
    </table>
</body>
</html>
