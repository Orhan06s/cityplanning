<?php

// Boots the Laravel app and programmatically invokes MetropolisController::moveGroup
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

// Create a kernel to set up the container
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\GridCell;
use Illuminate\Http\Request;
use App\Http\Controllers\MetropolisController;

echo "Searching for a linked group and an empty target...\n";

$sourceCell = GridCell::whereNotNull('linked_group_id')->first();
if (!$sourceCell) {
    echo "No existing linked group found — will create a temporary one.\n";
    // find any occupied cell to use as source
    $sourceCell = GridCell::whereNotNull('destination_id')->first();
    if (!$sourceCell) {
        echo "No occupied cell available to create temporary group.\n";
        exit(1);
    }
}

// Prepare a temporary linked group if source had no group
$controller = new MetropolisController();
$tried = 0;
$tempGroupCreated = false;
$originalLinked = $sourceCell->linked_group_id;
if (!$originalLinked) {
    $max = GridCell::max('linked_group_id');
    $newGroupId = ($max ? (int)$max : 0) + 1;
    $sourceCell->linked_group_id = $newGroupId;
    $sourceCell->save();
    $tempGroupCreated = true;
    echo "Temporary group {$newGroupId} assigned to source cell {$sourceCell->id}.\n";
}

$emptyCells = GridCell::whereNull('destination_id')->where('linked_group_id', null)->get();
foreach ($emptyCells as $empty) {
    if ($empty->id === $sourceCell->id) continue;
    $tried++;
    echo "Trying target id {$empty->id} (pos {$empty->position})...\n";
    $request = Request::create('/group/move', 'POST', [
        'source_cell_id' => $sourceCell->id,
        'target_cell_id' => $empty->id,
    ]);
    $response = $controller->moveGroup($request);
    $data = null;
    if ($response instanceof Illuminate\Http\JsonResponse) {
        $data = $response->getData(true);
    }
    echo "Result: ";
    print_r($data);
    if (!empty($data['success'])) {
        echo "Move succeeded to target {$empty->id}. Verifying DB changes...\n";
        $groupId = $sourceCell->linked_group_id;
        $groupCells = GridCell::where('linked_group_id', $groupId)->get();
        foreach ($groupCells as $c) {
            echo "Cell {$c->id} -> position {$c->position}\n";
        }
        // revert: move cells back and remove temp group if created
        if ($tempGroupCreated) {
            /** @var GridCell $c */
            foreach ($groupCells as $c) {
                // revert linked_group_id and do not change position here because we moved from original source
                if ($c->id == $sourceCell->id) {
                    $c->position = $sourceCell->position; // original pos
                    $c->linked_group_id = null;
                    $c->save();
                }
            }
            echo "Reverted temporary group and position.\n";
        }
        exit(0);
    }
}

echo "Tried {$tried} empty targets; no valid move found.\n";

// cleanup temporary group if created
if ($tempGroupCreated) {
    $sourceCell->linked_group_id = $originalLinked;
    $sourceCell->save();
    echo "Cleaned up temporary group assignment on source cell.\n";
}

exit(1);
