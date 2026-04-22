<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Destination extends Model
{
    protected $fillable = ['name', 'name_en', 'category', 'icon', 'description'];

    public function gridCells()
    {
        return $this->hasMany(GridCell::class);
    }

    // Geeft de naam terug in de huidige taal
    public function getNaam()
    {
        return app()->getLocale() === 'en' ? $this->name_en : $this->name;
    }
}
