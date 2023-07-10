<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sortie extends Model
{
    use HasFactory;
    protected $table = 'sortie';

    protected $fillable = [
        'matricule',
        'societe',
        'nom',
        'prenom',
        'compteur',
        'engin_id',
    ];

    function engin()  {
        return $this->belongsTo(Enjin::class,'engin_id');
    }
}
