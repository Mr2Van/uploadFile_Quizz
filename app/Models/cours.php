<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cours extends Model
{
    /** @use HasFactory<\Database\Factories\CoursFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'date_cours',
        'professeur_id'
    ];

    public function files()
    {
        return $this->hasMany(files::class,'professeur_id');


    }
    public function qcms()
    {
        return $this->hasMany(qcms::class, 'professeur_id');
    }

    public function professeur()
    {
        return $this->belongsTo(User::class,'professeur_id');
    }
}
