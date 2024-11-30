<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class questions extends Model
{
    /** @use HasFactory<\Database\Factories\QuestionsFactory> */
    use HasFactory;

    protected $fillable = [
        'enonce',
        'points',
        'qcms_id'
    ];



    public function qcms()
    {
        return $this->belongsTo(qcms::class,'qcms_id');
    }


    public function reponses()
    {
        return $this->hasMany(reponses::class, 'question_id');  
    }


    public function bonneReponse()
    {
        return $this->hasOne(reponses::class)->where('est_correcte', true);
    }
}
