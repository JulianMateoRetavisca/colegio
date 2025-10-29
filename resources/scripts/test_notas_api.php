<?php
// script de prueba para invocar los métodos de NotasController sin HTTP
require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\NotasController;
use Illuminate\Http\Request;

$controller = new NotasController();

echo "--- listaGrupos() ---\n";
$res = $controller->listaGrupos();
if (method_exists($res, 'getContent')) {
    echo $res->getContent() . "\n";
} else {
    var_export($res);
}

echo "\n--- listaEstudiantes() ---\n";
$res = $controller->listaEstudiantes();
if (method_exists($res, 'getContent')) {
    echo $res->getContent() . "\n";
} else {
    var_export($res);
}

echo "\n--- filtrar() sin parámetros ---\n";
$req = Request::create('/notas/filtros', 'GET', []);
$res = $controller->filtrar($req);
if (method_exists($res, 'getContent')) {
    echo $res->getContent() . "\n";
} else { var_export($res); }

echo "\n--- filtrar() con usuario=3 ---\n";
$req = Request::create('/notas/filtros', 'GET', ['usuario' => 3]);
$res = $controller->filtrar($req);
if (method_exists($res, 'getContent')) {
    echo $res->getContent() . "\n";
} else { var_export($res); }

echo "\n--- porGrupo(grupoId=1) ---\n";
$res = $controller->porGrupo(1);
if (method_exists($res, 'getContent')) {
    echo $res->getContent() . "\n";
} else { var_export($res); }

echo "\n--- estudiantesPorGrupo(1) ---\n";
$res = $controller->estudiantesPorGrupo(1);
if (method_exists($res, 'getContent')) {
    echo $res->getContent() . "\n";
} else { var_export($res); }

echo "\n--- porEstudiante(3) ---\n";
$res = $controller->porEstudiante(3);
if (method_exists($res, 'getContent')) {
    echo $res->getContent() . "\n";
} else { var_export($res); }
