<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'user_id', 'phone', 'address', 'salary', 'joining_date'
    ];

    // Relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // app/Models/Employee.php
    public function shifts()
    {
        return $this->hasMany(Shift::class);
    }
    // app/Models/Employee.php
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
