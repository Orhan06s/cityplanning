<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DayNightModifier extends Model
{
    /**
    
     *
     * @var string
     */
    protected $table = 'day_night_modifiers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'function_name',
        'category',
        'day_modifier',
        'night_modifier',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;
}