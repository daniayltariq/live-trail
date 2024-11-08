<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{

    protected $table = 'attendance';

    protected $fillable = [
        'user_id',
        'date',
        'time_in',
        'time_out',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
