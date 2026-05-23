<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FunctionEffect extends Model
{
    protected $fillable = ['function_name', 'category', 'points'];

    public static function getMatrixByFunction()
    {
        $effects = self::all();
        $matrix = [];

        foreach ($effects as $effect) {
            if (!isset($matrix[$effect->function_name])) {
                $matrix[$effect->function_name] = [];
            }
            $matrix[$effect->function_name][$effect->category] = $effect->points;
        }

        return $matrix;
    }
}
