<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GridCell extends Model
{
    protected $fillable = ['position', 'destination_id', 'linked_group_id'];

    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }

    public static function normalizedGrid(int $columns = 4, int $rows = 3)
    {
        $gridSize = $columns * $rows;
        /** @var \Illuminate\Database\Eloquent\Collection<int, self> $cells */
        $cells = self::with('destination')
            ->orderBy('position')
            ->orderBy('id')
            ->get();

        $unique = [];
        /** @var self[] $orphans */
        $orphans = [];

        /** @var self $cell */
        foreach ($cells as $cell) {
            $pos = $cell->position;
            if ($pos >= 0 && $pos < $gridSize && !isset($unique[$pos])) {
                $unique[$pos] = $cell;
            } else {
                $orphans[] = $cell;
            }
        }

        for ($pos = 0; $pos < $gridSize; $pos++) {
            if (!isset($unique[$pos])) {
                if (count($orphans) > 0) {
                    /** @var self $cell */
                    $cell = array_shift($orphans);
                    $cell->position = $pos;
                    $cell->linked_group_id = null;
                    $cell->save();
                    $unique[$pos] = $cell;
                } else {
                    $unique[$pos] = self::create([
                        'position' => $pos,
                        'destination_id' => null,
                        'linked_group_id' => null,
                    ]);
                }
            }
        }

        /** @var self $cell */
        foreach ($orphans as $cell) {
            $cell->destination_id = null;
            $cell->linked_group_id = null;
            $cell->save();
        }

        return collect($unique)->sortKeys()->values();
    }
}
