<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class files extends Model
{
    /** @use HasFactory<\Database\Factories\FilesFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'titre',
        'description',
        'path',
        'url',
        'type',
        'cours_id',
    ];

    public function cours()
    {
        return $this->belongsTo(cours::class,'professeur_id');
    }

}
