<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use App\Models\GridCell;
$cells = GridCell::orderBy('position')->get();
foreach ($cells as $c) {
    echo "id={$c->id} pos={$c->position} dest=" . ($c->destination_id ?? 'NULL') . " linked=" . ($c->linked_group_id ?? 'NULL') . "\n";
}
