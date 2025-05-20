<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class EventStaff extends Pivot
{
    /**
     * Tabel yang terkait dengan model
     */
    protected $table = 'event_staff';

    /**
     * Indicates if the IDs are auto-incrementing.
     */
    public $incrementing = true;
    
    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = true;

    /**
     * Daftar atribut yang dapat diisi
     */
    protected $fillable = [
        'event_id',
        'staff_id',
        'status',
    ];

    /**
     * Relasi ke model Event
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Relasi ke model Staff
     */
    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}
