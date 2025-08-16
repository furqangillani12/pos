<?php

// app/Models/Attendance.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = [
        'employee_id', 'date', 'check_in', 'check_out', 'status', 'notes'
    ];
    protected $casts = [
        'date' => 'date',
    ];


    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
