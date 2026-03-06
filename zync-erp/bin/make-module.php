<?php

if ($argc < 2) {
    echo "Usage: php bin/make-module.php ModuleName\n";
    exit;
}

$module = ucfirst($argv[1]);

$base = __DIR__ . '/../app/Modules/' . $module;

if (is_dir($base)) {
    echo "Module already exists: $module\n";
    exit;
}

$folders = [
    $base,
    "$base/Controllers",
    "$base/Services",
    "$base/Models",
    "$base/Routes",
    "$base/Views"
];

foreach ($folders as $folder) {
    mkdir($folder, 0755, true);
    echo "Created: $folder\n";
}

file_put_contents(
"$base/Controllers/{$module}Controller.php",
"<?php

namespace App\Modules\\$module\Controllers;

use App\Core\Controller;

class {$module}Controller extends Controller
{
    public function index()
    {
        return \$this->view('".strtolower($module)."/index');
    }
}
");

file_put_contents(
"$base/Services/{$module}Service.php",
"<?php

namespace App\Modules\\$module\Services;

class {$module}Service
{

}
");

file_put_contents(
"$base/Routes/routes.php",
"<?php

\$router->get('/".strtolower($module)."', '{$module}Controller@index');
");

file_put_contents(
"$base/Views/index.php",
"<h1>$module module</h1>"
);

echo "Module $module created successfully\n";