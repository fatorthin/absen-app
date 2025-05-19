<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'date', 'description', 'classroom_id'];

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function students()
    {
        // Access students through reports
        return $this->belongsToMany(Student::class, 'reports', 'event_id', 'student_id')
            ->withPivot('status')
            ->withTimestamps();
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }
}
