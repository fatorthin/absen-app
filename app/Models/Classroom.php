<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Classroom extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = ['name'];

    protected $casts = [
        'student_id' => 'array',
    ];

    public function students()
    {
        return $this->belongsToMany(Student::class);
    }
}
