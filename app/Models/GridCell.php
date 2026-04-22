<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GridCell extends Model
{
    protected $fillable = ['position', 'destination_id'];

    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }
}
