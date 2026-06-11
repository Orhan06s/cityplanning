<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SimulationEvent extends Model
{
    protected $fillable = ['type', 'custom_label', 'custom_icon', 'location', 'start_date', 'start_time', 'end_date', 'end_time', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active'  => 'boolean',
            'start_date' => 'date',
            'end_date'   => 'date',
        ];
    }

    // Geldt het evenement voor de hele grid (geen specifieke locatie)?
    public function isGridWide(): bool
    {
        return $this->location === null;
    }

    // Loopt het evenement vandaag (binnen de ingestelde periode)?
    public function isCurrentlyActive(): bool
    {
        $today = now()->startOfDay();

        return $this->is_active
            && !$this->start_date->startOfDay()->gt($today)
            && !$this->end_date->startOfDay()->lt($today);
    }

    // Status van het evenement: gepland, actief of verlopen.
    public function getStatusAttribute(): string
    {
        $today = now()->startOfDay();

        if ($this->start_date->startOfDay()->gt($today)) {
            return 'gepland';
        }

        if ($this->end_date->startOfDay()->lt($today)) {
            return 'verlopen';
        }

        return 'actief';
    }
}
