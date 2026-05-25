<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\GridCell;

$cells = GridCell::orderBy('id')->get();
$pos = 0;
/** @var GridCell $c */
foreach ($cells as $c) {
    $c->position = $pos;
    $c->linked_group_id = null;
    $c->save();
    echo "Set id={$c->id} -> pos={$pos}\n";
    $pos++;
}

echo "Reset complete.\n";
