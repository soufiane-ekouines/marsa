<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enjin extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'Nom_enjin',
        'Modele_enjin',
        'Matricule',
        'Kilometrage',
        'Etat',
        'Commentaire',
        'famille_enjin_id',
    ];

    function famille_enjin()  {
        return $this->belongsTo(famille_enjin::class);
    }
    function entrer()  {
        return $this->hasOne(Sortie::class,"engin_id");
    }
    function sortie()  {
        return $this->hasOne(Entrer::class,"engin_id");
    }
}
