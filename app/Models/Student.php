<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Student extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = ['name', 'no_wa', 'group_id', 'class', 'avatar', 'birthdate', 'gender', 'parent_name', 'parent_phone'];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function classrooms()
    {
        return $this->belongsToMany(Classroom::class);
    }

    public function events()
    {
        // Access events through reports
        return $this->belongsToMany(Event::class, 'reports', 'student_id', 'event_id')
            ->withPivot('status')
            ->withTimestamps();
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }
}
