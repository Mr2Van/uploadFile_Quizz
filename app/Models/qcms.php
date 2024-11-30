<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class qcms extends Model
{
    /** @use HasFactory<\Database\Factories\QcmsFactory> */
    use HasFactory;

    protected $fillable = [
        'titre',
        'description',
        'duration',
        'start_date',
        'end_date',
        'est_actif',
        'cours_id',
    ];

    protected $dates = ['start_date', 'end_date'];

    public function cours()
    {
        return $this->belongsTo(cours::class,'professeur_id');
    }

    public function questions()
    {
        return $this->hasMany(questions::class);
    }
}
