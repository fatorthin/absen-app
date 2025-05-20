<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Staff extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = ['uuid','name','group_id','phone', 'role'];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_staff')
                ->using(EventStaff::class)
                ->withPivot('status')
                ->withTimestamps();
    }
}
