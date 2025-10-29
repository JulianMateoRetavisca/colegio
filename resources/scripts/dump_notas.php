<?php
// Script de diagnóstico: vuelca algunas notas con relación a estudiante en JSON
require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\NotaModel;

$notas = NotaModel::with('estudiante')->take(50)->get()->map(function($n){
    return [
        'id' => $n->id,
        'estudiante_id' => $n->estudiante_id,
        'estudiante_name' => $n->estudiante ? $n->estudiante->name : null,
        'materia_id' => $n->materia_id,
        'nota' => (string)$n->nota,
        'periodo' => $n->periodo,
    ];
});

echo json_encode($notas->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
