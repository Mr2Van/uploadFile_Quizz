<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class reponses extends Model
{
    /** @use HasFactory<\Database\Factories\ReponsesFactory> */
    use HasFactory;


    protected $fillable = [
        'libelle',
        'description',
        'est_correcte',
        'question_id',
    ];

    public function question()
    {
        return $this->belongsTo(questions::class,   "question_id");
    }

    
}
