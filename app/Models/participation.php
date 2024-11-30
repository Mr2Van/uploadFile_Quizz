<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class participation extends Model
{
    /** @use HasFactory<\Database\Factories\ParticipationFactory> */
    use HasFactory;
    protected $fillable = [
        'user_id', 
        'qcm_id', 
        'score', 
        'total_points', 
        'date_participation'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function qcm()
    {
        return $this->belongsTo(qcms::class);
    }
}
