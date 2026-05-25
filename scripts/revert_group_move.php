<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\GridCell;
use Illuminate\Http\Request;
use App\Http\Controllers\MetropolisController;

// Find an empty cell at position 0
$slot0 = GridCell::where('position', 0)->first();
if (!$slot0) {
    echo "No cell at position 0 found.\n";
    exit(1);
}

if ($slot0->destination_id !== null || $slot0->linked_group_id !== null) {
    echo "Position 0 is not empty; can't safely revert automatically.\n";
    exit(1);
}

// Find any linked group (prefer one where min position > 0)
$groupCell = GridCell::whereNotNull('linked_group_id')->orderBy('position')->first();
if (!$groupCell) {
    echo "No linked group found to revert.\n";
    exit(1);
}

$groupId = $groupCell->linked_group_id;
// pick the group member with smallest position as source
$source = GridCell::where('linked_group_id', $groupId)->orderBy('position')->first();

echo "Attempting to move group {$groupId} (source cell {$source->id} pos {$source->position}) to position 0 (cell id {$slot0->id}).\n";

$controller = new MetropolisController();
$request = Request::create('/group/move', 'POST', [
    'source_cell_id' => $source->id,
    'target_cell_id' => $slot0->id,
]);

$response = $controller->moveGroup($request);
if ($response instanceof Illuminate\Http\JsonResponse) {
    $data = $response->getData(true);
    print_r($data);
    if (!empty($data['success'])) {
        echo "Revert succeeded.\n";
        exit(0);
    }
}

echo "Revert failed.\n";
exit(1);
